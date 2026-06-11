<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 4: Junior Houseman</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 4: Junior Houseman & their Patients</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    <?php
    $sql = "SELECT D.doctorName, P.patientName, N.nurseID as StaffNurseID
            FROM DOCTOR D 
            JOIN TREATMENTDONEBY TDB ON D.doctorNo = TDB.doctorID 
            JOIN TREATMENT T ON TDB.treatmentCode = T.treatmentCode 
            JOIN COMPLAINT C ON T.complaintCode = C.complaintCode 
            JOIN PATIENT P ON C.patientNo = P.patientNo 
            JOIN CAREUNIT CU ON P.careUnitID = CU.careUnitID 
            JOIN NURSE N ON CU.inChargeNurseID = N.nurseID 
            WHERE D.position LIKE '%Junior%'
            ORDER BY D.doctorName, P.patientName";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    echo "<div class='table-wrapper'><table>
            <thead>
                <tr>
                    <th>Junior Doctor</th>
                    <th>Patient Name</th>
                    <th>Care Unit Staff Nurse ID</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['doctorName']}</td>
                <td>{$row['patientName']}</td>
                <td>{$row['StaffNurseID']}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
