<?php
include '../config.php';

$id = $_GET['id'];
$query = "SELECT * FROM handphone WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
?>

<form method="POST" action="update_stok.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $data['id'] ?>">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" value="<?= $data['nama'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Brand</label>
        <input type="text" name="brand" class="form-control" value="<?= $data['brand'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Spesifikasi</label>
        <input type="text" name="spesifikasi" class="form-control" value="<?= $data['spesifikasi'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Grade</label>
        <input type="text" name="grade" class="form-control" maxlength="1" value="<?= $data['grade'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Warna</label>
        <input type="text" name="warna" class="form-control" value="<?= $data['warna'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Jumlah</label>
        <input type="number" name="jumlah" class="form-control" value="<?= $data['jumlah'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Ganti Gambar</label>
        <input type="file" name="gambar" class="form-control">
        <?php if (!empty($data['gambar'])): ?>
            <p class="mt-2">Gambar saat ini:<br><img src="../uploads/<?= $data['gambar'] ?>" width="100"></p>
        <?php endif; ?>
    </div>
    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
</form>
