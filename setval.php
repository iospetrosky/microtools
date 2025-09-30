
<?php
/*
This is the new version, based on the lights and sensors in the
shed.
*/
include "my_pdo.php";
printf("Ciao\n");

//$pdo = new my_pdo("mysql", "sql110.infinityfree.com", "if0_40051494_sensors","if0_40051494", "Shannara71");

$pdo = new my_pdo("mysql", "localhost", "iam", "pi", "emberlee1");
$sensor = isset($_GET['sensor']) ? $_GET['sensor'] : null;
$timec  = isset($_GET['timec']) ? $_GET['timec'] : null;
$rval   = isset($_GET['rval']) ? floatval($_GET['rval']) : null;

if ($rval == null) {
    printf("Invalid values \n");
} else {
    $pdo->exec_prepared("INSERT INTO readings (sensor, timec, rval) VALUES (?, ?, ?)" , [$sensor, $timec, $rval]);
}

?>