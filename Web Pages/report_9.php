<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 9: Performance History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 9: Performance History for a Particular Doctor</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    
    <div class="form-container">
        <form action="" method="post">
            <div class="form-group">
                <label for="doctorNo">Select Doctor:</label>
                <select id="doctorNo" name="doctorNo" required>
                    <option value="">-- Select Doctor --</option>
                    <?php
                    $sql_docs = "SELECT doctorNo, doctorName FROM DOCTOR ORDER BY doctorName";
                    $stmt_docs = sqlsrv_query($conn, $sql_docs);
                    while ($docRow = sqlsrv_fetch_array($stmt_docs, SQLSRV_FETCH_ASSOC)) {
                        $selected = (isset($_POST['doctorNo']) && $_POST['doctorNo'] == $docRow['doctorNo']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($docRow['doctorNo']) . "' $selected>" . htmlspecialchars($docRow['doctorName']) . " (No: {$docRow['doctorNo']})</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="submit" value="View History">
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['doctorNo'])) {
        $doctorNo = $_POST['doctorNo'];
        
        $sql = "SELECT D.doctorName, DP.date, DP.grade 
                FROM DOCTOR_PROGRESS DP 
                JOIN DOCTOR D ON DP.doctorNo = D.doctorNo 
                WHERE DP.doctorNo = ?
                ORDER BY DP.date DESC";
        $stmt = sqlsrv_query($conn, $sql, array($doctorNo));
        
        if ($stmt === false) die(print_r(sqlsrv_errors(), true));
        
        echo "<div class='table-wrapper'><table>
                <thead>
                    <tr>
                        <th>Doctor Name</th>
                        <th>Review Date</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>";
        $hasData = false;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $hasData = true;
            $date = $row['date'] ? $row['date']->format('Y-m-d') : 'N/A';
            echo "<tr>
                    <td>{$row['doctorName']}</td>
                    <td>{$date}</td>
                    <td><strong>{$row['grade']}</strong></td>
                  </tr>";
        }
        if (!$hasData) {
            echo "<tr><td colspan='3' style='text-align:center;'>No performance history available.</td></tr>";
        }
        echo "</tbody></table></div>";
    }
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
