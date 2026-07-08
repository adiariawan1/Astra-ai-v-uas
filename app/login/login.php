<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Astra AI</title>
</head>
<body>

<h2>Login Astra AI</h2>

<?php
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
?>

<form action="process_login.php" method="POST">

    <label>Email</label><br>
    <input
        type="email"
        name="email"
        required
    ><br><br>

    <label>Password</label><br>
    <input
        type="password"
        name="password"
        required
    ><br><br>

    <button type="submit">
        Login
    </button>

</form>

</body>
</html>