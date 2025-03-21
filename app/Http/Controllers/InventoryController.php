<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inventory.view')->only('index', 'movements');
        $this->middleware('permission:inventory.adjust')->only('adjust', 'processAdjustment');
        $this->middleware('permission:inventory.entry')->only('entry', 'processEntry');
    }

    public function index()
    {
        $lowStockProducts = Product::whereRaw('stock <= min_stock')
            ->with('category')
            ->get();
            
        return Inertia::render('Inventory/Index', [
            'lowStockProducts' => $lowStockProducts,
            'totalLowStock' => $lowStockProducts->count(),
            'outOfStockCount' => Product::where('stock', 0)->count(),
        ]);
    }
    
    public function movements(Request $request)
    {
        $movements = InventoryMovement::with(['product', 'user'])
            ->when($request->product_id, function($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->when($request->type, function($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
            
        $products = Product::orderBy('name')->get(['id', 'name']);
        
        return Inertia::render('Inventory/Movements', [
            'movements' => $movements,
            'products' => $products,
            'filters' => $request->only(['product_id', 'type', 'date_from', 'date_to'])
        ]);
    }
    
    public function adjust()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get();
            
        return Inertia::render('Inventory/Adjust', [
            'products' => $products
        ]);
    }
    
    public function processAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'new_stock' => 'required|integer|min:0',
            'notes' => 'required|string'
        ]);
        
        $product = Product::findOrFail($validated['product_id']);
        $previousStock = $product->stock;
        $newStock = $validated['new_stock'];
        $quantity = $newStock - $previousStock;
        
        DB::transaction(function() use ($product, $previousStock, $newStock, $quantity, $validated) {
            // Actualizar stock del producto
            $product->update(['stock' => $newStock]);
            
            // Registrar movimiento
            InventoryMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'quantity' => $quantity,
                'previous_stock' => $previousStock,
                'current_stock' => $newStock,
                'type' => 'adjustment',
                'reference_type' => 'manual_adjustment',
                'reference_id' => 0,
                'notes' => $validated['notes']
            ]);
        });
        
        return redirect()->route('inventory.movements')
            ->with('success', 'Ajuste de inventario realizado correctamente');
    }
    
    public function entry()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get();
            
        return Inertia::render('Inventory/Entry', [
            'products' => $products
        ]);
    }
    
    public function processEntry(Request $request)
    {
        $validated = $request->validate([
            'entries' => 'required|array',
            'entries.*.product_id' => 'required|exists:products,id',
            'entries.*.quantity' => 'required|integer|min:1',
            'reference' => 'required|string',
            'notes' => 'nullable|string'
        ]);
        
        DB::transaction(function() use ($validated) {
            foreach ($validated['entries'] as $entry) {
                $product = Product::findOrFail($entry['product_id']);
                $previousStock = $product->stock;
                $newStock = $previousStock + $entry['quantity'];
                
                // Actualizar stock del producto
                $product->update(['stock' => $newStock]);
                
                // Registrar movimiento
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'quantity' => $entry['quantity'],
                    'previous_stock' => $previousStock,
                    'current_stock' => $newStock,
                    'type' => 'entry',
                    'reference_type' => 'purchase',
                    'reference_id' => 0,
                    'notes' => $validated['notes'] ?? "Entrada de inventario: {$validated['reference']}"
                ]);
            }
        });
        
        return redirect()->route('inventory.movements')
            ->with('success', 'Entrada de inventario registrada correctamente');
    }
}