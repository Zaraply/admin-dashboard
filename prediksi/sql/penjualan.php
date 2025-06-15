<?php
function getPenjualan($koneksi, $tipe, $brand, $bulan) {
    // Ambil tanggal mulai 12 bulan lalu dari sekarang
    $tanggalMulai = date('Y-m-d', strtotime("-$bulan months"));

    $query = "SELECT jumlah FROM penjualan 
              WHERE nama = ? AND brand = ? AND tanggal >= ? 
              ORDER BY tanggal DESC LIMIT ?";
    
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sssi", $tipe, $brand, $tanggalMulai, $bulan);
    $stmt->execute();
    $result = $stmt->get_result();

    $penjualan = [];
    while ($row = $result->fetch_assoc()) {
        $penjualan[] = (int) $row['jumlah'];
    }

    $stmt->close();

    return $penjualan;
}
