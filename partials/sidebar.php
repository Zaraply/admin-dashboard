<?php include($_SERVER['DOCUMENT_ROOT'] . '/admin-dashboard/config/path.php'); ?>

<!-- Fixed Sidebar Layout -->
<div>
    <!-- Sidebar -->
    <nav class="bg-dark text-white p-3" style="width: 220px; height: 100vh; position: fixed; top: 0; left: 0;">
        <h4 class="text-warning mb-4">TsurayaCell</h4>
        <ul class="nav flex-column nav-pills">
            <li class="nav-item">
                <a class="nav-link text-white hover-link" href="<?= $base_url ?>/index.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white hover-link" href="<?= $base_url ?>/crud/stok_handphone.php">Stok Handphone</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white hover-link" href="<?= $base_url ?>/transaksi/masuk.php">Transaksi Masuk</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white hover-link" href="<?= $base_url ?>/transaksi/keluar.php">Transaksi Keluar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white hover-link" href="<?= $base_url ?>/transaksi/riwayat_transaksi.php">Riwayat Transaksi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white hover-link" href="<?= $base_url ?>/prediksi/prediksi_stok.php">Pengadaan Stok</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white hover-link" href="#">Akun</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white hover-link" href="#">Pengaturan</a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="p-4" style="margin-left: 220px;">
