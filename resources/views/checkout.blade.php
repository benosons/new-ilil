<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout — Keripik iLiL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #060d08; --bg2: #0b1a10; --surface: rgba(10,28,18,.88);
            --stroke: rgba(255,255,255,.10); --accent: #39d98a; --accent2: #ffd54a;
            --danger: #ff3b5c; --text: #f0f0f0; --muted: rgba(255,255,255,.55);
        }
        body {
            min-height: 100vh; background: linear-gradient(160deg, #060d08 0%, #0b1a10 40%, #071a0d 100%);
            font-family: 'Inter', system-ui, sans-serif; color: var(--text);
            display: flex; align-items: flex-start; justify-content: center; padding: 32px 16px;
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        .checkout-wrap {
            width: 100%; max-width: 880px;
        }
        .checkout-header {
            text-align: center; margin-bottom: 28px;
        }
        .checkout-header img { width: 48px; height: 48px; border-radius: 14px; border: 1px solid var(--stroke); padding: 3px; background: rgba(255,255,255,.06); margin-bottom: 10px; }
        .checkout-header h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 4px; }
        .checkout-header p { font-size: .88rem; color: var(--muted); }

        .checkout-grid {
            display: grid; grid-template-columns: 1fr 380px; gap: 20px; align-items: start;
        }
        @media (max-width: 768px) { .checkout-grid { grid-template-columns: 1fr; } }

        .card {
            background: var(--surface); border: 1px solid var(--stroke); border-radius: 18px;
            padding: 24px; backdrop-filter: blur(18px);
        }
        .card h3 { font-size: .95rem; font-weight: 700; margin-bottom: 16px; }

        /* Cart items */
        .cart-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.06); font-size: .88rem;
        }
        .cart-item:last-child { border-bottom: none; }
        .cart-item-left { display: flex; align-items: center; gap: 10px; }
        .cart-item-thumb {
            width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,.06);
            border: 1px solid var(--stroke); display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .cart-item-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-name { font-weight: 600; }
        .cart-item-meta { font-size: .78rem; color: var(--muted); }
        .cart-item-qty {
            display: flex; align-items: center; gap: 6px;
        }
        .qty-btn {
            width: 28px; height: 28px; border-radius: 8px; border: 1px solid var(--stroke);
            background: rgba(255,255,255,.06); color: var(--text); font-size: .9rem;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all .15s;
        }
        .qty-btn:hover { background: rgba(255,255,255,.12); }
        .qty-num { font-weight: 700; min-width: 20px; text-align: center; }
        .qty-rm { color: var(--danger); border-color: rgba(255,59,92,.2); }
        .qty-rm:hover { background: rgba(255,59,92,.15); }

        .cart-empty { text-align: center; padding: 28px; color: var(--muted); font-size: .88rem; }

        /* Summary */
        .summary-row {
            display: flex; justify-content: space-between; padding: 6px 0; font-size: .88rem;
        }
        .summary-total {
            display: flex; justify-content: space-between; padding: 12px 0; font-size: 1.05rem;
            font-weight: 700; border-top: 1px solid var(--stroke); margin-top: 8px;
        }
        .summary-total .total-val { color: var(--accent); }

        /* Form */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: .82rem; font-weight: 600; color: rgba(255,255,255,.75); margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 11px 16px; font-size: .92rem; color: var(--text);
            background: rgba(255,255,255,.06); border: 1px solid var(--stroke); border-radius: 10px;
            outline: none; transition: border-color .2s, box-shadow .2s; font-family: inherit;
        }
        .form-control:focus { border-color: rgba(57,217,138,.5); box-shadow: 0 0 0 3px rgba(57,217,138,.1); }
        .form-control::placeholder { color: rgba(255,255,255,.3); }
        textarea.form-control { min-height: 70px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 520px) { .form-row { grid-template-columns: 1fr; } }

        /* Buttons */
        .btn-pay {
            display: block; width: 100%; padding: 14px; font-size: 1rem; font-weight: 700;
            color: #071a0d; background: linear-gradient(135deg, #39d98a, #2bc47a);
            border: none; border-radius: 12px; cursor: pointer; transition: all .15s;
            font-family: inherit;
        }
        .btn-pay:hover { transform: translateY(-1px); box-shadow: 0 4px 20px rgba(57,217,138,.35); }
        .btn-pay:active { transform: scale(.98); }
        .btn-pay:disabled { opacity: .6; cursor: not-allowed; transform: none; box-shadow: none; }

        .error-msg {
            background: rgba(255,59,92,.12); border: 1px solid rgba(255,59,92,.25); color: #ff6b82;
            font-size: .82rem; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px;
            display: none;
        }

        .back-link { display: block; text-align: center; margin-top: 20px; font-size: .82rem; color: var(--muted); text-decoration: none; }
        .back-link:hover { color: var(--accent); }

        /* add item btn from checkout */
        .add-more-link { display: inline-block; margin-top: 12px; font-size: .82rem; }
    </style>
</head>
<body>
    <div class="checkout-wrap">
        <div class="checkout-header">
            <img src="{{ asset('assets/brand/logo.png') }}" alt="Keripik iLiL">
            <h1>🧾 Checkout</h1>
            <p>Lengkapi data kamu untuk melanjutkan pembayaran.</p>
        </div>

        <div class="checkout-grid">
            {{-- Left: Customer Form --}}
            <div class="card">
                <h3>📋 Data Pengiriman</h3>
                <form id="checkoutForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer_name">Nama Lengkap *</label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control"
                                   placeholder="Nama lengkap kamu" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_phone">No. Telepon *</label>
                            <input type="tel" id="customer_phone" name="customer_phone" class="form-control"
                                   placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="customer_email">Email (opsional)</label>
                        <input type="email" id="customer_email" name="customer_email" class="form-control"
                               placeholder="email@example.com">
                    </div>
                    <div class="form-group">
                        <label for="customer_address">Alamat Pengiriman</label>
                        <textarea id="customer_address" name="customer_address" class="form-control"
                                  placeholder="Alamat lengkap untuk pengiriman..."></textarea>
                    </div>

                    <div class="error-msg" id="checkoutError"></div>

                    <button type="submit" id="payBtn" class="btn-pay">
                        💳 Bayar Sekarang
                    </button>
                </form>

                <a href="{{ url('/') }}" class="back-link">← Kembali ke Landing Page</a>
            </div>

            {{-- Right: Cart Summary --}}
            <div>
                <div class="card" style="margin-bottom: 16px">
                    <h3>🛒 Keranjang (<span id="itemCount">0</span> item)</h3>
                    <div id="cartItems"></div>
                    <a href="{{ url('/') }}#produk" class="add-more-link">+ Tambah produk lain</a>
                </div>

                <div class="card">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong id="sumSubtotal">Rp 0</strong>
                    </div>
                    <div class="summary-row">
                        <span style="color: var(--muted)">Ongkir</span>
                        <span style="color: var(--muted)">Ditentukan penjual</span>
                    </div>
                    
                    {{-- Voucher Row --}}
                    <div class="summary-row" id="discountRow" style="display:none; color:var(--accent)">
                        <span>Diskon <small id="voucherCodeDisplay" style="font-family:monospace; background:rgba(57,217,138,.1); padding:2px 4px; border-radius:4px"></small></span>
                        <strong id="sumDiscount">-Rp 0</strong>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span class="total-val" id="sumTotal">Rp 0</span>
                    </div>

                    {{-- Voucher Input --}}
                    <div style="margin-top:16px; padding-top:16px; border-top:1px solid var(--stroke)">
                        <div class="form-group mb-0">
                            <label for="voucherCode" style="font-size:.78rem">Punya Kode Promo?</label>
                            <div style="display:flex; gap:8px">
                                <input type="text" id="voucherCode" class="form-control" placeholder="Masukan kode..." style="text-transform:uppercase">
                                <button type="button" id="btnCheckVoucher" class="btn-pay" style="width:auto; padding:0 16px; font-size:.85rem; background:var(--surface); border:1px solid var(--stroke); color:var(--text)">
                                    Pakai
                                </button>
                            </div>
                            <small id="voucherMsg" style="display:block; margin-top:6px; font-size:.75rem"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap.js --}}
    <script src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
    <script>
        const rupiah = (n) => 'Rp ' + (n || 0).toLocaleString('id-ID');

        // --- Cart Management ---
        let cartData = JSON.parse(localStorage.getItem('ilil_cart') || '[]');
        let activeVoucher = null; // { code, type, value }

        const cartItemsEl = document.getElementById('cartItems');
        const itemCountEl = document.getElementById('itemCount');
        const sumSubtotalEl = document.getElementById('sumSubtotal');
        const sumTotalEl = document.getElementById('sumTotal');
        const discountRow = document.getElementById('discountRow');
        const sumDiscountEl = document.getElementById('sumDiscount');
        const voucherCodeDisplay = document.getElementById('voucherCodeDisplay');
        const voucherMsg = document.getElementById('voucherMsg');

        function calculateTotals() {
            let subtotal = 0;
            let totalItems = 0;

            cartData.forEach(item => {
                subtotal += item.price * item.qty;
                totalItems += item.qty;
            });

            // Calculate Discount
            let discount = 0;
            if (activeVoucher) {
                if (activeVoucher.type === 'fixed') {
                    discount = parseFloat(activeVoucher.value);
                } else {
                    discount = subtotal * (parseFloat(activeVoucher.value) / 100);
                }
                if (discount > subtotal) discount = subtotal;
            }

            const total = subtotal - discount;

            // Update UI
            itemCountEl.textContent = String(totalItems);
            sumSubtotalEl.textContent = rupiah(subtotal);
            
            if (activeVoucher) {
                discountRow.style.display = 'flex';
                sumDiscountEl.textContent = '-' + rupiah(discount);
                voucherCodeDisplay.textContent = activeVoucher.code;
            } else {
                discountRow.style.display = 'none';
            }

            sumTotalEl.textContent = rupiah(total);
        }

        function renderCheckoutCart() {
            cartItemsEl.innerHTML = '';
            
            if (cartData.length === 0) {
                cartItemsEl.innerHTML = '<div class="cart-empty">Keranjang kosong. <a href="/">Kembali belanja →</a></div>';
                calculateTotals();
                return;
            }

            cartData.forEach((item, index) => {
                const sub = item.price * item.qty;
                
                const row = document.createElement('div');
                row.className = 'cart-item';
                row.innerHTML = `
                    <div class="cart-item-left">
                        <div class="cart-item-thumb">
                            ${item.img ? `<img src="${item.img}" alt="${item.name}">` : '🍌'}
                        </div>
                        <div>
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-meta">${rupiah(item.price)} × ${item.qty} = <strong>${rupiah(sub)}</strong></div>
                        </div>
                    </div>
                    <div class="cart-item-qty">
                        <button type="button" class="qty-btn" data-action="dec" data-index="${index}">−</button>
                        <span class="qty-num">${item.qty}</span>
                        <button type="button" class="qty-btn" data-action="inc" data-index="${index}">+</button>
                        <button type="button" class="qty-btn qty-rm" data-action="rm" data-index="${index}">✕</button>
                    </div>
                `;
                cartItemsEl.appendChild(row);
            });

            // Bind qty buttons
            cartItemsEl.querySelectorAll('.qty-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const i = parseInt(btn.dataset.index);
                    const act = btn.dataset.action;
                    if (act === 'dec') {
                        cartData[i].qty = Math.max(1, cartData[i].qty - 1);
                    } else if (act === 'inc') {
                        cartData[i].qty += 1;
                    } else if (act === 'rm') {
                        cartData.splice(i, 1);
                    }
                    localStorage.setItem('ilil_cart', JSON.stringify(cartData));
                    renderCheckoutCart(); // re-render
                });
            });

            calculateTotals();
        }
        renderCheckoutCart();

        // --- Voucher Check ---
        document.getElementById('btnCheckVoucher').addEventListener('click', async function() {
            const codeInput = document.getElementById('voucherCode');
            const code = codeInput.value.trim();
            const btn = this;

            if (!code) return;

            btn.disabled = true;
            btn.textContent = '...';
            voucherMsg.textContent = '';
            voucherMsg.style.color = 'var(--muted)';

            try {
                const res = await fetch('{{ route("checkout.check-voucher") }}', { // defined later in routes
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code,
                        items: cartData.map(i => ({ product_id: i.product_id, quantity: i.qty }))
                    })
                });
                
                const data = await res.json();

                if (data.valid) {
                    activeVoucher = {
                        code: data.code,
                        type: data.type,
                        value: data.value
                    };
                    voucherMsg.textContent = `Promo "${data.code}" berhasil dipakai! Hemat ${data.discount_formatted}`;
                    voucherMsg.style.color = 'var(--accent)';
                    calculateTotals();
                } else {
                    activeVoucher = null;
                    voucherMsg.textContent = data.message;
                    voucherMsg.style.color = 'var(--danger)';
                    calculateTotals();
                }

            } catch (err) {
                console.error(err);
                voucherMsg.textContent = 'Gagal mengecek voucher.';
                voucherMsg.style.color = 'var(--danger)';
            } finally {
                btn.disabled = false;
                btn.textContent = 'Pakai';
            }
        });

        // --- Checkout Form Submit ---
        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const errorEl = document.getElementById('checkoutError');
            errorEl.style.display = 'none';

            if (cartData.length === 0) {
                errorEl.textContent = 'Keranjang kosong! Tambahkan produk terlebih dahulu.';
                errorEl.style.display = 'block';
                return;
            }

            const form = new FormData(this);
            const payBtn = document.getElementById('payBtn');
            payBtn.disabled = true;
            payBtn.textContent = '⏳ Memproses pesanan...';

            try {
                const payload = {
                    customer_name: form.get('customer_name'),
                    customer_phone: form.get('customer_phone'),
                    customer_email: form.get('customer_email'),
                    customer_address: form.get('customer_address'),
                    items: cartData.map(i => ({
                        product_id: i.product_id,
                        quantity: i.qty,
                    })),
                    voucher_code: activeVoucher ? activeVoucher.code : null // Send voucher code
                };

                const res = await fetch('{{ route("checkout.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!data.success) {
                    throw new Error(data.message || 'Terjadi kesalahan saat memproses pesanan');
                }

                // Open Midtrans Snap popup
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        localStorage.removeItem('ilil_cart');
                        window.location.href = '{{ route("checkout.success") }}';
                    },
                    onPending: function(result) {
                        localStorage.removeItem('ilil_cart');
                        window.location.href = '{{ route("checkout.success") }}' + '?pending=1';
                    },
                    onError: function(result) {
                        window.location.href = '{{ route("checkout.failed") }}';
                    },
                    onClose: function() {
                        payBtn.disabled = false;
                        payBtn.textContent = '💳 Bayar Sekarang';
                    }
                });

            } catch (err) {
                errorEl.textContent = err.message;
                errorEl.style.display = 'block';
                payBtn.disabled = false;
                payBtn.textContent = '💳 Bayar Sekarang';
            }
        });
    </script>
</body>
</html>
