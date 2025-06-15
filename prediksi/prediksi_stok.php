<?php
include '../config.php';
include '../partials/header.php';
include '../partials/sidebar.php';

// Ambil semua brand unik dari tabel
$allBrands = [];
$brandResult = mysqli_query($koneksi, "SELECT DISTINCT brand FROM prediksi_history");
while ($row = mysqli_fetch_assoc($brandResult)) {
    $allBrands[] = $row['brand'];
}

// Ambil filter dari form
$filterBrand = isset($_GET['filter_brand']) ? $_GET['filter_brand'] : '';
$filterPeriode = isset($_GET['filter_periode']) ? $_GET['filter_periode'] : '';
$sortOrder = (isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc') ? 'ASC' : 'DESC';

// Buat query dasar
$query = "SELECT * FROM prediksi_history WHERE 1=1";

// Tambahkan filter jika ada
if (!empty($filterBrand)) {
    $query .= " AND brand = '" . mysqli_real_escape_string($koneksi, $filterBrand) . "'";
}
if (!empty($filterPeriode)) {
    $query .= " AND periode = " . intval($filterPeriode);
}

$query .= " ORDER BY tanggal $sortOrder";

// Eksekusi query
$result = mysqli_query($koneksi, $query);
$history = [];
while ($row = mysqli_fetch_assoc($result)) {
    $history[] = $row;
}

// Ambil semua brand unik dari tabel penjualan
$brands = [];
$result = mysqli_query($koneksi, "SELECT DISTINCT brand FROM penjualan ORDER BY brand ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $brands[] = $row['brand'];
}

// Ambil semua tipe berdasarkan brand dari tabel penjualan
$tipeList = [];
$result = mysqli_query($koneksi, "SELECT DISTINCT brand, nama FROM penjualan ORDER BY brand, nama ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $tipeList[$row['brand']][] = $row['nama'];
}

function getPenjualan($koneksi, $tipe, $brand, $bulan) {
    $tanggalMulai = date('Y-m-d', strtotime("-$bulan months"));

    $query = "SELECT jumlah FROM penjualan WHERE tanggal >= ?";
    $params = [$tanggalMulai];
    $types = "s";

    if (!empty($tipe)) {
        $query .= " AND nama = ?";
        $types .= "s";
        $params[] = $tipe;
    }
    if (!empty($brand)) {
        $query .= " AND brand = ?";
        $types .= "s";
        $params[] = $brand;
    }

    $query .= " ORDER BY tanggal DESC LIMIT ?";
    $types .= "i";
    $params[] = $bulan;

    $stmt = $koneksi->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $koneksi->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $penjualan = [];
    while ($row = $result->fetch_assoc()) {
        $penjualan[] = (int) $row['jumlah'];
    }

    $stmt->close();
    return $penjualan;
}

function hitungWMA($data, $periode) {
    if (count($data) < $periode) return 0;

    $total = 0;
    $bobotTotal = 0;
    for ($i = 0; $i < $periode; $i++) {
        $bobot = $periode - $i;
        $total += $data[$i] * $bobot;
        $bobotTotal += $bobot;
    }
    return $bobotTotal > 0 ? $total / $bobotTotal : 0;
}

$hasil_prediksi = null;
$error = null;
$actual = 0;
$tanggal = date('Y-m-d H:i:s');

if ($_SERVER["REQUEST_METHOD"] !== "POST" && isset($_GET['brand']) && isset($_GET['tipe'])) {
    $_POST['brand'] = $_GET['brand'];
    $_POST['tipe']  = $_GET['tipe'];
    $_POST['bulan'] = 3;
    $_POST['show_modal'] = '1';
    $_SERVER["REQUEST_METHOD"] = "POST";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipe   = $_POST['tipe'] ?? '';
    $brand  = $_POST['brand'] ?? '';
    $bulan  = (int) ($_POST['bulan'] ?? 3);

    $penjualan = getPenjualan($koneksi, $tipe, $brand, $bulan);

    if (!empty($penjualan) && count($penjualan) >= $bulan) {
        $hasil_prediksi = hitungWMA($penjualan, $bulan);
        $actual = $penjualan[0];

        $stmt = $koneksi->prepare("INSERT INTO prediksi_history (tipe, brand, periode, actual, prediction, tanggal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidds", $tipe, $brand, $bulan, $actual, $hasil_prediksi, $tanggal);
        $stmt->execute();
        $stmt->close();
    } else {
        $error = "Data penjualan belum mencukupi.";
    }
}

$show_modal = isset($_POST['show_modal']) && ($_POST['show_modal'] == '1');

// $history = [];
// $result = $koneksi->query("SELECT * FROM prediksi_history ORDER BY tanggal DESC LIMIT 50");
// while ($row = $result->fetch_assoc()) {
//     $history[] = $row;
// }
?>

<!-- Pengadaan Stok Handphone -->
<div class="container mt-4">
    <h2 class="mb-4">Pengadaan Stok Handphone</h2>
    <form method="post" class="card p-4 shadow-sm mb-4">
        <div class=""><h4 class="card-title">Hitung Prediksi Stok</h4></div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Brand</label>
                <select name="brand" id="brandSelect" class="form-select" onchange="filterTipe()">
                    <option value="">-- Semua Brand --</option>
                    <?php foreach ($brands as $b): ?>
                        <option value="<?= htmlspecialchars($b) ?>" <?= (isset($brand) && $brand === $b) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tipe Handphone</label>
                <select name="tipe" id="tipeSelect" class="form-select">
                    <option value="">-- Semua Tipe --</option>
                    <?php foreach ($tipeList as $b => $tipes): ?>
                        <?php foreach ($tipes as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>" data-brand="<?= htmlspecialchars($b) ?>" 
                                <?= (isset($tipe) && $tipe === $t) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Periode Bulan</label>
                <select name="bulan" class="form-select" required>
                    <?php foreach ([3, 6, 9, 12] as $p): ?>
                        <option value="<?= $p ?>" <?= (isset($bulan) && $bulan == $p) ? 'selected' : '' ?>>
                            <?= $p ?> Bulan
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="d-flex flex-column gap-2">
            <button type="submit" class="btn btn-primary">Hitung Prediksi</button>
            <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Reset</a>
        </div>
        
        <input type="hidden" name="show_modal" value="1">
    </form>

    <!-- Hasil Prediksi -->
    <?php if ($hasil_prediksi !== null || !empty($error)): ?>
        <div class="card mb-4 shadow-sm d-flex flex-row align-items-center p-4">
            <div class="flex-grow-1">
                <?php if ($hasil_prediksi !== null): ?>
                    <h4 class="card-title mb-2">Hasil Prediksi</h4>
                    <p class="mb-1">Prediksi stok bulan depan: <strong><?= round($hasil_prediksi) ?> unit</strong></p>
                    <p class="mb-1">Penjualan terakhir: <strong><?= $actual ?> unit</strong></p>
                    <p class="mb-0">Tanggal Prediksi: <strong><?= $tanggal ?></strong></p>
                <?php else: ?>
                    <div class="alert alert-warning mb-0"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
            </div>
            <div class="ms-3 d-flex flex-column gap-2">
                <?php if ($hasil_prediksi !== null): ?>
                    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">Cetak Prediksi</button>
                <?php endif; ?>
                <a href="prediksi_stok.php" class="btn btn-sm btn-outline-danger">Tutup</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['hapus']) && $_GET['hapus'] === 'berhasil'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data berhasil dihapus.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Riwayat Prediksi + Filter -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h4 class="mb-4">Riwayat Prediksi</h4>

            <!-- Form Filter -->
                <form method="get" class="mb-4">
                    <div class="flex row g-3 align-items-end">
                        <!-- Filter Brand -->
                        <div class="col-md-4">
                            <label for="filter_brand" class="form-label fw-semibold">Brand</label>
                            <select name="filter_brand" id="filter_brand" class="form-select">
                                <option value="">-- Semua Brand --</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= htmlspecialchars($b) ?>" <?= (isset($filter_brand) && $filter_brand === $b) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($b) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filter Periode -->
                        <div class="col-md-3">
                            <label for="filter_periode" class="form-label fw-semibold">Periode Prediksi</label>
                            <select name="filter_periode" id="filter_periode" class="form-select">
                                <option value="">-- Semua Periode --</option>
                                <?php foreach ([3, 6, 9, 12] as $p): ?>
                                    <option value="<?= $p ?>" <?= ($filterPeriode == $p) ? 'selected' : '' ?>><?= $p ?> Bulan</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Urutan -->
                        <div class="col-md-3">
                            <label for="sort_order" class="form-label fw-semibold">Urutkan Berdasarkan</label>
                            <select name="sort_order" id="sort_order" class="form-select">
                                <option value="desc" <?= ($sortOrder == 'DESC') ? 'selected' : '' ?>>Terbaru ke Terlama</option>
                                <option value="asc" <?= ($sortOrder == 'ASC') ? 'selected' : '' ?>>Terlama ke Terbaru</option>
                            </select>
                        </div>

                        <!-- Tombol -->
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>


            <!-- Daftar Riwayat -->
            <?php if (!empty($history)): ?>
                <div class="list-group">
                    <?php
                        $itemsPerPage = 10;
                        $totalItems = count($history);
                        $total_page = ceil($totalItems / $itemsPerPage);
                        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                        $start = ($page - 1) * $itemsPerPage;
                        $currentItems = array_slice($history, $start, $itemsPerPage);
                    ?>

                    <?php foreach ($currentItems as $h): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <h6><?= htmlspecialchars($h['brand']) ?> - <?= htmlspecialchars($h['tipe']) ?></h6>
                                <small>
                                    <strong>Periode:</strong> <?= $h['periode'] ?> bulan |
                                    <strong>Penjualan terakhir:</strong> <?= $h['actual'] ?> unit |
                                    <strong>Prediksi:</strong> <?= round($h['prediction']) ?> unit |
                                    <strong>Tanggal:</strong> <?= date('d-m-Y', strtotime($h['tanggal'])) ?>
                                </small>
                            </div>
                            <form method="post" action="hapus_prediksi.php" onsubmit="return confirm('Yakin ingin menghapus prediksi ini?')">
                                <input type="hidden" name="id" value="<?= $h['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_page; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link"
                                href="?page=<?= $i ?>&filter_brand=<?= urlencode($filter_brand ?? '') ?>&filter_periode=<?= urlencode($filterPeriode ?? '') ?>&sort_order=<?= urlencode($sortOrder ?? '') ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="alert alert-warning mt-3">Tidak ada data prediksi sesuai filter.</div>
            <?php endif; ?>
        </div>
    </div>

    <a href="../index.php" class="btn btn-secondary mt-3">&larr; Kembali ke Dashboard</a>
</div>


<script>
function filterTipe() {
    const selectedBrand = document.getElementById('brandSelect').value;
    const tipeSelect = document.getElementById('tipeSelect');
    const options = tipeSelect.options;

    for (let i = 0; i <script options.length; i++) {
        const option = options[i];
        const brand = option.getAttribute('data-brand');
        if (!brand || selectedBrand === "" || brand === selectedBrand) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }

    tipeSelect.value = ""; // Reset value ketika brand diganti
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js">
function cetakPDF() {
    const element = document.getElementById('hasilPrediksi');
    html2pdf().set({
        margin: 10,
        filename: 'hasil_prediksi.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(element).save();
}
</script>



<?php include('../partials/footer.php'); ?>