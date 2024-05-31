<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/phpmailer/phpmailer/src/Exception.php";
require "../vendor/phpmailer/phpmailer/src/PHPMailer.php";
require "../vendor/phpmailer/phpmailer/src/SMTP.php";
require "../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$conn = require_once "config.php";

$response = array("status" => "", "message" => "");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    $sql = "SELECT id FROM Users WHERE email='$email'";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        function generateRandomPassword($length = 8)
        {
            $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $charactersLength = strlen($characters);
            $randomPassword = "";
            for ($i = 0; $i < $length; $i++) {
                $randomPassword .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomPassword;
        }

        $newPassword = generateRandomPassword();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateSql = "UPDATE Users SET password='$hashedPassword' WHERE email='$email'";
        if (mysqli_query($link, $updateSql) === TRUE) {
            $mail = new PHPMailer(true);
            try {

                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;
                $mail->Username = "1missy60722@gmail.com";
                $mail->Password = $_ENV["GMAIL_APP_PASSWORD"];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = "UTF-8";

                $mail->setFrom("1missy60722@gmail.com", "二哈記帳");
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "重設密碼通知";
                $mail->Body = "親愛的使用者,<br><br>您的新密碼為: $newPassword<br>請使用此密碼登入並立即修改您的密碼。<br><br>謝謝!";
                $mail->AltBody = "親愛的使用者,\n\n您的新密碼為: $newPassword\n請使用此密碼登入並立即修改您的密碼。\n\n謝謝!";

                $mail->send();
                $response["status"] = "success";
                $response["message"] = "已將密碼郵件發送至您的信箱，登入後請立即修改密碼。";
            } catch (Exception $e) {
                $response["status"] = "error";
                $response["message"] = "發送郵件失敗。郵件伺服器錯誤: {$mail->ErrorInfo}";
            }
        } else {
            $response["status"] = "error";
            $response["message"] = "更新密碼時出錯: " . mysqli_error($link);
        }
    } else {
        $response["status"] = "error";
        $response["message"] = "沒找到該電子郵件地址對應的使用者，請重新輸入。";
    }

    mysqli_close($link);

    echo json_encode($response);
}
?>