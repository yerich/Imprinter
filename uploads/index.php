<?php
/**
 * Create a thumbnail
 *
 * @author Brett @ Mr PHP
 */

include("../include/init.php");
 
// define allowed image sizes
$sizes = array(
    '157x153',
    '600x600',
);

// ensure there was a thumb in the URL
if (!$_GET['thumb']) {
    error('no thumb');
}

// get the thumbnail from the URL
$thumb = strip_tags(htmlspecialchars($_GET['thumb']));

// get the image and size
$thumb_array = explode('/',$thumb);
foreach($thumb_array as $value) {   //Don't allow thumbnails of thumbnails
    if($value == "thumb")
        error("Invalid path: $thumb not found");
}

array_shift($thumb_array);
$size = $thumb_array[0];
array_shift($thumb_array);
$image = WEB_ROOT.'/uploads/'.implode('/',$thumb_array);
list($width,$height) = explode('x',$size);

// ensure the size is valid
if ($width < 1 || $height < 1 || $width > 1980 || $height > 1980) {
    error("Invalid size: $width by $height");
}

// ensure the image file exists
if (!file_exists($image)) {
    error("No source image: $image not found");
}

// generate the thumbnail
require(WEB_ROOT.'/include/phpthumb/phpthumb.class.php');
$phpThumb = new phpThumb();
$phpThumb->setSourceFilename($image);
$phpThumb->setParameter('w',$width);
$phpThumb->setParameter('h',$height);
$phpThumb->setParameter('f',substr($thumb,-3,3)); // set the output format
//$phpThumb->setParameter('far','C'); // scale outside
//$phpThumb->setParameter('bg','FFFFFF'); // scale outside
if (!$phpThumb->GenerateThumbnail()) {
    error('cannot generate thumbnail');
}

// make the directory to put the image
if (!mkpath(dirname($thumb),true)) {
        error('cannot create directory');
}

// write the file
if (!$phpThumb->RenderToFile(WEB_ROOT."/uploads/$thumb")) {
    error("Cannot save thumbnail to ".WEB_ROOT."/uploads/$thumb");
}

// redirect to the thumb
// note: you need the '?new' or IE wont do a redirect
header('Location: '.dirname($_SERVER['SCRIPT_NAME']).'/'.$thumb.'?new');

// basic error handling
function error($error) {
    trigger_error($error);
    exit();
}
//recursive dir function
function mkpath($path, $mode){
    is_dir(dirname($path)) || mkpath(dirname($path), $mode);
    return is_dir($path) || @mkdir($path,0777,$mode);
}
