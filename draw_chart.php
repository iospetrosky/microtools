<?php
$host = "";
$user = "";
$password = "";
$database = "";

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

$sensorParam = $_GET['sensor'] ?? '';
$sensorList = array_map('trim', explode(',', $sensorParam));

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$labels = [];
$datasets = [];

foreach ($sensorList as $sensor) {
    $sensorEscaped = mysqli_real_escape_string($conn, $sensor);
    $sql = "SELECT timec, rval FROM readings WHERE sensor = '$sensorEscaped' ORDER BY id ASC";
    $result = $conn->query($sql);

    $timec = [];
    $rval = [];

    while ($row = $result->fetch_assoc()) {
        $timec[] = $row['timec'];
        $rval[] = $row['rval'];
    }
    // keep only the last 40 entries
    $timec = array_slice($timec, -40);  
    $rval  = array_slice($rval, -40);

    if (count($timec) > count($labels)) {
        // keep always the longest labels array
        $labels = $timec;
    }


    $datasets[] = [
        'label' => strtoupper($sensor),
        'data' => $rval,
        'borderColor' => 'rgba(' . rand(50,200) . ',' . rand(50,200) . ',' . rand(50,200) . ',1)',
        'backgroundColor' => 'rgba(75,192,192,0.2)',
        'borderWidth' => 2
    ];
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('lineChart').getContext('2d');
const lineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: <?php echo json_encode($datasets); ?>
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