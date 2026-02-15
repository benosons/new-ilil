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
    entries.forEach((e, i) => {
        if (e.isIntersecting) {
            // Stagger effect for items appearing together
            setTimeout(() => {
                e.target.classList.add("in");
            }, i * 80); 
        }
    });
}, { threshold: 0.12 });
$$(".reveal").forEach(el => io.observe(el));

/* ==========================
   Sticky Nav
========================== */
const nav = document.querySelector(".nav");
if (nav) {
    console.log("Nav found, adding sticky listener");
    window.addEventListener("scroll", () => {
        if (window.scrollY > 20) {
            nav.classList.add("scrolled");
        } else {
            nav.classList.remove("scrolled");
        }
    }, { passive: true });
} else {
    console.error("Nav element not found!");
}

/* ==========================
   Parallax background chips — clusters + mouse parallax
========================== */
const bg = $("#bg");

const seed = 1337;
function rand(i) {
    let x = (seed + i * 99991) >>> 0;
    x = (x * 1664525 + 1013904223) >>> 0;
    return x / 2 ** 32;
}

// Generate clusters of 3-4 chips
const CLUSTER_COUNT = 14;
const chipVariants = ["chip-1", "chip-2", "chip-3"];
const allChips = [];

for (let c = 0; c < CLUSTER_COUNT; c++) {
    const chipsPerCluster = 3 + Math.floor(rand(c * 77) * 2); // 3 atau 4
    const clusterX = ((c * 1.618033988749 * 37.7) % 105) - 2;
    const clusterY = ((c * 1.618033988749 * 23.3 + rand(c * 5) * 25) % 120) - 10;
    const clusterDepth = 0.05 + rand(c * 13) * 0.28; // depth per cluster

    for (let j = 0; j < chipsPerCluster; j++) {
        const div = document.createElement("div");
        const variant = chipVariants[(c * 3 + j) % 3];
        div.className = `chip ${variant}`;

        // Posisi relatif ke cluster center — tersebar 3-8vw dari pusat
        const offsetX = (rand(c * 100 + j * 7) - 0.5) * 8;
        const offsetY = (rand(c * 100 + j * 11) - 0.5) * 6;
        const chipX = clusterX + offsetX;
        const chipY = clusterY + offsetY;

        // Depth sedikit berbeda per chip dalam cluster
        const depth = clusterDepth + (rand(c * 50 + j * 3) - 0.5) * 0.06;

        // Skala berdasarkan depth + variasi
        const scale = 0.35 + depth * 2.0 + rand(c * 30 + j * 9) * 0.5;

        // Rotasi acak
        const rot = rand(c * 20 + j * 17) * 360;

        // Fase unik untuk floating
        const phase = rand(c * 40 + j * 23) * Math.PI * 2;
        const speed = 0.6 + rand(c * 60 + j * 31) * 0.8; // kecepatan float berbeda

        div.style.left = chipX + "vw";
        div.style.top = chipY + "vh";
        div.style.transform = `translate3d(0,0,0) rotate(${rot}deg) scale(${scale})`;

        bg.appendChild(div);

        allChips.push({
            el: div,
            x: chipX,
            y: chipY,
            depth,
            baseRot: rot,
            baseScale: scale,
            phase,
            speed
        });
    }
}

// Generate solo scattered chips — berbeda ukuran, tersebar sendiri-sendiri
const SOLO_COUNT = 18;
for (let s = 0; s < SOLO_COUNT; s++) {
    const div = document.createElement("div");
    const variant = chipVariants[s % 3];
    div.className = `chip ${variant}`;

    // Posisi tersebar luas — pakai offset berbeda dari clusters
    const soloX = ((s * 1.618033988749 * 53.1 + 17) % 108) - 4;
    const soloY = ((s * 1.618033988749 * 31.7 + 41) % 140) - 20;

    // Depth bervariasi
    const depth = 0.04 + rand(s * 200 + 1) * 0.30;

    // Ukuran sangat bervariasi: dari sangat kecil (0.15) sampai besar (1.4)
    const sizeVariety = [0.15, 0.2, 0.25, 0.35, 0.5, 0.65, 0.8, 1.0, 1.2, 1.4];
    const scale = sizeVariety[s % sizeVariety.length] + rand(s * 201) * 0.15;

    const rot = rand(s * 202) * 360;
    const phase = rand(s * 203) * Math.PI * 2;
    const speed = 0.5 + rand(s * 204) * 1.0;

    div.style.left = soloX + "vw";
    div.style.top = soloY + "vh";
    div.style.transform = `translate3d(0,0,0) rotate(${rot}deg) scale(${scale})`;

    bg.appendChild(div);

    allChips.push({
        el: div,
        x: soloX,
        y: soloY,
        depth,
        baseRot: rot,
        baseScale: scale,
        phase,
        speed
    });
}

// Mouse tracking untuk parallax cursor
let mouseX = 0.5; // normalized 0-1
let mouseY = 0.5;
let targetMouseX = 0.5;
let targetMouseY = 0.5;

window.addEventListener("mousemove", (e) => {
    targetMouseX = e.clientX / window.innerWidth;
    targetMouseY = e.clientY / window.innerHeight;
}, { passive: true });

let scrollY = 0;
let targetScroll = 0;

function updateParallax() {
    scrollY += (targetScroll - scrollY) * 0.08;
    // Smooth mouse lerp
    mouseX += (targetMouseX - mouseX) * 0.04;
    mouseY += (targetMouseY - mouseY) * 0.04;

    const t = scrollY;
    const now = performance.now();

    // Mouse offset dari center (-0.5 to +0.5)
    const mx = (mouseX - 0.5);
    const my = (mouseY - 0.5);

    for (let i = 0; i < allChips.length; i++) {
        const c = allChips[i];
        const { el, depth, baseRot, baseScale, phase, speed } = c;

        // Scroll parallax
        const scrollX = Math.sin((t * 0.0012) + i * 0.7) * (14 * depth);
        const scrollYOffset = t * depth * 0.22;

        // Idle floating — continuous bobbing
        const floatX = Math.sin(now * 0.0003 * speed + phase) * (8 + depth * 18);
        const floatY = Math.cos(now * 0.00025 * speed + phase * 1.3) * (6 + depth * 14);

        // Mouse cursor parallax — depth chip besar = reaksi lebih besar
        const mouseParX = mx * depth * -80;
        const mouseParY = my * depth * -60;

        // Rotasi: base + scroll + idle wobble
        const rot = baseRot + (t * 0.008 * depth) + Math.sin(now * 0.00015 * speed + phase) * 5;

        // Scale pulse halus saat idle
        const scalePulse = baseScale + Math.sin(now * 0.0004 * speed + phase * 0.7) * 0.03;

        const totalX = scrollX + floatX + mouseParX;
        const totalY = scrollYOffset + floatY + mouseParY;

        el.style.transform = `translate3d(${totalX}px, ${totalY}px, 0) rotate(${rot}deg) scale(${scalePulse})`;
    }

    bg.style.transform = `translate3d(0, ${-(t * 0.04)}px, 0)`;
    requestAnimationFrame(updateParallax);
}

window.addEventListener("scroll", () => { targetScroll = window.scrollY || 0; }, { passive: true });
updateParallax();

/* ==========================
   Product data-driven render
========================== */
// Use server-side products if available, fallback to hardcoded
const products = (window.__PRODUCTS__ && window.__PRODUCTS__.length > 0)
    ? window.__PRODUCTS__
    : [
        { id: 1, key: "original", name: "Original",       desc: "Rasa natural & gurih. Tipis, renyah.",       price: 15000, img: ASSETS.variants.original, glow: "rgba(255,213,74,.22)" },
        { id: 2, key: "keju",     name: "Keju",            desc: "Aroma keju creamy, balance manis.",          price: 15000, img: ASSETS.variants.keju,     glow: "rgba(255,255,255,.18)" },
        { id: 3, key: "balado",   name: "Balado",          desc: "Pedas-manis gurih, bikin nagih.",            price: 15000, img: ASSETS.variants.balado,   glow: "rgba(255,120,60,.20)" },
        { id: 4, key: "coklat",   name: "Coklat",          desc: "Manis coklat tebal, cocok dessert.",         price: 17000, img: ASSETS.variants.coklat,   glow: "rgba(120,75,45,.22)" },
        { id: 5, key: "pedas",    name: "Pedas",           desc: "Lebih nendang untuk pecinta pedas.",         price: 15000, img: ASSETS.variants.pedas,    glow: "rgba(255,59,92,.18)" },
        { id: 6, key: "pedas2",   name: "Pedas Level 2",   desc: "Kalau mau extra nendang (dummy).",           price: 15000, img: ASSETS.variants.pedas,    glow: "rgba(255,59,92,.22)" }
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
                <button class="btn mini primary add" type="button" data-name="${p.name}" data-price="${p.price}" data-id="${p.id}" data-img="${p.img}">Tambah</button>
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

function saveCartToStorage() {
    const data = [];
    for (const [name, it] of cart.entries()) {
        data.push({ name, product_id: it.product_id, price: it.price, qty: it.qty, img: it.img || '' });
    }
    localStorage.setItem('ilil_cart', JSON.stringify(data));
}

function addItem(name, price, productId, img) {
    const cur = cart.get(name) || { price, qty: 0, product_id: productId, img: img || '' };
    cur.price = price;
    cur.product_id = productId;
    if (img) cur.img = img;
    cur.qty += 1;
    cart.set(name, cur);
    renderCart();
    saveCartToStorage();
}

function updateQty(name, delta) {
    const it = cart.get(name);
    if (!it) return;
    it.qty = Math.max(1, it.qty + delta);
    cart.set(name, it);
    renderCart();
    saveCartToStorage();
}

function removeItem(name) {
    cart.delete(name);
    renderCart();
    saveCartToStorage();
}

function openCart() { modal.classList.add("open"); renderCart(); }
function closeCart() { modal.classList.remove("open"); }

$("#cartBtn").onclick = openCart;
$("#closeModal").onclick = closeCart;
modal.addEventListener("click", (e) => { if (e.target === modal) closeCart(); });

document.addEventListener("click", (e) => {
    const btn = e.target.closest(".add");
    if (!btn) return;
    addItem(btn.dataset.name, parseInt(btn.dataset.price, 10), parseInt(btn.dataset.id, 10), btn.dataset.img || '');
});

// Restore cart from localStorage on page load
try {
    const saved = JSON.parse(localStorage.getItem('ilil_cart') || '[]');
    saved.forEach(item => {
        cart.set(item.name, { price: item.price, qty: item.qty, product_id: item.product_id, img: item.img || '' });
    });
    renderCart();
} catch(e) {}

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
        if (cart.size === 0) { openCart(); return; }
        saveCartToStorage();
        window.location.href = '/checkout';
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
    // Grid 4 columns, no loop
    const layout = [0, 1, 2, 3].map(i => ({ i }));

    galleryGrid.innerHTML = layout.map((x) => {
        const g = ASSETS.gallery[x.i];
        return `
            <div class="shot reveal" data-i="${x.i}">
                <img src="${g.src}" alt="${g.cap}" loading="lazy">
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
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.outputColorSpace = THREE.SRGBColorSpace;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(40, 1, 0.1, 100);
    camera.position.set(0, 0.3, 4);

    // === Lighting (studio-style) ===
    scene.add(new THREE.AmbientLight(0xffffff, 0.4));

    const key = new THREE.DirectionalLight(0xffffff, 1.2);
    key.position.set(3, 3, 3);
    key.castShadow = true;
    key.shadow.mapSize.set(512, 512);
    key.shadow.bias = -0.002;
    scene.add(key);

    const rimLight = new THREE.DirectionalLight(0xffffff, 0.8);
    rimLight.position.set(0, 3, -3);
    scene.add(rimLight);

    const rimGold = new THREE.PointLight(0xffd54a, 0.9, 12);
    rimGold.position.set(2, 1.5, -2);
    scene.add(rimGold);

    const rimGreen = new THREE.PointLight(0x39d98a, 0.6, 10);
    rimGreen.position.set(-2, 0.8, -1.5);
    scene.add(rimGreen);

    scene.fog = new THREE.FogExp2(0x07130c, 0.03);

    const group = new THREE.Group();
    scene.add(group);

    // === Stage Platform ===
    const plate = new THREE.Mesh(
        new THREE.CylinderGeometry(1.7, 1.8, 0.05, 64),
        new THREE.MeshStandardMaterial({ color: 0x0a1c12, roughness: 0.35, metalness: 0.3, transparent: true, opacity: 0.6 })
    );
    plate.position.y = -1.18;
    plate.receiveShadow = true;
    group.add(plate);

    // Glow ring
    const ring = new THREE.Mesh(
        new THREE.RingGeometry(1.35, 1.72, 64),
        new THREE.MeshBasicMaterial({ color: 0x39d98a, transparent: true, opacity: 0.1, side: THREE.DoubleSide })
    );
    ring.rotation.x = -Math.PI / 2;
    ring.position.y = -1.14;
    group.add(ring);

    // === Pouch Geometry (modified BoxGeometry) ===
    function createPouchGeometry() {
        const width = 1.25, height = 2.0, depth = 0.42;
        const geo = new THREE.BoxGeometry(width, height, depth, 40, 60, 20);

        const pos = geo.attributes.position;
        const v = new THREE.Vector3();
        const halfH = height * 0.5;
        const halfW = width * 0.5;

        for (let i = 0; i < pos.count; i++) {
            v.fromBufferAttribute(pos, i);

            const ny = v.y / halfH;   // -1..1
            const nx = v.x / halfW;   // -1..1

            // Bulge (filled pouch)
            const curve = (1 - Math.min(1, Math.abs(nx))) ** 1.6;
            const signZ = Math.sign(v.z) || 1;
            v.z += signZ * 0.12 * curve * (0.55 + 0.45 * (1 - Math.abs(ny)));

            // Side pinch
            v.x *= 1 - 0.08 * (1 - Math.abs(ny));

            // Top seal flatten
            if (ny > 0.8) v.z *= 0.9;

            // Bottom gusset feel
            if (ny < -0.8) {
                v.z *= 0.85;
                v.x *= 1.05;
                v.y += 0.02 * (-(ny + 0.8) / 0.2);
            }

            pos.setXYZ(i, v.x, v.y, v.z);
        }

        geo.computeVertexNormals();
        return geo;
    }

    // === Curved Label ===
    function createLabel(texture) {
        const w = 0.95, h = 1.2;
        const geo = new THREE.PlaneGeometry(w, h, 40, 40);

        const pos = geo.attributes.position;
        const v = new THREE.Vector3();
        const halfW = w * 0.5;

        for (let i = 0; i < pos.count; i++) {
            v.fromBufferAttribute(pos, i);
            const nx = v.x / halfW;
            v.z = -0.10 * nx * nx; // hug the pouch curvature
            pos.setXYZ(i, v.x, v.y, v.z);
        }
        geo.computeVertexNormals();

        const mat = new THREE.MeshStandardMaterial({
            map: texture,
            transparent: true,
            roughness: 0.65,
            metalness: 0
        });

        const mesh = new THREE.Mesh(geo, mat);
        mesh.position.set(0, -0.05, 0.28);
        return mesh;
    }

    // === Create Pouch ===
    const pouch = new THREE.Mesh(
        createPouchGeometry(),
        new THREE.MeshStandardMaterial({ color: 0xf7f7f7, roughness: 0.9, metalness: 0 })
    );
    pouch.castShadow = true;
    pouch.receiveShadow = true;
    group.add(pouch);

    // === Load Label Texture (pack-hero.png) ===
    let label = null;
    const loader = new THREE.TextureLoader();
    loader.load(ASSETS.heroPack, (tex) => {
        tex.colorSpace = THREE.SRGBColorSpace;
        tex.anisotropy = renderer.capabilities.getMaxAnisotropy();
        label = createLabel(tex);
        group.add(label);
    });

    // === Shadow plane ===
    const shadowPlane = new THREE.Mesh(
        new THREE.PlaneGeometry(4, 4),
        new THREE.ShadowMaterial({ opacity: 0.3 })
    );
    shadowPlane.rotation.x = -Math.PI / 2;
    shadowPlane.position.y = -1.16;
    shadowPlane.receiveShadow = true;
    group.add(shadowPlane);

    // === Chip Particles ===
    const chipCount = 30;
    const chipGeo = new THREE.PlaneGeometry(0.24, 0.24);
    const chips3D = [];

    for (let i = 0; i < chipCount; i++) {
        const mat = new THREE.MeshStandardMaterial({
            color: new THREE.Color().setHSL(0.11 + Math.random() * 0.06, 0.75, 0.5 + Math.random() * 0.2),
            roughness: 0.7, metalness: 0.04,
            transparent: true, opacity: 0.15 + Math.random() * 0.22,
            side: THREE.DoubleSide
        });
        const m = new THREE.Mesh(chipGeo, mat);
        const angle = (i / chipCount) * Math.PI * 2;
        const radius = 2.0 + (i % 5) * 0.12;
        m.position.set(Math.cos(angle) * radius, (i % 8) * 0.18 - 0.6, Math.sin(angle) * radius);
        m.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI);
        m.castShadow = true;
        group.add(m);
        chips3D.push(m);
    }

    // === Resize ===
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

    // === Animate ===
    let t0 = performance.now();
    function animate(now) {
        const dt = (now - t0) / 1000;
        t0 = now;

        const s = clamp((window.scrollY || 0) / (document.body.scrollHeight - innerHeight + 1), 0, 1);

        // Pouch gentle wobble
        pouch.rotation.y = Math.sin(now * 0.0005) * 0.22 + s * 0.1;
        pouch.rotation.x = Math.sin(now * 0.00038) * 0.04;
        pouch.position.y = Math.sin(now * 0.0007) * 0.035;

        // Label follows pouch rotation
        if (label) {
            label.rotation.y = pouch.rotation.y;
            label.rotation.x = pouch.rotation.x;
            label.position.y = -0.05 + pouch.position.y;
        }

        // Slow group rotation
        group.rotation.y += dt * (0.15 + s * 0.35);
        group.rotation.x = Math.sin(now * 0.00045) * 0.04 + s * 0.03;

        // Ring pulse
        ring.material.opacity = 0.07 + Math.sin(now * 0.001) * 0.04;

        // Chip particles
        for (let i = 0; i < chips3D.length; i++) {
            const m = chips3D[i];
            m.rotation.x += dt * (0.25 + i * 0.004);
            m.rotation.y += dt * (0.2 + i * 0.005);
            m.position.y += Math.sin(now * 0.0007 + i) * 0.0006;
        }

        // Camera cinematic
        camera.position.x = Math.sin(now * 0.00025) * 0.16;
        camera.position.y = 0.35 + Math.cos(now * 0.0004) * 0.07 - s * 0.12;
        camera.lookAt(0, 0.1, 0);

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
