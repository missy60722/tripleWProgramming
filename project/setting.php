<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>設置</title>
    <script>
        window.onload = function () {
            let username = document.forms["setForm"]["username"];
            username.focus();
        }

        function validateForm() {
            let username = document.forms["setForm"]["username"];
            let password = document.forms["setForm"]["password"];
            let confirmPassword = document.forms["setForm"]["confirmPassword"];

            if (username.value == "") {
                alert("請輸入帳號");
                username.focus();
                return false;
            }
            if (password.value == "") {
                alert("請輸入密碼");
                password.focus();
                return false;
            }
            if (password.value.length < 6) {
                alert("密碼長度不足（長度需 >= 6）");
                x.focus();
                return false;
            }
            if (confirmPassword.value == "") {
                alert("請再次輸入密碼");
                y.focus();
                return false;
            }
            if (password.value !== confirmPassword.value) {
                alert("請確認密碼輸入是否正確");
                confirmPassword.focus();
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <aside class="sidebar">
        <a href="home.html">記帳</a>
        <a href="account.php">我的帳本</a>
        <a href="report.html">統計數據</a>
        <a href="setting.php">設置</a>
        <a href="logout.php">登出</a>
    </aside>

    <div class="container">
        <h1>二哈記帳</h1>
        <h2>設置</h2>
        <form name="setForm" method="post" action="" onsubmit="return validateForm()">
            <input type="text" id="username" name="username" placeholder="帳號" value="<?php echo $current_username; ?>"
                required>
            <input type="password" id="password" name="password" placeholder="新密碼" required>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="確認新密碼" required>
            <input type="submit" value="更新" name="submit" style="width: 100%">
        </form>
    </div>

    <?php
    session_start();
    $conn = require_once "config.php";
    $user_id = $_SESSION["id"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_username = $_POST["username"];
        $new_password = $_POST["password"];
        $confirm_password = $_POST["confirmPassword"];

        $sql_check_username = "SELECT COUNT(*) as count FROM Users WHERE username = '$new_username' AND id != '$user_id'";
        $result_check_username = mysqli_query($conn, $sql_check_username);
        $row_check_username = mysqli_fetch_assoc($result_check_username);
        if ($row_check_username["count"] > 0) {
            function_alert("該帳號已被使用，請選擇另一個帳號");
            exit;
        }

        $sql_get_password = "SELECT password FROM Users WHERE id = '$user_id'";
        $result_get_password = mysqli_query($conn, $sql_get_password);
        $row_get_password = mysqli_fetch_assoc($result_get_password);
        $old_password_hash = $row_get_password["password"];
        if (password_verify($new_password, $old_password_hash)) {
            function_alert("新密碼不能與舊密碼相同，請選擇另一個密碼");
            exit;
        }

        if ($new_password !== $confirm_password) {
            function_alert("兩次密碼輸入不相同");
            exit;
        }

        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE Users SET username = '$new_username', password = '$new_password_hash' WHERE id = '$user_id'";
        if (mysqli_query($conn, $sql)) {
            $_SESSION["username"] = $new_username;
            function_alert("設置已更新");
        } else {
            function_alert("更新時出錯：" . mysqli_error($conn) . "");
        }
    }


    $sql = "SELECT username FROM Users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $current_username = mysqli_fetch_assoc($result)["username"];

    mysqli_close($conn);
    function function_alert($message)
    {
        echo "<script>alert('$message');
        </script>";
        return false;
    }
    ?>
</body>

</html>