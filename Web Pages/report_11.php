<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 11: Treatments Between Dates</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 11: Treatments for a Complaint Between Dates</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    
    <div class="form-container">
        <form action="" method="post">
            <div class="form-group">
                <label for="complaintCode">Select Complaint:</label>
                <select id="complaintCode" name="complaintCode" required>
                    <option value="">-- Select Complaint --</option>
                    <?php
                    $sql_comp = "SELECT DISTINCT complaintCode, complaintType FROM COMPLAINT ORDER BY complaintType";
                    $stmt_comp = sqlsrv_query($conn, $sql_comp);
                    while ($row = sqlsrv_fetch_array($stmt_comp, SQLSRV_FETCH_ASSOC)) {
                        $selected = (isset($_POST['complaintCode']) && $_POST['complaintCode'] == $row['complaintCode']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['complaintCode']) . "' $selected>" . htmlspecialchars($row['complaintType']) . " (Code: {$row['complaintCode']})</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="startDate">From Date:</label>
                <input type="date" id="startDate" name="startDate" required value="<?php echo isset($_POST['startDate']) ? $_POST['startDate'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="endDate">To Date:</label>
                <input type="date" id="endDate" name="endDate" required value="<?php echo isset($_POST['endDate']) ? $_POST['endDate'] : ''; ?>">
            </div>
            <input type="submit" value="Search Treatments">
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['complaintCode'])) {
        $complaintCode = $_POST['complaintCode'];
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        
        $sql = "SELECT T.treatmentCode, T.startDate, T.endDate, C.complaintType 
                FROM TREATMENT T 
                JOIN COMPLAINT C ON T.complaintCode = C.complaintCode 
                WHERE C.complaintCode = ? AND T.startDate >= ? AND T.startDate <= ?
                ORDER BY T.treatmentCode";
        $stmt = sqlsrv_query($conn, $sql, array($complaintCode, $startDate, $endDate));
        
        if ($stmt === false) die(print_r(sqlsrv_errors(), true));
        
        echo "<div class='table-wrapper'><table>
                <thead>
                    <tr>
                        <th>Complaint Type</th>
                        <th>Treatment Code</th>
                        <th>Treatment Start Date</th>
                        <th>Treatment End Date</th>
                    </tr>
                </thead>
                <tbody>";
        $hasData = false;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $hasData = true;
            $sDate = $row['startDate'] ? $row['startDate']->format('Y-m-d') : 'N/A';
            $eDate = $row['endDate'] ? $row['endDate']->format('Y-m-d') : 'Ongoing';
            echo "<tr>
                    <td>{$row['complaintType']}</td>
                    <td>{$row['treatmentCode']}</td>
                    <td>{$sDate}</td>
                    <td>{$eDate}</td>
                  </tr>";
        }
        if (!$hasData) {
            echo "<tr><td colspan='4' style='text-align:center;'>No treatments found in this date range.</td></tr>";
        }
        echo "</tbody></table></div>";
    }
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
