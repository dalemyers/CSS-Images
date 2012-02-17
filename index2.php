<?php
		$md5 = md5('');
		$image = imagecreatefrompng('MCQR2.png');
		$width = imagesx($image);
		$height = imagesy($image);
		$html = '';
		
		for($i=0;$i<$height;$i++){
			for($j=0;$j<$width;$j++){
				//The next block gets the RGBA values for the current pixel at location ($j,$i)
				$rgb = imagecolorat($image, $j, $i);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$r = dechex($r) . '';
				$g = dechex($g) . '';
				$b = dechex($b) . '';
				$r = ((strlen($r) == 1) ? ('0' . $r) : $r);  //Padding with a 0 if it needs it
				$g = ((strlen($g) == 1) ? ('0' . $g) : $g);
				$b = ((strlen($b) == 1) ? ('0' . $b) : $b);
				$hexcolour = '#' . $r . $g . $b;
				if($hexcolour != '#000001'){
					echo '0';
				} else {
					echo '1';
				}
				
			}
			echo "\n";
		}
		
?>