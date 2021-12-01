
<?php

/**
 * \file
 * \brief Connects to the database and check its state.
 * 
 * @author https://github.com/HugoFara/ HugoFara
 */

require __DIR__ . "/settings.php";
require __DIR__ . "/../connect.inc.php";

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
    if($data != "") { $result = "'" . mysqli_real_escape_string($GLOBALS['DBCONNECTION'], $data) . "'"; 
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

// -------------------------------------------------------------

function LWTTableCheck() 
{
    if (mysqli_num_rows(do_mysqli_query("SHOW TABLES LIKE '\\_lwtgeneral'")) == 0) {
        runsql("CREATE TABLE IF NOT EXISTS _lwtgeneral ( LWTKey varchar(40) NOT NULL, LWTValue varchar(40) DEFAULT NULL, PRIMARY KEY (LWTKey) ) ENGINE=MyISAM DEFAULT CHARSET=utf8", '');
        if (mysqli_num_rows(do_mysqli_query("SHOW TABLES LIKE '\\_lwtgeneral'")) == 0) { my_die("Unable to create table '_lwtgeneral'!"); 
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


// -------------------------------------------------------------

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



function connect_to_database($server, $userid, $passwd, $dbname) 
{
    /**
     * @var mysqli|false|null $DBCONNECTION
     * Connection to the database
     */
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

function get_database_prefixes($tbpref) 
{
    // *** GLOBAL VARIABLES ***
    /**
     * @var string $tbpref 
     * Current Table Prefix
     */
    /**
     * @var string $fixed_tbpref
     * Table Prefix is fixed, no changes possible
     */

    // Is $tbpref set in connect.inc.php? Take it and $fixed_tbpref=1.
    // If not: $fixed_tbpref=0. Is it set in table "_lwtgeneral"? Take it.
    // If not: Use $tbpref = '' (no prefix, old/standard behaviour).

    if (!isset($tbpref)) {
        $fixed_tbpref = 0;
        $p = LWTTableGet("current_table_prefix");
        if (isset($p)) { 
            $tbpref = $p; 
        } else {
            $tbpref = '';
        }
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
    return array($tbpref, $fixed_tbpref);
}
// *******************************************************************


function start_database_connection() 
{
    global $DBCONNECTION, $server, $userid, $passwd, $dbname, 
    $debug, $tbpref, $fixed_tbpref, $dspltime;
    // Start Timer
    if (!empty($dspltime)) {
        get_execution_time(); 
    }
    $DBCONNECTION = connect_to_database($server, $userid, $passwd, $dbname);
    $prefixes = get_database_prefixes($tbpref);
    $tbpref = $prefixes[0];
    $fixed_tbpref = $prefixes[1];
    // check/update db
    check_update_db($debug, $tbpref, $dbname);
}

start_database_connection();

?>
