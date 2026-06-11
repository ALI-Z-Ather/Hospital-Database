<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 6: Doctor Experience & Treatments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 6: Complaints, Treatments & Doctor Exp.</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    <?php
    $sql = "SELECT C.complaintType, T.treatmentCode, D.doctorName, DE.fromDate, DE.toDate, DE.position, DE.establishment 
            FROM COMPLAINT C 
            JOIN TREATMENT T ON C.complaintCode = T.complaintCode 
            JOIN TREATMENTDONEBY TDB ON T.treatmentCode = TDB.treatmentCode 
            JOIN DOCTOR D ON TDB.doctorID = D.doctorNo 
            LEFT JOIN DOCTOR_EXPERIENCE DE ON D.doctorNo = DE.doctorNo
            ORDER BY C.complaintType, D.doctorName, DE.fromDate";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    echo "<div class='table-wrapper'><table>
            <thead>
                <tr>
                    <th>Complaint Type</th>
                    <th>Treatment Code</th>
                    <th>Treated By</th>
                    <th>Exp. From Date</th>
                    <th>Exp. To Date</th>
                    <th>Exp. Position</th>
                    <th>Exp. Establishment</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $from = $row['fromDate'] ? $row['fromDate']->format('Y-m-d') : 'N/A';
        $to = $row['toDate'] ? $row['toDate']->format('Y-m-d') : 'N/A';
        echo "<tr>
                <td>{$row['complaintType']}</td>
                <td>{$row['treatmentCode']}</td>
                <td>{$row['doctorName']}</td>
                <td>{$from}</td>
                <td>{$to}</td>
                <td>{$row['position']}</td>
                <td>{$row['establishment']}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
