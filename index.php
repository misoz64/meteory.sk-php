<?
//$file = fopen("http://radio.data.free.fr/live_datas/Vsetin_112011rmob.TXT", 'r');
$file = fopen("Vsetin_112011rmob.TXT", 'r');
if (!$file) {
    echo "<p>Unable to open remote file.\n";
    exit;
}

$line_num= -1;
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
        if (!strstr($line, "?"))
        {
           echo "<img src='image.php?data=".urlencode(trim($line))."'>";
        }
    }
    else
    {
        echo "<br>".$line;
    }
}

fclose($file);
?>
