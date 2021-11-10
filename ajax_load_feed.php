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

require_once 'inc/session_utility.php';

session_write_close();
$imported_feed=0;
$nif=0;
$msg='';
$feed = get_links_from_rss($_POST['NfSourceURI'], get_nf_option($_POST['NfOptions'], 'article_source'));
if(empty($feed)) {
    $msg.= 'Error: Could not load "' .$_POST['NfName']. '" ! ';
    echo "<div class=\"red\"><p> $msg </p></div>";        
}
else{
    $sql = 'INSERT IGNORE INTO ' . $tbpref . 'feedlinks (FlTitle,FlLink,FlText,FlDescription,FlDate,FlAudio,FlNfID) VALUES ';
    $valuesArr = array();
    foreach ($feed as $data){
        $d_title=convert_string_to_sqlsyntax($data['title']);
        $d_link=convert_string_to_sqlsyntax($data['link']);
        $d_text=convert_string_to_sqlsyntax((isset($data['text']))?($data['text']):null);
        $d_desc=convert_string_to_sqlsyntax($data['desc']);
        $d_date=convert_string_to_sqlsyntax($data['date']);
        $d_audio=convert_string_to_sqlsyntax($data['audio']);
        $d_feed=convert_string_to_sqlsyntax($_POST['NfID']);
        $valuesArr[] = "($d_title,$d_link,$d_text,$d_desc,$d_date,$d_audio,$d_feed)";
    }
    $sql .= implode(',', $valuesArr);
    do_mysqli_query($sql);
    $imported_feed=mysqli_affected_rows($GLOBALS["DBCONNECTION"]);
    $nif=count($valuesArr)-$imported_feed;
    unset($valuesArr);
    do_mysqli_query('UPDATE ' . $tbpref . 'newsfeeds SET NfUpdate="'.time().'" where NfID='.$_POST['NfID']);
    $nf_max_links=null;
    $nf_max_links=get_nf_option($_POST['NfOptions'], 'max_links');
    if(!$nf_max_links) {
        if (get_nf_option($_POST['NfOptions'], 'article_source')) {
            $nf_max_links=getSettingWithDefault('set-max-articles-with-text');
        }
        else { $nf_max_links=getSettingWithDefault('set-max-articles-without-text'); 
        }
    }
    if(!$imported_feed) { $imported_feed="no"; 
    }
    $msg= $_POST['NfName'] . ": $imported_feed new article";
    if($imported_feed>1) { $msg.= "s"; 
    }
    $msg.= " imported";
    if($nif>1) { $msg.= ", $nif articles are dublicates"; 
    }
    if($nif==1) { $msg.= ", $nif dublicated article"; 
    }
    $result=do_mysqli_query("SELECT COUNT(*) AS total FROM " . $tbpref . "feedlinks WHERE FlNfID in (".$_POST['NfID'].")");
    $row = mysqli_fetch_assoc($result);
    $to=($row['total']-$nf_max_links);
    if($to>0) {
        do_mysqli_query("DELETE FROM " . $tbpref . "feedlinks WHERE FlNfID in (".$_POST['NfID'].") ORDER BY FlDate LIMIT $to");
        $msg.= ", $to old article(s) deleted";
    }
    echo "<div class=\"msgblue\"><p> $msg </p></div>";
}
session_start();
$_SESSION['feed_loaded'][$_POST['cnt']]=$msg;
session_write_close();
?>
