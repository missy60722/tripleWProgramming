<?php
$conn = require_once "config.php";

session_start();
$user_id = $_SESSION["id"];

$order_by = "name";
$table_header = "<th>項目</th><th>金額</th><th>日期</th><th>分類</th><th>類型</th><th>備註</th>";

if (isset($_GET["type"])) {
    $type = $_GET["type"];
    switch ($type) {
        case "type":
            $order_by = "type";
            $table_header = "<th>類型</th><th>項目</th><th>金額</th><th>日期</th><th>分類</th><th>備註</th>";
            break;
        case "date":
            $order_by = "created_at";
            $table_header = "<th>日期</th><th>項目</th><th>金額</th><th>分類</th><th>類型</th><th>備註</th>";
            break;
        case "category":
            $order_by = "category";
            $table_header = "<th>分類</th><th>項目</th><th>金額</th><th>日期</th><th>類型</th><th>備註</th>";
            break;
        case "name":
            $order_by = "name";
            $table_header = "<th>項目</th><th>金額</th><th>日期</th><th>分類</th><th>類型</th><th>備註</th>";
            break;
        default:
            break;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update"])) {
        $id = $_POST["id"];
        $name = $_POST["name"];
        $amount = $_POST["amount"];
        $date = $_POST["date"];
        $category = $_POST["category"];
        $type = $_POST["type"];
        $note = $_POST["note"];
        $sql = "UPDATE Transactions SET name='$name', amount='$amount', created_at='$date', category='$category', type='$type', note='$note' WHERE id='$id' AND user_id='$user_id'";
        mysqli_query($conn, $sql);
    } elseif (isset($_POST["delete"])) {
        $id = $_POST["id"];
        $sql = "DELETE FROM Transactions WHERE id='$id' AND user_id='$user_id'";
        mysqli_query($conn, $sql);
    }
}

$sql = "SELECT * FROM Transactions WHERE user_id = '$user_id' ORDER BY $order_by";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/zh.js"></script>
    <title>我的帳本</title>
    <style>
        .container {
            width: 80%;
            max-width: 800px;
        }

        #transactionForm {
            display: none;
        }
    </style>
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
        <div id="options" class="options">
            <button onclick="showByName()">依項目呈現</button>
            <button onclick="showByType()">依類型呈現</button>
            <button onclick="showByDate()">依日期呈現</button>
            <button onclick="showByCategory()">依分類呈現</button>
        </div>
        <h2>交易明細</h2>
        <form id="transactionForm" action="account.php" method="POST">
            <input type="hidden" name="id" id="transactionId">
            <label for="name">項目:</label>
            <input type="text" name="name" id="name" required>
            <label for="amount">金額:</label>
            <input type="number" name="amount" id="amount" min="0" required>
            <label for="date">日期:</label>
            <input type="date" name="date" id="date" required>
            <label for="category">分類:</label>
            <input type="text" name="category" id="category" required>
            <label for="type">類型:</label>
            <input type="text" name="type" id="type" required>
            <label for="note">備註:</label>
            <input type="text" name="note" id="note">
            <button type="submit" name="update">更新</button>
        </form>
        <table border="1">
            <tr>
                <?php echo $table_header; ?>
                <th>操作</th>
            </tr>
            <script>
                function showByName() {
                    window.location.href = "account.php?type=name";
                }
                function showByType() {
                    window.location.href = "account.php?type=type";
                }
                function showByDate() {
                    window.location.href = "account.php?type=date";
                }
                function showByCategory() {
                    window.location.href = "account.php?type=category";
                }
                function editTransaction(id, name, amount, date, category, type, note) {
                    document.getElementById("transactionForm").style.display = "block";
                    document.getElementById("transactionId").value = id;
                    document.getElementById("name").value = name;
                    document.getElementById("amount").value = amount;
                    document.getElementById("date").value = date;
                    document.getElementById("category").value = category;
                    document.getElementById("type").value = type;
                    document.getElementById("note").value = note;
                    document.getElementById("name").focus();
                }

                function confirmDelete() {
                    return confirm("確定要刪除這筆交易嗎？");
                }

                document.addEventListener("DOMContentLoaded", function () {
                    flatpickr("#date", {
                        dateFormat: "Y-m-d",
                        defaultDate: new Date(),
                        locale: "zh"
                    });
                });
            </script>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                if ($type === "type") {
                    echo "<td>" . $row["type"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["amount"] . "</td>";
                    echo "<td>" . $row["created_at"] . "</td>";
                    echo "<td>" . $row["category"] . "</td>";
                    echo "<td>" . $row["note"] . "</td>";
                } elseif ($type === "date") {
                    echo "<td>" . $row["created_at"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["amount"] . "</td>";
                    echo "<td>" . $row["category"] . "</td>";
                    echo "<td>" . $row["type"] . "</td>";
                    echo "<td>" . $row["note"] . "</td>";
                } elseif ($type === "category") {
                    echo "<td>" . $row["category"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["amount"] . "</td>";
                    echo "<td>" . $row["created_at"] . "</td>";
                    echo "<td>" . $row["type"] . "</td>";
                    echo "<td>" . $row["note"] . "</td>";
                } else {
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["amount"] . "</td>";
                    echo "<td>" . $row["created_at"] . "</td>";
                    echo "<td>" . $row["category"] . "</td>";
                    echo "<td>" . $row["type"] . "</td>";
                    echo "<td>" . $row["note"] . "</td>";
                }
                echo "<td>
                        <button onclick=\"editTransaction('" . $row["id"] . "', '" . $row["name"] . "', '" . $row["amount"] . "', '" . $row["created_at"] . "', '" . $row["category"] . "', '" . $row["type"] . "', '" . $row["note"] . "')\">編輯</button>
                        <form action='account.php' method='POST' style='display:inline;' onsubmit='return confirmDelete();'>
                            <input type='hidden' name='id' value='" . $row["id"] . "'>
                            <button type='submit' name='delete'>刪除</button>
                        </form>
                      </td>";
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