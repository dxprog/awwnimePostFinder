<?php

require('config.php');
require('lib/db.php');
require('lib/awwnime.php');

Lib\Db::Connect('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS);

$url = isset($_GET['url']) ? $_GET['url'] : false;

$out = null;

$id = uniqid();
$fileName = 'cache/' . $id;

if ($url) {

	$out = new stdClass;
	$out->original = $url;

	$result = Lib\Awwnime::downloadImage($url, $fileName);
	if ($result) {
		$out->matches = Lib\Awwnime::findSimilarImages($fileName, 4);
		unlink($fileName);
	}
	
	$callback = isset($_GET['callback']) ? $_GET['callback'] : false;
	$out = $callback ? $callback . '(' . json_encode($out) . ');' : json_encode($out);
	header('Content-Type: text/javascript');
	echo $out;
	
} else if (count($_FILES) > 0) {
	if (is_uploaded_file($_FILES['txtFile']['tmp_name'])) {
		if (move_uploaded_file($_FILES['txtFile']['tmp_name'], $fileName)) {
			
			$info = Lib\Awwnime::getImageType($fileName);
			if ($info) {
				
				// Rename the uploaded file to have the correct extension to play nice with browsers that may be stupid
				$ext = $info == 'image/jpeg' ? '.jpg' : ($info == 'image/png' ? '.png' : '.gif');
				rename($fileName, $fileName . $ext);
				$fileName .= $ext;
				
				$out = new stdClass;
				$out->matches = Lib\Awwnime::findSimilarImages($fileName, 4);
				$out->original = $fileName;
				
				echo '<script type="text/javascript">window.parent.resultsCallback(' . json_encode($out) . ')</script>';
				
			}
		}
	}

}