<?php

$con = new mysqli("localhost", "pi", "emberlee1", "iam");
if ($con->connect_errno) {
    printf("X");
    exit();
} 
$sql = sprintf("update sensor_measures set measure = 50 where id = %d", $_GET["id"]);

if ($con->query($sql) === TRUE) {
    echo "A";
} else {
    echo "B";
}
  
$con->close();
