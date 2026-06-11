<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 2: Wards and Care Units</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 2: Wards, Sisters, and Care Units</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    <?php
    $sql = "SELECT W.wardName, C.careUnitID, N.nurseID as InChargeNurse, N.staffType,
                   (SELECT TOP 1 n2.nurseID FROM NURSE n2 JOIN CAREUNIT c2 ON n2.careUnitID = c2.careUnitID WHERE c2.wardName = W.wardName AND n2.staffType = 'Senior' AND n2.serviceTime = '08:00:00') as DaySister,
                   (SELECT TOP 1 n3.nurseID FROM NURSE n3 JOIN CAREUNIT c3 ON n3.careUnitID = c3.careUnitID WHERE c3.wardName = W.wardName AND n3.staffType = 'Senior' AND n3.serviceTime = '00:00:00') as NightSister
            FROM WARD W 
            JOIN CAREUNIT C ON W.wardName = C.wardName 
            LEFT JOIN NURSE N ON C.inChargeNurseID = N.nurseID
            ORDER BY W.wardName, C.careUnitID";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    echo "<div class='table-wrapper'><table>
            <thead>
                <tr>
                    <th>Ward Name</th>
                    <th>Day Sister (Nurse ID)</th>
                    <th>Night Sister (Nurse ID)</th>
                    <th>Care Unit ID</th>
                    <th>Staff Nurse In Charge</th>
                    <th>Staff Type</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $daySister = $row['DaySister'] ?? 'N/A';
        $nightSister = $row['NightSister'] ?? 'N/A';
        $inCharge = $row['InChargeNurse'] ?? 'N/A';
        $staffType = $row['staffType'] ?? 'N/A';
        echo "<tr>
                <td>{$row['wardName']}</td>
                <td>{$daySister}</td>
                <td>{$nightSister}</td>
                <td>{$row['careUnitID']}</td>
                <td>{$inCharge}</td>
                <td>{$staffType}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
