<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Retail POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { background-color: #f4f6f9; font-size: 13px; overflow: hidden; }
        .pos-container { display: flex; height: 100vh; }
        .left-panel { flex: 1; padding: 12px; overflow-y: auto; }
        .right-panel { width: 400px; background: white; border-left: 1px solid #ddd; padding: 12px; overflow-y: auto; display: flex; flex-direction: column;}
        .card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 12px; border: none; }
        .card-header { background: white; border-bottom: 1px solid #f0f0f0; font-weight: 600; padding: 10px 15px; }
        .cart-table th { background: #f8f9fa; font-size: 12px; }
        .cart-table td { font-size: 13px; vertical-align: middle; }
        .qty-control button { width: 26px; height: 26px; padding: 0; line-height: 1; font-weight: bold; }
        .payment-method button { margin-right: 4px; margin-bottom: 4px; font-size: 12px; }
        .payment-method button.active { background-color: #0d6efd; color: white; border-color: #0d6efd; }
        .total-box { background: linear-gradient(135deg, #e9ecef, #dee2e6); padding: 12px; border-radius: 8px; margin-top: 8px; }
        .change-box { background: #d1e7dd; padding: 10px; border-radius: 8px; margin-top: 8px; text-align: center; transition: all 0.3s; }
        .change-box.short-payment { background: #f8d7da; }
        .quick-pay-btn { font-size: 12px; padding: 4px 8px; }
        .product-results { position: absolute; width: calc(100% - 24px); max-height: 300px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .sidebar-toggle-btn { position: fixed; bottom: 15px; right: 15px; z-index: 999; }
        .keyboard-hint { font-size: 10px; color: #999; }
    </style>
</head>
<body>

<div class="pos-container">
    <!-- LEFT PANEL -->
    <div class="left-panel">
        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2 bg-white p-3 rounded shadow-sm">
            <div>
                <h5 class="mb-0 fw-bold"><i class="fas fa-store text-primary"></i> TOKO RETAIL MAKMUR</h5>
                <small class="text-muted">Jl. Contoh No. 123 • Jember • Telp. 0812-xxxx-xxxx</small>
            </div>
            <div class="text-end">
                <div class="text-muted small">No. Transaksi</div>
                <div class="fw-bold text-primary" id="transactionNumber">TRX-{{ date('Ymd') }}-AUTO</div>
                <div class="small text-muted" id="currentDate"></div>
            </div>
        </div>

        <!-- SEARCH & CUSTOMER -->
        <div class="card">
            <div class="card-header"><i class="fas fa-search"></i> Tambah Barang <span class="keyboard-hint">(F2 untuk fokus)</span></div>
            <div class="card-body">
                <div class="input-group mb-3 position-relative">
                    <input type="text" id="searchProduct" class="form-control" placeholder="Cari nama barang / kode / barcode..." autocomplete="off">
                    <button class="btn btn-primary" type="button"><i class="fas fa-search"></i> Cari</button>
                    <div class="list-group product-results" id="productResults" style="display: none; top: 100%; left: 0;"></div>
                </div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pelanggan</label>
                        <select class="form-select form-select-sm">
                            <option>Umum</option>
                            <option>Pelanggan Member</option>
                            <option>Member VIP</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">No. Member / HP</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Opsional">
                    </div>
                </div>
            </div>
        </div>

        <!-- CART -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-shopping-cart"></i> Keranjang Belanja</span>
                <span id="itemCount" class="badge bg-primary">0 Item</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 cart-table">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Barang</th>
                            <th width="100" class="text-end">Harga</th>
                            <th width="140" class="text-center">Qty</th>
                            <th width="120" class="text-end">Subtotal</th>
                            <th width="40"></th>
                        </tr>
                    </thead>
                    <tbody id="cartBody"></tbody>
                </table>
            </div>
        </div>

        <!-- HELD TRANSACTIONS -->
        <div class="card" id="heldCard">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-pause-circle"></i> Transaksi Ditahan</span>
                <span class="badge bg-warning text-dark" id="heldCount">0</span>
            </div>
            <div class="card-body" id="heldList" style="max-height: 200px; overflow-y: auto;"></div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <h6 class="fw-bold mb-3"><i class="fas fa-calculator"></i> Ringkasan Pembayaran</h6>

        <div class="d-flex justify-content-between mb-2">
            <span>Total Item</span>
            <strong id="totalQty">0</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <span>Subtotal</span>
            <strong id="subtotal">Rp 0</strong>
        </div>

        <div class="row g-2 mb-2">
            <div class="col-6">
                <label class="form-label small mb-0">Diskon (%)</label>
                <input type="number" id="discountPercent" class="form-control form-control-sm" value="0" min="0" oninput="calculateTotal()">
            </div>
            <div class="col-6">
                <label class="form-label small mb-0">Diskon (Rp)</label>
                <input type="number" id="discountAmount" class="form-control form-control-sm" value="0" min="0" oninput="calculateTotal()">
            </div>
            <div class="col-6">
                <label class="form-label small mb-0">Pajak / PPN</label>
                <input type="number" id="tax" class="form-control form-control-sm" value="0" min="0" oninput="calculateTotal()">
            </div>
            <div class="col-6">
                <label class="form-label small mb-0">Biaya Lain</label>
                <input type="number" id="otherFee" class="form-control form-control-sm" value="0" min="0" oninput="calculateTotal()">
            </div>
        </div>

        <div class="total-box d-flex justify-content-between align-items-center">
            <span class="fw-bold">TOTAL AKHIR</span>
            <h4 class="mb-0 fw-bold text-primary" id="grandTotal">Rp 0</h4>
        </div>

        <div class="mt-3">
            <label class="form-label fw-bold small">Uang Dibayar <span class="keyboard-hint">(F4)</span></label>
            <input type="number" id="payment" class="form-control form-control-lg fw-bold text-end" placeholder="0" oninput="calculateChange()">
            <div class="d-flex gap-1 mt-2">
                <button class="btn btn-outline-secondary quick-pay-btn flex-fill" onclick="quickPay(10000)">10rb</button>
                <button class="btn btn-outline-secondary quick-pay-btn flex-fill" onclick="quickPay(20000)">20rb</button>
                <button class="btn btn-outline-secondary quick-pay-btn flex-fill" onclick="quickPay(50000)">50rb</button>
                <button class="btn btn-outline-secondary quick-pay-btn flex-fill" onclick="quickPay(100000)">100rb</button>
            </div>
            <div class="d-flex gap-1 mt-2">
                <button class="btn btn-outline-primary quick-pay-btn flex-fill" onclick="quickPay('bulat')">Bulatkan</button>
                <button class="btn btn-outline-success quick-pay-btn flex-fill" onclick="quickPay('pas')">Uang Pas</button>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label fw-bold small">Metode Pembayaran</label>
            <div class="payment-method">
                <button class="btn btn-outline-primary btn-sm active" onclick="selectPayment(this)">Tunai</button>
                <button class="btn btn-outline-primary btn-sm" onclick="selectPayment(this)">QRIS</button>
                <button class="btn btn-outline-primary btn-sm" onclick="selectPayment(this)">Debit</button>
                <button class="btn btn-outline-primary btn-sm" onclick="selectPayment(this)">Kredit</button>
                <button class="btn btn-outline-primary btn-sm" onclick="selectPayment(this)">E-Wallet</button>
                <button class="btn btn-outline-primary btn-sm" onclick="selectPayment(this)">Transfer</button>
            </div>
        </div>

        <div class="change-box" id="changeBox">
            <div class="small fw-bold" id="changeLabel">KEMBALIAN</div>
            <h4 class="mb-0 fw-bold" id="change">Rp 0</h4>
        </div>

        <div class="mt-3 d-grid gap-2">
            <div class="row g-2">
                <div class="col-6"><button class="btn btn-warning w-100" onclick="holdTransaction()"><i class="fas fa-pause"></i> Tahan</button></div>
                <div class="col-6"><button class="btn btn-danger w-100" onclick="cancelTransaction()"><i class="fas fa-times"></i> Batal (ESC)</button></div>
            </div>
            <button class="btn btn-success btn-lg mt-2" onclick="processPayment()"><i class="fas fa-print"></i> BAYAR & CETAK <span class="keyboard-hint text-white">(F8)</span></button>
        </div>

        <div class="text-center mt-2">
            <a href="/kasir/riwayat" class="small text-decoration-none"><i class="fas fa-history"></i> Lihat Riwayat Transaksi</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/kasir.js') }}"></script>
</body>
</html>