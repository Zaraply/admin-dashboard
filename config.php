<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "toko_handphone";

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
define("BASE_URL", "/admin-dashboard");

?>
