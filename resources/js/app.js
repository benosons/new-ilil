/**
 * Keripik iLiL — Landing Page JavaScript
 * Three.js cinematic hero + parallax + cart + lightbox + tilt + scroll-spy
 */

const $ = (q, el = document) => el.querySelector(q);
const $$ = (q, el = document) => Array.from(el.querySelectorAll(q));
const clamp = (v, a, b) => Math.max(a, Math.min(b, v));

/* ==========================
   CONFIG
========================== */
const ASSETS = {
    logo: "/assets/brand/logo.png",
    heroPack: "/assets/products/pack-hero.png",
    variants: {
        original: "/assets/variants/original.jpg",
        coklat:   "/assets/variants/coklat.jpg",
        keju:     "/assets/variants/keju.jpg",
        balado:   "/assets/variants/balado.jpg",
        pedas:    "/assets/variants/pedas.jpg"
    },
    gallery: [
        { src: "/assets/gallery/g1.jpg", cap: "Galeri 01" },
        { src: "/assets/gallery/g2.jpg", cap: "Galeri 02" },
        { src: "/assets/gallery/g3.jpg", cap: "Galeri 03" },
        { src: "/assets/gallery/g4.jpg", cap: "Galeri 04" }
    ]
};

// WA / Marketplace / Web — sesuaikan
const WA_NUMBER = "628xxxxxxxxxx";
const MARKET_URL = "#";
const WEB_CHECKOUT_URL = "#";

/* ==========================
   Reveal cinematic
========================== */
const io = new IntersectionObserver((entries) => {
    for (const e of entries) {
        if (e.isIntersecting) e.target.classList.add("in");
    }
}, { threshold: 0.12 });
$$(".reveal").forEach(el => io.observe(el));

/* ==========================
   Parallax background chips
========================== */
const bg = $("#bg");
const chips = $$(".chip", bg);

const seed = 1337;
function rand(i) {
    let x = (seed + i * 99991) >>> 0;
    x = (x * 1664525 + 1013904223) >>> 0;
    return x / 2 ** 32;
}

chips.forEach((c, i) => {
    const r1 = rand(i * 3 + 1), r2 = rand(i * 3 + 2), r3 = rand(i * 3 + 3);
    const left = Math.round(r1 * 100);
    const top = Math.round(r2 * 120) - 10;
    const s = 0.55 + r3 * 0.75;
    const rot = (r2 * 70 - 35);
    c.style.left = left + "vw";
    c.style.top = top + "vh";
    c.style.transform = `translate3d(0,0,0) rotate(${rot}deg) scale(${s})`;
    c.dataset.baseRot = String(rot);
    c.dataset.baseScale = String(s);
});

let scrollY = 0;
let targetScroll = 0;

function updateParallax() {
    scrollY += (targetScroll - scrollY) * 0.08;
    const t = scrollY;

    chips.forEach((c, i) => {
        const depth = parseFloat(c.dataset.depth || "0.15");
        const baseRot = parseFloat(c.dataset.baseRot || "0");
        const baseScale = parseFloat(c.dataset.baseScale || "1");
        const x = Math.sin((t * 0.0012) + i) * (18 * depth);
        const y = (t * depth * 0.22);
        const rot = baseRot + (t * 0.01 * depth);
        c.style.transform = `translate3d(${x}px, ${y}px, 0) rotate(${rot}deg) scale(${baseScale})`;
    });

    bg.style.transform = `translate3d(0, ${-(t * 0.04)}px, 0)`;
    requestAnimationFrame(updateParallax);
}

window.addEventListener("scroll", () => { targetScroll = window.scrollY || 0; }, { passive: true });
updateParallax();

/* ==========================
   Product data-driven render
========================== */
const products = [
    { key: "original", name: "Original",       desc: "Rasa natural & gurih. Tipis, renyah.",       price: 15000, img: ASSETS.variants.original, glow: "rgba(255,213,74,.22)" },
    { key: "keju",     name: "Keju",            desc: "Aroma keju creamy, balance manis.",          price: 15000, img: ASSETS.variants.keju,     glow: "rgba(255,255,255,.18)" },
    { key: "balado",   name: "Balado",          desc: "Pedas-manis gurih, bikin nagih.",            price: 15000, img: ASSETS.variants.balado,   glow: "rgba(255,120,60,.20)" },
    { key: "coklat",   name: "Coklat",          desc: "Manis coklat tebal, cocok dessert.",         price: 17000, img: ASSETS.variants.coklat,   glow: "rgba(120,75,45,.22)" },
    { key: "pedas",    name: "Pedas",           desc: "Lebih nendang untuk pecinta pedas.",         price: 15000, img: ASSETS.variants.pedas,    glow: "rgba(255,59,92,.18)" },
    { key: "pedas2",   name: "Pedas Level 2",   desc: "Kalau mau extra nendang (dummy).",           price: 15000, img: ASSETS.variants.pedas,    glow: "rgba(255,59,92,.22)" }
];

const productGrid = $("#productGrid");
const rupiah = (n) => "Rp " + (n || 0).toLocaleString("id-ID");

function renderProducts() {
    productGrid.innerHTML = products.map(p => `
        <div class="product reveal tilt" style="--glow:${p.glow}" data-name="${p.name}" data-price="${p.price}">
            <div class="thumb"><img src="${p.img}" alt="${p.name}"></div>
            <h3>${p.name}</h3>
            <p>${p.desc}</p>
            <div class="price-row">
                <span class="price">${rupiah(p.price)}</span>
                <button class="btn mini primary add" type="button" data-name="${p.name}" data-price="${p.price}">Tambah</button>
            </div>
        </div>
    `).join("");
    $$(".reveal", productGrid).forEach(el => io.observe(el));
}
renderProducts();

/* ==========================
   Cinematic tilt
========================== */
function addTilt(el, strength = 10) {
    let rAF = null;
    const onMove = (e) => {
        const rect = el.getBoundingClientRect();
        const px = (e.clientX - rect.left) / rect.width;
        const py = (e.clientY - rect.top) / rect.height;
        const rx = (0.5 - py) * strength;
        const ry = (px - 0.5) * strength;
        if (rAF) cancelAnimationFrame(rAF);
        rAF = requestAnimationFrame(() => {
            el.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-1px)`;
        });
    };
    const onLeave = () => {
        if (rAF) cancelAnimationFrame(rAF);
        el.style.transform = `perspective(900px) rotateX(0deg) rotateY(0deg) translateY(0px)`;
    };
    el.addEventListener("mousemove", onMove);
    el.addEventListener("mouseleave", onLeave);
}

addTilt($("#heroTilt"), 6);
addTilt($("#threeTilt"), 5);

function bindCardTilts() {
    $$(".product.tilt").forEach(el => addTilt(el, 10));
}
bindCardTilts();

/* ==========================
   Cart
========================== */
const cart = new Map();
const cartCount = $("#cartCount");
const modal = $("#modal");
const cartList = $("#cartList");
const sumItems = $("#sumItems");
const sumSubtotal = $("#sumSubtotal");

function renderCart() {
    cartList.innerHTML = "";
    let items = 0;
    let subtotal = 0;

    for (const [name, it] of cart.entries()) {
        items += it.qty;
        subtotal += it.qty * it.price;

        const row = document.createElement("div");
        row.className = "item";
        row.innerHTML = `
            <div class="item-left">
                <div class="item-thumb" aria-hidden="true"></div>
                <div>
                    <strong>${name}</strong>
                    <span>${rupiah(it.price)} • qty ${it.qty}</span>
                </div>
            </div>
            <div class="qty">
                <button type="button" data-act="dec" aria-label="Kurangi">−</button>
                <b style="min-width:18px;text-align:center">${it.qty}</b>
                <button type="button" data-act="inc" aria-label="Tambah">+</button>
                <button type="button" data-act="rm" aria-label="Hapus" style="margin-left:6px">✕</button>
            </div>
        `;
        row.querySelector('[data-act="dec"]').onclick = () => updateQty(name, -1);
        row.querySelector('[data-act="inc"]').onclick = () => updateQty(name, +1);
        row.querySelector('[data-act="rm"]').onclick = () => removeItem(name);
        cartList.appendChild(row);
    }

    cartCount.textContent = String(items);
    sumItems.textContent = String(items);
    sumSubtotal.textContent = rupiah(subtotal);

    if (cart.size === 0) {
        const empty = document.createElement("div");
        empty.className = "item";
        empty.style.justifyContent = "center";
        empty.style.color = "rgba(255,255,255,.7)";
        empty.innerHTML = "Keranjang masih kosong. Tambah produk dulu ya 🙂";
        cartList.appendChild(empty);
    }
    bindLinks();
}

function addItem(name, price) {
    const cur = cart.get(name) || { price, qty: 0 };
    cur.price = price;
    cur.qty += 1;
    cart.set(name, cur);
    renderCart();
}

function updateQty(name, delta) {
    const it = cart.get(name);
    if (!it) return;
    it.qty = Math.max(1, it.qty + delta);
    cart.set(name, it);
    renderCart();
}

function removeItem(name) {
    cart.delete(name);
    renderCart();
}

function openCart() { modal.classList.add("open"); renderCart(); }
function closeCart() { modal.classList.remove("open"); }

$("#cartBtn").onclick = openCart;
$("#closeModal").onclick = closeCart;
modal.addEventListener("click", (e) => { if (e.target === modal) closeCart(); });

document.addEventListener("click", (e) => {
    const btn = e.target.closest(".add");
    if (!btn) return;
    addItem(btn.dataset.name, parseInt(btn.dataset.price, 10));
});

/* ==========================
   Checkout links
========================== */
function buildWAMessage() {
    let msg = "Halo Keripik iLiL, saya mau pesan:%0A";
    let i = 1;
    for (const [name, it] of cart.entries()) {
        msg += `${i}. ${name} x${it.qty}%0A`;
        i++;
    }
    msg += "%0ATerima kasih!";
    return msg;
}

function bindLinks() {
    const waLink = `https://wa.me/${WA_NUMBER}?text=${buildWAMessage()}`;
    $("#ctaWa").href = waLink;
    $("#orderWa").href = waLink;
    $("#checkoutWa").href = waLink;

    $("#ctaMk").href = MARKET_URL;
    $("#orderMk").href = MARKET_URL;
    $("#checkoutMk").href = MARKET_URL;

    const goWeb = () => {
        if (cart.size === 0) openCart();
        else {
            alert("Checkout web sedang dalam pengembangan. Nanti arahkan ke route /checkout.");
        }
    };
    $("#ctaWeb").onclick = goWeb;
    $("#orderWeb").onclick = goWeb;
    $("#checkoutWeb").onclick = goWeb;

    $("#waText").textContent = "+" + WA_NUMBER;
}
bindLinks();

/* ==========================
   Gallery + Lightbox
========================== */
const galleryGrid = $("#galleryGrid");

function renderGallery() {
    const layout = [
        { span: 4, i: 0 }, { span: 4, i: 1 }, { span: 4, i: 2 },
        { span: 12, i: 3 }
    ];
    galleryGrid.innerHTML = layout.map((x) => {
        const g = ASSETS.gallery[x.i];
        return `
            <div class="shot reveal" style="grid-column: span ${x.span}" data-i="${x.i}">
                <img src="${g.src}" alt="${g.cap}">
                <div class="cap">📸 ${g.cap}</div>
            </div>
        `;
    }).join("");
    $$(".reveal", galleryGrid).forEach(el => io.observe(el));
}
renderGallery();

const lightbox = $("#lightbox");
const lightImg = $("#lightImg");
const lightCap = $("#lightCap");
let lightIndex = 0;

function openLight(i) {
    lightIndex = i;
    const g = ASSETS.gallery[lightIndex];
    lightImg.src = g.src;
    lightCap.textContent = g.cap;
    lightbox.classList.add("open");
    lightbox.setAttribute("aria-hidden", "false");
}

function closeLight() {
    lightbox.classList.remove("open");
    lightbox.setAttribute("aria-hidden", "true");
}

function nextLight(dir) {
    const n = ASSETS.gallery.length;
    lightIndex = (lightIndex + dir + n) % n;
    const g = ASSETS.gallery[lightIndex];
    lightImg.src = g.src;
    lightCap.textContent = g.cap;
}

galleryGrid.addEventListener("click", (e) => {
    const shot = e.target.closest(".shot");
    if (!shot) return;
    openLight(parseInt(shot.dataset.i, 10));
});
$("#closeLight").onclick = closeLight;
$("#prevImg").onclick = () => nextLight(-1);
$("#nextImg").onclick = () => nextLight(+1);
lightbox.addEventListener("click", (e) => { if (e.target === lightbox) closeLight(); });

/* ==========================
   Scroll-spy active nav
========================== */
const navLinks = $$(".nav-links a");
const sections = navLinks.map(a => $(a.getAttribute("href"))).filter(Boolean);
const spy = new IntersectionObserver((entries) => {
    entries.forEach(en => {
        if (!en.isIntersecting) return;
        const id = "#" + en.target.id;
        navLinks.forEach(a => a.classList.toggle("active", a.getAttribute("href") === id));
    });
}, { threshold: 0.45 });
sections.forEach(s => spy.observe(s));

/* ==========================
   Three.js cinematic hero
========================== */
async function initThreeScene() {
    const THREE = await import("https://unpkg.com/three@0.160.0/build/three.module.js");

    const canvas = $("#three");
    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
    renderer.setPixelRatio(Math.min(devicePixelRatio, 2));

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(40, 1, 0.1, 100);
    camera.position.set(0, 0.65, 4.4);

    // Cinematic lights
    const key = new THREE.DirectionalLight(0xffffff, 1.1);
    key.position.set(2.5, 3.2, 2.2);
    scene.add(key);

    const fill = new THREE.DirectionalLight(0xffffff, 0.55);
    fill.position.set(-3.2, 1.4, 2.0);
    scene.add(fill);

    const rim = new THREE.PointLight(0xffd54a, 1.1, 14);
    rim.position.set(0.8, 1.0, -2.6);
    scene.add(rim);

    const greenRim = new THREE.PointLight(0x39d98a, 0.9, 14);
    greenRim.position.set(-1.0, 0.8, -2.2);
    scene.add(greenRim);

    scene.fog = new THREE.FogExp2(0x07130c, 0.05);

    const group = new THREE.Group();
    scene.add(group);

    // Stage ring
    const plate = new THREE.Mesh(
        new THREE.CircleGeometry(1.65, 80),
        new THREE.MeshStandardMaterial({ color: 0x06130c, roughness: 0.9, metalness: 0.02, transparent: true, opacity: 0.52 })
    );
    plate.rotation.x = -Math.PI / 2;
    plate.position.y = -1.12;
    group.add(plate);

    // Pack texture
    const loader = new THREE.TextureLoader();
    const packTex = loader.load(ASSETS.heroPack);
    packTex.colorSpace = THREE.SRGBColorSpace;
    packTex.anisotropy = renderer.capabilities.getMaxAnisotropy();

    const pack = new THREE.Mesh(
        new THREE.PlaneGeometry(1.95, 2.75, 1, 1),
        new THREE.MeshStandardMaterial({
            map: packTex,
            transparent: true,
            roughness: 0.65,
            metalness: 0.02
        })
    );
    pack.position.set(0, 0.05, 0.6);
    group.add(pack);

    // Shadow plane
    const shadow = new THREE.Mesh(
        new THREE.PlaneGeometry(2.2, 3.1),
        new THREE.MeshBasicMaterial({ color: 0x000000, transparent: true, opacity: 0.18 })
    );
    shadow.position.set(0.05, 0.02, 0.45);
    group.add(shadow);

    // Chip particles
    const chipCount = 42;
    const chipGeo = new THREE.PlaneGeometry(0.32, 0.32);
    const chips3D = [];

    for (let i = 0; i < chipCount; i++) {
        const mat = new THREE.MeshStandardMaterial({
            color: 0xffd54a,
            roughness: 0.75,
            metalness: 0.04,
            transparent: true,
            opacity: 0.16 + Math.random() * 0.26,
            side: THREE.DoubleSide
        });
        const m = new THREE.Mesh(chipGeo, mat);
        const a = i / chipCount * Math.PI * 2;
        const r = 1.9 + (i % 7) * 0.08;
        m.position.set(Math.cos(a) * r, (i % 9) * 0.14 - 0.6, Math.sin(a) * r);
        m.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI);
        group.add(m);
        chips3D.push(m);
    }

    // Resize handler
    function resize() {
        const wrap = $("#threeWrap");
        const w = wrap.clientWidth;
        const h = wrap.clientHeight;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
    }
    window.addEventListener("resize", resize);
    resize();

    // Animate loop
    let t0 = performance.now();
    function animate(now) {
        const dt = (now - t0) / 1000;
        t0 = now;

        const s = clamp((window.scrollY || 0) / (document.body.scrollHeight - innerHeight + 1), 0, 1);

        pack.rotation.y = Math.sin(now * 0.00055) * 0.18 + s * 0.10;
        pack.rotation.x = Math.sin(now * 0.00045) * 0.06 - s * 0.06;
        pack.position.y = 0.05 + Math.sin(now * 0.0010) * 0.03;

        group.rotation.y += dt * (0.25 + s * 0.55);
        group.rotation.x = Math.sin(now * 0.00055) * 0.06 + s * 0.05;

        for (let i = 0; i < chips3D.length; i++) {
            const m = chips3D[i];
            m.rotation.x += dt * (0.35 + i * 0.006);
            m.rotation.y += dt * (0.28 + i * 0.007);
            m.position.y += Math.sin(now * 0.001 + i) * 0.0009;
        }

        camera.position.x = Math.sin(now * 0.00035) * 0.14;
        camera.position.y = 0.70 + Math.cos(now * 0.0005) * 0.06 - s * 0.18;
        camera.lookAt(0, 0.05, 0);

        renderer.render(scene, camera);
        requestAnimationFrame(animate);
    }
    requestAnimationFrame(animate);
}

initThreeScene();

/* ==========================
   ESC handler
========================== */
window.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        closeCart();
        closeLight();
    }
});
