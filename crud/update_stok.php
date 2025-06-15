<?php
include '../config.php';

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $brand = $_POST['brand'];
    $spesifikasi = $_POST['spesifikasi'];
    $grade = $_POST['grade'];
    $warna = $_POST['warna'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];

    // Status otomatis
    if ($jumlah >= 100) {
        $status = 'Tersedia';
    } elseif ($jumlah >= 1) {
        $status = 'Sedikit';
    } else {
        $status = 'Habis';
    }

    // Cek gambar
    $query = "SELECT gambar FROM handphone WHERE id = $id";
    $res = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($res);
    $gambar_final = $row['gambar'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $gambarBaru = basename($_FILES['gambar']['name']);
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $target_dir = '../uploads/' . $gambarBaru;
        move_uploaded_file($tmp_name, $target_dir);
        $gambar_final = $gambarBaru;
    }

    // Update ke database
    $stmt = $koneksi->prepare("UPDATE handphone SET nama=?, brand=?, spesifikasi=?, grade=?, warna=?, jumlah=?, harga=?, status=?, gambar=? WHERE id=?");
    $stmt->bind_param("sssssidssi", $nama, $brand, $spesifikasi, $grade, $warna, $jumlah, $harga, $status, $gambar_final, $id);
    if ($stmt->execute()) {
        header('Location: stok_handphone.php');
        exit;
    } else {
        echo "Gagal update: " . $stmt->error;
    }
}
?>
