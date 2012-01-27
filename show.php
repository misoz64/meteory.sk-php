<?
error_reporting(E_STRICT|E_ALL);
//$file = fopen("http://radio.data.free.fr/live_datas/Vsetin_112011rmob.TXT", 'r');
//defined($_GET["file"]) or die("Filename must be selected");
$file = fopen("../../smrst/rmob/".$_GET["file"], 'r');
if (!$file) {
    echo "<p>Unable to open remote file.\n";
    exit;
}

Class ReportType {
    const Daily   = 0;
	const Monthly = 1;
	const CSV	  = 2;
}


switch($_GET["type"]){
    case "daily":
        $report = ReportType::Daily;
        break;
    case "overview":
        $report = ReportType::Monthly;
        break;
	case "csv":
        $report = ReportType::CSV;	
		break;
    default:
        die("Unknown preview type.");
}

$max_value = 0;
$line_num  = -1;
$data      = array();
$info      = array();

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
        if ($report == ReportType::Monthly)
        {
            $line_data = preg_split("/[|]+/", $line);
            foreach($line_data as $value)
            {
                $value = trim($value);
                if ((is_numeric($value)) && ($value > $max_value))
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

switch ($report){
	case(ReportType::Daily):
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
		break;

	case(ReportType::Monthly):
		require "overview.php";
		$overview = new Overview();
		$overview->import($data);
		$overview->main($max_value);
		break;

	case(ReportType::CSV):
//		header('Content-Type: text/csv');
		// FIXME: month
		echo ",00h,01h,02h,03h,04h,05h,06h,07h,08h,09h,10h,11h,12h,13h,14h,15h,16h,17h,18h,19h,20h,21h,22h,23h<br>";
		foreach($data as $line) 
		{
			// FIXME: 
			$array_line = preg_split("/[|]+/", $line);
			foreach($array_line as $value)
			{
				$value = trim($value);
				echo is_numeric($value) ? $value : "";
				echo ","; // separator
			}
			echo "<br>";
		}
		break;

	default:
		die("Unknown type");
}

fclose($file);
?>
