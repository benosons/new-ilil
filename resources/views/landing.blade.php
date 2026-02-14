@extends('layouts.app')

@section('title', 'Keripik iLiL — Berani Coba, Berani Ketagihan')
@section('meta_description', 'Keripik pisang premium dari Bandung. Cinematic landing page glassmorphism + parallax + Three.js.')

@section('content')
    {{-- Cinematic overlays --}}
    <div class="cinema"></div>
    <div class="grain"></div>

    {{-- Fixed parallax background --}}
    <div class="bg-parallax" id="bg">
        <div class="chip" data-depth="0.12"></div>
        <div class="chip" data-depth="0.18"></div>
        <div class="chip" data-depth="0.22"></div>
        <div class="chip" data-depth="0.10"></div>
        <div class="chip" data-depth="0.16"></div>
        <div class="chip" data-depth="0.26"></div>
        <div class="chip" data-depth="0.14"></div>
        <div class="chip" data-depth="0.20"></div>
        <div class="chip" data-depth="0.08"></div>
        <div class="chip" data-depth="0.24"></div>
        <div class="chip" data-depth="0.30"></div>
        <div class="chip" data-depth="0.11"></div>
    </div>

    {{-- Navbar --}}
    <div class="container nav">
        <div class="nav-inner glass reveal">
            <a class="brand" href="#top">
                <img src="{{ asset('assets/brand/logo.png') }}" alt="Keripik iLiL" />
                <div class="flex flex-col leading-tight">
                    <strong>Keripik iLiL</strong>
                    <span class="text-xs" style="color:var(--muted)">Berani Coba, Berani Ketagihan</span>
                </div>
            </a>

            <nav class="nav-links" aria-label="Navigasi">
                <a href="#tentang">Tentang</a>
                <a href="#produk">Produk</a>
                <a href="#galeri">Galeri</a>
                <a href="#tim">Tim</a>
                <a href="#pesan">Pesan</a>
            </nav>

            <div class="nav-actions">
                <div class="cart-pill" id="cartBtn" role="button" aria-label="Buka keranjang">
                    <span class="opacity-90">🛒</span>
                    <span class="font-black">Cart</span>
                    <span class="cart-count" id="cartCount">0</span>
                </div>
                <a class="btn primary" href="#pesan">Order</a>
            </div>
        </div>
    </div>

    {{-- HERO --}}
    <header class="section hero" id="top">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-copy glass reveal" id="heroTilt">
                    <div class="badge"><span class="dot"></span> Bandung • Banana Chips • Tanpa MSG (sesuaikan)</div>

                    <h1>
                        <span style="color:var(--accent)">Berani Coba</span><br/>
                        <span style="color:var(--accent2)">Berani Ketagihan</span>
                    </h1>

                    <p>
                        Keripik pisang tipis dengan rasa yang "nempel" dari gigitan pertama.
                        Desain modern glassmorphism + parallax + animasi Three.js untuk vibe premium.
                    </p>

                    <div class="hero-cta">
                        <a class="btn whatsapp" id="ctaWa" href="#" title="Pesan via WhatsApp">💬 Pesan WhatsApp</a>
                        <a class="btn market" id="ctaMk" href="#" title="Checkout Marketplace">🛍️ Marketplace</a>
                        <button class="btn primary" id="ctaWeb" type="button" title="Checkout via Web">🧾 Checkout Web</button>
                        <a class="btn ghost" href="#produk" title="Lihat varian rasa">🍌 Pilih Rasa</a>
                    </div>

                    <div class="hero-note">
                        <span class="badge">✨ Cinematic: tilt hover + glow + film grain</span>
                        <span class="badge">🎛️ Scroll = parallax + motion kamera</span>
                    </div>
                </div>

                <div class="hero-right glass reveal" id="threeTilt" aria-label="Animasi produk utama (real pack)">
                    <div id="threeWrap">
                        <canvas id="three"></canvas>
                    </div>
                    <div class="three-overlay glass">
                        <small>
                            <b>Produk Utama (Real Pack)</b><br/>
                            Three.js menampilkan foto pack kamu + chip particles.
                        </small>
                        <span class="kbd">SCROLL</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- TENTANG --}}
    <section class="section compact" id="tentang">
        <div class="container">
            <div class="section-title reveal">
                <div>
                    <h2>Tentang Keripik iLiL</h2>
                    <p>Story brand versi modern—tinggal tempel narasi asli kamu.</p>
                </div>
            </div>

            <div class="about-grid">
                <div class="about-copy glass reveal">
                    <p class="m-0 leading-relaxed" style="color:var(--muted)">
                        Keripik iLiL dibuat tipis, renyah, dan cocok jadi teman santai.
                        Fokus kami: rasa konsisten, packaging rapi, dan pengalaman beli yang gampang.
                    </p>
                </div>

                <div class="about-stats">
                    <div class="stat reveal">
                        <strong>6+</strong>
                        <span>Varian rasa</span>
                    </div>
                    <div class="stat reveal">
                        <strong>Bandung</strong>
                        <span>Asal produksi</span>
                    </div>
                    <div class="stat reveal">
                        <strong>Fast Order</strong>
                        <span>WA • Market • Web</span>
                    </div>
                    <div class="stat reveal">
                        <strong>Premium Look</strong>
                        <span>Glass + Cinematic</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PRODUK --}}
    <section class="section" id="produk">
        <div class="container">
            <div class="section-title reveal">
                <div>
                    <h2>Varian Rasa</h2>
                    <p>Semua card pakai foto asli. Hover = cinematic tilt + glow beda per varian.</p>
                </div>
                <span class="badge">💡 Harga bisa kamu sesuaikan</span>
            </div>

            <div class="product-grid" id="productGrid"></div>
        </div>
    </section>

    {{-- GALERI --}}
    <section class="section compact" id="galeri">
        <div class="container">
            <div class="section-title reveal">
                <div>
                    <h2>Galeri</h2>
                    <p>Klik foto untuk cinematic lightbox.</p>
                </div>
            </div>

            <div class="gallery-grid" id="galleryGrid"></div>
        </div>
    </section>

    {{-- TIM --}}
    <section class="section compact" id="tim">
        <div class="container">
            <div class="section-title reveal">
                <div>
                    <h2>Tim Kami</h2>
                    <p>Ganti avatar tim nanti. (Sementara pakai logo/placeholder)</p>
                </div>
            </div>

            <div class="team-grid">
                <div class="person reveal">
                    <div class="avatar"><img src="{{ asset('assets/brand/logo.png') }}" alt="Icha"></div>
                    <div><strong>Icha</strong><span>Founder</span></div>
                </div>
                <div class="person reveal">
                    <div class="avatar"><img src="{{ asset('assets/brand/logo.png') }}" alt="Beno"></div>
                    <div><strong>Beno</strong><span>Tim IT</span></div>
                </div>
                <div class="person reveal">
                    <div class="avatar"><img src="{{ asset('assets/brand/logo.png') }}" alt="Mah Jijil"></div>
                    <div><strong>Mah Jijil</strong><span>Produksi</span></div>
                </div>
                <div class="person reveal">
                    <div class="avatar"><img src="{{ asset('assets/brand/logo.png') }}" alt="Kika"></div>
                    <div><strong>Kika</strong><span>Umum</span></div>
                </div>
            </div>
        </div>
    </section>

    {{-- PESAN --}}
    <section class="section" id="pesan">
        <div class="container">
            <div class="section-title reveal">
                <div>
                    <h2>Pesan Sekarang</h2>
                    <p>WA / Marketplace / Checkout Web (cart).</p>
                </div>
            </div>

            <div class="order-grid">
                <div class="order-box glass reveal">
                    <div class="badge"><span class="dot"></span> Checkout cepat • Link tinggal ganti</div>
                    <h3 class="mt-2.5 mb-0">Pilih cara beli yang paling nyaman.</h3>
                    <p>
                        1) <b>WhatsApp</b> untuk tanya stok/ongkir cepat. <br/>
                        2) <b>Marketplace</b> untuk voucher & promo. <br/>
                        3) <b>Web Checkout</b> untuk experience brand yang rapih (cart).
                    </p>

                    <div class="order-actions">
                        <a class="btn whatsapp" id="orderWa" href="#">💬 Pesan via WhatsApp</a>
                        <a class="btn market" id="orderMk" href="#">🛍️ Checkout Marketplace</a>
                        <button class="btn primary" id="orderWeb" type="button">🧾 Checkout di Web</button>
                    </div>

                    <div class="order-mini">
                        <div class="mini-card">
                            <strong>Jam Operasional</strong>
                            <span>09:00–21:00</span>
                        </div>
                        <div class="mini-card">
                            <strong>Lokasi</strong>
                            <span>Bandung, Indonesia</span>
                        </div>
                    </div>
                </div>

                <div class="order-box glass reveal">
                    <h3 class="mt-0 mb-2.5">Info Kontak</h3>
                    <div class="mini-card mb-3">
                        <strong>WhatsApp</strong>
                        <span id="waText">+62 8xx-xxxx-xxxx</span>
                    </div>
                    <div class="mini-card mb-3">
                        <strong>Marketplace</strong>
                        <span>Shopee / Tokopedia</span>
                    </div>
                    <div class="mini-card">
                        <strong>Email</strong>
                        <span>hello@keripikilil.example</span>
                    </div>

                    <p class="mt-3" style="color:var(--muted)">
                        Tips: ganti link WA pakai format <code>https://wa.me/62xxxx</code> + pesan default.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer>
        <div class="container">
            <div class="footer-inner glass reveal">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('assets/brand/logo.png') }}" alt="Keripik iLiL"
                         class="w-[34px] h-[34px] object-contain rounded-[14px] p-1.5"
                         style="border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06)" />
                    <div>
                        <div class="font-black" style="color: rgba(255,255,255,.86)">Keripik iLiL</div>
                        <div style="color: rgba(255,255,255,.60)">© {{ date('Y') }} — Cinematic Glass Landing Page</div>
                    </div>
                </div>
                <div class="linkrow">
                    <a href="#tentang">Tentang</a>
                    <a href="#produk">Produk</a>
                    <a href="#galeri">Galeri</a>
                    <a href="#tim">Tim</a>
                    <a href="#pesan">Pesan</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Cart Modal --}}
    <div class="modal" id="modal">
        <div class="modal-panel glass">
            <div class="modal-head">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg">🛒</span>
                    <div>
                        <div class="font-black">Keranjang</div>
                        <div class="text-xs" style="color:var(--muted)">Tambah produk dari section "Varian Rasa".</div>
                    </div>
                </div>
                <button class="btn ghost" id="closeModal" type="button">Tutup</button>
            </div>

            <div class="modal-body">
                <div class="list" id="cartList"></div>

                <div class="summary">
                    <div class="summary-row">
                        <span>Total item</span>
                        <b id="sumItems">0</b>
                    </div>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <b id="sumSubtotal">Rp 0</b>
                    </div>
                    <div class="summary-row">
                        <span>Ongkir</span>
                        <b style="color:var(--muted)">Nanti</b>
                    </div>

                    <div class="flex gap-2.5 flex-wrap mt-3">
                        <a class="btn whatsapp" id="checkoutWa" href="#">💬 Checkout WA</a>
                        <a class="btn market" id="checkoutMk" href="#">🛍️ Marketplace</a>
                        <button class="btn primary" id="checkoutWeb" type="button">🧾 Pay</button>
                    </div>

                    <div class="muted">
                        Ini templating. Nanti bisa disambungkan ke backend + payment.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gallery Lightbox --}}
    <div class="lightbox" id="lightbox" aria-hidden="true">
        <div class="lightbox-inner">
            <img id="lightImg" alt="Preview Galeri">
            <div class="lightbox-bar">
                <small id="lightCap">Galeri</small>
                <div class="flex gap-2">
                    <button class="iconbtn" id="prevImg" type="button">‹</button>
                    <button class="iconbtn" id="nextImg" type="button">›</button>
                    <button class="iconbtn" id="closeLight" type="button">✕</button>
                </div>
            </div>
        </div>
    </div>
@endsection
