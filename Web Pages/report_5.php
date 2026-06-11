<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report 5: Consultants Speciality</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Report 5: Consultants with Unique Speciality</h1>
</header>
<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
    <?php
    $sql = "SELECT D.doctorName, C.specialityName 
            FROM CONSULTANT C 
            JOIN DOCTOR D ON C.doctorNo = D.doctorNo
            ORDER BY C.specialityName, D.doctorName";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    echo "<div class='table-wrapper'><table>
            <thead>
                <tr>
                    <th>Consultant Name</th>
                    <th>Speciality</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['doctorName']}</td>
                <td>{$row['specialityName']}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
    ?>
</div>
<?php sqlsrv_close($conn); ?>
</body>
</html>
