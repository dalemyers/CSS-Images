<?php
	$extension = '.png';
	$md5 = md5_file($_GET['url']);
	$filename = $md5.'.html';
	if(!file_exists($filename)){
		$image = imagecreatefrompng($_GET['url']);
		$width = imagesx($image);
		$height = imagesy($image);
		$html .= '<table cellpadding="0" cellspacing="0">' . "\n";
		for($i=0;$i<$height;$i++){
			$html .= "\t<tr height=\"1\">\n";
			for($j=0;$j<$width;$j++){
				$rgb = imagecolorat($image, $j, $i);
				$alpha = ($rgb & 0x7F000000) >> 24;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$r = dechex($r);
				$g = dechex($g);
				$b = dechex($b);
				$r = ((strlen($r) == 1) ? ('0' . $r) : $r);
				$g = ((strlen($g) == 1) ? ('0' . $g) : $g);
				$b = ((strlen($b) == 1) ? ('0' . $b) : $b);
				$html .= "\t\t" . '<td width="1" bgcolor="#' . $r . $g . $b . '" ';
				$alpha = 127 - $alpha;
				$alpha = $alpha / 127.0;
				$html .= 'style="opacity:' . $alpha . ';filter:alpha(opacity=' . $alpha * 100 . '"';
				$html .= '></td>' . "\n";
			}
			$html .= "\t</tr>\n";
			file_put_contents($filename, $html, FILE_APPEND | LOCK_EX);
			$html = '';
		}
		$html .= '</table>';
		//echo $html;
		file_put_contents($filename, $html, FILE_APPEND | LOCK_EX);
	}
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/cssimage/' . $filename);
?>