<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'paid_orders' => Order::where('status', 'paid')->count(),
            'total_revenue' => Order::whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->sum('total'),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_users' => User::count(),
        ];

        $recentOrders = Order::with('items')
            ->latest()
            ->take(10)
            ->get();

        // --- Chart Data ---
        
        // 1. Revenue (Last 30 Days)
        $revenueData = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->whereNotIn('status', ['cancelled', 'pending'])
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date');

        $chartLabels = [];
        $chartValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            $chartValues[] = $revenueData[$date] ?? 0;
        }

        // 2. Status Distribution
        $statusDistribution = [
            $stats['pending_orders'],
            $stats['paid_orders'],
            Order::where('status', 'processing')->count(),
            Order::where('status', 'shipped')->count(),
            Order::where('status', 'completed')->count(),
            Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'chartLabels', 'chartValues', 'statusDistribution'));
    }
}
