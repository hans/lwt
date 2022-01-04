
<?php

/**
 * \file
 * \brief Connects to the database and check its state.
 * 
 * @author https://github.com/HugoFara/ HugoFara
 */

require_once __DIR__ . "/kernel_utility.php";
require __DIR__ . "/../connect.inc.php";

/**
 * Do a SQL query to the database. 
 * It is a wrapper for mysqli_query function.
 * 
 * @param string $sql Query using SQL syntax
 * 
 * @global mysqli $DBCONNECTION COnnection to the database
 * 
 */ 
function do_mysqli_query($sql)
{
    global $DBCONNECTION;
    $res = mysqli_query($DBCONNECTION, $sql);
    if ($res == false) {
        echo '</select></p></div><div style="padding: 1em; color:red; font-size:120%; background-color:#CEECF5;">' .
        '<p><b>Fatal Error in SQL Query:</b> ' . 
        tohtml($sql) . 
        '</p>' . 
        '<p><b>Error Code &amp; Message:</b> [' . 
        mysqli_errno($DBCONNECTION) . 
        '] ' . 
        tohtml(mysqli_error($DBCONNECTION)) . 
        "</p></div><hr /><pre>Backtrace:\n\n";
        debug_print_backtrace();
        echo '</pre><hr />';
        die('</body></html>');
    }
    else {
        return $res; 
    }
}

/**
 * Run a SQL query, you can specify its behavior and error message.
 * 
 * @param string $sql       MySQL query
 * @param string $m         Success phrase to prepend to the number of affected rows
 * @param bool   $sqlerrdie To die on errors (default = TRUE)
 * 
 * @return string Error message if failure, or the number of affected rows
 */
function runsql($sql, $m, $sqlerrdie = true) 
{
    if ($sqlerrdie) {
        $res = do_mysqli_query($sql); 
    }
    else {
        $res = mysqli_query($GLOBALS['DBCONNECTION'], $sql); 
    }        
    if ($res == false) {
        $message = "Error: " . mysqli_error($GLOBALS['DBCONNECTION']);
    } else {
        $num = mysqli_affected_rows($GLOBALS['DBCONNECTION']);
        $message = (($m == '') ? (string)$num : ($m . ": " . $num));
    }
    return $message;
}


/**
 * Return the record "value" in the first line of the database if found.
 *
 * @param  string $sql MySQL query
 * @return string|null
 */
function get_first_value($sql) 
{
    $res = do_mysqli_query($sql);        
    $record = mysqli_fetch_assoc($res);
    if ($record) { 
        $d = $record["value"]; 
    }
    else {
        $d = null; 
    }
    mysqli_free_result($res);
    return $d;
}

// -------------------------------------------------------------

function prepare_textdata($s) 
{
    return str_replace("\r\n", "\n", stripTheSlashesIfNeeded($s));
}

// -------------------------------------------------------------

function prepare_textdata_js($s) 
{
    $s = convert_string_to_sqlsyntax($s);
    if ($s == "NULL") { 
        return "''"; 
    }
    return str_replace("''", "\\'", $s);
}


// -------------------------------------------------------------

function convert_string_to_sqlsyntax($data) 
{
    $result = "NULL";
    $data = trim(prepare_textdata($data));
    if ($data != "") { 
        $result = "'" . mysqli_real_escape_string($GLOBALS['DBCONNECTION'], $data) . "'"; 
    }
    return $result;
}

// -------------------------------------------------------------

function convert_string_to_sqlsyntax_nonull($data) 
{
    $data = trim(prepare_textdata($data));
    return  "'" . mysqli_real_escape_string($GLOBALS['DBCONNECTION'], $data) . "'";
}

// -------------------------------------------------------------

function convert_string_to_sqlsyntax_notrim_nonull($data) 
{
    return "'" . mysqli_real_escape_string($GLOBALS['DBCONNECTION'], prepare_textdata($data)) . "'";
}

// -------------------------------------------------------------

function convert_regexp_to_sqlsyntax($input) 
{
    $output = preg_replace_callback(
        "/\\\\x\{([\da-z]+)\}/ui", function ($a) {
            $num = $a[1];
            $dec = hexdec($num);
            return "&#$dec;";
        }, preg_replace(array('/\\\\(?![-xtfrnvup])/u','/(?<=[[^])[\\\\]-/u'), array('','-'), $input)
    );
    return convert_string_to_sqlsyntax_nonull(html_entity_decode($output, ENT_NOQUOTES, 'UTF-8'));
}

/**
 * Validate a language ID
 * 
 * @param string $currentlang Language ID to validate
 * 
 * @global string '' if the language is not valid.
 */
function validateLang($currentlang) 
{
    global $tbpref;
    $sql = 
    'SELECT count(LgID) AS value 
    FROM ' . $tbpref . 'languages 
    WHERE LgID=' . ((int)$currentlang);
    if ($currentlang != '') {
        if (get_first_value($sql) == 0
        ) {  
            $currentlang = ''; 
        } 
    }
    return $currentlang;
}

/**
 * Validate a text ID
 * 
 * @param string $currenttext Text ID to validate
 * 
 * @global string '' if the text is not valid.
 */
function validateText($currenttext) 
{
    global $tbpref;
    if ($currenttext != '') {
        if (get_first_value(
            'select count(TxID) as value from ' . $tbpref . 'texts where TxID=' . 
            ((int)$currenttext) 
        ) == 0
        ) {  
            $currenttext = ''; 
        } 
    }
    return $currenttext;
}

// -------------------------------------------------------------

function validateTag($currenttag,$currentlang) 
{
    global $tbpref;
    if ($currenttag != '' && $currenttag != -1) {
        if ($currentlang == '') {
            $sql = "select (" . $currenttag . " in (select TgID from " . $tbpref . "words, " . $tbpref . "tags, " . $tbpref . "wordtags where TgID = WtTgID and WtWoID = WoID group by TgID order by TgText)) as value"; 
        }
        else {
            $sql = "select (" . $currenttag . " in (select TgID from " . $tbpref . "words, " . $tbpref . "tags, " . $tbpref . "wordtags where TgID = WtTgID and WtWoID = WoID and WoLgID = " . $currentlang . " group by TgID order by TgText)) as value"; 
        }
        $r = get_first_value($sql);
        if ($r == 0 ) { 
            $currenttag = ''; 
        } 
    }
    return $currenttag;
}

// -------------------------------------------------------------

function validateArchTextTag($currenttag,$currentlang) 
{
    global $tbpref;
    if ($currenttag != '' && $currenttag != -1) {
        if ($currentlang == '') {
            $sql = "select (" . $currenttag . " in (select T2ID from " . $tbpref . "archivedtexts, " . $tbpref . "tags2, " . $tbpref . "archtexttags where T2ID = AgT2ID and AgAtID = AtID group by T2ID order by T2Text)) as value"; 
        }
        else {
            $sql = "select (" . $currenttag . " in (select T2ID from " . $tbpref . "archivedtexts, " . $tbpref . "tags2, " . $tbpref . "archtexttags where T2ID = AgT2ID and AgAtID = AtID and AtLgID = " . $currentlang . " group by T2ID order by T2Text)) as value"; 
        }
        $r = get_first_value($sql);
        if ($r == 0 ) { $currenttag = ''; 
        } 
    }
    return $currenttag;
}

// -------------------------------------------------------------

function validateTextTag($currenttag,$currentlang) 
{
    global $tbpref;
    if ($currenttag != '' && $currenttag != -1) {
        if ($currentlang == '') {
            $sql = "select (" . $currenttag . " in (select T2ID from " . $tbpref . "texts, " . $tbpref . "tags2, " . $tbpref . "texttags where T2ID = TtT2ID and TtTxID = TxID group by T2ID order by T2Text)) as value"; 
        }
        else {
            $sql = "select (" . $currenttag . " in (select T2ID from " . $tbpref . "texts, " . $tbpref . "tags2, " . $tbpref . "texttags where T2ID = TtT2ID and TtTxID = TxID and TxLgID = " . $currentlang . " group by T2ID order by T2Text)) as value"; 
        }
        $r = get_first_value($sql);
        if ($r == 0 ) { $currenttag = ''; 
        } 
    }
    return $currenttag;
}

/** 
 * Convert a setting to 0 or 1
 *
 * @param  string $key The input value
 * @param  string $dft Default value to use
 * 
 * @return 0|1
 */
function getSettingZeroOrOne($key, $dft) 
{
    $r = getSetting($key);
    $r = ($r == '' ? $dft : ((((int)$r) !== 0) ? 1 : 0));
    return (int)$r;
}

/**
 * Get a setting from the database. It can also check for its validity.
 * 
 * @param  string $key Setting key. If $key is 'currentlanguage' or 
 *                     'currenttext', we validate language/text.
 * @return string $val Value in the database if found, or an empty string
 * @global string $tbpref Table name prefix
 */
function getSetting($key) 
{
    global $tbpref;
    $val = get_first_value(
        'SELECT StValue AS value 
        FROM ' . $tbpref . 'settings 
        WHERE StKey = ' . convert_string_to_sqlsyntax($key)
    );
    if (isset($val)) {
        $val = trim($val);
        if ($key == 'currentlanguage' ) { 
            $val = validateLang($val); 
        }
        if ($key == 'currenttext' ) { 
            $val = validateText($val); 
        }
        return $val;
    }
    else { 
        return ''; 
    }
}

/**
 * Get the settings value for a specific key. Return a default value when possible
 * 
 * @param  string $key Settings key
 * 
 * @return string Requested setting, or default value, or ''
 * 
 * @global string $tbpref Table name prefix
 */
function getSettingWithDefault($key) 
{
    global $tbpref;
    $dft = get_setting_data();
    $val = get_first_value(
        'SELECT StValue AS value
         FROM ' . $tbpref . 'settings
         WHERE StKey = ' . convert_string_to_sqlsyntax($key)
    );
    if (isset($val) && $val != '') {
        return trim($val); 
    }
    if (array_key_exists($key, $dft)) { 
        return $dft[$key]['dft']; 
    }
    return '';
    
}

/**
 * Save the setting identified by a key with a specific value.
 * 
 * @param string $k Setting key
 * @param mixed  $v Setting value, will get converted to string
 * 
 * @global string $tbpref Table name prefix
 * 
 * @return string Error or success message
 */
function saveSetting($k, $v) 
{
    global $tbpref;
    $dft = get_setting_data();
    if (!isset($v)) {
        return ''; 
    }
    $v = stripTheSlashesIfNeeded($v);
    if ($v === '') {
        return '';
    }
    runsql(
        'DELETE FROM ' . $tbpref . 'settings 
        WHERE StKey = ' . convert_string_to_sqlsyntax($k), 
        ''
    );
    if (array_key_exists($k, $dft) && $dft[$k]['num']) {
        $v = (int)$v;
        if ($v < $dft[$k]['min']) { 
            $v = $dft[$k]['dft']; 
        }
        if ($v > $dft[$k]['max']) { 
            $v = $dft[$k]['dft']; 
        }
    }
    $dum = runsql(
        'INSERT INTO ' . $tbpref . 'settings (StKey, StValue) values(' .
        convert_string_to_sqlsyntax($k) . ', ' . 
        convert_string_to_sqlsyntax($v) . ')', 
        ''
    );
    return $dum;
}

/**
 * Check if the _lwtgeneral table exists, create it if not.
 */
function LWTTableCheck()
{
    if (mysqli_num_rows(do_mysqli_query("SHOW TABLES LIKE '\\_lwtgeneral'")) == 0) {
        runsql("CREATE TABLE IF NOT EXISTS _lwtgeneral ( LWTKey varchar(40) NOT NULL, LWTValue varchar(40) DEFAULT NULL, PRIMARY KEY (LWTKey) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
        if (mysqli_num_rows(do_mysqli_query("SHOW TABLES LIKE '\\_lwtgeneral'")) == 0) { 
            my_die("Unable to create table '_lwtgeneral'!"); 
        }
    }
}

// -------------------------------------------------------------

function LWTTableSet($key, $val)
{
    LWTTableCheck();
    runsql("INSERT INTO _lwtgeneral (LWTKey, LWTValue) VALUES (" . convert_string_to_sqlsyntax($key) . ", " . convert_string_to_sqlsyntax($val) . ") ON DUPLICATE KEY UPDATE LWTValue = " . convert_string_to_sqlsyntax($val), '');
}

// -------------------------------------------------------------

function LWTTableGet($key)
{
    LWTTableCheck();
    return get_first_value("SELECT LWTValue as value FROM _lwtgeneral WHERE LWTKey = " . convert_string_to_sqlsyntax($key));
}

/**
 * Adjust the auto-incrementation in the database.
 * 
 * @global string $tbpref Database table prefix
 */
function adjust_autoincr($table, $key) 
{
    global $tbpref;
    $val = get_first_value('SELECT max(' . $key .')+1 AS value FROM ' . $tbpref . $table);
    if (!isset($val)) { 
        $val = 1; 
    }
    $sql = 'ALTER TABLE ' . $tbpref . $table . ' AUTO_INCREMENT = ' . $val;
    $res = do_mysqli_query($sql);
}

/**
 * Optimize the database.
 * 
 * @global string $trbpref Table prefix
 */
function optimizedb() 
{
    global $tbpref;
    adjust_autoincr('archivedtexts', 'AtID');
    adjust_autoincr('languages', 'LgID');
    adjust_autoincr('sentences', 'SeID');
    adjust_autoincr('texts', 'TxID');
    adjust_autoincr('words', 'WoID');
    adjust_autoincr('tags', 'TgID');
    adjust_autoincr('tags2', 'T2ID');
    adjust_autoincr('newsfeeds', 'NfID');
    adjust_autoincr('feedlinks', 'FlID');
    $sql = 
    'SHOW TABLE STATUS 
    WHERE Engine IN ("MyISAM","Aria") AND ((Data_free / Data_length > 0.1 AND Data_free > 102400) OR Data_free > 1048576) AND Name';
    if(empty($tbpref)) { 
        $sql.= " NOT LIKE '\_%'"; 
    }
    else { 
        $sql.= " LIKE " . convert_string_to_sqlsyntax(rtrim($tbpref, '_')) . "'\_%'"; 
    }
    $res = do_mysqli_query($sql);
    while($row = mysqli_fetch_assoc($res)) {
        runsql('OPTIMIZE TABLE ' . $row['Name'], '');
    }
    mysqli_free_result($res);
}

/**
 * @global string $tbpref Database table prefix
 */
function set_word_count() 
{
    global $tbpref;
    $sqlarr = array();
    $i=0;
    $min=0;
    $max=0;

    if (get_first_value('SELECT (@m := group_concat(LgID)) value FROM ' . $tbpref . 'languages WHERE UPPER(LgRegexpWordCharacters)="MECAB"')) {
        $db_to_mecab = sys_get_temp_dir() . "/" . $tbpref . "db_to_mecab.txt";
        $mecab_to_db = sys_get_temp_dir() . "/" . $tbpref . "mecab_to_db.txt";
        $mecab_args = ' -F %m%t\\t -U %m%t\\t -E \\n ';
        /*if(!is_dir(sys_get_temp_dir() . "/lwt")) {
            mkdir(sys_get_temp_dir() . "/lwt", 0777);
            chmod(sys_get_temp_dir() . "/lwt", 0777);
        }*/
        if (file_exists($db_to_mecab)) { 
            unlink($db_to_mecab); 
        }

        $mecab = get_mecab_path($mecab_args);

        do_mysqli_query(
            'SELECT WoID, WoTextLC FROM ' . $tbpref . 'words 
            WHERE WoLgID in(@m) AND WoWordCount = 0 
            into outfile ' . convert_string_to_sqlsyntax($db_to_mecab)
        );
        $handle = popen($mecab . $db_to_mecab, "r");
        $fp = fopen($mecab_to_db, 'w');
        if (!feof($handle)) {
            while (!feof($handle)) {
                $row = fgets($handle, 1024);
                $arr  = explode("4\t", $row, 2);
                //var_dump($arr);
                if (!empty($arr[1])) {
                    $cnt = substr_count(preg_replace('$[^267]\t$u', '', $arr[1]), "\t");
                    if(empty($cnt)) { $cnt =1; 
                    }
                    fwrite($fp, $arr[0] . "\t" . $cnt . "\n");
                }
            }
            pclose($handle);
            fclose($fp);
            do_mysqli_query('CREATE TEMPORARY TABLE ' . $tbpref . 'mecab ( MID mediumint(8) unsigned NOT NULL, MWordCount tinyint(3) unsigned NOT NULL, PRIMARY KEY (MID)) CHARSET=utf8');
            do_mysqli_query('LOAD DATA LOCAL INFILE ' . convert_string_to_sqlsyntax($mecab_to_db) . ' INTO TABLE ' . $tbpref . 'mecab (MID, MWordCount)');
            do_mysqli_query('UPDATE ' . $tbpref . 'words join ' . $tbpref . 'mecab on MID = WoID SET WoWordCount = MWordCount');
            do_mysqli_query('DROP TABLE ' . $tbpref . 'mecab');

            unlink($mecab_to_db);
            unlink($db_to_mecab);
        }
    }
    $sql= "select WoID, WoTextLC, LgRegexpWordCharacters, LgSplitEachChar from " . $tbpref . "words, " . $tbpref . "languages where WoWordCount=0 and WoLgID = LgID order by WoID";
    $result = do_mysqli_query($sql);
    while($rec = mysqli_fetch_assoc($result)){
        if ($rec['LgSplitEachChar']) {
            $textlc = preg_replace('/([^\s])/u', "$1 ", $rec['WoTextLC']);
        }
        else{
            $textlc = $rec['WoTextLC'];
        }
        $sqlarr[]= ' WHEN ' . $rec['WoID'] . ' THEN ' . preg_match_all('/([' . $rec['LgRegexpWordCharacters'] . ']+)/u', $textlc, $ma);
        if(++$i % 1000 == 0) {
            if(!empty($sqlarr)) {
                $max=$rec['WoID'];
                $sqltext = "UPDATE  " . $tbpref . "words SET WoWordCount  = CASE WoID";
                $sqltext .= implode(' ', $sqlarr) . ' END where WoWordCount=0 and WoID between ' . $min . ' and ' . $max;
                do_mysqli_query($sqltext);
                $min=$max;
            }
            $sqlarr = array();
        }
    }
    mysqli_free_result($result);
    if(!empty($sqlarr)) {
        $sqltext = "UPDATE  " . $tbpref . "words SET WoWordCount  = CASE WoID";
        $sqltext .= implode(' ', $sqlarr) . ' END where WoWordCount=0';
        do_mysqli_query($sqltext);
    }
}

/**
 * Parse the input text.
 * 
 * @param string $text Text to parse
 * @param string $lid  Language ID (LgID from languages table)
 * @param int    $id   References whether the text is new to the database
 *                     $id = -1     => Check, return protocol
 *                     $id = -2     => Only return sentence array
 *                     $id = TextID => Split: insert sentences/textitems entries in DB
 * 
 * @global string $tbpref Database table prefix
 */
function splitCheckText($text, $lid, $id) 
{
    global $tbpref;
    $wo = $nw = $mw = $wl = array();
    $wl_max = 0;
    $set_wo_sql = $set_wo_sql_2 = $del_wo_sql = $init_var = $mw_sql = $sql = '';
    $sql = "SELECT * FROM " . $tbpref . "languages WHERE LgID=" . $lid;
    $res = do_mysqli_query($sql);
    $record = mysqli_fetch_assoc($res);
    if ($record == false) { 
        my_die("Language data not found: $sql"); 
    }
    $removeSpaces = $record['LgRemoveSpaces'];
    $splitEachChar = $record['LgSplitEachChar'];
    $splitSentence = $record['LgRegexpSplitSentences'];
    $noSentenceEnd = $record['LgExceptionsSplitSentences'];
    $termchar = $record['LgRegexpWordCharacters'];
    $replace = explode("|", $record['LgCharacterSubstitutions']);
    $rtlScript = $record['LgRightToLeft'];
    mysqli_free_result($res);
    $s = prepare_textdata($text);
    //if(is_callable('normalizer_normalize')) $s = normalizer_normalize($s);

    $file_name = sys_get_temp_dir() . "/" . $tbpref . "tmpti.txt";
    do_mysqli_query('TRUNCATE TABLE ' . $tbpref . 'temptextitems');

    $s = str_replace(array('}','{'), array(']','['), $s);    // because of sent. spc. char
    foreach ($replace as $value) {
        $fromto = explode("=", trim($value));
        if(count($fromto) >= 2) {
            $s = str_replace(trim($fromto[0]), trim($fromto[1]), $s);
        }
    }

    if ('MECAB'== strtoupper(trim($termchar))) {
        //$mecab_args = ' -F %m\\t%F-[0,1,2,3]\\n -U %m\\t%F-[0,1,2,3]\\n -E ¶\\t記号-句点\\n ';
        $mecab_args = ' -F %m\\t%t\\t%h\\n -U %m\\t%t\\t%h\\n -E EOS\\t3\\t7\\n ';
        $mecab = get_mecab_path($mecab_args);
        $s = preg_replace('/[ \t]+/u', ' ', $s);
        $s = trim($s);
        if ($id == -1) { 
            echo "<div id=\"check_text\" style=\"margin-right:50px;\"><h4>Text</h4><p>" . str_replace("\n", "<br /><br />", tohtml($s)). "</p>"; 
        }
        $handle = popen($mecab .' -o ' . $file_name, 'w');
        $write = fwrite($handle, $s);
        pclose($handle);

        runsql(
            "CREATE TEMPORARY TABLE IF NOT EXISTS " . $tbpref . "temptextitems2
             (TiCount smallint(5) unsigned NOT NULL,
             TiSeID mediumint(8) unsigned NOT NULL,
             TiOrder smallint(5) unsigned NOT NULL,
             TiWordCount tinyint(3) unsigned NOT NULL,
             TiText varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
            ) DEFAULT CHARSET=utf8", 
            ''
        );
        do_mysqli_query('SET @a:=0, @g:=0, @s:=' . ($id>0?'(SELECT ifnull(max(`SeID`)+1,1) FROM `' . $tbpref . 'sentences`)':1) . ',@d:=0,@h:=0,@i:=0;');
        $delim = '\n';
        //$sql= 'LOAD DATA LOCAL INFILE ' . convert_string_to_sqlsyntax($file_name) . ' INTO TABLE ' . $tbpref . 'temptextitems2 FIELDS TERMINATED BY \'\\t\' LINES TERMINATED BY \'' . $delim . '\' (@c,@f) set TiSeID = if(@g=2 OR @c="¶",@s:=@s+(@d:=@h)+1,@s), TiCount = (@d:=@d+CHAR_LENGTH(@c))+1-CHAR_LENGTH(@c), TiOrder = if(case when @f like \'記号-句点\' then @g:=2  when @f like \'記号%\' then @g:=1 when @f like \'名詞-数\' then @g:=1 when @c rlike \'[0-9a-zA-Z]+\' then @g:=1 else @g:=@h end is null, null, @a:=@a+if((@i=1) and (@g=1),0,1)+if((@i=0) and (@g=0),1,0)), TiText = @c, TiWordCount= case when (@i:=@g) is NULL then NULL when @g=0 then 1 else 0 end';
        $sql 
        = 'LOAD DATA LOCAL INFILE ' . convert_string_to_sqlsyntax($file_name) . '
         INTO TABLE ' . $tbpref . 'temptextitems2
         FIELDS TERMINATED BY \'\\t\' LINES
         TERMINATED BY \'' . $delim . '\' (@c,@e,@f)
         SET TiSeID = if(@g=2 or (@f="7" and @c="EOS"), @s:=@s+(@d:=@h)+1,@s),
          TiCount = (@d:=@d+CHAR_LENGTH(@c))+1-CHAR_LENGTH(@c),
          TiOrder = if(
            CASE
                WHEN @f = \'7\' then if(@c="EOS",(@g:=2) and (@c:="¶"),@g:=2) 
                WHEN LOCATE(@e,\'267\') then @g:=@h else @g:=1 end is null, null, @a:=@a+if((@i=1) and (@g=1),0,1)+if((@i=0) and (@g=0),1,0)), TiText = @c, TiWordCount=
                    CASE 
                        WHEN (@i:=@g) IS NULL THEN NULL
                        WHEN @g=0 THEN 1 ELSE 0 
                    END';
        do_mysqli_query($sql);
        do_mysqli_query('DELETE FROM ' . $tbpref . 'temptextitems2 WHERE TiOrder=@a');
        do_mysqli_query('INSERT INTO ' . $tbpref . 'temptextitems (TiCount, TiSeID, TiOrder, TiWordCount, TiText) SELECT min(TiCount) s, TiSeID, TiOrder, TiWordCount, group_concat(TiText order by TiCount SEPARATOR \'\') FROM ' . $tbpref . 'temptextitems2 WHERE 1 group by TiOrder');
        do_mysqli_query('DROP TABLE ' . $tbpref . 'temptextitems2');
    } else {
        $s = str_replace("\n", " ¶", $s);
        $s = trim($s);
        if ($splitEachChar) {
            $s = preg_replace('/([^\s])/u', "$1\t", $s);
        }
        $s = preg_replace('/\s+/u', ' ', $s);
        if ($id == -1) { 
            echo "<div id=\"check_text\" style=\"margin-right:50px;\"><h4>Text</h4><p " .  ($rtlScript ? 'dir="rtl"' : '') . ">" . str_replace("¶", "<br /><br />", tohtml($s)). "</p>"; 
        }
        //    "\r" => Sentence delimiter, "\t" and "\n" => Word delimiter
        $s = preg_replace_callback(
            "/(\S+)\s*((\.+)|([$splitSentence]))([]'`\"”)‘’‹›“„«»』」]*)(?=(\s*)(\S+|$))/u", 
            fn ($matches) => find_latin_sentence_end($matches, $noSentenceEnd), 
            $s
        );
        $s = str_replace(array("¶"," ¶"), array("¶\r","\r¶"), $s);
        $s = preg_replace(array('/([^' . $termchar . '])/u','/\n([' . $splitSentence . '][\'`"”)\]‘’‹›“„«»』」]*)\n\t/u','/([0-9])[\n]([:.,])[\n]([0-9])/u'), array("\n$1\n","$1","$1$2$3"), $s);
        if($id == -2) {
            return explode("\r", remove_spaces(str_replace(array("\r\r","\t","\n"), array("\r","",""), $s), $removeSpaces));
        }

        $fp = fopen($file_name, 'w');
        fwrite($fp, remove_spaces(preg_replace("/(\n|^)(?!1\t)/u", "\n0\t", trim(preg_replace(array("/\r(?=[]'`\"”)‘’‹›“„«»』」 ]*\r)/u",'/[\n]+\r/u','/\r([^\n])/u',"/\n[.](?![]'`\"”)‘’‹›“„«»』」]*\r)/u","/(\n|^)(?=.?[$termchar][^\n]*\n)/u"), array("","\r","\r\n$1",".\n","\n1\t"), str_replace(array("\t","\n\n"), array("\n",""), $s)))), $removeSpaces));
        fclose($fp);
        do_mysqli_query('SET @a=0, @b=' . ($id>0?'(SELECT ifnull(max(`SeID`)+1,1) FROM `' . $tbpref . 'sentences`)':1) . ',@d=0,@e=0;');
        $sql= 'LOAD DATA LOCAL INFILE '. convert_string_to_sqlsyntax($file_name) . ' INTO TABLE ' . $tbpref . 'temptextitems FIELDS TERMINATED BY \'\\t\' LINES TERMINATED BY \'\\n\' (@w,@c) set TiSeID = @b, TiCount = (@d:=@d+CHAR_LENGTH(@c))+1-CHAR_LENGTH(@c), TiOrder = if(@c like "%\\r",case when (@c:=REPLACE(@c,"\\r","")) is NULL then NULL when (@b:=@b+1) is NULL then NULL when @d:= @e is NULL then NULL else @a:=@a+1 end, @a:=@a+1), TiText = @c,TiWordCount=@w';
        do_mysqli_query($sql);
    }
    unlink($file_name);

    if ($id==-1) {//check text
    
        $res = do_mysqli_query('SELECT GROUP_CONCAT(TiText order by TiOrder SEPARATOR "") Sent FROM ' . $tbpref . 'temptextitems group by TiSeID');
        echo '<h4>Sentences</h4><ol>';
        while($record = mysqli_fetch_assoc($res)){
            echo "<li>" . tohtml($record['Sent']) . "</li>";
        }
        mysqli_free_result($res);
        echo '</ol>';
        $res = do_mysqli_query('SELECT count(`TiOrder`) cnt, if(0=TiWordCount,0,1) as len, lower(TiText) as word, WoTranslation from ' . $tbpref . 'temptextitems left join ' . $tbpref . 'words on lower(TiText)=WoTextLC and WoLgID=' . $lid . ' group by lower(TiText)');
        while($record = mysqli_fetch_assoc($res)){
            if($record['len']==1) {
                $wo[]= array(tohtml($record['word']),$record['cnt'],tohtml($record['WoTranslation']));
            }
            else{
                $nw[]= array(tohtml($record['word']),tohtml($record['cnt']));
            }
        }
        mysqli_free_result($res);
        echo "<script type=\"text/javascript\">\nWORDS = ", json_encode($wo), ";\nNOWORDS = ", json_encode($nw), ";\n</script>";
    }//check text end

    $res = do_mysqli_query("SELECT WoWordCount as len, count(WoWordCount) as cnt FROM " . $tbpref . "words where WoLgID = " . $lid . " and WoWordCount > 1 group by WoWordCount");
    while($record = mysqli_fetch_assoc($res)){
        if($wl_max < $record['len']) { $wl_max = $record['len']; 
        }
        $wl[] = $record['len'];
        $mw_sql .= ' WHEN ' . $record['len'] . ' THEN @a' . ($record['len'] * 2 - 1);
    }
    mysqli_free_result($res);
    $sql = '';
    if(!empty($wl)) {//text has expressions
        do_mysqli_query('SET GLOBAL max_heap_table_size = 1024 * 1024 * 1024 * 2');
        do_mysqli_query('SET GLOBAL tmp_table_size = 1024 * 1024 * 1024 * 2');
        for ($i=$wl_max*2 -1; $i>1; $i--) {
            $set_wo_sql .= 'WHEN (@a' . strval($i) . ':=@a' . strval($i-1) . ') IS NULL THEN NULL ';
            $set_wo_sql_2 .= 'WHEN (@a' . strval($i) . ':=@a' . strval($i-2) . ') IS NULL THEN NULL ';
            $del_wo_sql .= 'WHEN (@a' . strval($i) . ':=@a0) IS NULL THEN NULL ';
            $init_var .= '@a' . strval($i) . '=0,';
        }
        do_mysqli_query('set ' . $init_var . '@a1=0,@a0=0,@b=0,@c="",@d=0,@e=0,@f="",@h=0;');
        do_mysqli_query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $tbpref . 'numbers( n  tinyint(3) unsigned NOT NULL);');
        do_mysqli_query('TRUNCATE TABLE ' . $tbpref . 'numbers');
        do_mysqli_query('INSERT IGNORE INTO ' . $tbpref . 'numbers(n) VALUES (' . implode('),(', $wl) . ');');
        $sql = (($id>0)?'SELECT straight_join WoID, sent, TiOrder - (2*(n-1)) TiOrder, n TiWordCount,word':'SELECT straight_join count(WoID) cnt, n as len, lower(WoText) as word, WoTranslation');
        $sql .= ' FROM (SELECT straight_join if(@b=TiSeID and @h=TiOrder,if((@h:=TiOrder+@a0) is null,TiSeID,TiSeID),if(@b=TiSeID, IF((@d=1) and (0<>TiWordCount), CASE ' . $set_wo_sql_2 . ' WHEN (@a1:=TiCount+@a0) IS NULL THEN NULL WHEN (@b:=TiSeID+@a0) IS NULL THEN NULL WHEN (@h:=TiOrder+@a0) IS NULL THEN NULL WHEN (@c:=concat(@c,TiText)) IS NULL THEN NULL WHEN (@d:=(0<>TiWordCount)+@a0) IS NULL THEN NULL ELSE TiSeID END, CASE ' . $set_wo_sql . ' WHEN (@a1:=TiCount+@a0) IS NULL THEN NULL WHEN (@b:=TiSeID+@a0) IS NULL THEN NULL WHEN (@h:=TiOrder+@a0) IS NULL THEN NULL WHEN (@c:=concat(@c,TiText)) IS NULL THEN NULL WHEN (@d:=(0<>TiWordCount)+@a0) IS NULL THEN NULL ELSE TiSeID END), CASE '  . $del_wo_sql . ' WHEN (@a1:=TiCount+@a0) IS NULL THEN NULL WHEN (@b:=TiSeID+@a0) IS NULL THEN NULL WHEN (@h:=TiOrder+@a0) IS NULL THEN NULL WHEN (@c:=concat(TiText,@f)) IS NULL THEN NULL WHEN (@d:=(0<>TiWordCount)+@a0) IS NULL THEN NULL ELSE TiSeID END)) sent, if(@d=0,NULL,if(CRC32(@z:=substr(@c,case n' . $mw_sql . ' end))<>CRC32(lower(@z)),@z,"")) word,if(@d=0 or ""=@z,NULL,lower(@z)) lword, TiOrder,n FROM ' . $tbpref . 'numbers , ' . $tbpref . 'temptextitems) ti, ' . $tbpref . 'words where lword is not null and WoLgID=' . $lid . ' and WoTextLC=lword and WoWordCount=n' . (($id>0)?' union all ':' group by WoID order by WoTextLC');
    }//text has expressions end
    if($id>0) {
        do_mysqli_query('ALTER TABLE ' . $tbpref . 'textitems2 ALTER Ti2LgID SET DEFAULT ' . $lid . ', ALTER Ti2TxID SET DEFAULT ' . $id);
        do_mysqli_query('insert into ' . $tbpref . 'textitems2 (Ti2WoID, Ti2SeID, Ti2Order, Ti2WordCount, Ti2Text) ' . $sql . 'select  WoID, TiSeID, TiOrder, TiWordCount, TiText FROM ' . $tbpref . 'temptextitems left join ' . $tbpref . 'words on lower(TiText) = WoTextLC and TiWordCount=1 and WoLgID = ' . $lid . ' order by TiOrder,TiWordCount');
        do_mysqli_query('ALTER TABLE ' . $tbpref . 'sentences ALTER SeLgID SET DEFAULT ' . $lid . ', ALTER SeTxID SET DEFAULT ' . $id);
        do_mysqli_query('set @a=0;');
        do_mysqli_query('INSERT INTO ' . $tbpref . 'sentences ( SeOrder, SeFirstPos, SeText) SELECT @a:=@a+1, min(if(TiWordCount=0,TiOrder+1,TiOrder)),GROUP_CONCAT(TiText order by TiOrder SEPARATOR "") FROM ' . $tbpref . 'temptextitems group by TiSeID');
        do_mysqli_query('ALTER TABLE ' . $tbpref . 'textitems2 ALTER Ti2LgID DROP DEFAULT, ALTER Ti2TxID DROP DEFAULT');
        do_mysqli_query('ALTER TABLE ' . $tbpref . 'sentences ALTER SeLgID DROP DEFAULT, ALTER SeTxID DROP DEFAULT');
    }
    if($id==-1) {//check text
        if(!empty($wl)) {
            $res = do_mysqli_query($sql);
            while($record = mysqli_fetch_assoc($res)){
                $mw[]= array(tohtml($record['word']),$record['cnt'],tohtml($record['WoTranslation']));
            }
            mysqli_free_result($res);
        }
        echo "<script type=\"text/javascript\">\nMWORDS = ", json_encode($mw), ";\n";
        if($rtlScript) {
            echo '$(function() {$("li").attr("dir","rtl");});';
        }
        ?>
   h='<h4>Word List <span class="red2">(red = already saved)</span></h4><ul class="wordlist">';
   $.each(WORDS,function(k,v){h+= '<li><span' + (v[2]==""?"":' class="red2"') + '>[' + v[0] + '] — ' + v[1] + (v[2]==""?"":' — ' + v[2]) + '</span></li>';});
   $('#check_text').append(h);
   h='</ul><p>TOTAL: ' + WORDS.length +'</p><h4>Expression List</span></h4><ul class="expressionlist">';
   $.each(MWORDS,function(k,v){h+= '<li><span>[' + v[0] + '] — ' + v[1] + (v[2]==""?"":' — ' + v[2]) + '</span></li>';});
   $('#check_text').append(h);
   h='</ul><p>TOTAL: ' + MWORDS.length +'</p><h4>Non-Word List</span></h4><ul class="nonwordlist">';
   $.each(NOWORDS,function(k,v){h+= '<li>[' + v[0] + '] — ' + v[1] + '</li>';});
   $('#check_text').append(h + '</ul><p>TOTAL: ' + NOWORDS.length +'</p>');
   </script>

        <?php
    }//check text end
    do_mysqli_query('TRUNCATE TABLE ' . $tbpref . 'temptextitems');
}


/**
 * Reparse all texts in order.
 * 
 * @global string $tbpref Database table prefix
 */ 
function reparse_all_texts() 
{
    global $tbpref;
    runsql('TRUNCATE ' . $tbpref . 'sentences', '');
    runsql('TRUNCATE ' . $tbpref . 'textitems2', '');
    adjust_autoincr('sentences', 'SeID');
    set_word_count();
    $sql = "select TxID, TxLgID from " . $tbpref . "texts";
    $res = do_mysqli_query($sql);
    while ($record = mysqli_fetch_assoc($res)) {
        $id = $record['TxID'];
        splitCheckText(
            get_first_value('select TxText as value from ' . $tbpref . 'texts where TxID = ' . $id), $record['TxLgID'], $id 
        );
    }
    mysqli_free_result($res);
}

/**
 * Check and/or update the database.
 * 
 * @global mysqli $DBCONNECTION Connection to the database
 */
function check_update_db($debug, $tbpref, $dbname) 
{
    $tables = array();
    
    $res = do_mysqli_query(str_replace('_', "\\_", "SHOW TABLES LIKE " . convert_string_to_sqlsyntax_nonull($tbpref . '%')));
    while ($row = mysqli_fetch_row($res)) {
        $tables[] = $row[0]; 
    }
    mysqli_free_result($res);
    
    $count = 0;  /// counter for cache rebuild
    
    // Rebuild Tables if missing (current versions!)
    
    if (in_array($tbpref . 'archivedtexts', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding archivedtexts</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "archivedtexts ( AtID smallint(5) unsigned NOT NULL AUTO_INCREMENT, AtLgID tinyint(3) unsigned NOT NULL, AtTitle varchar(200) NOT NULL, AtText text NOT NULL, AtAnnotatedText longtext NOT NULL, AtAudioURI varchar(200) DEFAULT NULL, AtSourceURI varchar(1000) DEFAULT NULL, PRIMARY KEY (AtID), KEY AtLgID (AtLgID), KEY AtLgIDSourceURI (AtSourceURI(20),AtLgID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'languages', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding languages</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "languages ( LgID tinyint(3) unsigned NOT NULL AUTO_INCREMENT, LgName varchar(40) NOT NULL, LgDict1URI varchar(200) NOT NULL, LgDict2URI varchar(200) DEFAULT NULL, LgGoogleTranslateURI varchar(200) DEFAULT NULL, LgExportTemplate varchar(1000) DEFAULT NULL, LgTextSize smallint(5) unsigned NOT NULL DEFAULT '100', LgCharacterSubstitutions varchar(500) NOT NULL, LgRegexpSplitSentences varchar(500) NOT NULL, LgExceptionsSplitSentences varchar(500) NOT NULL, LgRegexpWordCharacters varchar(500) NOT NULL, LgRemoveSpaces tinyint(1) unsigned NOT NULL DEFAULT '0', LgSplitEachChar tinyint(1) unsigned NOT NULL DEFAULT '0', LgRightToLeft tinyint(1) unsigned NOT NULL DEFAULT '0', PRIMARY KEY (LgID), UNIQUE KEY LgName (LgName) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'sentences', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding sentences</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "sentences ( SeID mediumint(8) unsigned NOT NULL AUTO_INCREMENT, SeLgID tinyint(3) unsigned NOT NULL, SeTxID smallint(5) unsigned NOT NULL, SeOrder smallint(5) unsigned NOT NULL, SeText text, SeFirstPos smallint(5) unsigned NOT NULL, PRIMARY KEY (SeID), KEY SeLgID (SeLgID), KEY SeTxID (SeTxID), KEY SeOrder (SeOrder) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
        $count++;
    }
    
    if (in_array($tbpref . 'settings', $tables) == false) {
        if ($debug) {
             echo '<p>DEBUG: rebuilding settings</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "settings ( StKey varchar(40) NOT NULL, StValue varchar(40) DEFAULT NULL, PRIMARY KEY (StKey) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'textitems2', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding textitems2</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "textitems2 ( Ti2WoID mediumint(8) unsigned NOT NULL, Ti2LgID tinyint(3) unsigned NOT NULL, Ti2TxID smallint(5) unsigned NOT NULL, Ti2SeID mediumint(8) unsigned NOT NULL, Ti2Order smallint(5) unsigned NOT NULL, Ti2WordCount tinyint(3) unsigned NOT NULL, Ti2Text varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, PRIMARY KEY (Ti2TxID,Ti2Order,Ti2WordCount), KEY Ti2WoID (Ti2WoID)) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
        if (in_array($tbpref . 'textitems', $tables) != false) {
            //runsql('INSERT INTO ' . $tbpref . 'textitems2 (Ti2WoID,Ti2LgID,Ti2TxID,Ti2SeID,Ti2Order,Ti2WordCount,Ti2Text) select IFNULL(WoID,0), TiLgID,TiTxID, TiSeID, TiOrder, CASE WHEN TiIsNotWord = 1 THEN 0 ELSE TiWordCount END as WordCount, CASE WHEN STRCMP( TiText COLLATE utf8_bin ,TiTextLC)!=0 OR TiWordCount = 1 THEN TiText ELSE "" END as Text from ' . $tbpref . 'textitems left join ' . $tbpref . 'words on TiTextLC=WoTextLC and TiLgID=WoLgID where TiWordCount<2 or WoID IS NOT NULL','');
            runsql('TRUNCATE ' . $tbpref . 'textitems', '');
        }
        $count++;
    }


    if (in_array($tbpref . 'temptextitems', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding temptextitems</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "temptextitems ( TiCount smallint(5) unsigned NOT NULL, TiSeID mediumint(8) unsigned NOT NULL, TiOrder smallint(5) unsigned NOT NULL, TiWordCount tinyint(3) unsigned NOT NULL, TiText varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL) ENGINE=MEMORY DEFAULT CHARSET=utf8", '');
    }

    if (in_array($tbpref . 'tempwords', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding tempwords</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "tempwords (WoText varchar(250) DEFAULT NULL, WoTextLC varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, WoTranslation varchar(500) NOT NULL DEFAULT '*', WoRomanization varchar(100) DEFAULT NULL, WoSentence varchar(1000) DEFAULT NULL, WoTaglist varchar(255) DEFAULT NULL, PRIMARY KEY(WoTextLC) ) ENGINE=MEMORY DEFAULT CHARSET=utf8", '');
    }

    if (in_array($tbpref . 'texts', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding texts</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "texts ( TxID smallint(5) unsigned NOT NULL AUTO_INCREMENT, TxLgID tinyint(3) unsigned NOT NULL, TxTitle varchar(200) NOT NULL, TxText text NOT NULL, TxAnnotatedText longtext NOT NULL, TxAudioURI varchar(200) DEFAULT NULL, TxSourceURI varchar(1000) DEFAULT NULL, TxPosition smallint(5) DEFAULT 0, TxAudioPosition float DEFAULT 0, PRIMARY KEY (TxID), KEY TxLgID (TxLgID), KEY TxLgIDSourceURI (TxSourceURI(20),TxLgID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'words', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding words</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "words ( WoID mediumint(8) unsigned NOT NULL AUTO_INCREMENT, WoLgID tinyint(3) unsigned NOT NULL, WoText varchar(250) NOT NULL, WoTextLC varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, WoStatus tinyint(4) NOT NULL, WoTranslation varchar(500) NOT NULL DEFAULT '*', WoRomanization varchar(100) DEFAULT NULL, WoSentence varchar(1000) DEFAULT NULL, WoWordCount tinyint(3) unsigned NOT NULL DEFAULT 0, WoCreated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, WoStatusChanged timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', WoTodayScore double NOT NULL DEFAULT '0', WoTomorrowScore double NOT NULL DEFAULT '0', WoRandom double NOT NULL DEFAULT '0', PRIMARY KEY (WoID), UNIQUE KEY WoTextLCLgID (WoTextLC,WoLgID), KEY WoLgID (WoLgID), KEY WoStatus (WoStatus), KEY WoTranslation (WoTranslation(20)), KEY WoCreated (WoCreated), KEY WoStatusChanged (WoStatusChanged), KEY WoWordCount(WoWordCount), KEY WoTodayScore (WoTodayScore), KEY WoTomorrowScore (WoTomorrowScore), KEY WoRandom (WoRandom) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'tags', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding tags</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "tags ( TgID smallint(5) unsigned NOT NULL AUTO_INCREMENT, TgText varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, TgComment varchar(200) NOT NULL DEFAULT '', PRIMARY KEY (TgID), UNIQUE KEY TgText (TgText) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'wordtags', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding wordtags</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "wordtags ( WtWoID mediumint(8) unsigned NOT NULL, WtTgID smallint(5) unsigned NOT NULL, PRIMARY KEY (WtWoID,WtTgID), KEY WtTgID (WtTgID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'tags2', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding tags2</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "tags2 ( T2ID smallint(5) unsigned NOT NULL AUTO_INCREMENT, T2Text varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, T2Comment varchar(200) NOT NULL DEFAULT '', PRIMARY KEY (T2ID), UNIQUE KEY T2Text (T2Text) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'texttags', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding texttags</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "texttags ( TtTxID smallint(5) unsigned NOT NULL, TtT2ID smallint(5) unsigned NOT NULL, PRIMARY KEY (TtTxID,TtT2ID), KEY TtT2ID (TtT2ID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'newsfeeds', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding newsfeeds</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "newsfeeds (NfID tinyint(3) unsigned NOT NULL AUTO_INCREMENT,NfLgID tinyint(3) unsigned NOT NULL,NfName varchar(40) NOT NULL,NfSourceURI varchar(200) NOT NULL,NfArticleSectionTags text NOT NULL,NfFilterTags text NOT NULL,NfUpdate int(12) unsigned NOT NULL,NfOptions varchar(200) NOT NULL,PRIMARY KEY (NfID), KEY NfLgID (NfLgID), KEY NfUpdate (NfUpdate)) ENGINE=MyISAM  DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'feedlinks', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding feedlinks</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "feedlinks (FlID mediumint(8) unsigned NOT NULL AUTO_INCREMENT,FlTitle varchar(200) NOT NULL,FlLink varchar(400) NOT NULL,FlDescription text NOT NULL,FlDate datetime NOT NULL,FlAudio varchar(200) NOT NULL,FlText longtext NOT NULL,FlNfID tinyint(3) unsigned NOT NULL,PRIMARY KEY (FlID), KEY FlLink (FlLink), KEY FlDate (FlDate), UNIQUE KEY FlTitle (FlNfID,FlTitle)) ENGINE=MyISAM  DEFAULT CHARSET=utf8", '');
    }
    
    if (in_array($tbpref . 'archtexttags', $tables) == false) {
        if ($debug) { 
            echo '<p>DEBUG: rebuilding archtexttags</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS " . $tbpref . "archtexttags ( AgAtID smallint(5) unsigned NOT NULL, AgT2ID smallint(5) unsigned NOT NULL, PRIMARY KEY (AgAtID,AgT2ID), KEY AgT2ID (AgT2ID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
    }
    runsql('ALTER TABLE `' . $tbpref . 'sentences`  ADD SeFirstPos smallint(5) NOT NULL', '', $sqlerrdie = false);
    
    if ($count > 0) {        
        // Rebuild Text Cache if cache tables new
        if ($debug) { 
            echo '<p>DEBUG: rebuilding cache tables</p>'; 
        }
        reparse_all_texts();
    }
    
    // DB Version
    
    $currversion = get_version_number();
    
    $res = mysqli_query($GLOBALS['DBCONNECTION'], "select StValue as value from " . $tbpref . "settings where StKey = 'dbversion'");
    if (mysqli_errno($GLOBALS['DBCONNECTION']) != 0) { 
        my_die('There is something wrong with your database ' . $dbname . '. Please reinstall.'); 
    }
    $record = mysqli_fetch_assoc($res);
    if ($record) {
        $dbversion = $record["value"];
    } else {
        $dbversion = 'v001000000';
    }
    mysqli_free_result($res);
    
    // Do DB Updates if tables seem to be old versions
    
    if ($dbversion < $currversion ) {

        if ($debug) { 
            echo "<p>DEBUG: check DB collation: "; 
        }
        if('utf8utf8_general_ci' != get_first_value('SELECT concat(default_character_set_name, default_collation_name) as value FROM information_schema.SCHEMATA WHERE schema_name = "' . $dbname . '"')) {
            runsql("SET collation_connection = 'utf8_general_ci'", '');
            runsql('ALTER DATABASE ' . $dbname . ' CHARACTER SET utf8 COLLATE utf8_general_ci', '');
            if ($debug) { 
                echo 'changed to utf8_general_ci</p>'; 
            }
        }
        else if ($debug) { 
            echo 'OK</p>'; 
        }

        if ($debug) { 
            echo "<p>DEBUG: do DB updates: $dbversion --&gt; $currversion</p>"; 
        }
        runsql("ALTER TABLE " . $tbpref . "words ADD WoTodayScore DOUBLE NOT NULL DEFAULT 0, ADD WoTomorrowScore DOUBLE NOT NULL DEFAULT 0, ADD WoRandom DOUBLE NOT NULL DEFAULT 0", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "words ADD WoWordCount tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER WoSentence", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "words ADD INDEX WoTodayScore (WoTodayScore), ADD INDEX WoTomorrowScore (WoTomorrowScore), ADD INDEX WoRandom (WoRandom)", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "languages ADD LgRightToLeft tinyint(1) UNSIGNED NOT NULL DEFAULT  0", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "texts ADD TxAnnotatedText LONGTEXT NOT NULL AFTER TxText", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "archivedtexts ADD AtAnnotatedText LONGTEXT NOT NULL AFTER AtText", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "tags CHANGE TgComment TgComment VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "tags2 CHANGE T2Comment T2Comment VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "languages CHANGE LgGoogleTTSURI LgExportTemplate VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "texts ADD TxSourceURI VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "archivedtexts ADD AtSourceURI VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "texts ADD TxPosition smallint(5) NOT NULL DEFAULT  0", '', $sqlerrdie = false);
        runsql("ALTER TABLE " . $tbpref . "texts ADD TxAudioPosition float NOT NULL DEFAULT  0", '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'wordtags` DROP INDEX WtWoID', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'texttags` DROP INDEX TtTxID', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'archtexttags` DROP INDEX AgAtID', '', $sqlerrdie = false);

        runsql('ALTER TABLE `' . $tbpref . 'archivedtexts` MODIFY COLUMN `AtLgID` tinyint(3) unsigned NOT NULL, MODIFY COLUMN `AtID` smallint(5) unsigned NOT NULL, ADD INDEX AtLgIDSourceURI (AtSourceURI(20),AtLgID)', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'languages` MODIFY COLUMN `LgID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT, MODIFY COLUMN `LgRemoveSpaces` tinyint(1) unsigned NOT NULL, MODIFY COLUMN `LgSplitEachChar` tinyint(1) unsigned NOT NULL, MODIFY COLUMN `LgRightToLeft` tinyint(1) unsigned NOT NULL', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'sentences` MODIFY COLUMN `SeID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT, MODIFY COLUMN `SeLgID` tinyint(3) unsigned NOT NULL, MODIFY COLUMN `SeTxID` smallint(5) unsigned NOT NULL, MODIFY COLUMN `SeOrder` smallint(5) unsigned NOT NULL', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'texts` MODIFY COLUMN `TxID` smallint(5) unsigned NOT NULL AUTO_INCREMENT, MODIFY COLUMN `TxLgID` tinyint(3) unsigned NOT NULL, ADD INDEX TxLgIDSourceURI (TxSourceURI(20),TxLgID)', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'words` MODIFY COLUMN `WoID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT, MODIFY COLUMN `WoLgID` tinyint(3) unsigned NOT NULL, MODIFY COLUMN `WoStatus` tinyint(4) NOT NULL', '', $sqlerrdie = false);        
        runsql('ALTER TABLE `' . $tbpref . 'words` DROP INDEX WoTextLC', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'words` DROP INDEX WoLgIDTextLC, ADD UNIQUE INDEX WoTextLCLgID (WoTextLC,WoLgID)', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'words` ADD INDEX WoWordCount (WoWordCount)', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'archtexttags` MODIFY COLUMN `AgAtID` smallint(5) unsigned NOT NULL, MODIFY COLUMN `AgT2ID` smallint(5) unsigned NOT NULL', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'tags` MODIFY COLUMN `TgID` smallint(5) unsigned NOT NULL AUTO_INCREMENT', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'tags2` MODIFY COLUMN `T2ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'wordtags` MODIFY COLUMN `WtTgID` smallint(5) unsigned NOT NULL AUTO_INCREMENT', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'texttags` MODIFY COLUMN `TtTxID` smallint(5) unsigned NOT NULL, MODIFY COLUMN `TtT2ID` smallint(5) unsigned NOT NULL', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'temptextitems` ADD TiCount smallint(5) unsigned NOT NULL, DROP TiLgID, DROP TiTxID', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'temptextitems` ADD DROP INDEX TiTextLC', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'temptextitems` ADD  DROP TiTextLC', '', $sqlerrdie = false);
        runsql('ALTER TABLE `' . $tbpref . 'temptextitems` ADD TiCount smallint(5) unsigned NOT NULL', '', $sqlerrdie = false);
        runsql('UPDATE ' . $tbpref . 'sentences join ' . $tbpref . 'textitems2 on Ti2SeID=SeID and Ti2Order=SeFirstPos and Ti2WordCount=0 SET SeFirstPos=SeFirstPos+1', '', $sqlerrdie = false);
        if ($debug) { 
            echo '<p>DEBUG: rebuilding tts</p>'; 
        }
        runsql("CREATE TABLE IF NOT EXISTS tts ( TtsID mediumint(8) unsigned NOT NULL AUTO_INCREMENT, TtsTxt varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, TtsLc varchar(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, PRIMARY KEY (TtsID), UNIQUE KEY TtsTxtLC (TtsTxt,TtsLc) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=1", '');
        
        // set to current.
        saveSetting('dbversion', $currversion);
        saveSetting('lastscorecalc', '');  // do next section, too
    }

    // Do Scoring once per day, clean Word/Texttags, and optimize db
    $lastscorecalc = getSetting('lastscorecalc');
    $today = date('Y-m-d');
    if ($lastscorecalc != $today) {
        if ($debug) { 
            echo '<p>DEBUG: Doing score recalc. Today: ' . $today . ' / Last: ' . $lastscorecalc . '</p>'; 
        }
        runsql("UPDATE " . $tbpref . "words SET " . make_score_random_insert_update('u') ." where WoTodayScore>=-100 and WoStatus<98", '');
        runsql("DELETE " . $tbpref . "wordtags FROM (" . $tbpref . "wordtags LEFT JOIN " . $tbpref . "tags on WtTgID = TgID) WHERE TgID IS NULL", '');
        runsql("DELETE " . $tbpref . "wordtags FROM (" . $tbpref . "wordtags LEFT JOIN " . $tbpref . "words on WtWoID = WoID) WHERE WoID IS NULL", '');
        runsql("DELETE " . $tbpref . "texttags FROM (" . $tbpref . "texttags LEFT JOIN " . $tbpref . "tags2 on TtT2ID = T2ID) WHERE T2ID IS NULL", '');
        runsql("DELETE " . $tbpref . "texttags FROM (" . $tbpref . "texttags LEFT JOIN " . $tbpref . "texts on TtTxID = TxID) WHERE TxID IS NULL", '');
        runsql("DELETE " . $tbpref . "archtexttags FROM (" . $tbpref . "archtexttags LEFT JOIN " . $tbpref . "tags2 on AgT2ID = T2ID) WHERE T2ID IS NULL", '');
        runsql("DELETE " . $tbpref . "archtexttags FROM (" . $tbpref . "archtexttags LEFT JOIN " . $tbpref . "archivedtexts on AgAtID = AtID) WHERE AtID IS NULL", '');
        optimizedb();
        saveSetting('lastscorecalc', $today);
    }
}

// -------------------------------------------------------------

// --------------------  S T A R T  ---------------------------//


/**
 * Make the connection to the database.
 * 
 * @return mysqli|false Connection to the database
 */
function connect_to_database($server, $userid, $passwd, $dbname) 
{
    $DBCONNECTION = @mysqli_connect($server, $userid, $passwd, $dbname); // @ suppresses messages from function

    if ((!$DBCONNECTION) && mysqli_connect_errno() == 1049) {
        $DBCONNECTION = @mysqli_connect($server, $userid, $passwd);
        if (! $DBCONNECTION) { 
            my_die('DB connect error (MySQL not running or connection parameters are wrong; start MySQL and/or correct file "connect.inc.php"). Please read the documentation: https://learning-with-texts.sourceforge.io [Error Code: ' . mysqli_connect_errno() . ' / Error Message: ' . mysqli_connect_error() . ']'); 
        }
        runsql("CREATE DATABASE `" . $dbname . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci", '');
        mysqli_close($DBCONNECTION);
        $DBCONNECTION = @mysqli_connect($server, $userid, $passwd, $dbname);
    }

    if (!$DBCONNECTION) { 
        my_die('DB connect error (MySQL not running or connection parameters are wrong; start MySQL and/or correct file "connect.inc.php"). Please read the documentation: https://learning-with-texts.sourceforge.io [Error Code: ' . mysqli_connect_errno() . ' / Error Message: ' . mysqli_connect_error() . ']'); 
    }

    @mysqli_query($DBCONNECTION, "SET NAMES 'utf8'");

    // @mysqli_query($DBCONNECTION, "SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
    @mysqli_query($DBCONNECTION, "SET SESSION sql_mode = ''");
    return $DBCONNECTION;
}

/**
 * Get the prefixes for the database.
 * 
 * Is $tbpref set in connect.inc.php? Take it and $fixed_tbpref=1.
 * If not: $fixed_tbpref=0. Is it set in table "_lwtgeneral"? Take it.
 * If not: Use $tbpref = '' (no prefix, old/standard behaviour).
 * 
 * @param string|null $tbpref Temporary database table prefix
 * 
 * @return 0|1 Table Prefix is fixed, no changes possible
 */
function get_database_prefixes(&$tbpref) 
{
    // *** GLOBAL VARIABLES ***

    if (!isset($tbpref)) {
        $fixed_tbpref = 0;
        $p = LWTTableGet("current_table_prefix");
        $tbpref = isset($p) ? $p : '';
    } else {
        $fixed_tbpref = 1; 
    }

    $len_tbpref = strlen($tbpref); 
    if ($len_tbpref > 0) {
        if ($len_tbpref > 20) { 
            my_die('Table prefix/set "' . $tbpref . '" longer than 20 digits or characters. Please fix in "connect.inc.php".'); 
        }
        for ($i=0; $i < $len_tbpref; $i++) { 
            if (strpos("_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", substr($tbpref, $i, 1)) === false) {
                my_die('Table prefix/set "' . $tbpref . '" contains characters or digits other than 0-9, a-z, A-Z or _. Please fix in "connect.inc.php".'); 
            } 
        } 
    }

    if (!$fixed_tbpref) { 
        LWTTableSet("current_table_prefix", $tbpref); 
    }

    // *******************************************************************
    // IF PREFIX IS NOT '', THEN ADD A '_', TO ENSURE NO IDENTICAL NAMES
    if ($tbpref !== '') { 
        $tbpref .= "_"; 
    }
    return $fixed_tbpref;
}
// *******************************************************************

// Start Timer
if (!empty($dspltime)) {
    get_execution_time(); 
}
$DBCONNECTION = connect_to_database($server, $userid, $passwd, $dbname);
$tbpref = null;
$fixed_tbpref = get_database_prefixes($tbpref);
// check/update db
check_update_db($debug, $tbpref, $dbname);

?>
