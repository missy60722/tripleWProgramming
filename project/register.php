<?php
$conn = require_once ("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $check = "SELECT * FROM Users WHERE username='" . $username . "'";
    if (mysqli_num_rows(mysqli_query($conn, $check)) == 0) {
        $sql = "INSERT INTO Users (id,username, password)
            VALUES(NULL,'" . $username . "','" . $password_hash . "')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('註冊成功！'); 
            window.location='index.php';</script>";
            exit;
        } else {
            echo "Error creating table: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('你已經註冊過啦！'); 
        window.location='register.html';</script>";
        exit;
    }
}

mysqli_close($conn);
?>