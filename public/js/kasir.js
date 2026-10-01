let cart = [];
let productsCache = [];
let heldTransactions = JSON.parse(localStorage.getItem('held_transactions') || '[]');
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const searchInput = document.getElementById("searchProduct");
const resultBox = document.getElementById("productResults");

// ==========================================
// 1. PENCARIAN PRODUK
// ==========================================
searchInput.addEventListener("input", function() {
    const keyword = this.value.trim();
    if (keyword.length < 2) { resultBox.style.display = "none"; return; }

    fetch(`/api/cari-produk?q=${keyword}`)
        .then(res => res.json())
        .then(data => {
            productsCache = data;
            if (data.length === 0) {
                resultBox.innerHTML = `<div class="list-group-item">Barang tidak ditemukan</div>`;
            } else {
                resultBox.innerHTML = data.map(p => `
                    <a href="#" class="list-group-item list-group-item-action" onclick="addToCart(${p.id}, event)">
                        <div class="d-flex justify-content-between">
                            <div><strong>${p.name}</strong><br><small class="text-muted">${p.code} | Stok: ${p.stock}</small></div>
                            <div class="text-primary fw-bold">${formatRupiah(p.price)}</div>
                        </div>
                    </a>
                `).join("");
            }
            resultBox.style.display = "block";
        });
});

document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !resultBox.contains(e.target)) resultBox.style.display = "none";
});

// ==========================================
// 2. FORMAT RUPIAH
// ==========================================
function formatRupiah(value) {
    return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(value);
}

// ==========================================
// 3. ADD TO CART dengan VALIDASI STOK
// ==========================================
function addToCart(productId, event) {
    if (event) event.preventDefault();
    const product = productsCache.find(p => p.id === productId);
    if (!product) return;

    const existing = cart.find(item => item.id === productId);
    const currentQty = existing ? existing.qty : 0;

    // Validasi stok
    if (currentQty + 1 > product.stock) {
        alert(`Stok ${product.name} tidak cukup! Tersedia: ${product.stock}`);
        return;
    }

    if (existing) {
        existing.qty++;
    } else {
        cart.push({ ...product, qty: 1 });
    }

    searchInput.value = "";
    resultBox.style.display = "none";
    renderCart();
    searchInput.focus();
}

// ==========================================
// 4. RENDER CART
// ==========================================
function renderCart() {
    const body = document.getElementById("cartBody");
    if (cart.length === 0) {
        body.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Keranjang masih kosong.<br>Silakan cari atau scan barang.</td></tr>`;
        calculateTotal();
        return;
    }

    body.innerHTML = cart.map((item, index) => `
        <tr>
            <td>${index + 1}</td>
            <td><strong>${item.name}</strong><br><small class="text-muted">${item.code}</small></td>
            <td>${formatRupiah(item.price)}</td>
            <td class="text-center">
                <div class="qty-control justify-content-center">
                    <button class="btn btn-sm btn-outline-secondary" onclick="changeQty(${item.id}, -1)">-</button>
                    <input type="number" value="${item.qty}" min="1" max="${item.stock}" onchange="updateQty(${item.id}, this.value)" style="width:50px;text-align:center;">
                    <button class="btn btn-sm btn-outline-secondary" onclick="changeQty(${item.id}, 1)">+</button>
                </div>
            </td>
            <td class="text-end"><strong>${formatRupiah(item.price * item.qty)}</strong></td>
            <td class="text-center"><button class="btn btn-sm btn-danger" onclick="removeItem(${item.id})">×</button></td>
        </tr>
    `).join("");
    calculateTotal();
}

function changeQty(id, amount) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    const newQty = item.qty + amount;

    if (newQty > item.stock) {
        alert(`Stok ${item.name} tidak cukup! Maksimal: ${item.stock}`);
        return;
    }
    if (newQty < 1) return;

    item.qty = newQty;
    renderCart();
}

function updateQty(id, qty) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    let newQty = parseInt(qty) || 1;

    if (newQty > item.stock) {
        alert(`Stok ${item.name} tidak cukup! Maksimal: ${item.stock}`);
        newQty = item.stock;
    }
    item.qty = newQty;
    renderCart();
}

function removeItem(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

// ==========================================
// 5. HITUNG TOTAL
// ==========================================
function calculateTotal() {
    let totalQty = 0, subtotal = 0;
    cart.forEach(item => { totalQty += item.qty; subtotal += item.price * item.qty; });

    const discPercent = parseFloat(document.getElementById("discountPercent").value) || 0;
    const discAmount = parseFloat(document.getElementById("discountAmount").value) || 0;
    const tax = parseFloat(document.getElementById("tax").value) || 0;
    const otherFee = parseFloat(document.getElementById("otherFee").value) || 0;

    const totalDiscount = (subtotal * discPercent / 100) + discAmount;
    const grandTotal = Math.max(0, subtotal - totalDiscount + tax + otherFee);

    document.getElementById("totalQty").innerText = totalQty;
    document.getElementById("itemCount").innerText = totalQty + " Item";
    document.getElementById("subtotal").innerText = formatRupiah(subtotal);
    document.getElementById("grandTotal").innerText = formatRupiah(grandTotal);
    calculateChange();
}

function getGrandTotal() {
    let subtotal = 0;
    cart.forEach(item => { subtotal += item.price * item.qty; });
    const discPercent = parseFloat(document.getElementById("discountPercent").value) || 0;
    const discAmount = parseFloat(document.getElementById("discountAmount").value) || 0;
    const tax = parseFloat(document.getElementById("tax").value) || 0;
    const otherFee = parseFloat(document.getElementById("otherFee").value) || 0;
    const totalDiscount = (subtotal * discPercent / 100) + discAmount;
    return Math.max(0, subtotal - totalDiscount + tax + otherFee);
}

// ==========================================
// 6. HITUNG KEMBALIAN
// ==========================================
function calculateChange() {
    const grandTotal = getGrandTotal();
    const payment = parseFloat(document.getElementById("payment").value) || 0;
    const change = payment - grandTotal;
    const changeBox = document.getElementById("changeBox");
    const changeElement = document.getElementById("change");
    const changeLabel = document.getElementById("changeLabel");

    if (payment === 0) {
        changeBox.classList.remove("short-payment");
        changeElement.innerText = formatRupiah(0);
        changeLabel.innerText = "KEMBALIAN";
        return;
    }

    if (change >= 0) {
        changeBox.classList.remove("short-payment");
        changeElement.innerText = formatRupiah(change);
        changeLabel.innerText = "KEMBALIAN";
    } else {
        changeBox.classList.add("short-payment");
        changeElement.innerText = formatRupiah(Math.abs(change));
        changeLabel.innerText = "UANG KURANG";
    }
}

// ==========================================
// 7. NOMINAL CEPAT
// ==========================================
function quickPay(amount) {
    const grandTotal = getGrandTotal();
    const paymentInput = document.getElementById("payment");

    if (amount === 'pas') {
        paymentInput.value = grandTotal;
    } else if (amount === 'bulat') {
        // Bulatkan ke atas ke ribuan terdekat
        paymentInput.value = Math.ceil(grandTotal / 1000) * 1000;
    } else {
        paymentInput.value = amount;
    }
    calculateChange();
}

// ==========================================
// 8. PILIH METODE PEMBAYARAN
// ==========================================
function selectPayment(button) {
    document.querySelectorAll(".payment-method button").forEach(btn => btn.classList.remove("active"));
    button.classList.add("active");
}

// ==========================================
// 9. TAHAN TRANSAKSI (HOLD)
// ==========================================
function holdTransaction() {
    if (cart.length === 0) { alert("Tidak ada transaksi untuk ditahan."); return; }

    const held = {
        id: Date.now(),
        time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
        items: cart,
        customer: document.querySelector('select').value,
        total: getGrandTotal()
    };

    heldTransactions.push(held);
    localStorage.setItem('held_transactions', JSON.stringify(heldTransactions));

    cart = [];
    renderCart();
    renderHeldList();
    alert("Transaksi berhasil ditahan!");
}

function renderHeldList() {
    const container = document.getElementById("heldList");
    if (!container) return;

    if (heldTransactions.length === 0) {
        container.innerHTML = `<div class="text-muted small text-center py-2">Tidak ada transaksi ditahan.</div>`;
        return;
    }

    container.innerHTML = heldTransactions.map(h => `
        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
            <div>
                <strong>${h.time}</strong> - ${h.items.length} item
                <div class="small text-muted">${formatRupiah(h.total)}</div>
            </div>
            <div>
                <button class="btn btn-sm btn-success" onclick="resumeTransaction(${h.id})">Lanjutkan</button>
                <button class="btn btn-sm btn-danger" onclick="deleteHeld(${h.id})">×</button>
            </div>
        </div>
    `).join("");
}

function resumeTransaction(id) {
    const held = heldTransactions.find(h => h.id === id);
    if (!held) return;

    if (cart.length > 0) {
        if (!confirm("Keranjang saat ini akan diganti. Lanjutkan?")) return;
    }

    cart = held.items;
    heldTransactions = heldTransactions.filter(h => h.id !== id);
    localStorage.setItem('held_transactions', JSON.stringify(heldTransactions));
    renderCart();
    renderHeldList();
}

function deleteHeld(id) {
    if (!confirm("Hapus transaksi ditahan ini?")) return;
    heldTransactions = heldTransactions.filter(h => h.id !== id);
    localStorage.setItem('held_transactions', JSON.stringify(heldTransactions));
    renderHeldList();
}

// ==========================================
// 10. PROSES PEMBAYARAN
// ==========================================
function processPayment() {
    if (cart.length === 0) { alert("Keranjang masih kosong."); return; }

    const total = getGrandTotal();
    const payment = parseFloat(document.getElementById("payment").value) || 0;

    if (payment < total) { alert("Uang pembayaran masih kurang."); return; }

    const dataTransaksi = {
        items: cart,
        subtotal: cart.reduce((sum, item) => sum + (item.price * item.qty), 0),
        discount_percent: parseFloat(document.getElementById("discountPercent").value) || 0,
        discount_amount: parseFloat(document.getElementById("discountAmount").value) || 0,
        tax: parseFloat(document.getElementById("tax").value) || 0,
        other_fee: parseFloat(document.getElementById("otherFee").value) || 0,
        grand_total: total,
        paid_amount: payment,
        change_amount: payment - total,
        payment_method: document.querySelector(".payment-method button.active").innerText
    };

    fetch('/kasir/transaksi', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(dataTransaksi)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/kasir/struk/' + data.transaction_id;
        } else {
            alert("Gagal: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Terjadi kesalahan pada server.");
    });
}

// ==========================================
// 11. BATAL TRANSAKSI
// ==========================================
function cancelTransaction() {
    if (!confirm("Batalkan transaksi ini?")) return;
    cart = [];
    document.getElementById("payment").value = "";
    document.getElementById("discountPercent").value = 0;
    document.getElementById("discountAmount").value = 0;
    document.getElementById("tax").value = 0;
    document.getElementById("otherFee").value = 0;
    renderCart();
}

// ==========================================
// 12. KEYBOARD SHORTCUT
// ==========================================
document.addEventListener("keydown", function(event) {
    // F2 = Focus pencarian
    if (event.key === "F2") {
        event.preventDefault();
        searchInput.focus();
    }
    // F4 = Focus pembayaran
    if (event.key === "F4") {
        event.preventDefault();
        document.getElementById("payment").focus();
    }
    // F8 = Bayar
    if (event.key === "F8") {
        event.preventDefault();
        processPayment();
    }
    // ESC = Batal
    if (event.key === "Escape") {
        cancelTransaction();
    }
});

// ==========================================
// 13. TANGGAL OTOMATIS
// ==========================================
document.getElementById("currentDate").innerText = new Date().toLocaleDateString('id-ID', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
});

// ==========================================
// INISIALISASI
// ==========================================
renderCart();
renderHeldList();
searchInput.focus();