<?php
	//If the file has been previously generated before then we don't bother regenerating.
	//We check by md5-ing the file and checking to see if a file with that name exists.
	$extension = '.png';
	$md5 = md5_file($_GET['url']);
	$filename = $md5.'.html';
	if(!file_exists($filename)){
		//The file doesn't already exist so here we go with the generation.
		$image = imagecreatefrompng($_GET['url']);
		$width = imagesx($image);
		$height = imagesy($image);
		$debug = "Image Name: $filename \n Image Width: $width \n Image Height: $height \n";
		//This $html variable stores the HTML so that we can write to a file periodically.
		//If we don't do this then we are likely to run into memory issues.
		$html = '';
		$prev = '';
		$hasAlpha = false;
		for($i=0;$i<$height;$i++){
			for($j=0;$j<$width;$j++){
				$rgb = imagecolorat($image, $j, $i);
				$alpha = ($rgb & 0x7F000000) >> 24;
				$alpha = 127 - $alpha;
				$alpha = $alpha / 127.0;
				if($alpha != 1){
					$hasAlpha = true;
					break;
				}
			}
		}
		
		for($i=0;$i<$height;$i++){
			$html .= "<table cellpadding=\"0\" cellspacing=\"0\">\n\t<tr height=\"1\">\n";
			$prevColour = '';
			$prevAlpha = -1;
			$cellCounter = 0;
			for($j=0;$j<$width;$j++){
				//The next block gets the RGBA values for the current pixel at location ($j,$i)
				$rgb = imagecolorat($image, $j, $i);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$r = dechex($r);
				$g = dechex($g);
				$b = dechex($b);
				$r = ((strlen($r) == 1) ? ('0' . $r) : $r);  //Padding with a 0 if it needs it
				$g = ((strlen($g) == 1) ? ('0' . $g) : $g);
				$b = ((strlen($b) == 1) ? ('0' . $b) : $b);
				$hexcolour = '#' . $r . $g . $b;
				$alpha = ($rgb & 0x7F000000) >> 24;
				$alpha = 127 - $alpha;
				$alpha = $alpha / 127.0;
				$debug .= "($i,$j) - $hexcolour - $alpha - $cellCounter\n";
				if($j == 0){
					$prevAlpha = $alpha;
					$prevColour = $hexcolour;
				}
				$cellHTML = "\t\t" . '<td width="1" bgcolor="#" style="opacity"></td>' . "\n";
				if(($prevColour == $hexcolour) & ($prevAlpha == $alpha)){
					$cellCounter++;
					if($j==($width - 1)){
						$cellHTML = str_replace('<td width="1','<td width="' . $cellCounter,$cellHTML);
						$cellHTML = str_replace('bgcolor="#','bgcolor="' . $prevColour,$cellHTML);
						if($hasAlpha){
							$cellHTML = str_replace('style="opacity','style="opacity:' . $prevAlpha . ';filter:alpha(opacity=' . $prevAlpha * 100 . ')',$cellHTML);
						} else {
							$cellHTML = str_replace(' style="opacity"','',$cellHTML);
						}
						$html .= $cellHTML;
						$cellCounter = 0;
						$prevAlpha = $alpha;
						$prevColour = $hexcolour;
					}		
				} else {
					$cellHTML = str_replace('<td width="1','<td width="' . ($cellCounter),$cellHTML);
					$cellHTML = str_replace('bgcolor="#','bgcolor="' . $prevColour,$cellHTML);
					if($hasAlpha){
						$cellHTML = str_replace('style="opacity','style="opacity:' . $prevAlpha . ';filter:alpha(opacity=' . $prevAlpha * 100 . ')',$cellHTML);
					} else {
						$cellHTML = str_replace(' style="opacity"','',$cellHTML);
					}
					$html .= $cellHTML;
					$cellCounter = 1;
					$prevAlpha = $alpha;
					$prevColour = $hexcolour;
				}
			}
			$html .= "\t</tr>\n</table>\n";
			if($html == $prev) {
				$counter++;
				if($i==($height - 1)){
					$prev = str_replace('<tr height="1','<tr height="' . ($counter + 1),$prev);
					file_put_contents($filename, $prev, FILE_APPEND | LOCK_EX);
				}
			} else {
				$prev = str_replace('<tr height="1','<tr height="' . ($counter+1),$prev);
				file_put_contents($filename, $prev, FILE_APPEND | LOCK_EX);
				$prev = $html;
				$counter = 0;
			}
			$html = '';
		}
	}
	//file_put_contents('debug.log',$debug, FILE_APPEND | LOCK_EX);
	//The file now exists no matter what.
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/cssimages/' . $filename);
?>