<?php
$conn = require_once "config.php";

$username = $_POST["username"];
$password = $_POST["password"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM Users WHERE username ='" . $username . "'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) == 1 && password_verify($password, $row["password"])) {
        session_start();

        $_SESSION["loggedin"] = true;

        $_SESSION["id"] = $row["id"];
        $_SESSION["username"] = $row["username"];
        header("location:home.html");
    } else {
        function_alert("帳號或密碼錯誤！");
    }
} else {
    function_alert("Something wrong");
}

mysqli_close($conn);

function function_alert($message)
{
    echo "<script>alert('$message');
     window.location.href='index.php';</script>";
    return false;
}
?>