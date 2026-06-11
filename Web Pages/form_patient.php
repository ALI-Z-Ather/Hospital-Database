<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form 1: Patient Record</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Patient Record Lookup</h1>
</header>

<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>

    <div class="form-container">
        <form action="" method="post">
            <div class="form-group">
                <label for="patientNo">Input Patient No:</label>
                <input type="number" id="patientNo" name="patientNo" required value="<?php echo isset($_POST['patientNo']) ? htmlspecialchars($_POST['patientNo']) : ''; ?>">
            </div>
            <input type="submit" value="Search Patient">
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['patientNo'])) {
        $patientNo = intval($_POST['patientNo']);

        // Fetch Patient + Latest Admitting Consultant
        $sql_patient = "
            SELECT TOP 1
                P.patientNo, P.patientName, P.DOB,
                A.consultantDoctorNo,
                D.doctorName,
                C.specialityName AS consultantSpeciality
            FROM PATIENT P
            LEFT JOIN ADMITTED   A ON A.patientNo = P.patientNo
            LEFT JOIN DOCTOR     D ON D.doctorNo  = A.consultantDoctorNo
            LEFT JOIN CONSULTANT C ON C.doctorNo  = A.consultantDoctorNo
            WHERE P.patientNo = ?
            ORDER BY A.dateAdmitted DESC
        ";
        $stmt_patient = sqlsrv_query($conn, $sql_patient, array($patientNo));

        if ($stmt_patient === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $patientData = sqlsrv_fetch_array($stmt_patient, SQLSRV_FETCH_ASSOC);

        if ($patientData) {
            $dob = $patientData['DOB'] ? $patientData['DOB']->format('Y-m-d') : 'N/A';
            $docNo   = $patientData['consultantDoctorNo']   ?? 'N/A';
            $docName = $patientData['doctorName']           ?? 'N/A';
            $consult = $patientData['consultantSpeciality'] ?? 'N/A';

            echo "<div class='patient-record'>";
            echo "<h2>IVOR PAINE MEMORIAL HOSPITAL &mdash; PATIENT RECORD</h2>";
            echo "<table class='record-header'>
                    <tr>
                        <td><strong>Patient No:</strong> " . htmlspecialchars($patientData['patientNo']) . "</td>
                        <td><strong>Doctor No:</strong> " . htmlspecialchars($docNo) . "</td>
                    </tr>
                    <tr>
                        <td><strong>Patient Name:</strong> " . htmlspecialchars($patientData['patientName']) . "</td>
                        <td><strong>Doctor Name:</strong> " . htmlspecialchars($docName) . "</td>
                    </tr>
                    <tr>
                        <td><strong>Date of Birth:</strong> " . htmlspecialchars($dob) . "</td>
                        <td><strong>Consultant:</strong> " . htmlspecialchars($consult) . "</td>
                    </tr>
                  </table>";
            echo "</div>";

            // Fetch Complaints and Treatments
            $sql_medical = "
                SELECT C.complaintCode, T.treatmentCode, TDB.doctorID, T.startDate, T.endDate
                FROM COMPLAINT C
                LEFT JOIN TREATMENT T         ON C.complaintCode = T.complaintCode
                LEFT JOIN TREATMENTDONEBY TDB ON T.treatmentCode = TDB.treatmentCode
                WHERE C.patientNo = ?
                ORDER BY C.complaintCode, T.treatmentCode
            ";

            $stmt_medical = sqlsrv_query($conn, $sql_medical, array($patientNo));

            echo "<h3>Medical History</h3>";
            echo "<div class='table-wrapper'><table>
                    <thead>
                        <tr>
                            <th>Complaint Code</th>
                            <th>Treatment Code</th>
                            <th>Doctor</th>
                            <th>Date Treatment Started</th>
                            <th>Date Treatment Ended</th>
                        </tr>
                    </thead>
                    <tbody>";

            $hasRecords = false;
            while ($row = sqlsrv_fetch_array($stmt_medical, SQLSRV_FETCH_ASSOC)) {
                $hasRecords = true;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['complaintCode'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['treatmentCode'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['doctorID'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['startDate'] ? $row['startDate']->format('Y-m-d') : 'N/A') . "</td>";
                echo "<td>" . ($row['endDate']   ? $row['endDate']->format('Y-m-d')   : 'Ongoing') . "</td>";
                echo "</tr>";
            }

            if (!$hasRecords) {
                echo "<tr><td colspan='5' style='text-align:center;'>No medical complaints or treatments recorded.</td></tr>";
            }

            echo "</tbody></table></div>";

        } else {
            echo "<div class='alert'>Patient No. $patientNo not found in the system.</div>";
        }
    }
    ?>
</div>

<?php sqlsrv_close($conn); ?>
</body>
</html>
