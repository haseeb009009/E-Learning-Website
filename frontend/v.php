<?php
// Database connection
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";  // Change if needed
$dbname = "lms"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = $_POST['table'];
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM $table WHERE id = $id");
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $columns = $_POST['columns'];
        $values = $_POST['values'];
        $updateQuery = "UPDATE $table SET ";
        foreach ($columns as $index => $column) {
            $updateQuery .= "$column = '{$values[$index]}', ";
        }
        $updateQuery = rtrim($updateQuery, ', ') . " WHERE id = $id";
        $conn->query($updateQuery);
    } elseif (isset($_POST['create'])) {
        $columns = $_POST['columns'];
        $values = $_POST['values'];
        $columnsList = implode(',', $columns);
        $valuesList = "'" . implode("','", $values) . "'";
        $conn->query("INSERT INTO $table ($columnsList) VALUES ($valuesList)");
    }
}

// Get all table names in the database
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Tables View</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 1px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 1%;
            margin: 1px ;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 1px 1px 1px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 1px;
            border: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        form {
            display: inline-block;
        }
    </style>
</head>
<body>
<h2>Database Tables & CRUD Operations</h2>

<?php foreach ($tables as $table): ?>
    <h3>Table: <?php echo $table; ?></h3>
    <table>
        <tr>
            <?php
            // Get table columns
            $columns = $conn->query("SHOW COLUMNS FROM $table");
            $columnNames = [];
            while ($col = $columns->fetch_assoc()) {
                $columnNames[] = $col['Field'];
                echo "<th>{$col['Field']}</th>";
            }
            ?>
            <th>Actions</th>
        </tr>
        <?php
        // Get table data
        $data = $conn->query("SELECT * FROM $table");
        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "<td>
                <form method='POST'>
                    <input type='hidden' name='table' value='$table'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <button type='submit' name='delete'>Delete</button>
                </form>
                <form method='POST'>
                    <input type='hidden' name='table' value='$table'>
                    <input type='hidden' name='id' value='{$row['id']}'>";
            foreach ($columnNames as $colName) {
                echo "<input type='hidden' name='columns[]' value='$colName'>";
                echo "<input type='text' name='values[]' value='{$row[$colName]}'>";
            }
            echo "<button type='submit' name='update'>Update</button>
                </form>
            </td>";
            echo "</tr>";
        }
        ?>
        <tr>
            <form method="POST">
                <input type="hidden" name="table" value="<?php echo $table; ?>">
                <?php foreach ($columnNames as $colName): ?>
                    <td>
                        <input type="hidden" name="columns[]" value="<?php echo $colName; ?>">
                        <input type="text" name="values[]" placeholder="<?php echo $colName; ?>">
                    </td>
                <?php endforeach; ?>
                <td>
                    <button type="submit" name="create">Create</button>
                </td>
            </form>
        </tr>
    </table>
<?php endforeach; ?>

<?php $conn->close(); ?>

</body>
</html>
