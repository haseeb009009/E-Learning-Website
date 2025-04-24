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
            margin: 20px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
<h2>JUST FOR TESTING </h2>
<h2>Database Tables & Data</h2>

<?php foreach ($tables as $table): ?>
    <h3>Table: <?php echo $table; ?></h3>
    <table>
        <tr>
            <?php
            // Get table columns
            $columns = $conn->query("SHOW COLUMNS FROM $table");
            while ($col = $columns->fetch_assoc()) {
                echo "<th>{$col['Field']}</th>";
            }
            ?>
        </tr>
        <?php
        // Get table data
        $data = $conn->query("SELECT * FROM $table");
        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
<?php endforeach; ?>

<?php $conn->close(); ?>

</body>
</html>
