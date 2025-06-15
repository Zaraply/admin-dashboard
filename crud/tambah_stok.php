<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<?php
include '../config.php';?>
<?php include('../partials/header.php'); ?>
<?php include('../partials/sidebar.php'); ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $brand = $_POST['brand'];
    $spesifikasi = $_POST['spesifikasi'];
    $grade = $_POST['grade'];
    $warna = $_POST['warna'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $tanggal = $_POST['tanggal_penjualan'];

    $status = ($jumlah >= 100) ? 'Tersedia' : (($jumlah >= 1) ? 'Sedikit' : 'Habis');

    $query = "INSERT INTO handphone (nama, brand, spesifikasi, grade, warna, jumlah, harga, status)
              VALUES ('$nama', '$brand', '$spesifikasi', '$grade', '$warna', $jumlah, $harga, '$status')";

    mysqli_query($koneksi, $query);
    header("Location: ./stok_handphone.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gambar = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $target_dir = '../uploads/';
    $gambar_path = $target_dir . basename($gambar);

    if (move_uploaded_file($tmp_name, $gambar_path)) {
        $query = "INSERT INTO handphone (nama, brand, grade, spesifikasi, warna, jumlah, harga, status, gambar)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssssidss", $nama, $brand, $grade, $spesifikasi, $warna, $jumlah, $harga, $status, $gambar);
        $stmt->execute();
    } else {
        echo "<div class='alert alert-danger'>Gagal upload gambar.</div>";
    }
}

?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Data Handphone</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Handphone</button>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Handphone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
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
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include('../partials/footer.php'); ?>
