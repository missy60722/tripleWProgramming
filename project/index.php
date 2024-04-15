<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="../css/style.css">
    <title>登入介面</title>
    <script>
        window.onload = function () {
            let username = document.forms["loginForm"]["username"];
            username.focus();
        }

        function validateForm() {
            let username = document.forms["loginForm"]["username"];
            let password = document.forms["loginForm"]["password"];
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
            return true;
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>二哈記帳</h1>
        <h2>登入</h2>
        <form name="loginForm" method="post" action="login.php" onsubmit="return validateForm()">
            <input type="text" name="username" placeholder="帳號" required>
            <input type="password" name="password" id="password" placeholder="密碼" required>
            <input type="submit" value="登入" name="submit" style="width: 100%">
            <a href="register.html">還沒有帳號？現在就註冊！</a>
        </form>
    </div>
    <?php
    session_start();

    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        header("location: home.php");
        exit;
    }
    ?>
</body>

</html>