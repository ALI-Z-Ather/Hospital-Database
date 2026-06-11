<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 7: Patients with Multiple Complaints</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 7: Patients with Multiple Complaints and their Treatments</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    <?php
    $sql = "SELECT P.patientName, C.complaintType, T.treatmentCode 
            FROM PATIENT P 
            JOIN COMPLAINT C ON P.patientNo = C.patientNo 
            JOIN TREATMENT T ON C.complaintCode = T.complaintCode 
            WHERE P.patientNo IN (SELECT patientNo FROM COMPLAINT GROUP BY patientNo HAVING COUNT(*) > 1)
            ORDER BY P.patientName";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    echo "<div class='table-wrapper'><table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Complaint Type</th>
                    <th>Treatment Code</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['patientName']}</td>
                <td>{$row['complaintType']}</td>
                <td>{$row['treatmentCode']}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
