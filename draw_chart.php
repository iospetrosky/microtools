Change the following script so that parameter sensor may be a comma separated list. 
The SQL query will filter by all the possible values, which are strings. 
The chart must display a dataset for each value.


<?php
$host = "";
$user = "";
$password = "";
$database = "";


// Determine environment and set DB credentials
if (is_dir("/home/pi/WWW/lampone")) {
    $host = "localhost";
    $user = "pi";
    $password = "emberlee1";
    $database = "iam";
} else {
    $host = "fdb1033.awardspace.net";
    $user = "4689889_sensors";
    $password = "Shannara71";
    $database = "4689889_sensors";
}

//get sensor name from GET parameter
$sensor = isset($_GET['sensor']) ? $_GET['sensor'] : null;
if ($sensor == null) {
    die("Sensor parameter is required.\n");
}

// Connect to MySQL
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . "\n");
}

// Fetch data
$sql = "SELECT timec, rval FROM readings where sensor = '$sensor'  ORDER BY id ASC";
$result = $conn->query($sql);

$timec = [];
$rval = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $timec[] = $row['timec'];
        $rval[] = $row['rval'];
    }
} else {
    die( "0 results");
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    canvas#lineChart {
        display: block;
        margin: auto;
        border: 1px solid #ccc;
    }
    </style>    
</head>
<body>

<canvas id="lineChart" width="800" height="600"></canvas>
<script>
    const ctx = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($timec); ?>,
            datasets: [{
                label: '<?php echo strtoupper(htmlspecialchars($sensor)); ?>',
                data: <?php echo json_encode($rval); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>