<?php

/**************************************************************
Call: upload_words.php?....
      ... op=Import ... do the import 
Import terms from file or Text area
***************************************************************/

require_once 'inc/session_utility.php';

function my_str_getcsv($input) 
{
    $temp=fopen("php://memory", "rw");
    fwrite($temp, $input);
    fseek($temp, 0);
    $data = fgetcsv($temp);
    fclose($temp);
    return $data;
}

function notempty($var) 
{
    return(trim($var) != '');
}

function limit20(&$item, $key) 
{
    $item = mb_substr($item, 0, 20);
}

function savetag($item, $key, $wid) 
{
    global $tbpref;
    if(! in_array($item, $_SESSION['TAGS'])) {
        runsql('insert into ' . $tbpref . 'tags (TgText) values(' . convert_string_to_sqlsyntax($item) . ')', "");
        get_tags($refresh = 1);
    }
    runsql('insert ignore into ' . $tbpref . 'wordtags (WtWoID, WtTgID) select ' . $wid . ', TgID from ' . $tbpref . 'tags where TgText = ' . convert_string_to_sqlsyntax($item), "");
}

pagestart('Import Terms', true);
$message = '';

// Import

if (isset($_REQUEST['op'])) {
    
    // INSERT
    
    if ($_REQUEST['op'] == 'Import') {
        $overwrite = $_REQUEST["Over"];
        $tabs = $_REQUEST["Tab"];
        $lang = $_REQUEST["LgID"];
        $status = $_REQUEST["WoStatus"];
        $sql = "select * from " . $tbpref . "languages where LgID=" . $lang;
        $res = do_mysqli_query($sql);
        $record = mysqli_fetch_assoc($res);
        $termchar = $record['LgRegexpWordCharacters'];
        $splitEachChar = $record['LgSplitEachChar'];
        $removeSpaces = $record["LgRemoveSpaces"];
        $rtl = $record['LgRightToLeft'];
        $last_update = get_first_value("select max(WoStatusChanged) as value from " . $tbpref . "words");

        $col[1] = $_REQUEST["Col1"];
        $col[2] = $_REQUEST["Col2"];
        $col[3] = $_REQUEST["Col3"];
        $col[4] = $_REQUEST["Col4"];
        $col[5] = $_REQUEST["Col5"];
        $col=array_unique($col);

        $fields = array("txt"=>0,"tr"=>0,"ro"=>0,"se"=>0,"tl"=>0);
        $file_upl= isset($_FILES["thefile"]) && $_FILES["thefile"]["tmp_name"] != "" && $_FILES["thefile"]["error"] == 0;

        $max = max(array_keys($col));
        for ($j=1; $j<=$max; $j++) {
            if(!isset($col[$j])) { $col[$j]='@dummy'; 
            }
            else{
                switch ($col[$j]){
                case 'w':
                    $col[$j]=$removeSpaces?'@wotext':'WoText';
                    $fields["txt"]=$j;
                    break;
                case 't':
                    $col[$j]='WoTranslation';
                    $fields["tr"]=$j;
                    break;
                case 'r':
                    $col[$j]='WoRomanization';
                    $fields["ro"]=$j;
                    break;
                case 's':
                    $col[$j]='WoSentence';
                    $fields["se"]=$j;
                    break;
                case 'g':
                    $col[$j]='@taglist';
                    $fields["tl"]=$j;
                    break;
                case 'x':
                    if($j==$max) { unset($col[$j]); 
                    }
                    else { $col[$j]='@dummy'; 
                    }
                    break;
                }
            }
        
            /* Not merge from official
            $protokoll = '<h4>Import Report (Language: ' . getLanguage($lang) . ', Status: ' . $status . ')</h4><table class="tab1" cellspacing="0" cellpadding="5"><tr><th class="th1">Line</th><th class="th1">Term</th><th class="th1">Translation</th><th class="th1">Romanization</th><th class="th1">Sentence</th><th class="th1">Tag List</th><th class="th1">Message</th></tr>';
		
            if ( isset($_FILES["thefile"]) && $_FILES["thefile"]["tmp_name"] != "" && $_FILES["thefile"]["error"] == 0 ) {
            $lines = file($_FILES["thefile"]["tmp_name"], FILE_IGNORE_NEW_LINES);
            $l = count($lines);
            for ($i=0; $i<$l; $i++) {
            $lines[$i] = replace_supp_unicode_planes_char($lines[$i]);
            }
            } 
            else {
            $lines = explode("\n",replace_supp_unicode_planes_char(
            prepare_textdata($_REQUEST["Upload"])));
            */
        }
        if ($fields["txt"]>0) {
            $columns='(' . rtrim(implode(',', $col), ',') . ')';
            if ($tabs == 'h') {
                $tabs = ' FIELDS TERMINATED BY \'#\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' '; 
            }
            elseif ($tabs == 'c') { 
                $tabs = ' FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' ';
            } else {
                $tabs = ' FIELDS TERMINATED BY \'\\t\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' '; 
            }
            if ($_REQUEST["IgnFirstLine"] == '1') { $tabs.='IGNORE 1 LINES '; 
            }
            if ($file_upl ) { $file_name= $_FILES["thefile"]["tmp_name"]; 
            }
            else{
                $file_name = tempnam(sys_get_temp_dir(), "LWT");
                $temp = fopen($file_name, "w");
                fwrite($temp, prepare_textdata($_REQUEST["Upload"]));
                fseek($temp, 0);
                fclose($temp);
            }
            $sql= 'LOAD DATA LOCAL INFILE '. convert_string_to_sqlsyntax($file_name);
            //$sql.= ($overwrite)?' REPLACE':(' IGNORE') ;
            if($fields["tl"]==0 and $overwrite==0) {
                $sql.= ' IGNORE INTO TABLE ' . $tbpref . 'words ' . $tabs . $columns ;
                $sql.= ' SET WoLgID =  ' . $lang . ', ' . ($removeSpaces?'WoTextLC = LOWER(REPLACE(@wotext," ","")),WoText = REPLACE(@wotext," ","")':'WoTextLC = LOWER(WoText)') . ', WoStatus = ' . $status . ', WoStatusChanged = NOW(), ' . make_score_random_insert_update('u');
                runsql($sql, '');
            }
            else{
                runsql('SET GLOBAL max_heap_table_size = 1024 * 1024 * 1024 * 2', '');
                runsql('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $tbpref . 'numbers( n  tinyint(3) unsigned NOT NULL)', '');
                runsql("INSERT IGNORE INTO " . $tbpref . "numbers(n) VALUES ('1'),('2'),('3'),('4'),('5'),('6'),('7'),('8'),('9')", '');
                $sql.= ' INTO TABLE ' . $tbpref . 'tempwords ' . $tabs . $columns . ' SET ' . ($removeSpaces?'WoTextLC = LOWER(REPLACE(@wotext," ","")), WoText = REPLACE(@wotext," ","")':'WoTextLC = LOWER(WoText)');
                if($fields["tl"]!=0) { $sql.= ', WoTaglist = REPLACE(@taglist," ",",")'; 
                }
                runsql($sql, '');
                //*//
                if($overwrite>3) {
                    runsql('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $tbpref . 'merge_words(MID mediumint(8) unsigned NOT NULL AUTO_INCREMENT, MText  varchar(250) NOT NULL,  MTranslation  varchar(250) NOT NULL, PRIMARY KEY (MID), UNIQUE KEY (MText, MTranslation) ) DEFAULT CHARSET=utf8', '');

                    $wosep = getSettingWithDefault('set-term-translation-delimiters');
                    if(empty($wosep)) {
                        if ($tabs == 'h') { $wosep[0]="#"; 
                        }
                        elseif ($tabs == 'c') { $wosep[0]=",";
                        } else { $wosep[0]="\t"; 
                        }
                    }
                    $seplen = mb_strlen($wosep, 'UTF-8');
                    $WoTrRepl = $tbpref . 'words.WoTranslation';
                    for($i=1;$i<$seplen;$i++){
                        $WoTrRepl = 'REPLACE(' . $WoTrRepl . ', ' . convert_string_to_sqlsyntax($wosep[$i]) . ', ' . convert_string_to_sqlsyntax($wosep[0]) . ')';
                    }

                    runsql('insert ignore into ' . $tbpref . 'merge_words(MText,MTranslation) SELECT b.WoTextLC, trim(SUBSTRING_INDEX(SUBSTRING_INDEX(b.WoTranslation, ' . convert_string_to_sqlsyntax($wosep[0]) . ', ' . $tbpref . 'numbers.n), ' . convert_string_to_sqlsyntax($wosep[0]) . ', -1)) name FROM ' . $tbpref . 'numbers INNER JOIN (select ' . $tbpref . 'words.WoTextLC as WoTextLC, ' . $WoTrRepl . ' as WoTranslation from ' . $tbpref . 'tempwords left join ' . $tbpref . 'words ON ' . $tbpref . 'words.WoTextLC = ' . $tbpref . 'tempwords.WoTextLC and ' . $tbpref . 'words.WoTranslation != \'*\' and ' . $tbpref . 'words.WoLgID = ' . $lang . ') b on CHAR_LENGTH(b.WoTranslation)-CHAR_LENGTH(REPLACE(b.WoTranslation, ' . convert_string_to_sqlsyntax($wosep[0]) . ', \'\'))>=' . $tbpref . 'numbers.n-1 ORDER BY b.WoTextLC, n', '');

                    $tesep = $_REQUEST["transl_delim"];
                    if(empty($tesep)) {
                        if ($tabs == 'h') { $tesep[0]="#"; 
                        }
                        elseif ($tabs == 'c') { $tesep[0]=",";
                        } else { $tesep[0]="\t"; 
                        }
                    }

                    $seplen = mb_strlen($tesep, 'UTF-8');
                    $WoTrRepl = $tbpref . 'tempwords.WoTranslation';
                    for($i=1;$i<$seplen;$i++){
                        $WoTrRepl = 'REPLACE(' . $WoTrRepl . ', ' . convert_string_to_sqlsyntax($tesep[$i]) . ', ' . convert_string_to_sqlsyntax($tesep[0]) . ')';
                    }

                    runsql('insert ignore into ' . $tbpref . 'merge_words(MText,MTranslation) SELECT ' . $tbpref . 'tempwords.WoTextLC, trim(SUBSTRING_INDEX(SUBSTRING_INDEX(' . $WoTrRepl . ',' . convert_string_to_sqlsyntax($tesep[0]) . ' , ' . $tbpref . 'numbers.n), ' . convert_string_to_sqlsyntax($tesep[0]) . ', -1)) name FROM ' . $tbpref . 'numbers INNER JOIN ' . $tbpref . 'tempwords on CHAR_LENGTH(' . $tbpref . 'tempwords.WoTranslation)-CHAR_LENGTH(REPLACE(' . $WoTrRepl . ', ' . convert_string_to_sqlsyntax($tesep[0]) . ', \'\'))>=' . $tbpref . 'numbers.n-1 ORDER BY ' . $tbpref . 'tempwords.WoTextLC, n', '');
                    if($wosep[0]==',' or $wosep[0]==';') { $wosep = $wosep[0] . ' '; 
                    }
                    else { $wosep= ' ' . $wosep[0] . ' '; 
                    }
                    runsql('update ' . $tbpref . 'tempwords left join (SELECT MText, GROUP_CONCAT(trim(MTranslation) order by MID separator ' . convert_string_to_sqlsyntax_notrim_nonull($wosep) . ') AS Translation from ' . $tbpref . 'merge_words group by MText ) A on MText=WoTextLC set WoTranslation = Translation', '');
                    runsql('DROP TABLE ' . $tbpref . 'merge_words', '');
                }
                // */
                if($overwrite!=3 and $overwrite!=5) {
                    $sql=($overwrite!=0)?'INSERT ':('INSERT IGNORE ');
                    $sql.= ' INTO ' . $tbpref . 'words (WoTextLC , WoText, WoTranslation, WoRomanization, WoSentence, WoStatus, WoStatusChanged, WoLgID,' .  make_score_random_insert_update('iv')  .') ';
                    $sql.= 'select *, ' . $lang . ' as LgID, ' . make_score_random_insert_update('id') . ' from (select WoTextLC , WoText, WoTranslation, WoRomanization, WoSentence, ' . $status . ' as WoStatus, NOW() as WoStatusChanged from ' . $tbpref . 'tempwords) as tw';
                    //if($overwrite==1)$sql.= ' ON DUPLICATE KEY UPDATE ' . $tbpref . 'words.WoTranslation = tw.WoTranslation, ' . $tbpref . 'words.WoRomanization = tw.WoRomanization, ' . $tbpref . 'words.WoSentence = tw.WoSentence, ' . $tbpref . 'words.WoStatus = tw.WoStatus, ' . $tbpref . 'words.WoStatusChanged = tw.WoStatusChanged';
                    if($overwrite==1 or $overwrite==4) { $sql.= ' ON DUPLICATE KEY UPDATE ' . ($fields["tr"]?$tbpref . 'words.WoTranslation = tw.WoTranslation, ':'') . ($fields["ro"]?$tbpref . 'words.WoRomanization = tw.WoRomanization, ':'') . ($fields["se"]?$tbpref . 'words.WoSentence = tw.WoSentence, ':'') . $tbpref . 'words.WoStatus = tw.WoStatus, ' . $tbpref . 'words.WoStatusChanged = tw.WoStatusChanged'; 
                    }
                    if($overwrite==2) { $sql.= ' ON DUPLICATE KEY UPDATE ' . $tbpref . 'words.WoTranslation = case when ' . $tbpref . 'words.WoTranslation = "*" then tw.WoTranslation else ' . $tbpref . 'words.WoTranslation end, ' . $tbpref . 'words.WoRomanization = case when ' . $tbpref . 'words.WoRomanization IS NULL then tw.WoRomanization else ' . $tbpref . 'words.WoRomanization end, ' . $tbpref . 'words.WoSentence = case when ' . $tbpref . 'words.WoSentence IS NULL then tw.WoSentence else ' . $tbpref . 'words.WoSentence end, ' . $tbpref . 'words.WoStatusChanged = case when ' . $tbpref . 'words.WoSentence IS NULL or ' . $tbpref . 'words.WoRomanization IS NULL or ' . $tbpref . 'words.WoTranslation = "*" then tw.WoStatusChanged else ' . $tbpref . 'words.WoStatusChanged end'; 
                    }


                }
                else{
                    $sql = 'UPDATE ' . $tbpref . 'words AS a JOIN ' . $tbpref . 'tempwords AS b ON a.WoTextLC = b.WoTextLC SET a.WoTranslation = CASE WHEN b.WoTranslation = "" or b.WoTranslation = "*" THEN a.WoTranslation ELSE b.WoTranslation END, a.WoRomanization = CASE WHEN b.WoRomanization IS NULL or b.WoRomanization = "" THEN a.WoRomanization ELSE b.WoRomanization END, a.WoSentence = CASE WHEN b.WoSentence IS NULL or b.WoSentence = "" THEN a.WoSentence ELSE b.WoSentence END, a.WoStatusChanged = CASE WHEN (b.WoTranslation = "" or b.WoTranslation = "*") and (b.WoRomanization IS NULL or b.WoRomanization = "") and (b.WoSentence IS NULL or b.WoSentence = "") THEN a.WoStatusChanged ELSE NOW() END';
                }
                runsql($sql, '');
                if($fields["tl"]!=0) {
                    runsql('insert ignore into ' . $tbpref . 'tags (TgText) select name from (SELECT ' . $tbpref . 'tempwords.WoTextLC, SUBSTRING_INDEX(SUBSTRING_INDEX(' . $tbpref . 'tempwords.WoTaglist, \',\', ' . $tbpref . 'numbers.n), \',\', -1) name FROM ' . $tbpref . 'numbers INNER JOIN ' . $tbpref . 'tempwords ON CHAR_LENGTH(' . $tbpref . 'tempwords.WoTaglist)-CHAR_LENGTH(REPLACE(' . $tbpref . 'tempwords.WoTaglist, \',\', \'\'))>=' . $tbpref . 'numbers.n-1 ORDER BY WoTextLC, n) A', '');
                    runsql('INSERT IGNORE INTO ' . $tbpref . 'wordtags select WoID,TgID from (SELECT ' . $tbpref . 'tempwords.WoTextLC, SUBSTRING_INDEX(SUBSTRING_INDEX(' . $tbpref . 'tempwords.WoTaglist, \',\', ' . $tbpref . 'numbers.n), \',\', -1) name FROM ' . $tbpref . 'numbers INNER JOIN ' . $tbpref . 'tempwords ON CHAR_LENGTH(' . $tbpref . 'tempwords.WoTaglist)-CHAR_LENGTH(REPLACE(' . $tbpref . 'tempwords.WoTaglist, \',\', \'\'))>=' . $tbpref . 'numbers.n-1 ORDER BY WoTextLC, n) A,' . $tbpref . 'tags,' . $tbpref . 'words where name=TgText and A.WoTextLC=' . $tbpref . 'words.WoTextLC and WoLgID=' . $lang, '');
                }
                runsql('DROP TABLE ' . $tbpref . 'numbers', '');
                runsql("truncate " . $tbpref . "tempwords", '');
                if($fields["tl"]!=0) { get_tags(1); 
                }
            }
            if (!$file_upl ) {unlink($file_name);
            }
            set_word_count();
            runsql('UPDATE ' . $tbpref . 'words join ' . $tbpref . 'textitems2 on WoWordCount=1 and Ti2WoID=0 and lower(Ti2Text)=WoTextLC and Ti2LgID = WoLgID SET Ti2WoID=WoID', '');
            $mwords = get_first_value("select count(*) as value from " . $tbpref . "words where WoWordCount>1 and WoCreated > " . convert_string_to_sqlsyntax($last_update));
            if($mwords > 40) {
                runsql(
                    'delete from ' . $tbpref . 'sentences where SeLgID = ' . $lang, 
                    "Sentences deleted"
                );
                runsql(
                    'delete from ' . $tbpref . 'textitems2 where Ti2LgID = ' . $lang, 
                    "Text items deleted"
                );
                adjust_autoincr('sentences', 'SeID');
                $sql = "select TxID, TxText from " . $tbpref . "texts where TxLgID = " . $lang . " order by TxID";
                $res = do_mysqli_query($sql);
                $cntrp = 0;
                while ($record = mysqli_fetch_assoc($res)) {
                    $txtid = $record["TxID"];
                    $txttxt = $record["TxText"];
                    splitCheckText($txttxt, $lang, $txtid);
                    $cntrp++;
                }
                mysqli_free_result($res);
                //$message .= " / Reparsed texts: " . $cntrp;
            }
            elseif($mwords!=0) {
                $sqlarr = array();
                $res = do_mysqli_query("select WoID, WoTextLC, WoWordCount from " . $tbpref . "words where WoWordCount>1 and WoCreated > " . convert_string_to_sqlsyntax($last_update));
                while ($record = mysqli_fetch_assoc($res)) {
                    $len = $record['WoWordCount'];
                    $wid = $record['WoID'];
                    $textlc = $record['WoTextLC'];
                    $sqlarr[] = insertExpressions($textlc, $lang, $wid, $len, 2);
                }
                mysqli_free_result($res);
                $sqlarr = array_filter($sqlarr);
                if(!empty($sqlarr)) {
                    $sqltext = 'INSERT INTO ' . $tbpref . 'textitems2 (Ti2WoID,Ti2LgID,Ti2TxID,Ti2SeID,Ti2Order,Ti2WordCount,Ti2Text) VALUES ';
                    $sqltext .= rtrim(implode(',', $sqlarr), ',');
                    do_mysqli_query($sqltext);
                }
            }
            $recno = get_first_value('select count(*) as value from ' . $tbpref . 'words where WoStatusChanged > ' . convert_string_to_sqlsyntax($last_update));
        ?>
      <form name="form1" action="#" onsubmit="$('#res_data').load('ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','count':$('#recno').text(),'page':document.form1.page.options[document.form1.page.selectedIndex].value}); return false;"><div id="res_data"><table class="tab1"  cellspacing="0" cellpadding="2">
    <?php
    echo "</table></div></form>";
    ?>
   <script type="text/javascript">
   $('#res_data').load('ajax_show_imported_terms.php',{'last_update':'<?php echo $last_update; ?>','rtl':'<?php echo $rtl; ?>','count':'<?php echo $recno; ?>','page':'1'});
</script>
<?php

        }
        else if ($fields["tl"]>0) {

            $columns='';
            for ($j=1; $j<=$fields["tl"]; $j++) {
                $columns.= ($j==1?'(':',') . ($j==$fields["tl"]?'@taglist':'@dummy');
            }
            $columns.= ')';
            if ($tabs == 'h') {
                $tabs = ' FIELDS TERMINATED BY \'#\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' '; 
            }
            elseif ($tabs == 'c') {
                $tabs = ' FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' ';
            } else {
                $tabs = ' FIELDS TERMINATED BY \'\\t\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\' '; 
            }
            if ($_REQUEST["IgnFirstLine"] == '1') { $tabs.='IGNORE 1 LINES '; 
            }
            if ($file_upl ) { $file_name= $_FILES["thefile"]["tmp_name"]; 
            }
            else{
                $file_name = tempnam(sys_get_temp_dir(), "LWT");
                $temp = fopen($file_name, "w");
                fwrite($temp, prepare_textdata($_REQUEST["Upload"]));
                fseek($temp, 0);
                fclose($temp);
            }
            $sql= 'LOAD DATA LOCAL INFILE '. convert_string_to_sqlsyntax($file_name) .' IGNORE INTO TABLE ' . $tbpref . 'tempwords ' . $tabs . $columns . ' SET WoTextLC = REPLACE(@taglist," ",",")';
            runsql($sql, '');
            runsql('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $tbpref . 'numbers( n  tinyint(3) unsigned NOT NULL)', '');
            runsql("INSERT IGNORE INTO " . $tbpref . "numbers(n) VALUES ('1'),('2'),('3'),('4'),('5'),('6'),('7'),('8'),('9')", '');
            runsql('insert ignore into ' . $tbpref . 'tags (TgText) select name from (SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(' . $tbpref . 'tempwords.WoTextLC, \',\', ' . $tbpref . 'numbers.n), \',\', -1) name FROM ' . $tbpref . 'numbers INNER JOIN ' . $tbpref . 'tempwords ON CHAR_LENGTH(' . $tbpref . 'tempwords.WoTextLC)-CHAR_LENGTH(REPLACE(' . $tbpref . 'tempwords.WoTextLC, \',\', \'\'))>=' . $tbpref . 'numbers.n-1 ORDER BY WoTextLC, n) A', '');
            runsql('DROP TABLE ' . $tbpref . 'numbers', '');
            runsql("truncate " . $tbpref . "tempwords", '');
            get_tags(1);
            if (!$file_upl ) {unlink($file_name);
            }
        }
    } // $_REQUEST['op'] == 'Import'
    
    else {
        $message = 'Error: Wrong Operation: ' . $_REQUEST['op'];
        echo error_message_with_hide($message, 0);
    }

} else {

?>

    <form enctype="multipart/form-data" class="validate" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
    <table class="tab3" cellspacing="0" cellpadding="5">
    <tr>
    <td class="td1 center"><b>Language:</b></td>
    <td class="td1">
    <select name="LgID" class="notempty setfocus">
    <?php
    echo get_languages_selectoptions(getSetting('currentlanguage'), '[Choose...]');
    ?>
    </select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /> 
    </td>
    </tr>
    <tr>
    <td class="td1 center"><b>Import Data:</b><br /><br />
    Format per line:<br />
    C1 D C2 D C3 D C4 D C5<br />
    <br /><b>Field Delimiter "D":</b><br />
    <select name="Tab">
    <option value="c" selected="selected">Comma "," [CSV File, LingQ]</option>
    <option value="t">TAB (ASCII 9) [TSV File]</option>
    <option value="h">Hash "#" [Direct Input]</option>
    </select>
    <br />
    <br /><b>Ignore first line</b>: 
    <select name="IgnFirstLine">
    <option value="0" selected="selected">No</option>
    <option value="1">Yes</option>
    </select>
    <br />
    <br />
    <b>Column Assignment:</b><br />
    "C1": <select name="Col1">
    <option value="w" selected="selected">Term</option>
    <option value="t">Translation</option>
    <option value="r">Romanization</option>
    <option value="s">Sentence</option>
    <option value="g">Tag List</option>
    <option value="x">Don't import</option>
    </select><br />
    "C2": <select name="Col2">
    <option value="w">Term</option>
    <option value="t" selected="selected">Translation</option>
    <option value="r">Romanization</option>
    <option value="s">Sentence</option>
    <option value="g">Tag List</option>
    <option value="x">Don't import</option>
    </select><br />
    "C3": <select name="Col3">
    <option value="w">Term</option>
    <option value="t">Translation</option>
    <option value="r">Romanization</option>
    <option value="s">Sentence</option>
    <option value="g">Tag List</option>
    <option value="x" selected="selected">Don't import</option>
    </select><br />
    "C4": <select name="Col4">
    <option value="w">Term</option>
    <option value="t">Translation</option>
    <option value="r">Romanization</option>
    <option value="s">Sentence</option>
    <option value="g">Tag List</option>
    <option value="x" selected="selected">Don't import</option>
    </select><br />
    "C5": <select name="Col5">
    <option value="w">Term</option>
    <option value="t">Translation</option>
    <option value="r">Romanization</option>
    <option value="s">Sentence</option>
    <option value="g">Tag List</option>
    <option value="x" selected="selected">Don't import</option>
    </select><br />
    <br /><b>Import Mode</b>:<br />
    <select name="Over" onchange="if(parseInt(this.value)>3){$('#imp_transl_delim').removeClass('hide');$('#imp_transl_delim input').addClass('notempty');}else{ $('#imp_transl_delim input').removeClass('notempty');$('#imp_transl_delim').addClass('hide');}">
    <option value="0" title="- don't overwrite existent terms&#x000A;- import new terms" selected="selected">Import only new terms</option>
    <option value="1" title="- overwrite existent terms&#x000A;- import new terms">Replace all fields</option>
    <option value="2" title="- update only empty fields&#x000A;- import new terms">Update empty fields</option>
    <option value="3" title="- overwrite existing terms with new not empty values&#x000A;- don't import new terms">No new terms</option>
    <option value="4" title="- add new translations to existing ones&#x000A;- import new terms">Merge translation fields</option>
    <option value="5" title="- add new translations to existing ones&#x000A;- don't import new terms">Update existing translations</option>
    </select>
    <br /><div class="hide" id="imp_transl_delim">Import Translation Delimiter:<br />
    <input class="notempty" type="text" name="transl_delim" style="width:4em;" value="<?php echo getSettingWithDefault('set-term-translation-delimiters'); ?>" /> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></div><br />
    <b>Important:</b><br />
    You must specify the term.<br />
    Translation, romanization, <br />sentence and tag list<br />are optional. The tag list <br />must be separated either<br />by spaces or commas.
    </td>
    <td class="td1">
    Either specify a <b>File to upload</b>:<br />
    <input name="thefile" type="file" /><br /><br />
    <b>Or</b> type in or paste from clipboard (do <b>NOT</b> specify file):<br />
    <textarea class="checkoutsidebmp" data_info="Upload" name="Upload" cols="60" rows="25"></textarea>
    </td>
    </tr>
    <tr>
    <td class="td1 center"><b>Status</b> for all uploaded terms:</td>
    <td class="td1"><select class="notempty" name="WoStatus"><?php echo get_wordstatus_selectoptions(null, false, false); ?></select> <img src="icn/status-busy.png" title="Field must not be empty" alt="Field must not be empty" /></td>
    </tr>
    <tr>
    <td class="td1 center" colspan="2"><span class="red2">A DATABASE <input type="button" value="BACKUP" onclick="location.href='backup_restore.php';" /> MAY BE ADVISABLE!<br />PLEASE DOUBLE-CHECK EVERYTHING!</span><br /><input type="button" value="&lt;&lt; Back" onclick="location.href='index.php';" /> &nbsp; &nbsp; &nbsp; | &nbsp; &nbsp; &nbsp; <input type="submit" name="op" value="Import" /></td>
    </tr>
    </table>
    </form>
    
    <p>Sentences should contain the term in curly brackets "... {term} ...".<br />
    If not, such sentences can be automatically created later with the <br />"Set Term Sentences" action in the <input type="button" value="My Texts" onclick="location.href='edit_texts.php?query=&amp;page=1';" /> screen.</p>

<?php

}

pageend();

?>
