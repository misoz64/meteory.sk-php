<?
$handle = opendir('../../smrst/rmob/') or die ("Can't list the directory");

function sort_files($a, $b)
{
    if ($a[0] == $b[0])
	{
		if ($a[2] == $b[2])
		{
			if ($a[1] == $b[1]) return 0;
			return ($a[1] > $b[1]) ? -1 : 1;
		}
		return ($a[2] > $b[2]) ? -1 : 1;
	}
	return ($a[0] > $b[0]) ? -1 : 1;
}

$files = array();

while (false !== ($file = readdir($handle))) {
    if (preg_match('/rmob.TXT/', $file))
    {
		$row = preg_split("/[_]+/", $file);
		array_push($files, array($row[0], substr($row[1], 0, 2), substr($row[1], 2, 4)));
    }
}
closedir($handle);

usort($files, "sort_files");
foreach($files as $file_data)
{
	$file = $file_data[0]."_".$file_data[1].$file_data[2]."rmob.TXT";
    echo "<div><span><a href=show.php?type=csv&file=".$file.
		">$file  </a></span><span><a href=show.php?type=daily&file="
        .$file.">Daily report</a></span>  <span>"
        ."<a href=show.php?type=overview&file=".$file
        .">Overview per month</a></span></div>";
}
?>
