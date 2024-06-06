<?php
session_start();
$conn = require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $amount = $_POST["amount"];
    $date = $_POST["date"];

    if ($_POST["categorySelect"] === "其他") {
        $category = $_POST["categoryInput"];
    } else if ($_POST["categorySelect"] === "") {
        $category = $_POST["hiddenCategoryInput"];
    } else {
        $category = $_POST["categorySelect"];
    }

    $type = $_POST["type"];
    $note = isset($_POST["note"]) ? $_POST["note"] : null;

    $user_id = $_SESSION["id"];

    $sql = "INSERT INTO Transactions (user_id, name, amount, category, type, note, created_at, updated_at)
                VALUES ('" . $user_id . "', '" . $name . "', '" . $amount . "', '" . $category . "', '" . $type . "', '" . $note . "', NOW(), NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "資料已成功儲存";
        header("location:home.html");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

$sql = "SELECT category, SUM(amount) AS total_amount FROM Transactions GROUP BY category";
$result = mysqli_query($conn, $sql);

$transactions_data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $transactions_data[] = $row;
}

$transactions_json = json_encode($transactions_data);

mysqli_close($conn);
