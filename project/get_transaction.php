<?php
session_start();
$conn = require_once "config.php";

$user_id = $_SESSION["id"];

$sql = "SELECT * FROM Transactions WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

$transactions = array();
while ($row = mysqli_fetch_assoc($result)) {
    $transactions[] = $row;
}

header('Content-Type: application/json');
echo json_encode($transactions);
?>
