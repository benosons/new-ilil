<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard(\Illuminate\Http\Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

        $stockSoldStandard = \App\Models\OrderItem::whereHas('order', function ($q) use ($start, $end) {
            $q->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
              ->whereBetween('created_at', [$start, $end]);
        })->sum('quantity');
        
        $stockSoldStandalone = \App\Models\StandaloneOrderItem::whereHas('standaloneOrder', function ($q) use ($start, $end) {
            $q->whereIn('status', ['processed', 'completed'])
              ->whereBetween('created_at', [$start, $end]);
        })->sum('quantity');

        $stats = [
            'total_orders' => Order::whereBetween('created_at', [$start, $end])->count(),
            'total_standalone_orders' => \App\Models\StandaloneOrder::whereBetween('created_at', [$start, $end])->count(),
            'pending_orders' => Order::whereBetween('created_at', [$start, $end])->where('status', 'pending')->count(),
            'paid_orders' => Order::whereBetween('created_at', [$start, $end])->where('status', 'paid')->count(),
            'total_revenue' => Order::whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->whereBetween('created_at', [$start, $end])->sum('total') 
                             + \App\Models\StandaloneOrder::whereIn('status', ['processed', 'completed'])->whereBetween('created_at', [$start, $end])->sum('total_price'),
            'stock_sold' => $stockSoldStandard + $stockSoldStandalone,
            'total_buyers' => Order::whereBetween('created_at', [$start, $end])->distinct('customer_phone')->count('customer_phone') 
                            + \App\Models\StandaloneOrder::whereBetween('created_at', [$start, $end])->distinct('wa_number')->count('wa_number'),
            'total_products' => Product::count(), // Global, not filtered
            'active_products' => Product::where('is_active', true)->count(),
            'total_users' => User::whereBetween('created_at', [$start, $end])->count(),
        ];

        $recentOrders = Order::with('items')
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->take(10)
            ->get();

        // --- Chart Data ---
        $revenueDataWeb = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date');

        $revenueDataDirect = \App\Models\StandaloneOrder::selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->whereIn('status', ['processed', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date');

        $chartLabels = [];
        $chartValuesWeb = [];
        $chartValuesDirect = [];
        
        $daysDiff = $start->diffInDays($end);
        for ($i = 0; $i <= $daysDiff; $i++) {
            $currentDateStr = $start->copy()->addDays($i)->format('Y-m-d');
            $chartLabels[] = $start->copy()->addDays($i)->format('d M');
            $chartValuesWeb[] = $revenueDataWeb[$currentDateStr] ?? 0;
            $chartValuesDirect[] = $revenueDataDirect[$currentDateStr] ?? 0;
        }

        // 2. Status Distribution (Web)
        $statusDistributionWeb = [
            $stats['pending_orders'],
            $stats['paid_orders'],
            Order::where('status', 'processing')->whereBetween('created_at', [$start, $end])->count(),
            Order::where('status', 'shipped')->whereBetween('created_at', [$start, $end])->count(),
            Order::where('status', 'completed')->whereBetween('created_at', [$start, $end])->count(),
            Order::where('status', 'cancelled')->whereBetween('created_at', [$start, $end])->count(),
        ];

        // 3. Status Distribution (Direct)
        $statusDistributionDirect = [
            \App\Models\StandaloneOrder::where('status', 'pending')->whereBetween('created_at', [$start, $end])->count(),
            \App\Models\StandaloneOrder::where('status', 'processed')->whereBetween('created_at', [$start, $end])->count(),
            \App\Models\StandaloneOrder::where('status', 'completed')->whereBetween('created_at', [$start, $end])->count(),
            \App\Models\StandaloneOrder::where('status', 'cancelled')->whereBetween('created_at', [$start, $end])->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'chartLabels', 'chartValuesWeb', 'chartValuesDirect', 'statusDistributionWeb', 'statusDistributionDirect', 'startDate', 'endDate'));
    }
}
