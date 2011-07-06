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
		$html .= '<table cellpadding="0" cellspacing="0">' . "\n";
		//This $html variable stores the HTML so that we can write to a file periodically.
		//If we don't do this then we are likely to run into memory issues.
		for($i=0;$i<$height;$i++){
			$html .= "\t<tr height=\"1\">\n";
			for($j=0;$j<$width;$j++){
				//The next block gets the RGBA values for the current pixel at location ($j,$i)
				$rgb = imagecolorat($image, $j, $i);
				$alpha = ($rgb & 0x7F000000) >> 24;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$r = dechex($r);
				$g = dechex($g);
				$b = dechex($b);
				$r = ((strlen($r) == 1) ? ('0' . $r) : $r);  //Padding with a 0 if it needs it
				$g = ((strlen($g) == 1) ? ('0' . $g) : $g);
				$b = ((strlen($b) == 1) ? ('0' . $b) : $b);
				$html .= "\t\t" . '<td width="1" bgcolor="#' . $r . $g . $b . '" ';
				$alpha = 127 - $alpha;
				$alpha = $alpha / 127.0;
				$html .= 'style="opacity:' . $alpha . ';filter:alpha(opacity=' . $alpha * 100 . '"'; //applies the transparency attributes
				$html .= '></td>' . "\n";
			}
			$html .= "\t</tr>\n";
			file_put_contents($filename, $html, FILE_APPEND | LOCK_EX); //We must lock the file
			$html = '';
		}
		$html .= '</table>';
		file_put_contents($filename, $html, FILE_APPEND | LOCK_EX);
	}
	//The file now exists no matter what.
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/cssimage/' . $filename);
?>