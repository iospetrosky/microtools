<?php
include "my_pdo.php";

$pdo = new my_pdo("mysql", "localhost", "iam","pi", "emberlee1");
echo "<pre>Running\n";

switch ($_GET['act']) {
    case 'ADDMEASURE':
        $newval = (object) [
            'sensor_type' => $_GET['senstype'],
            'measure' => $_GET['measure'],
            'sensor_location' => $_GET['sensloc']
        ];
        if ($pdo->insert_object('greenhouse_sensors', $newval)) {
            echo "DBADD_OK";
        } else {
            echo "DBADD_ERR";
        }
        exit();
    case 'GETPARAMS':
        $params = array();
        $sql = "select par_name, par_value from greenhouse_params";
        $ds = $pdo->query($sql);
        foreach($ds as $param) {
            $params[$param->par_name] = $param->par_value;
        }
        echo json_encode($params);
        exit();
    case 'SWITCHLIGHT':
        echo "Attempting to switch lights " . $_GET['mode'] . "\n";
        $command = escapeshellcmd("/home/pi/WWW/lampone/tuya_switch_greenhouse.py" ) ;
        //echo $command;
        $output = shell_exec($command . " " . $_GET['mode']);
        echo $output;
        exit();
}

// if we get here it means we did not specify an activity
$u_agent = $_SERVER['HTTP_USER_AGENT'];
$isMobile = false;
if(strlen(strstr($u_agent,"Mobi")) > 0 ){ 
    $isMobile = true;
} // not that it matters for now

// tutorial on tables here
// https://wisdmlabs.com/blog/responsive-tables-using-css-div-tag/

// for the color palettes
// https://www.rapidtables.com/web/color/RGB_Color.html
?>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
div {
    border: 1px solid black;
}

#measure_table {
    width: <?php
        if ($isMobile) { echo "100%"; } else { echo "300px" ;}
    ?>;
    display:table;
    font-family: Arial, Helvetica, sans-serif;
}
#measure_table_body {
    display: table-row-group;
}
#measure_table_caption {
    display: table-caption;
    font-weight: bold;
    padding: 5px;
    background-color: gray;
}

.bg_green {
    background-color: #A3FF87;
}

.bg_white {
    background-color: #EBEBEB;
}

.bg_global {
    background-color: #FAFFA9;
}

.measure_table_row {
    display: table-row;
}
.measure_table_cell {
    display: table-cell;
    padding: 5px;
}

</style>

<meta http-equiv="refresh" content="1800"><!-- refresh every 30 minutes -->

</head>

<body>
<div id='measure_table'>
<?php 
// get the last 3 records of the table, representing the last measure taken
$sql = "select * from greenhouse_sensors where sensor_location = 'GLOBAL' order by id desc, sensor_type asc limit 1";
$today = "unset";
if ($ds = $pdo->query($sql, true)) {
    $today = $ds->meas_time;
}

if ($ds = $pdo->query($sql)) {
    echo "<div id='measure_table_caption'>Last measure: " . $today . "</div>"; 
    echo "<div id='measure_table_body'>";
    foreach($ds as $measure) {
        echo "<div class='measure_table_row bg_global'>";
        $isMobile?$tmp_st = "":$tmp_st="style='width:120px'";
        echo "<div class='measure_table_cell' $tmp_st>" . $measure->sensor_type . "</div>";
        echo "<div class='measure_table_cell'>" . $measure->measure . "</div>";
        echo "</div>";
    }
}

$locations = ['GREEN','WHITE'];
foreach($locations as $loc) {
    echo "<div class='measure_table_cell bg_$loc'>Location</div>"; 
    echo "<div class='measure_table_cell bg_$loc'>$loc</div>"; 
    $sql = "select * from greenhouse_sensors where sensor_location = '$loc' order by id desc, sensor_type asc limit 2";
    if ($ds = $pdo->query($sql)) {
        foreach($ds as $measure) {
            echo "<div class='measure_table_row bg_$loc'>";
            $isMobile?$tmp_st = "":$tmp_st="style='width:120px'";
            echo "<div class='measure_table_cell' $tmp_st>" . $measure->sensor_type . "</div>";
            echo "<div class='measure_table_cell'>" . $measure->measure . "</div>";
            echo "</div>";
        }
    }
}
echo "</div>"; #measure_table_body
?>
</div>

</body>