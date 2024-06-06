<?php
$conn = require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM Users WHERE username ='" . $username . "' OR email ='" . $email . "'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        function_alert("帳號或電子郵件已經存在。");
    } else {
        $sql = "INSERT INTO Users (username, email, password) VALUES ('$username', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('註冊成功');
            window.location.href='index.php';</script>";
        } else {
            function_alert("註冊失敗，請稍後再試。");
        }
    }
}

mysqli_close($conn);

function function_alert($message)
{
    echo "<script>alert('$message');
     window.location.href='register.html';</script>";
    return false;
}
