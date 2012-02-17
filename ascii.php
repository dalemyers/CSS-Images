<?php
//echo '<head><style> body{ line-height:1px; font-size:1px; font-family: "Courier New"}</style>';

$image = $_GET['url']; 
$img = imagecreatefrompng($image); 
$height = imagesy($img);
if($height > 75){
	$img = resizeToHeight(75,$img);
}
$width = imagesx($img); 
$height = imagesy($img); 
//header('Content-Type: image/png');
//imagepng($img);
//echo "<title>$height, $width</title></head>";

for($h=0;$h<$height;$h++){ 
    for($w=0;$w<=$width;$w++){ 
        $rgb = ImageColorAt($img, $w, $h); 
        $r = ($rgb >> 16) & 0xFF; 
        $g = ($rgb >> 8) & 0xFF; 
        $b = $rgb & 0xFF; 
        if($w == $width){ 
            echo '<br>'; 
        }else{ 
           echo '<span style="color:rgb('.$r.','.$g.','.$b.');">#</span>';
        } 
    } 
} 


   function resizeToHeight($height,$img) {
 
      $ratio = $height / imagesy($img);
      $width = imagesx($img) * $ratio;
      return resize($width,$height,$img);
   }
 
 
   function resize($width,$height,$img) {
      $new = imagecreatetruecolor($width, $height);
	  imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
      imagealphablending($new, false);
      imagesavealpha($new, true);
      imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, imagesx($img), imagesy($img));
      return $new;
   }      
 

?>