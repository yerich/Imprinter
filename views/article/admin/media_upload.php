<?php
/**
 * This script handles media uploads for articles, via the fileupload jquery plugin
 * 
 * @author rye
 * @package Imprinter
 */
include("../../include/init.php");
checkUserLogin($user);

foreach($_FILES as $key=>$value) {
	foreach($value as $k => $v) {
		$_FILES[$key][$k] = $v[0];
	}
}
$uploadedFiles = UploadedFilesFactory::getUploadedFiles();

$uploadedFiles->filterFiles(array("jpg", "bmp", "png", "gif", "jpeg"), 50000000);
if(!is_dir(WEB_ROOT."/uploads/".date("Y"))) mkdir(WEB_ROOT."/uploads/".date("Y"));
if(!is_dir(WEB_ROOT."/uploads/".date("Y")."/".date("m"))) mkdir(WEB_ROOT."/uploads/".date("Y")."/".date("m"));

$uploadedFiles->saveUploadedFiles("uploads/".date("Y")."/".date("m")."/");
$savedFiles = $uploadedFiles->getSaved();



$jsonValue = array();
foreach($savedFiles as $value) {
	$fileJson = array();
	$fileJson['name'] = $value['name'];
	$fileJson['size'] = $value['size'];
	$fileJson['url'] = $value['url'];
	$fileJson['delete_url'] = "/admin/deleteMedia.php?file=".urlencode($value['url']);
	$fileJson['delete_type'] = "DELETE";
	$fileJson['origname'] = $value['origname'];
	$fileJson['unique'] = $_POST['unique'];
	$jsonValue[] = $fileJson;
}
echo json_encode($jsonValue);
die();
