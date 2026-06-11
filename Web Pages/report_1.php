<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 1: Consultants and Teams</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 1: Consultants and their Teams</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    <?php
    $sql = "SELECT C.doctorNo as ConsultantNo, D1.doctorName as ConsultantName, D2.doctorNo as TeamDoctorNo, D2.doctorName as TeamDoctorName 
            FROM CONSULTANT C 
            JOIN DOCTOR D1 ON C.doctorNo = D1.doctorNo 
            JOIN TEAMOFDOCTORS T ON T.leaderDoctorNo = C.doctorNo 
            JOIN DOCTOR D2 ON D2.teamID = T.SNo
            ORDER BY D1.doctorName, D2.doctorName";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    echo "<div class='table-wrapper'><table>
            <thead>
                <tr>
                    <th>Consultant No</th>
                    <th>Consultant Name</th>
                    <th>Team Doctor No</th>
                    <th>Team Doctor Name</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['ConsultantNo']}</td>
                <td>{$row['ConsultantName']}</td>
                <td>{$row['TeamDoctorNo']}</td>
                <td>{$row['TeamDoctorName']}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
