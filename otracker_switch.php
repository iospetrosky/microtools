<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php

function make_sql() {
    if ($_GET["id"] != '') {
        if ($_GET["delete"] == 1) {
            return sprintf("delete from oo_status_history where id = %d", $_GET["id"]);
        }
        if ($_GET['newname'] != '') {
            return sprintf("update oo_status_history set player = '%s' where id = %d", $_GET["newname"], $_GET["id"]);
        }
    }
    return '';
}


echo "<PRE>";

// echo $_GET["id"] . "\n";
// echo $_GET["newname"] . "\n";
// echo $_GET["delete"] . "\n";

$con = new mysqli("localhost", "pi", "emberlee1", "iam");
echo "con";
if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error);
    exit();
} else {
    printf("DB connection OK\n");
}
$sql = make_sql();
printf($sql);

if ($sql != '') {
    if ($con->query($sql) === TRUE) {
        echo "\nRecord updated successfully\n";
    } else {
        echo "\nError updating record: " . $conn->error . "\n";
    }
} else {
    echo "Nothing to be executed on the server\n";
}
  
$con->close();
?>
</body>