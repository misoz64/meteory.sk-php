<?php
/*
 chart.php v0.2
 ______________________________________________________________________
 Creates bar and line charts and graphs in PHP
 ______________________________________________________________________
 Requires:
  PHP 5.0 / GD Library
 ______________________________________________________________________
 Copyright:
  (C) 2007 Chris Tomlinson. christo@mightystuff.net
  http://mightystuff.net
  
  This library is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.
  
  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.
 
  http://www.gnu.org/copyleft/lesser.txt
 ______________________________________________________________________ 
 Usage:
  $g = new Graph(imagewidth, imageheight, xoffset, yoffset, graphwidth, graphheight);
  
    
  Where:
  FILE = the file to retrieve
  SIZE = the maximum size of the thumbnail in pixels
 ______________________________________________________________________
 Changes:
  0.1 - first release
  0.2 - added most default values
*/

class Chart
{
	public $debug;
	private $maxv;
	
	public $minx;
	public $maxx;
	
	public $miny;
	public $maxy;
	
	public $gridy = 10;
	public $padding;
	public $gradient = false;
	
	private $gridyConverted;
	public $background_colour;
	
	private $image;
	private $image_yscales;
	
	public $maxYScale = 100;
	
	function __construct($width = 400, $height = 300, $minx = 30, $miny = 10, $maxx = 380, $maxy = 280)
	{
		$this->width = $width;
		$this->height = $height;
		$this->minx = $minx;
		$this->maxx = $maxx;
		$this->miny = $miny;
		$this->maxy = $maxy;
		
		$this->gridheight = $this->maxy - $this->miny;
		$this->gridwidth = $this->maxx - $this->minx;
		
		$this->padding = 5;

		// common colours
		$this->image	= imagecreatetruecolor ($this->width, $this->height);
		$this->gray 	= imagecolorallocate ($this->image, 0xcc,0xcc,0xcc);
		$this->gray_lite = imagecolorallocate ($this->image, 0xee,0xee,0xee);
		$this->gray_dark = imagecolorallocate ($this->image, 0x7f,0x7f,0x7f);
		$this->white     = imagecolorallocate ($this->image, 0xff,0xff,0xff);
		$this->black 	= $this->allocateColour('000000');		
		
		// Fill in the background of the image
		$i = $this->image;
		imagefilledrectangle($i, 0, 0, $this->width, $this->height, imagecolorallocate($i, 255,255,255));

		// create an underlying image for the yscales 
		$this->image_yscales = imagecreatetruecolor($this->width, $this->height);
		imagefilledrectangle($this->image_yscales, 0, 0, $this->width, $this->height, imagecolorallocate ($this->image_yscales, 255, 255, 255));
	}
	
	/**
	 * Output graph as an image
	 *
	 * @param string $file
	 */
	function output($file = null)
	{
		$overlay = $this->image;
		$underlay = $this->image_yscales;
		
		// copy bars over yscales
		imagecolortransparent($overlay, imagecolorallocate($overlay, 255,255,255));
		imagecopymerge($underlay,$overlay,0,0,0,0,$this->width,$this->height,100);	
		
		if ($file)
		{
			// create a file
			imagepng($this->image_yscales, $file);
			imagedestroy($this->image_yscales);
		} else {
			// output directly
			header ("Content-type: image/png");
			imagepng($this->image_yscales);
			imagedestroy($this->image_yscales);
		}
	}
	
	function addBars($values, $start_colour, $end_colour='ffffff')
	{
		$this->startcolour = $this->allocateColour($start_colour);
		$this->endcolour = $this->allocateColour($end_colour);

		// Get the total number of columns we are going to plot
		$this->columns  = count($values);
		
		// Set the amount of space between each column
		
		// Get the width of 1 column
		
		$this->column_width = $this->gridwidth / $this->columns ;

		$this->maxv = 0;
		
		// Calculate the maximum value we are going to plot (if not set externally)
		if (!$this->maxYScale)
		{
			for($i=0;$i<$this->columns;$i++)
			{
				$this->maxv = max($values[$i],$this->maxv);
			}	
		} else {
			$this->maxv = $this->maxYScale;// - $this->miny;
		}

		// Now plot each column
		for($i=0; $i < $this->columns; $i++)
	    {
	        if ($this->maxv > 0)
	        {
	    		$column_height = (($this->gridheight - $this->miny) / 100) * (( $values[$i] / ($this->maxv)) *100);
	        }
	      
	        $x1 = ($i * $this->column_width) + $this->minx + ($this->padding/2);
	        $y1 = $this->gridheight - $column_height;
	        $x2 = ((($i+1) * $this->column_width) - $this->padding) + $this->minx + ($this->padding/2);
	        $y2 = $this->gridheight; // + $this->miny; // bottom y point
	        
			//$length = abs($y2-$y1);
			
			if ($this->gradient)
			{
				$this->fillGradientRectangle($x1, $y1, $x2, $y2, $start_colour, $end_colour);
			} else {
				imagefilledrectangle($this->image,$x1,$y1,$x2,$y2,$this->startcolour);
			}
			
			// This part is just for 3D effect
	
	        //imageline($this->image,$x1,$y1,$x1,$y2,$this->gray_lite);
	        //imageline($this->image,$x1,$y2,$x2,$y2,$this->gray_lite);
	        //imageline($this->image,$x2,$y1,$x2,$y2,$this->gray_dark);

	        if ($this->debug) echo "<br/>column width: ".$this->column_width;
			if ($this->debug) echo "<br/>columns: ".$this->columns;
			if ($this->debug) echo '<table border=1><tr><td>'.$x1.'</td><td>'.$y1.'</td></tr>';
			if ($this->debug) echo '<tr><td>'.$x2.'</td><td>'.$y2.'</td></tr></table>';
        
	    }
	}	

	function addXLabels($values, $textcolour = 'ffffff')
	{
		for($i=0; $i < count($values); $i++)
	    {
	    	$this->createText($values[$i], $textcolour, ($this->gridwidth / count($values))*$i + $this->minx + ($this->padding/2), $this->gridheight + $this->miny);
	    }
	}
	
	function addYScale($textcolour = 'ffffff')
	{
		imagesetthickness ($this->image, 1);
		imagerectangle($this->image,$this->minx,$this->miny,$this->maxx,$this->maxy - $this->miny,$this->gray);
		
		$this->gridyConverted = ($this->gridheight-$this->miny) / ($this->maxYScale / $this->gridy);

		$i=0;
		
		for($y=$this->miny; $y <= $this->maxy; $y+=$this->gridyConverted)
	    {
	    	$y_converted = $this->maxy - $y;// + $this->miny;
	    	
	    	// grid lines
	    	$style = array($this->black, $this->white, $this->white, $this->white, $this->white); //, $red, $w, $w, $w, $w, $w);
			imagesetstyle($this->image_yscales, $style);
	    	imageline( $this->image_yscales, $this->minx, $y_converted, $this->maxx, $y_converted, IMG_COLOR_STYLED);

	    	// y axis lines
	    	imageline( $this->image_yscales, $this->minx+5, $y_converted, $this->minx-5, $y_converted, $this->gray );
	    	imageline( $this->image_yscales, $this->maxx+5, $y_converted, $this->maxx-5, $y_converted, $this->gray );
	    	
	    	$this->createText($i, $textcolour, $this->minx -30, $y_converted-5);
	    	$i += $this->gridy;

	    }
	    
	    // add minor lines
		for($y=$this->miny; $y < $this->maxy; $y+=$this->gridyConverted/5)
	    {
			$y_converted = $this->maxy - $y;
	    	imageline( $this->image_yscales, $this->minx+2, $y_converted, $this->minx-2, $y_converted, $this->gray );
	    	imageline( $this->image_yscales, $this->maxx+2, $y_converted, $this->maxx-2, $y_converted, $this->gray );		
	    }
	}
		
	function createText($text, $textcolour, $x=0, $y=0)
	{
		$colour = $this->allocateColour($textcolour);
		$bgcolour = $this->allocateColour('ffffff');
		$font="arial.ttf";
		$textsize=10;
		$margin=0;
		$size = @imagettfbbox ( ceil($textsize) * 3, 0, $font, $text);
		$width = abs($size[0]) + abs($size[2]) + $margin * 3;
		$height = abs($size[1]) + abs($size[5]) + $margin * 3;
		$inputimage = imagecreatetruecolor($width, $height);
		imagefill ( $inputimage, 0, 0, $bgcolour );
		imagettftext($inputimage, ($textsize * 3), 0, $margin/2, ($textsize * 3) + ($margin/2), $colour, $font, $text);
		$outputimage = imagecreatetruecolor($width/3, $height/3);
		imagecopyresampled($outputimage, $inputimage, 0, 0, 0, 0, $width/3, $height/3, $width, $height);
	    
		imagecopy($this->image, $outputimage, $x, $y, 0, 0, $width/3, $height/3);
		
		//imagepng($inputimage,'output.png');
	    //echo '<img src="output.png"/>';
	    //exit;
	}
	
	function allocateColour($colour)
	{
		# create text colour
		$hexcolour = chunk_split(str_replace("#","",$colour), 2,":");
		$hexcolour = explode(":",$hexcolour);
		
		$bincolour[0] = hexdec("0x{$hexcolour[0]}");
		$bincolour[1] = hexdec("0x{$hexcolour[1]}");
		$bincolour[2] = hexdec("0x{$hexcolour[2]}");
		
		return imagecolorallocate($this->image, $bincolour[0], $bincolour[1], $bincolour[2]);
	}
	
	function fillGradientRectangle($x1, $y1, $x2, $y2, $end_colour, $start_colour )
	{
		// define colour for these bars
		// set first colour
		$hexcolour = chunk_split($start_colour, 2,":");
		$hexcolour = explode(":",$hexcolour);
		
		$startcolour[0] = hexdec("0x{$hexcolour[0]}");
		$startcolour[1] = hexdec("0x{$hexcolour[1]}");
		$startcolour[2] = hexdec("0x{$hexcolour[2]}");
		
		# set end colour
		$hexcolour = chunk_split($end_colour, 2,":");
		$hexcolour = explode(":",$hexcolour);
		$endcolour[0] = hexdec("0x{$hexcolour[0]}");
		$endcolour[1] = hexdec("0x{$hexcolour[1]}");
		$endcolour[2] = hexdec("0x{$hexcolour[2]}");
		
		// gradient fill
		for($grad=0; $grad < ($x2-$x1); $grad++)
		{
			$thiscolour = imagecolorallocate($this->image, 
							abs( ( ( ($startcolour[0] - $endcolour[0]) / ($x2-$x1) ) * $grad ) - $startcolour[0] ), 
							abs( ( ( ($startcolour[1] - $endcolour[1]) / ($x2-$x1) ) * $grad ) - $startcolour[1] ), 
							abs( ( ( ($startcolour[2] - $endcolour[2]) / ($x2-$x1) ) * $grad ) - $startcolour[2] )
							);
			
			//imageline ( $im, 0, $grad, $width, $i, $thiscolour );
			imageline ( $this->image, $x1+$grad, $y1, $x1+$grad, $y2, $thiscolour );
			
		}
		
		imagerectangle($this->image,$x1,$y1,$x2,$y2,$this->startcolour);
	}
	
	function addLines($values, $colour = '000000')
	{
		imagesetthickness ($this->image, 2);
		
		$x = array();
		$y = $values;
		
		for ($yi=0; $yi<count($y); $yi++)
		{
			$y[$yi] = $this->gridheight - $y[$yi];// - $this->miny;
		}

		for ($xi=0; $xi<$this->gridwidth; $xi+=$this->gridwidth/count($values))
		{
			$x[] = $xi;
		}
		
		
		for($i=1; $i<count($y); $i++)
		{
			imageline($this->image, $x[$i-1] + $this->minx, $y[$i-1], $x[$i] + $this->minx, $y[$i], $this->allocateColour($colour));
		}		
	}
	
	function drawBezierLine($p,$steps)
	{
		$t = 1 / $steps;
		$temp = $t * $t;
		$ret = array();
		$f = $p[0];
		$fd = 3 * ($p[1] - $p[0]) * $t;
		$fdd_per_2=3*($p[0]-2*$p[1]+$p[2])*$temp;
		$fddd_per_2=3*(3*($p[1]-$p[2])+$p[3]-$p[0])*$temp*$t;
		$fddd = $fddd_per_2 + $fddd_per_2;
		$fdd = $fdd_per_2 + $fdd_per_2;
		$fddd_per_6 = $fddd_per_2 * (1.0 / 3);
		for ($loop=0; $loop<$steps; $loop++) {
			array_push($ret,$f);
			$f = $f + $fd + $fdd_per_2 + $fddd_per_6;
			$fd = $fd + $fdd + $fddd_per_2;
			$fdd = $fdd + $fddd;
			$fdd_per_2 = $fdd_per_2 + $fddd_per_2;
		}
		return $ret;
	}
}