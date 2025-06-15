<?php
include '../config.php';
include('../partials/header.php');
include('../partials/sidebar.php');

// Filter
$filter_brand = $_GET['brand'] ?? '';
$filter_nama = $_GET['nama'] ?? '';
$filter_grade = $_GET['grade'] ?? '';
$filter_harga = $_GET['harga'] ?? '';

// Ambil semua brand unik dari database
$query_brand = $koneksi->query("SELECT DISTINCT brand FROM handphone ORDER BY brand ASC");
$brand_options = [];
while ($row = $query_brand->fetch_assoc()) {
    $brand_options[] = $row['brand'];
}

$where = [];
if ($filter_brand !== '') $where[] = "brand LIKE '%$filter_brand%'";
if ($filter_nama !== '') $where[] = "nama LIKE '%$filter_nama%'";
if ($filter_grade !== '') $where[] = "grade = '$filter_grade'";
if ($filter_harga !== '') {
    if ($filter_harga == '1') $where[] = "harga < 2000000";
    elseif ($filter_harga == '2') $where[] = "harga BETWEEN 2000000 AND 5000000";
    elseif ($filter_harga == '3') $where[] = "harga > 5000000";
}

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Pagination
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM handphone $where_sql LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $sql);

// Total
$count_result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM handphone $where_sql");
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_page = ceil($total_data / $limit);

// Simpan Data (POST dari modal)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $brand = $_POST['brand'];
    $spesifikasi = $_POST['spesifikasi'];
    $grade = $_POST['grade'];
    $warna = $_POST['warna'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $tanggal_penjualan = $_POST['tanggal_penjualan'];

    // Hitung status
    $status = $jumlah >= 10 ? 'Tersedia' : ($jumlah >= 5 ? 'Sedikit' : 'Habis');

    $sql_insert = "INSERT INTO handphone (nama, brand, spesifikasi, grade, warna, jumlah, harga, tanggal_penjualan, status) 
                   VALUES ('$nama', '$brand', '$spesifikasi', '$grade', '$warna', '$jumlah', '$harga', '$tanggal_penjualan', '$status')";
    mysqli_query($koneksi, $sql_insert);
    header("Location: ./stok_handphone.php");
    exit;
}
?>



<!-- Konten -->
<div class="container mt-4">
    <h2 class="mb-3">Daftar Stok Handphone</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="get" class="row g-2 w-100">
            <div class="col-md-2"><input type="text" name="nama" value="<?= htmlspecialchars($filter_nama) ?>" class="form-control" placeholder="Nama"></div>
            <div class="col-md-2">
                <select name="brand" class="form-select">
                    <option value="">Semua Brand</option>
                    <?php foreach ($brand_options as $brand): ?>
                        <option value="<?= htmlspecialchars($brand) ?>" <?= $filter_brand == $brand ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <select name="grade" class="form-select">
                    <option value="">Semua Grade</option>
                    <option value="A" <?= $filter_grade == 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $filter_grade == 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= $filter_grade == 'C' ? 'selected' : '' ?>>C</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="harga" class="form-select">
                    <option value="">Semua Harga</option>
                    <option value="1" <?= $filter_harga == '1' ? 'selected' : '' ?>>Di bawah 2 juta</option>
                    <option value="2" <?= $filter_harga == '2' ? 'selected' : '' ?>>2 - 5 juta</option>
                    <option value="3" <?= $filter_harga == '3' ? 'selected' : '' ?>>Di atas 5 juta</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Filter</button></div>
        </form>
        <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Handphone</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Gambar</th><th>Nama</th><th>Brand</th><th>Spesifikasi</th><th>Grade</th>
                    <th>Warna</th><th>Jumlah</th><th>Harga</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) {
                $badge_class = $row['status'] == 'Tersedia' ? 'success' :
                               ($row['status'] == 'Sedikit' ? 'warning' : 'danger');
            ?>
                <tr>
                    <td>
                        <?php if (!empty($row['gambar'])): ?>
                            <img src="../uploads/<?= $row['gambar'] ?>" alt="Gambar" style="width: 80px;">
                        <?php else: ?>
                            <span class="text-muted">Tidak ada</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['brand'] ?></td>
                    <td><?= $row['spesifikasi'] ?></td>
                    <td><?= $row['grade'] ?></td>
                    <td><?= $row['warna'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><span class="badge bg-<?= $badge_class ?>"><?= $row['status'] ?></span></td>
                    <td>
                        <a href="./edit_stok_form.php" class="btn btn-warning btn-sm openEditModal" data-id="<?= $row['id']; ?>">Edit</a>
                        <a href="./hapus_stok.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&brand=<?= $filter_brand ?>&nama=<?= $filter_nama ?>&grade=<?= $filter_grade ?>&harga=<?= $filter_harga ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Modal Tambah Handphone -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Handphone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Nama</label>
                        <input name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Brand</label>
                        <input name="brand" class="form-control" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Spesifikasi</label>
                        <input name="spesifikasi" class="form-control" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Grade</label>
                        <input name="grade" class="form-control" maxlength="1" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Warna</label>
                        <input name="warna" class="form-control" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Jumlah</label>
                        <input name="jumlah" type="number" class="form-control" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Harga</label>
                        <input name="harga" type="number" class="form-control" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Tanggal Penjualan</label>
                        <input type="date" name="tanggal_penjualan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Upload Gambar</label>
                        <input type="file" name="gambar" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Simpan</button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Stok Handphone</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalEditContent">
        <!-- Form akan dimuat via AJAX di sini -->
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.openEditModal', function(e) {
    e.preventDefault();
    var id = $(this).data('id');

    $('#modalEditContent').html('Loading...');
    $('#editModal').modal('show');

    $.get('edit_stok_form.php', { id: id }, function(data) {
        $('#modalEditContent').html(data);
    });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
