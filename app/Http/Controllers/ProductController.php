<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends Controller
{
    public function __construct()
{
    $this->middleware('permission:products.view')->only('index', 'show');
    $this->middleware('permission:products.create')->only('create', 'store');
    $this->middleware('permission:products.edit')->only('edit', 'update', 'toggleActive', 'toggleFeatured');
    $this->middleware('permission:products.delete')->only('destroy');
}

    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->when($request->category_id, function($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->stock_status, function($query, $status) {
                if ($status === 'low') {
                    $query->whereRaw('stock <= min_stock');
                } elseif ($status === 'out') {
                    $query->where('stock', 0);
                }
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::where('active', 1)->orderBy('name')->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id', 'stock_status', 'sort', 'direction'])
        ]);
    }

    public function create()
    {
        $categories = Category::where('active', 1)->orderBy('name')->get();
        
        return Inertia::render('Products/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:1024',
            'active' => 'boolean',
            'featured' => 'boolean',
            'barcode' => 'nullable|unique:products,barcode'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sku'] = $this->generateSku($validated['name'], $validated['category_id']);
        
        if (empty($validated['barcode'])) {
            $validated['barcode'] = $this->generateBarcode();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Registrar movimiento de inventario inicial
        if ($product->stock > 0) {
            $this->registerInventoryMovement(
                $product,
                $product->stock,
                0,
                $product->stock,
                'entry',
                'initial',
                $product->id,
                'Inventario inicial'
            );
        }

        return redirect()->route('products.index')
            ->with('success', 'Producto creado correctamente');
    }

    public function show(Product $product)
    {
        $product->load('category');
        
        // Generar imagen del código de barras
        $generator = new BarcodeGeneratorPNG();
        $barcodeImage = base64_encode($generator->getBarcode($product->barcode, $generator::TYPE_CODE_128));
        
        return Inertia::render('Products/Show', [
            'product' => $product,
            'barcodeImage' => $barcodeImage
        ]);
    }

    public function edit(Product $product)
    {
        $product->load('category');
        $categories = Category::where('active', 1)->orderBy('name')->get();
        
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:1024',
            'active' => 'boolean',
            'featured' => 'boolean',
            'barcode' => 'nullable|unique:products,barcode,' . $product->id
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        if (empty($validated['barcode'])) {
            $validated['barcode'] = $this->generateBarcode();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function toggleActive(Product $product)
    {
        $product->update([
            'active' => !$product->active
        ]);

        return back()->with('success', 'Estado de producto actualizado');
    }

    public function toggleFeatured(Product $product)
    {
        $product->update([
            'featured' => !$product->featured
        ]);

        return back()->with('success', 'Destacado de producto actualizado');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente');
    }

    protected function generateSku($name, $categoryId)
    {
        $category = Category::find($categoryId);
        $prefix = substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $category->name)), 0, 3);
        
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $id = $lastProduct ? $lastProduct->id + 1 : 1;
        
        return $prefix . '-' . str_pad($id, 5, '0', STR_PAD_LEFT);
    }

    protected function generateBarcode()
    {
        $prefix = '7896123'; // Prefijo personalizado
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $id = $lastProduct ? $lastProduct->id + 1 : 1;
        $barcode = $prefix . str_pad($id, 6, '0', STR_PAD_LEFT);
        
        // Añadir dígito verificador para EAN-13
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($barcode[$i]) * ($i % 2 === 0 ? 1 : 3);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        
        return $barcode . $checkDigit;
    }

    protected function registerInventoryMovement($product, $quantity, $previousStock, $currentStock, $type, $referenceType, $referenceId, $notes = null)
    {
        return \App\Models\InventoryMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
            'type' => $type,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes
        ]);
    }
}