<?
require_once('Chart.php');
$data = preg_split("/[|]+/", $_GET["data"]);

if (strstr($data[1], "?"))
{
    exit;
}

$bars = array_slice($data, 1);
$dates = array('0h','.','.','.','4h','.','.','.','8h','.','.','.', '12h',
               '.', '.', '.', '16h', '.', '.', '.', '20h', '.', '.', '.', '24h');
$graph = new Chart();
$graph->addBars($bars, '0000ff');
$graph->addXLabels($dates, '000000');
$graph->addYScale('000000');
$graph->output();
?>
