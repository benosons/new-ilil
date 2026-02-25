@extends('admin.layouts.app')
@section('page_title', 'Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_orders'] }}</div>
            <div class="stat-label">Total Pesanan</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #ffd54a">{{ $stats['pending_orders'] }}</div>
            <div class="stat-label">Menunggu Bayar</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['active_products'] }}</div>
            <div class="stat-label">Produk Aktif</div>
        </div>
    </div>

    <div class="grid-2-1">
        <div class="card">
            <h3 style="margin-bottom:16px; font-size:1rem">Tren Pendapatan (30 Hari)</h3>
            <canvas id="revenueChart" height="120"></canvas>
        </div>
        <div class="card">
            <h3 style="margin-bottom:16px; font-size:1rem">Status Pesanan</h3>
            <div style="position:relative; height:200px">
                <canvas id="statusChart"></canvas>
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
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($chartValues),
                    borderColor: '#39d98a',
                    backgroundColor: 'rgba(57, 217, 138, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        const ctxStat = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStat, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Paid', 'Processing', 'Shipped', 'Completed', 'Cancelled'],
                datasets: [{
                    data: @json($statusDistribution),
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
                    legend: { position: 'right', labels: { color: '#aaa', boxWidth: 10 } }
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
