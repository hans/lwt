<?php

/**************************************************************
"Learning with Texts" (LWT) is free and unencumbered software 
released into the PUBLIC DOMAIN.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a
compiled binary, for any purpose, commercial or non-commercial,
and by any means.

In jurisdictions that recognize copyright laws, the author or
authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this
dedication for the benefit of the public at large and to the 
detriment of our heirs and successors. We intend this 
dedication to be an overt act of relinquishment in perpetuity
of all present and future rights to this software under
copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.

For more information, please refer to [http://unlicense.org/].
***************************************************************/

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' );

if(isset($_REQUEST['url']) and isset($_REQUEST['woid'])){
$url = $_REQUEST['url'];
$q = $_REQUEST['woid'];

if($url=='DEL'){
	$i = get_first_value('select ImID as value from ' . $tbpref . 'images where ImWoID = ' . $q);
	if(isset($i)){
		$filename = './thumbnails/' . $tbpref . 'thumbs/' . $i . '.jpg';
		if(file_exists($filename)){
			unlink($filename);
		}
		do_mysqli_query( 'delete from ' . $tbpref . 'images where ImID = ' . $i);
		adjust_autoincr('images','ImID');
	}
	echo '{"ImWoID":"',$q,'"}';
}
else{
	do_mysqli_query( 'INSERT IGNORE INTO ' . $tbpref . 'images (ImWoID) VALUES ('.$q.')');
	$i = get_first_value('select ImID as value from ' . $tbpref . 'images where ImWoID = ' . $q);
	$path = './thumbnails/' . $tbpref . 'thumbs';
	$filename = $path . '/' . $i . '.jpg';
		if(is_callable('curl_init')){ //use curl if exists
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$imgfile = curl_exec($ch);
			curl_close ($ch);
		}
		else{
			$ctx = stream_context_create(array("http"=>array("method"=>"GET","header"=>"Referer: \r\n")));
			$imgfile = file_get_contents($url, false, $ctx);
		}
	
		if(!empty($imgfile)){
			if(!is_dir($path)){
				if(!is_dir('./thumbnails')){
					mkdir('./thumbnails',0777);
				}
				mkdir($path,0777);
			}
			if(file_exists($filename)){
				unlink($filename);
			}
			$file = fopen($filename,"wb");
			fwrite($file,$imgfile);
			fclose($file);
			chmod($filename, 0777);
		}
		echo '{"ImID":"',$i,'","ImWoID":"',$q,'"}';
	}
}
?>
