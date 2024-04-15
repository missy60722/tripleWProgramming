<?php
$conn = require_once "config.php";

session_start();
$user_id = $_SESSION["id"];

$order_by = "name";
$table_header = "<th>項目</th><th>金額</th><th>日期</th><th>分類</th><th>類型</th><th>備註</th>";

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    switch ($type) {
        case 'type':
            $order_by = "type";
            $table_header = "<th>類型</th><th>項目</th><th>金額</th><th>日期</th><th>分類</th><th>備註</th>";
            break;
        case 'date':
            $order_by = "created_at";
            $table_header = "<th>日期</th><th>項目</th><th>金額</th><th>分類</th><th>類型</th><th>備註</th>";
            break;
        case 'category':
            $order_by = "category";
            $table_header = "<th>分類</th><th>項目</th><th>金額</th><th>日期</th><th>類型</th><th>備註</th>";
            break;
        case 'name':
            $order_by = "name";
            $table_header = "<th>項目</th><th>金額</th><th>日期</th><th>分類</th><th>類型</th><th>備註</th>";
            break;
        default:
            break;
    }
}

$sql = "SELECT * FROM Transactions WHERE user_id = '$user_id' ORDER BY $order_by";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="../css/style.css">
    <title>我的帳本</title>
    <style>
        .container {
            width: 80%;
            max-width: 800px;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <a href="home.html">記帳</a>
        <a href="account.php">我的帳本</a>
        <a href="report.php">統計數據</a>
        <a href="setting.php">設置</a>
        <a href="logout.php">登出</a>
    </aside>
    <div class="container">
        <div id="options" class="options">
            <button onclick="showByName()">依項目呈現</button>
            <button onclick="showByType()">依類型呈現</button>
            <button onclick="showByDate()">依日期呈現</button>
            <button onclick="showByCategory()">依分類呈現</button>
        </div>
        <h2>交易明細</h2>
        <table border="1">
            <tr>
                <?php echo $table_header; ?>
            </tr>
            <script>
                function showByName() {
                    window.location.href = 'account.php?type=name';
                }
                function showByType() {
                    window.location.href = 'account.php?type=type';
                }

                function showByDate() {
                    window.location.href = 'account.php?type=date';
                }

                function showByCategory() {
                    window.location.href = 'account.php?type=category';
                }
            </script>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                if ($type === 'type') {
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['amount'] . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>" . $row['category'] . "</td>";
                    echo "<td>" . $row['note'] . "</td>";
                } elseif ($type === 'date') {
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['amount'] . "</td>";
                    echo "<td>" . $row['category'] . "</td>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['note'] . "</td>";
                } elseif ($type === 'category') {
                    echo "<td>" . $row['category'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['amount'] . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['note'] . "</td>";
                } else {
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['amount'] . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>" . $row['category'] . "</td>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['note'] . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>