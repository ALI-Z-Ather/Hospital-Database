<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form 3: Staff Record</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Staff Performance Review</h1>
</header>

<div class="container">
    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>

    <div class="form-container">
        <form action="" method="post">
            <div class="form-group">
                <label for="staffNo">Input Staff No (Doctor No):</label>
                <input type="number" id="staffNo" name="staffNo" required value="<?php echo isset($_POST['staffNo']) ? htmlspecialchars($_POST['staffNo']) : ''; ?>">
            </div>
            <input type="submit" value="Search Staff">
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['staffNo'])) {
        $staffNo = intval($_POST['staffNo']);

        // Fetch Doctor details
        $sql_doc = "SELECT doctorNo, doctorName, position, dateOfJoin FROM DOCTOR WHERE doctorNo = ?";
        $stmt_doc = sqlsrv_query($conn, $sql_doc, array($staffNo));

        if ($stmt_doc === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $docData = sqlsrv_fetch_array($stmt_doc, SQLSRV_FETCH_ASSOC);

        if ($docData) {
            $dateJoined = $docData['dateOfJoin'] ? $docData['dateOfJoin']->format('Y-m-d') : 'N/A';

            echo "<div class='staff-record'>";
            echo "<h2>IVOR PAINE MEMORIAL HOSPITAL &mdash; CONSULTANT TEAM RECORD</h2>";
            echo "<table class='record-header'>
                    <tr>
                        <td><strong>Staff No:</strong> " . htmlspecialchars($docData['doctorNo']) . "</td>
                        <td><strong>Name:</strong> " . htmlspecialchars($docData['doctorName']) . "</td>
                    </tr>
                    <tr>
                        <td><strong>Position:</strong> " . htmlspecialchars($docData['position']) . "</td>
                        <td><strong>Date joined team:</strong> " . htmlspecialchars($dateJoined) . "</td>
                    </tr>
                  </table>";
            echo "</div>";

            // Fetch Experience
            $sql_exp = "SELECT fromDate, toDate, position, establishment FROM DOCTOR_EXPERIENCE WHERE doctorNo = ? ORDER BY fromDate DESC";
            $stmt_exp = sqlsrv_query($conn, $sql_exp, array($staffNo));

            // Fetch Progress
            $sql_prog = "SELECT date, grade FROM DOCTOR_PROGRESS WHERE doctorNo = ? ORDER BY date DESC";
            $stmt_prog = sqlsrv_query($conn, $sql_prog, array($staffNo));

            echo "<div class='record-grid' style='display:flex; gap:20px; flex-wrap:wrap;'>";

            // Previous Experience
            echo "<div style='flex:1; min-width:320px;'>";
            echo "<h3>Previous Experience</h3>";
            echo "<div class='table-wrapper'><table>
                    <thead>
                        <tr>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Position</th>
                            <th>Establishment</th>
                        </tr>
                    </thead>
                    <tbody>";
            $hasExp = false;
            while ($row = sqlsrv_fetch_array($stmt_exp, SQLSRV_FETCH_ASSOC)) {
                $hasExp = true;
                echo "<tr>";
                echo "<td>" . ($row['fromDate'] ? $row['fromDate']->format('Y-m-d') : 'N/A') . "</td>";
                echo "<td>" . ($row['toDate']   ? $row['toDate']->format('Y-m-d')   : 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['position']) . "</td>";
                echo "<td>" . htmlspecialchars($row['establishment']) . "</td>";
                echo "</tr>";
            }
            if (!$hasExp) {
                echo "<tr><td colspan='4' style='text-align:center;'>No previous experience recorded.</td></tr>";
            }
            echo "</tbody></table></div>";
            echo "</div>";

            // Progress
            echo "<div style='flex:1; min-width:280px;'>";
            echo "<h3>Progress</h3>";
            echo "<div class='table-wrapper'><table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Performance Grade</th>
                        </tr>
                    </thead>
                    <tbody>";
            $hasProg = false;
            while ($row = sqlsrv_fetch_array($stmt_prog, SQLSRV_FETCH_ASSOC)) {
                $hasProg = true;
                echo "<tr>";
                echo "<td>" . ($row['date'] ? $row['date']->format('Y-m-d') : 'N/A') . "</td>";
                echo "<td><strong>" . htmlspecialchars($row['grade']) . "</strong></td>";
                echo "</tr>";
            }
            if (!$hasProg) {
                echo "<tr><td colspan='2' style='text-align:center;'>No performance grades recorded.</td></tr>";
            }
            echo "</tbody></table></div>";
            echo "</div>";

            echo "</div>"; // end record-grid

        } else {
            echo "<div class='alert'>Staff No. $staffNo not found in the system.</div>";
        }
    }
    ?>
</div>

<?php sqlsrv_close($conn); ?>
</body>
</html>
