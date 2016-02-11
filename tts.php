<?php

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

$q = convert_string_to_sqlsyntax($_GET["q"]);
$tl = convert_string_to_sqlsyntax($_GET["tl"]);
$tts_save = getSettingWithDefault("set-tts");
if($tts_save==1){
	do_mysqli_query('INSERT IGNORE INTO tts (TtsTxt,TtsLc) VALUES ('.$q.','.$tl.')');
}
$ttsid = strval(get_first_value("SELECT TtsID AS value FROM tts where TtsTxt=$q and TtsLc=$tl"));
if(empty($ttsid)){
	$ttsid=$_GET["q"];
}
$path = './tts/'.$_GET["tl"];
$filename = $path .'/'. $ttsid . '.mp3';
if (!file_exists($filename)) {
	if(is_callable('curl_init')){ //use curl if exists
		$txt=htmlspecialchars($_GET['q']);
		$txt=rawurlencode($txt);
		$tl=$_GET["tl"];
		header("Content-type: audio/mpeg");
		$url="http://translate.google.com/translate_tts?ie=utf-8&q=$txt&tl=$tl&client=tw-ob";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$soundfile = curl_exec($ch);
	}
	else{
		$qs = http_build_query(array("ie" => "utf-8","tl" => $_GET["tl"], "q" => $_GET["q"], "client" => "tw-ob"));
		$ctx = stream_context_create(array("http"=>array("method"=>"GET","header"=>"Referer: \r\n")));
		$soundfile = file_get_contents("http://translate.google.com/translate_tts?".$qs, false, $ctx);
	}
	if(!empty($soundfile) && $tts_save==1){
		if(!is_dir($path)){
			if(!is_dir('./tts')){
				mkdir('./tts',0777);
			}
			mkdir($path,0777);
		}
		$file = fopen($filename,"wb");
		fwrite($file,$soundfile);
		fclose($file);
		chmod($filename, 0777);
	}
}
else
	$soundfile = file_get_contents ($filename);
if(!empty($soundfile)){
	header("Content-type: audio/mpeg");
	header("Content-Transfer-Encoding: binary");
	header('Pragma: no-cache');
	header('Expires: 0');

	echo $soundfile;
}
?>
