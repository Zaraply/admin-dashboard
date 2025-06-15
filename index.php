<?php include('config.php'); ?>
<?php include('partials/header.php'); ?>
<?php include('partials/sidebar.php'); ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tsuraya Cell</title>
    <!-- Link ke Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <div class="container mt-1">
        <h1 class="text-black">Dashboard</h1>
    </div>
</body>
</html>

<?php
$total_penjualan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM penjualan"))['total'];
$total_user = 2; // Jika belum ada tabel user, bisa diset statis dulu

// Ambil data penjualan bulan lalu untuk menghitung persentase
$bulan_lalu = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT SUM(jumlah) as total FROM penjualan 
    WHERE MONTH(tanggal) = MONTH(CURDATE()) - 1
"))['total'];
$persentase = ($total_penjualan && $bulan_lalu) 
              ? round((($total_penjualan - $bulan_lalu) / $bulan_lalu) * 100, 1)
              : 0;
?>

<div class="row mb-2">
    <div class="col-md-3">
        <div class="card p-3 bg-light">
            <h6>Total Penjualan</h6>
            <h4><?= $total_penjualan ?> pcs</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 bg-light">
            <h6>Total User</h6>
            <h4><?= $total_user ?> Orang</h4>
            <small></small>
        </div>
    </div>
</div>


<div class="container mt-2">
    <h2 class="mb-2">Daftar Stok Handphone</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nama</th><th>Brand</th><th>Spesifikasi</th><th>Grade</th>
                <th>Warna</th><th>Jumlah</th><th>Harga</th><th>Status</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mengambil data handphone dari database
            $sql = "SELECT * FROM handphone";
            $result = mysqli_query($koneksi, $sql);
            // Jumlah data per halaman
            $limit = 5;
            // Halaman saat ini (default = 1)
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            // Ambil data handphone dengan LIMIT dan OFFSET
            $sql = "SELECT * FROM handphone LIMIT $limit OFFSET $offset";
            $result = mysqli_query($koneksi, $sql);
            // Hitung total data untuk pagination
            $total_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM handphone"))['total'];
            $total_page = ceil($total_data / $limit);
            // Menampilkan data handphone
            while ($row = mysqli_fetch_assoc($result)) {
                // Menentukan warna badge berdasarkan status
                $badge_class = ($row['status'] == 'Tersedia') ? 'success' :
                               (($row['status'] == 'Sedikit') ? 'warning' : 'danger');
                ?>
                <tr>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['brand'] ?></td>
                    <td><?= $row['spesifikasi'] ?></td>
                    <td><?= $row['grade'] ?></td>
                    <td><?= $row['warna'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><span class="badge bg-<?= $badge_class ?>"><?= $row['status'] ?></span></td>
                    <td>
                        <!-- Tautan untuk edit dan hapus data -->
                        <a href="./edit_stok.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="./hapus_stok.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <nav>
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
    </nav>
</div>


<?php include('partials/footer.php'); ?>
