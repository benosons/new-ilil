@extends('admin.layouts.app')
@section('page_title', 'Dashboard')

@section('content')
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px;">
        <h2 style="margin: 0; font-size: 1.2rem;">Performa Penjualan</h2>
        <form action="{{ route('admin.dashboard') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; background: rgba(0,0,0,0.2); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--stroke);">
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="start_date" style="font-size: 0.85rem; color: var(--muted);">Mulai:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--stroke); background: rgba(255,255,255,0.05); color: #fff; outline: none; font-size: 0.9rem;" required>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="end_date" style="font-size: 0.85rem; color: var(--muted);">Sampai:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--stroke); background: rgba(255,255,255,0.05); color: #fff; outline: none; font-size: 0.9rem;" required>
            </div>
            <button type="submit" class="btn primary" style="padding: 8px 16px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-funnel"></i> Terapkan
            </button>
        </form>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_orders'] }}</div>
            <div class="stat-label">Total Pesanan Web</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #5b8def">{{ $stats['total_standalone_orders'] }}</div>
            <div class="stat-label">Pesanan Langsung</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #ffd54a">{{ $stats['pending_orders'] }}</div>
            <div class="stat-label">Menunggu Bayar (Web)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan (Gabungan)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['active_products'] }}</div>
            <div class="stat-label">Produk Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #39d98a">{{ number_format((int)$stats['stock_sold'], 0, ',', '.') }}</div>
            <div class="stat-label">Stok Terjual</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #a855f7">{{ number_format((int)$stats['total_buyers'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Pembeli Unik</div>
        </div>
    </div>

    <style>
        .custom-charts-grid {
            display: grid;
            gap: 24px;
            margin-bottom: 24px;
            grid-template-columns: 1fr;
        }
        @media (min-width: 1024px) {
            .custom-charts-grid {
                grid-template-columns: 65% 1fr;
            }
            .custom-charts-grid .trend-card {
                grid-row: 1 / span 2;
                display: flex;
                flex-direction: column;
            }
            .custom-charts-grid .trend-card .chart-wrapper {
                flex: 1;
                position: relative;
                min-height: 400px;
            }
            .custom-charts-grid .web-card,
            .custom-charts-grid .direct-card {
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            .custom-charts-grid .web-card .chart-wrapper,
            .custom-charts-grid .direct-card .chart-wrapper {
                flex: 1;
                position: relative;
                min-height: 150px;
            }
        }
    </style>
    <div class="custom-charts-grid">
        <div class="card trend-card">
            <h3 style="margin-bottom:16px; font-size:1rem">Tren Pendapatan</h3>
            <div class="chart-wrapper">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="card web-card">
            <h3 style="margin-bottom:16px; font-size:1rem; text-align: center;">Status Web (Midtrans)</h3>
            <div class="chart-wrapper">
                <canvas id="statusChartWeb"></canvas>
            </div>
        </div>
        <div class="card direct-card">
            <h3 style="margin-bottom:16px; font-size:1rem; text-align: center;">Status Pesanan Langsung</h3>
            <div class="chart-wrapper">
                <canvas id="statusChartDirect"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Web (Midtrans)',
                        data: @json($chartValuesWeb),
                        borderColor: '#39d98a',
                        backgroundColor: 'rgba(57, 217, 138, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Pesanan Langsung',
                        data: @json($chartValuesDirect),
                        borderColor: '#5b8def',
                        backgroundColor: 'rgba(91, 141, 239, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: true, labels: { color: '#ccc' } } 
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        const ctxStatWeb = document.getElementById('statusChartWeb').getContext('2d');
        new Chart(ctxStatWeb, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Paid', 'Processing', 'Shipped', 'Completed', 'Cancelled'],
                datasets: [{
                    data: @json($statusDistributionWeb),
                    backgroundColor: [
                        '#ffd54a', '#39d98a', '#5b8def', '#a855f7', '#22c55e', '#ff3b5c'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { color: '#aaa', boxWidth: 10, font: { size: 10 } } }
                }
            }
        });

        const ctxStatDirect = document.getElementById('statusChartDirect').getContext('2d');
        new Chart(ctxStatDirect, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processed', 'Completed', 'Cancelled'],
                datasets: [{
                    data: @json($statusDistributionDirect),
                    backgroundColor: [
                        '#ffd54a', '#5b8def', '#22c55e', '#ff3b5c'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { color: '#aaa', boxWidth: 10, font: { size: 10 } } }
                }
            }
        });
    </script>
    @endpush

    <div class="card">
        <div class="card-header">
            <h3>Pesanan Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">Lihat Semua →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->formatted_total }}</td>
                            <td>{!! $order->status_badge !!}</td>
                            <td class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted" style="text-align:center; padding:24px">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
