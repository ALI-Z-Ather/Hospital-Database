<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form 2: Ward Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Ward Management Record</h1>
</header>

<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>

    <div class="form-container">
        <form action="" method="post">
            <div class="form-group">
                <label for="wardName">Select Ward Name:</label>
                <select id="wardName" name="wardName" required>
                    <option value="">-- Select Ward --</option>
                    <?php
                    // Fetch wards for dropdown
                    $sql_wards = "SELECT wardName FROM WARD ORDER BY wardName";
                    $stmt_wards = sqlsrv_query($conn, $sql_wards);
                    while ($wardRow = sqlsrv_fetch_array($stmt_wards, SQLSRV_FETCH_ASSOC)) {
                        $selected = (isset($_POST['wardName']) && $_POST['wardName'] == $wardRow['wardName']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($wardRow['wardName']) . "' $selected>" . htmlspecialchars($wardRow['wardName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="submit" value="Generate Ward Report">
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['wardName'])) {
        $wardName = $_POST['wardName'];

        // --- Ward Header (specialty + nurses) ---
        $sql_ward = "SELECT specialityName FROM WARD WHERE wardName = ?";
        $stmt_ward = sqlsrv_query($conn, $sql_ward, array($wardName));
        $wardRow = $stmt_ward ? sqlsrv_fetch_array($stmt_ward, SQLSRV_FETCH_ASSOC) : null;
        $specialty = $wardRow['specialityName'] ?? 'N/A';

        // Day Sister: senior nurse on day shift (08:00) in this ward
        $sql_day = "
            SELECT TOP 1 N.nurseID
            FROM NURSE N
            JOIN CAREUNIT CU ON CU.careUnitID = N.careUnitID
            WHERE CU.wardName = ? AND N.staffType = 'Senior' AND N.serviceTime = '08:00:00'
            ORDER BY N.nurseID
        ";
        $stmt_day = sqlsrv_query($conn, $sql_day, array($wardName));
        $dayRow = $stmt_day ? sqlsrv_fetch_array($stmt_day, SQLSRV_FETCH_ASSOC) : null;
        $daySister = $dayRow ? ('Nurse #' . $dayRow['nurseID']) : 'N/A';

        // Night Sister: senior nurse on night shift (00:00)
        $sql_night = "
            SELECT TOP 1 N.nurseID
            FROM NURSE N
            JOIN CAREUNIT CU ON CU.careUnitID = N.careUnitID
            WHERE CU.wardName = ? AND N.staffType = 'Senior' AND N.serviceTime = '00:00:00'
            ORDER BY N.nurseID
        ";
        $stmt_night = sqlsrv_query($conn, $sql_night, array($wardName));
        $nightRow = $stmt_night ? sqlsrv_fetch_array($stmt_night, SQLSRV_FETCH_ASSOC) : null;
        $nightSister = $nightRow ? ('Nurse #' . $nightRow['nurseID']) : 'N/A';

        // Staff Nurses (registered = Senior) and Non-registered (Junior)
        $sql_staff = "
            SELECT N.nurseID, N.staffType
            FROM NURSE N
            JOIN CAREUNIT CU ON CU.careUnitID = N.careUnitID
            WHERE CU.wardName = ?
            ORDER BY N.nurseID
        ";
        $stmt_staff = sqlsrv_query($conn, $sql_staff, array($wardName));
        $staffNurses = [];
        $nonRegNurses = [];
        if ($stmt_staff) {
            while ($n = sqlsrv_fetch_array($stmt_staff, SQLSRV_FETCH_ASSOC)) {
                $label = 'Nurse #' . $n['nurseID'];
                if ($n['staffType'] === 'Senior') $staffNurses[] = $label;
                else $nonRegNurses[] = $label;
            }
        }

        echo "<div class='ward-record'>";
        echo "<h2>IVOR PAINE MEMORIAL HOSPITAL &mdash; WARD RECORD</h2>";
        echo "<table class='record-header'>
                <tr>
                    <td><strong>Ward Name:</strong> " . htmlspecialchars($wardName) . "</td>
                    <td><strong>Specialty:</strong> " . htmlspecialchars($specialty) . "</td>
                </tr>
                <tr>
                    <td><strong>Day Sister:</strong> " . htmlspecialchars($daySister) . "</td>
                    <td><strong>Night Sister:</strong> " . htmlspecialchars($nightSister) . "</td>
                </tr>
                <tr>
                    <td><strong>Staff Nurses:</strong> " . htmlspecialchars(empty($staffNurses) ? 'None' : implode(', ', $staffNurses)) . "</td>
                    <td><strong>Non-registered Nurses:</strong> " . htmlspecialchars(empty($nonRegNurses) ? 'None' : implode(', ', $nonRegNurses)) . "</td>
                </tr>
              </table>";
        echo "</div>";

        // --- Patient Information ---
        $sql_patients = "
            SELECT P.patientNo, P.patientName, A.bedNo, B.careUnitID,
                   A.consultantDoctorNo, D.doctorName, A.dateAdmitted
            FROM ADMITTED A
            JOIN PATIENT  P ON P.patientNo = A.patientNo
            JOIN BED      B ON B.bedNo     = A.bedNo
            JOIN CAREUNIT CU ON CU.careUnitID = B.careUnitID
            LEFT JOIN DOCTOR D ON D.doctorNo = A.consultantDoctorNo
            WHERE CU.wardName = ?
            ORDER BY A.dateAdmitted DESC
        ";
        $stmt_patients = sqlsrv_query($conn, $sql_patients, array($wardName));

        if ($stmt_patients === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        echo "<h3>Patient Information</h3>";
        echo "<div class='table-wrapper'><table>
                <thead>
                    <tr>
                        <th>Patient No</th>
                        <th>Patient Name</th>
                        <th>Care Unit</th>
                        <th>Bed No</th>
                        <th>Consultant</th>
                        <th>Date Admitted</th>
                    </tr>
                </thead>
                <tbody>";

        $hasPatients = false;
        while ($row = sqlsrv_fetch_array($stmt_patients, SQLSRV_FETCH_ASSOC)) {
            $hasPatients = true;
            $consultant = $row['doctorName']
                ? ($row['doctorName'] . ' (#' . $row['consultantDoctorNo'] . ')')
                : ('#' . $row['consultantDoctorNo']);
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['patientNo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['patientName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['careUnitID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['bedNo']) . "</td>";
            echo "<td>" . htmlspecialchars($consultant) . "</td>";
            echo "<td>" . ($row['dateAdmitted'] ? $row['dateAdmitted']->format('Y-m-d') : 'N/A') . "</td>";
            echo "</tr>";
        }

        if (!$hasPatients) {
            echo "<tr><td colspan='6' style='text-align:center;'>No patients currently admitted in this ward.</td></tr>";
        }

        echo "</tbody></table></div>";
    }
    ?>
</div>

<?php sqlsrv_close($conn); ?>
</body>
</html>
