<?php
echo 'Hapus Stok';
include '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM handphone WHERE id = $id";

    if (mysqli_query($koneksi, $query)) {
        header('Location: stok_handphone.php');
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
