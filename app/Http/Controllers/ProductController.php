<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category');
        
        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        // Filtro de categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filtro de stock
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('stock <= min_stock AND stock > 0');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            }
        }
        
        // Ordenamiento
        $sort = $request->filled('sort') ? $request->sort : 'created_at';
        $direction = $request->filled('direction') ? $request->direction : 'desc';
        $query->orderBy($sort, $direction);
        
        // Exportación (si se solicita)
        if ($request->has('format') && $request->format === 'export') {
            return $this->export($query);
        }
        
        $products = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => Category::all(),
            'filters' => $request->all(['search', 'category_id', 'stock_status', 'sort', 'direction'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'nullable|string|max:255|unique:products',
            'image' => 'nullable|image|max:1024',
            'active' => 'boolean',
            'featured' => 'boolean',
        ]);
        
        // Crear slug
        $validated['slug'] = Str::slug($validated['name']);
        
        // Procesar imagen si existe
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        // Generar código de barras si no se proporciona
        if (empty($validated['barcode'])) {
            $validated['barcode'] = $this->generateBarcode();
        }
        
        // Crear el producto
        $product = Product::create($validated);
        
        // Generar SKU
        $product->sku = $this->generateSku($product);
        $product->save();
        
        // Registrar movimiento de inventario para el stock inicial
        if ($validated['stock'] > 0) {
            InventoryMovement::create([
                'product_id' => $product->id,
                'quantity' => $validated['stock'],
                'type' => 'initial',
                'notes' => 'Stock inicial',
                'user_id' => auth()->id()
            ]);
        }
        
        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente');
    }

    public function show(Product $product)
    {
        // Generar imagen del código de barras
        $generator = new BarcodeGeneratorPNG();
        $barcodeImage = base64_encode($generator->getBarcode($product->barcode, $generator::TYPE_CODE_128));
        
        return Inertia::render('Products/Show', [
            'product' => $product->load('category'),
            'barcodeImage' => $barcodeImage
        ]);
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'image' => 'nullable|image|max:1024',
            'active' => 'boolean',
            'featured' => 'boolean',
        ]);
        
        // Actualizar slug
        $validated['slug'] = Str::slug($validated['name']);
        
        // Procesar imagen si existe
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        // Si se solicitó eliminar la imagen
        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }
        
        // Actualizar el producto
        $product->update($validated);
        
        // Actualizar SKU si cambió la categoría
        if ($product->wasChanged('category_id')) {
            $product->sku = $this->generateSku($product);
            $product->save();
        }
        
        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente');
    }

    public function toggleActive(Product $product)
    {
        $product->active = !$product->active;
        $product->save();
        
        return back()->with('success', 'Estado del producto actualizado');
    }

    public function toggleFeatured(Product $product)
    {
        $product->featured = !$product->featured;
        $product->save();
        
        return back()->with('success', 'Estado destacado del producto actualizado');
    }

    private function generateSku(Product $product)
    {
        $categoryPrefix = $product->category ? strtoupper(substr($product->category->name, 0, 3)) : 'PRD';
        $productId = str_pad($product->id, 5, '0', STR_PAD_LEFT);
        
        return "{$categoryPrefix}-{$productId}";
    }

    private function generateBarcode()
    {
        do {
            // Generar un código EAN-13
            $prefix = '200'; // Prefijo para productos internos
            $number = $prefix . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
            
            // Calcular dígito de verificación
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += $number[$i] * ($i % 2 === 0 ? 1 : 3);
            }
            $checkDigit = (10 - ($sum % 10)) % 10;
            
            $barcode = $number . $checkDigit;
        } while (Product::where('barcode', $barcode)->exists());
        
        return $barcode;
    }

    public function export($query = null)
    {
        if (!$query) {
            $query = Product::query()->with('category');
        }
        
        $products = $query->get();
        
        $csvFileName = 'productos_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];
        
        $handle = fopen('php://temp', 'r+');
        
        // Cabeceras CSV
        fputcsv($handle, [
            'ID', 'SKU', 'Código de Barras', 'Nombre', 'Categoría', 
            'Precio de Compra', 'Precio de Venta', 'Stock', 'Stock Mínimo',
            'Activo', 'Destacado', 'Fecha de Creación'
        ]);
        
        // Datos
        foreach ($products as $product) {
            fputcsv($handle, [
                $product->id,
                $product->sku,
                $product->barcode,
                $product->name,
                $product->category ? $product->category->name : 'Sin categoría',
                $product->purchase_price,
                $product->sale_price,
                $product->stock,
                $product->min_stock,
                $product->active ? 'Sí' : 'No',
                $product->featured ? 'Sí' : 'No',
                $product->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200, $headers);
    }
}