<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    $admin = mysqli_fetch_assoc($result);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin['username'];
        header("Location: index.php");
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!-- Form Login HTML -->
<form method="post" class="container mt-5" style="max-width: 400px;">
    <h2>Login Admin</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required />
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required />
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
