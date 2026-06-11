<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 3: Patients & Treatments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 3: Patients, Complaints & Treatments</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    <?php
    $sql = "SELECT P.patientNo, P.patientName, C.complaintCode, C.complaintType, T.treatmentCode, T.startDate, T.endDate 
            FROM PATIENT P 
            JOIN COMPLAINT C ON P.patientNo = C.patientNo 
            JOIN TREATMENT T ON C.complaintCode = T.complaintCode
            ORDER BY P.patientName, T.startDate";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    echo "<div class='table-wrapper'><table>
            <thead>
                <tr>
                    <th>Patient No</th>
                    <th>Patient Name</th>
                    <th>Complaint Code</th>
                    <th>Complaint Type</th>
                    <th>Treatment Code</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $start = $row['startDate'] ? $row['startDate']->format('Y-m-d') : 'N/A';
        $end = $row['endDate'] ? $row['endDate']->format('Y-m-d') : 'Ongoing';
        echo "<tr>
                <td>{$row['patientNo']}</td>
                <td>{$row['patientName']}</td>
                <td>{$row['complaintCode']}</td>
                <td>{$row['complaintType']}</td>
                <td>{$row['treatmentCode']}</td>
                <td>{$start}</td>
                <td>{$end}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
