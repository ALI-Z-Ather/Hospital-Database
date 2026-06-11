<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 10: Full Medical Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 10: Full Medical Details for a Particular Patient</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    
    <div class="form-container">
        <form action="" method="post">
            <div class="form-group">
                <label for="patientNo">Select Patient:</label>
                <select id="patientNo" name="patientNo" required>
                    <option value="">-- Select Patient --</option>
                    <?php
                    $sql_pts = "SELECT patientNo, patientName FROM PATIENT ORDER BY patientName";
                    $stmt_pts = sqlsrv_query($conn, $sql_pts);
                    while ($ptRow = sqlsrv_fetch_array($stmt_pts, SQLSRV_FETCH_ASSOC)) {
                        $selected = (isset($_POST['patientNo']) && $_POST['patientNo'] == $ptRow['patientNo']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($ptRow['patientNo']) . "' $selected>" . htmlspecialchars($ptRow['patientName']) . " (No: {$ptRow['patientNo']})</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="submit" value="View Details">
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['patientNo'])) {
        $patientNo = $_POST['patientNo'];
        
        $sql = "SELECT P.patientNo, P.patientName, P.DOB, A.dateAdmitted, C.complaintType, T.treatmentCode, T.startDate, T.endDate, D.doctorName 
                FROM PATIENT P 
                LEFT JOIN ADMITTED A ON P.patientNo = A.patientNo 
                LEFT JOIN COMPLAINT C ON P.patientNo = C.patientNo 
                LEFT JOIN TREATMENT T ON C.complaintCode = T.complaintCode
                LEFT JOIN TREATMENTDONEBY TDB ON T.treatmentCode = TDB.treatmentCode
                LEFT JOIN DOCTOR D ON TDB.doctorID = D.doctorNo
                WHERE P.patientNo = ?
                ORDER BY C.complaintType, T.startDate";
        $stmt = sqlsrv_query($conn, $sql, array($patientNo));
        
        if ($stmt === false) die(print_r(sqlsrv_errors(), true));
        
        echo "<div class='table-wrapper'><table>
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>DOB</th>
                        <th>Admission Date</th>
                        <th>Complaint Type</th>
                        <th>Treatment Code</th>
                        <th>Treatment Start</th>
                        <th>Doctor</th>
                    </tr>
                </thead>
                <tbody>";
        $hasData = false;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $hasData = true;
            $dob = $row['DOB'] ? $row['DOB']->format('Y-m-d') : 'N/A';
            $adm = $row['dateAdmitted'] ? $row['dateAdmitted']->format('Y-m-d') : 'N/A';
            $start = $row['startDate'] ? $row['startDate']->format('Y-m-d') : 'N/A';
            
            echo "<tr>
                    <td>{$row['patientName']}</td>
                    <td>{$dob}</td>
                    <td>{$adm}</td>
                    <td>{$row['complaintType']}</td>
                    <td>{$row['treatmentCode']}</td>
                    <td>{$start}</td>
                    <td>{$row['doctorName']}</td>
                  </tr>";
        }
        if (!$hasData) {
            echo "<tr><td colspan='7' style='text-align:center;'>No details available.</td></tr>";
        }
        echo "</tbody></table></div>";
    }
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
