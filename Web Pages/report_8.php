<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 8: Patients by Treatment/Complaint</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 8: Patients Grouped by Treatment within Complaint</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    <?php
    $sql = "SELECT C.complaintType, T.treatmentCode, P.patientName 
            FROM PATIENT P 
            JOIN COMPLAINT C ON P.patientNo = C.patientNo 
            JOIN TREATMENT T ON C.complaintCode = T.complaintCode 
            ORDER BY C.complaintType, T.treatmentCode, P.patientName";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    echo "<div class='table-wrapper'><table>
            <thead>
                <tr>
                    <th>Complaint Type</th>
                    <th>Treatment Code</th>
                    <th>Patient Name</th>
                </tr>
            </thead>
            <tbody>";
    $currentComplaint = "";
    $currentTreatment = "";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $complaintDisplay = ($row['complaintType'] !== $currentComplaint) ? $row['complaintType'] : "";
        $treatmentDisplay = ($row['treatmentCode'] !== $currentTreatment || $complaintDisplay !== "") ? $row['treatmentCode'] : "";
        
        $currentComplaint = $row['complaintType'];
        $currentTreatment = $row['treatmentCode'];

        echo "<tr>
                <td><strong>{$complaintDisplay}</strong></td>
                <td>{$treatmentDisplay}</td>
                <td>{$row['patientName']}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
