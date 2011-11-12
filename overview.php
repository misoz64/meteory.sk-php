<?
header("Content-Type: image/jpeg");
$cell_border = 2;
$cell_size=8;
$canvas_x_offset = $canvas_y_offset = 20;
$canvas_x_size   = (31*$cell_size)+(32*$cell_border);
$canvas_y_size   = (24*$cell_size)+(25*$cell_border);
$im = @imagecreate($canvas_x_offset + $canvas_x_size + 100,
                   $canvas_y_offset + $canvas_y_size + 100)
    or die("Cannot Initialize new GD image stream");

$col_white = imagecolorallocate($im, 255, 255, 255);
$col_black = imagecolorallocate($im, 0, 0, 0);

// black canvas
imagefilledrectangle($im, $canvas_x_offset,$canvas_y_offset,
                     $canvas_x_offset+$canvas_x_size,
                     $canvas_y_offset+$canvas_y_size,
                     $col_black);
// color palete
$palette_offset = 5;
$color_palette  = array(
    imagecolorallocate($im, hexdec("07"), hexdec("43"), hexdec("ff")),
    imagecolorallocate($im, hexdec("00"), hexdec("53"), hexdec("ff")),
    imagecolorallocate($im, hexdec("07"), hexdec("5c"), hexdec("ff")),
    imagecolorallocate($im, hexdec("02"), hexdec("6c"), hexdec("ff")),
    imagecolorallocate($im, hexdec("08"), hexdec("89"), hexdec("fe")),

    imagecolorallocate($im, hexdec("00"), hexdec("a0"), hexdec("f4")),
    imagecolorallocate($im, hexdec("00"), hexdec("c1"), hexdec("ff")),
    imagecolorallocate($im, hexdec("00"), hexdec("e7"), hexdec("fe")),
    imagecolorallocate($im, hexdec("3e"), hexdec("fe"), hexdec("e9")),
    imagecolorallocate($im, hexdec("74"), hexdec("f3"), hexdec("c8")),

    imagecolorallocate($im, hexdec("6d"), hexdec("ff"), hexdec("b3")),
    imagecolorallocate($im, hexdec("79"), hexdec("ff"), hexdec("f4")),
    imagecolorallocate($im, hexdec("8c"), hexdec("fb"), hexdec("70")),
    imagecolorallocate($im, hexdec("ac"), hexdec("fe"), hexdec("5e")),
    imagecolorallocate($im, hexdec("c7"), hexdec("f9"), hexdec("58")),

    imagecolorallocate($im, hexdec("ec"), hexdec("fe"), hexdec("4a")),
    imagecolorallocate($im, hexdec("fd"), hexdec("e2"), hexdec("00")),
    imagecolorallocate($im, hexdec("ff"), hexdec("b6"), hexdec("01")),
    imagecolorallocate($im, hexdec("fe"), hexdec("a2"), hexdec("03")),
    imagecolorallocate($im, hexdec("ff"), hexdec("8c"), hexdec("00")),

    imagecolorallocate($im, hexdec("fa"), hexdec("72"), hexdec("04")),
    imagecolorallocate($im, hexdec("f4"), hexdec("5d"), hexdec("02")),
    imagecolorallocate($im, hexdec("ff"), hexdec("4e"), hexdec("02")),
    imagecolorallocate($im, hexdec("fc"), hexdec("00"), hexdec("01")),
);
imagefilledrectangle($im, $canvas_x_offset+$canvas_x_size+$palette_offset,
                          $canvas_y_offset,
                          $canvas_x_offset+$canvas_x_size+$palette_offset+(2*$cell_border)+$cell_size,
                          $canvas_y_offset+$canvas_y_size,
                          $col_black);
$x_pos = $canvas_x_offset+$canvas_x_size+$palette_offset+$cell_border;
$y_pos = 20;
for($y=0;$y<24;$y++)
{
    $y_pos = $y_pos + $cell_border;
    imagefilledrectangle($im, $x_pos, $y_pos,$x_pos+$cell_size, $y_pos+$cell_size, $color_palette[$y]);
    $y_pos = $y_pos + $cell_size;
}

// hour labels
imagestring($im, 2, 0+$cell_border, $canvas_y_offset, "00h", $col_black);
imagestring($im, 2, 0+$cell_border, $canvas_y_offset+(6*$cell_size)+(6*$cell_border), "06h", $col_black);
imagestring($im, 2, 0+$cell_border, $canvas_y_offset+(12*$cell_size)+(12*$cell_border), "12h", $col_black);
imagestring($im, 2, 0+$cell_border, $canvas_y_offset+(18*$cell_size)+(18*$cell_border), "18h", $col_black);
imagestring($im, 2, 0+$cell_border, $canvas_y_offset+(23*$cell_size)+(23*$cell_border), "23h", $col_black);
//day labels
imagestring($im, 2, $canvas_x_offset, 0+$cell_border, "1   Days --->", $col_black);
imagestring($im, 2, $canvas_x_offset+(14*$cell_size)+(14*$cell_border), 0+$cell_border, "15", $col_black);
imagestring($im, 2, $canvas_x_offset+(30*$cell_size)+(30*$cell_border), 0+$cell_border, "31", $col_black);
//color palette labels
imagestring($im, 2, $canvas_x_offset+$canvas_x_size+(2*$palette_offset)+$cell_size+(2*$cell_border),
                 0+$canvas_y_offset, "0", $col_black);
imagestring($im, 2, $canvas_x_offset+$canvas_x_size+(2*$palette_offset)+$cell_size+(2*$cell_border),
                 0+$canvas_y_offset+(12*$cell_size)+(12*$cell_border), "mid", $col_black);
imagestring($im, 2, $canvas_x_offset+$canvas_x_size+(2*$palette_offset)+$cell_size+(2*$cell_border),
                 0+$canvas_y_offset+(23*$cell_size)+(23*$cell_border), "max", $col_black);


// canvas cells
$x_pos = 20;
for($x=0;$x<31;$x++)
{
    $y_pos = 20;
    $x_pos = $x_pos + $cell_border;
    for($y=0;$y<24;$y++)
    {
        $y_pos = $y_pos + $cell_border;
        imagefilledrectangle($im, $x_pos, $y_pos,$x_pos+$cell_size, $y_pos+$cell_size, $col_white);
        $y_pos = $y_pos + $cell_size;
    }
    $x_pos = $x_pos + $cell_size;
}
imagejpeg($im);
imagedestroy($im);
?>
