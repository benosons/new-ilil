<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 8px; margin-top: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #39d98a; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #071a0d; font-size: 24px; }
        .info { margin-bottom: 20px; color: #555; font-size: 14px; line-height: 1.6; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { text-align: left; padding: 10px; background: #f8f8f8; border-bottom: 1px solid #eee; }
        .table td { padding: 10px; border-bottom: 1px solid #eee; }
        .total { text-align: right; font-size: 18px; font-weight: bold; color: #39d98a; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #888; }
        .btn { display: inline-block; padding: 10px 20px; background: #39d98a; color: #000; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Keripik iLiL</h1>
            <p>Terima kasih sudah memesan!</p>
        </div>

        <div class="info">
            <p>Halo <strong>{{ $order->customer_name }}</strong>,</p>
            <p>Pesanan kamu dengan nomor <strong>#{{ $order->order_number }}</strong> telah kami terima.</p>
            @if($order->status == 'paid')
                <p>Pembayaran berhasil diverifikasi. Pesanan sedang diproses.</p>
            @elseif($order->status == 'pending')
                <p>Silakan selesaikan pembayaran agar pesanan segera diproses.</p>
            @endif
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th style="text-align:right">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="text-align:right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total: {{ $order->formatted_total }}
        </div>

        <div class="footer">
            <p>Jika ada pertanyaan, silakan balas email ini atau hubungi kami via WhatsApp.</p>
            <p>&copy; {{ date('Y') }} Keripik iLiL. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
