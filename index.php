<?
$handle = opendir('.') or die ("Can't list the directory");

while (false !== ($file = readdir($handle))) {
    if (preg_match('/rmob.TXT/', $file))
    {
        echo "<div>$file  <span><a href=show.php?type=daily&file="
             .$file.">Daily report</a></span>  <span>"
             ."<a href=show.php?type=overview&file=".$file
             .">Overview per month</a></span></div>";
    }
}
closedir($handle);
?>
