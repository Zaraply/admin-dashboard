<?php
// Koneksi ke database
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Hapus data berdasarkan ID
    $query = "DELETE FROM prediksi_history WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        // Redirect dengan parameter sukses
        header("Location: prediksi_stok.php?hapus=berhasil");
        exit;
    } else {
        echo "Gagal menghapus data.";
    }

    $stmt->close();
    $koneksi->close();
} else {
    echo "Permintaan tidak valid.";
}
?>
