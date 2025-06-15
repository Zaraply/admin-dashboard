<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<?php
include '../config.php';
include('../partials/header.php');
include('../partials/sidebar.php');
?>

<?php
// Ambil nilai filter tanggal jika ada
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Query dengan filter tanggal jika ada
if ($tanggal_awal && $tanggal_akhir) {
    $query = "SELECT * FROM penjualan 
              WHERE tanggal BETWEEN ? AND ? 
              ORDER BY tanggal DESC";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ss", $tanggal_awal, $tanggal_akhir);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT * FROM penjualan ORDER BY tanggal DESC";
    $result = $koneksi->query($query);
}
?>

<div class="container mt-2">
    <h2>Riwayat Transaksi Penjualan</h2>

    <form method="GET" class="mb-3">
        <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
        <input type="date" name="tanggal_awal" class="form-control" required value="<?= htmlspecialchars($tanggal_awal) ?>">
        
        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
        <input type="date" name="tanggal_akhir" class="form-control" required value="<?= htmlspecialchars($tanggal_akhir) ?>">
        
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
        <a href="?" class="btn btn-secondary mt-2">Reset</a>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Handphone</th>
                <th>Brand</th>
                <th>Jumlah Terjual</th>
                <th>Total Harga</th>
                <th>Tanggal Penjualan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$no}</td>
                            <td>" . htmlspecialchars($row['nama']) . "</td>
                            <td>" . htmlspecialchars($row['brand']) . "</td>
                            <td>" . (int)$row['jumlah'] . "</td>
                            <td>Rp" . number_format($row['harga'], 0, ',', '.') . "</td>
                            <td>" . htmlspecialchars($row['tanggal']) . "</td>
                          </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>Tidak ada data penjualan</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<a href="../index.php" class="btn btn-secondary mt-3">← Kembali ke Dashboard</a>

<?php include('../partials/footer.php'); ?>
