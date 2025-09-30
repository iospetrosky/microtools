<?php
/*
This is the new version, based on the lights and moisture sensors in the shed.
Online hosted at AwardSpace.
*/

$host = "";
$user = "";
$password = "";
$database = "";
echo "<pre>\n";

// Determine environment and set DB credentials
if (is_dir("/home/pi/WWW/lampone")) {
    // Local Raspberry Pi connection
    $host = "localhost";
    $user = "pi";
    $password = "emberlee1";
    $database = "iam";
} else {
    echo "Using remote DB\n";
    // Remote InfinityFree connection
    $host = "fdb1033.awardspace.net";
    $user = "4689889_sensors";
    $password = "Shannara71";
    $database = "4689889_sensors";
}

// Connect to MySQL
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . "\n");
}

// Prepare sensor reading
$sensor = isset($_GET['sensor']) ? $_GET['sensor'] : null;
$timec  = date('H:i');
$rval   = isset($_GET['rval']) ? floatval($_GET['rval']) : null;

if ($sensor == 1) {
    $sensor = "light";
} elseif ($sensor == 2) {
    $sensor = "moist01";
} elseif ($sensor == 3) {
    $sensor = "moist02";
} else {
    $sensor = null;
}


if (($rval === null) || ($sensor === null)) {
    echo "Invalid values\n";
} else {
    // Insert into readings table
    $stmt = mysqli_prepare($conn, "INSERT INTO readings (sensor, timec, rval) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssd", $sensor, $timec, $rval);

    if (mysqli_stmt_execute($stmt)) {
        echo "Reading inserted successfully\n";
    } else {
        echo "Error inserting reading: " . mysqli_error($conn) . "\n";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>