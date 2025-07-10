<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Exports\StockMovementsExport;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_movements' => StockMovement::count(),
            'total_value' => Product::sum(\DB::raw('price * quantity')),
            'low_stock_products' => Product::where('quantity', '<=', 'min_quantity')->where('quantity', '>', 0)->count(),
            'out_of_stock_products' => Product::where('quantity', 0)->count(),
        ];

        // Produits par catégorie
        $productsByCategory = Category::withCount('products')->get();

        // Mouvements récents
        $recentMovements = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Produits en rupture
        $outOfStockProducts = Product::where('quantity', 0)->get();

        // Produits en stock faible
        $lowStockProducts = Product::where('quantity', '<=', 'min_quantity')
            ->where('quantity', '>', 0)
            ->get();

        return view('reports.index', compact(
            'stats',
            'productsByCategory',
            'recentMovements',
            'outOfStockProducts',
            'lowStockProducts'
        ));
    }

    public function exportProducts()
    {
        return Excel::download(new ProductsExport, 'produits_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportStockMovements()
    {
        return Excel::download(new StockMovementsExport, 'mouvements_stock_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function stockValue()
    {
        $products = Product::with('category')
            ->orderBy('price', 'desc')
            ->get()
            ->map(function ($product) {
                $product->stock_value = $product->price * $product->quantity;
                return $product;
            });

        $totalValue = $products->sum('stock_value');

        return view('reports.stock-value', compact('products', 'totalValue'));
    }

    public function categoryReport()
    {
        $categories = Category::with(['products' => function ($query) {
            $query->select('id', 'category_id', 'name', 'price', 'quantity');
        }])->get();

        $categories = $categories->map(function ($category) {
            $category->total_products = $category->products->count();
            $category->total_value = $category->products->sum(function ($product) {
                return $product->price * $product->quantity;
            });
            $category->low_stock_count = $category->products->filter(function ($product) {
                return $product->quantity <= $product->min_quantity && $product->quantity > 0;
            })->count();
            $category->out_of_stock_count = $category->products->filter(function ($product) {
                return $product->quantity == 0;
            })->count();
            return $category;
        });

        return view('reports.category', compact('categories'));
    }

    public function movementReport(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(50);

        // Statistiques des mouvements
        $movementStats = [
            'total_entries' => StockMovement::where('type', 'in')->count(),
            'total_exits' => StockMovement::where('type', 'out')->count(),
            'total_adjustments' => StockMovement::where('type', 'adjustment')->count(),
        ];

        return view('reports.movements', compact('movements', 'movementStats'));
    }
}
