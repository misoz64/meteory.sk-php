<?
error_reporting(E_STRICT|E_ALL);
//$file = fopen("http://radio.data.free.fr/live_datas/Vsetin_112011rmob.TXT", 'r');
$file = fopen("Vsetin_112011rmob.TXT", 'r');
if (!$file) {
    echo "<p>Unable to open remote file.\n";
    exit;
}

switch($_GET["type"]){
    case "daily":
        $show_daily_report=1;
        break;
    case "overview":
        $show_daily_report=0;
        break;
    default:
        die("Unknown preview type.");
}

$max_value=0;
$line_num= -1;
$data = array();
$info = array();

while (!feof ($file)) {
    $line = fgets ($file);
    $line_num++;
    // skip first line
    if ($line_num == 0)
    {
        continue;
    }
    if ($line_num<=31)
    {
        if (!$show_daily_report)
        {
            $line_data = preg_split("/[|]+/", $line);
            foreach($line_data as $value)
            {
                if ($value > $max_value)
                {
                    $max_value = $value;
                }
            }
        }
        array_push($data, $line);
    }
    else
    {
        array_push($info, $line);
    }
}

if ($show_daily_report)
{
    // show information
    foreach($info as $line)
    {
        echo "<br>".$line;
    }
    // show daily graphs
    foreach($data as $line)
    {
        if (!strstr($line, "?"))
        {
           echo "<img src='daily.php?data=".urlencode(trim($line))."'>";
        }
    }
}
else
{
    require "overview.php";
    $overview = new Overview();
    $overview->main();
}

fclose($file);
?>
