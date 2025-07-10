<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('stock-movements.index', compact('movements'));
    }

    public function create()
    {
        $products = Product::all();
        return view('stock-movements.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500',
            'reference' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            $quantityBefore = $product->quantity;
            
            // Calculer la nouvelle quantité selon le type de mouvement
            $quantityChange = match($request->type) {
                'in' => $request->quantity,
                'out' => -$request->quantity,
                'adjustment' => $request->quantity - $quantityBefore
            };
            
            $quantityAfter = $quantityBefore + $quantityChange;
            
            // Vérifier que la quantité finale n'est pas négative
            if ($quantityAfter < 0) {
                throw new \Exception('La quantité finale ne peut pas être négative.');
            }
            
            // Créer le mouvement de stock
            StockMovement::create([
                'product_id' => $request->product_id,
                'type' => $request->type,
                'quantity' => abs($quantityChange),
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reason' => $request->reason,
                'reference' => $request->reference,
                'user_id' => auth()->id()
            ]);
            
            // Mettre à jour la quantité du produit
            $product->update(['quantity' => $quantityAfter]);
            
            // Vérifier les alertes après mise à jour
            if ($quantityAfter <= $product->min_quantity && $quantityAfter > 0) {
                NotificationService::createLowStockNotification($product);
            } elseif ($quantityAfter == 0) {
                NotificationService::createOutOfStockNotification($product);
            }
        });

        return redirect()->route('stock-movements.index')
            ->with('success', 'Mouvement de stock enregistré avec succès !');
    }

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['product', 'user']);
        return view('stock-movements.show', compact('stockMovement'));
    }

    public function productHistory(Product $product)
    {
        $movements = $product->stockMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('stock-movements.product-history', compact('product', 'movements'));
    }

    public function quickEntry()
    {
        $products = Product::all();
        return view('stock-movements.quick-entry', compact('products'));
    }

    public function quickExit()
    {
        $products = Product::where('quantity', '>', 0)->get();
        return view('stock-movements.quick-exit', compact('products'));
    }
}
