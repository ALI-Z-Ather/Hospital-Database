<?php
$serverName = "\\SQLEXPRESS10";
$connectionOptions = array(
    "Database" => "Ivor_Paine_Memorial_Hospital",
    "Uid" => "",
    "PWD" => "",
    "TrustServerCertificate" => true
);

// Connect
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die("<div style='color:red; padding:20px;'><strong>Database Connection Failed:</strong><br>" . print_r(sqlsrv_errors(), true) . "</div>");
}
?>