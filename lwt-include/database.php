<?php
/**
 * Database functions.
 *
 * @package LWT
 * @subpackage Database
 * @since 2.0
 */

/**
 * @global PDO $lwt_db PDO connection
 */
$lwt_db = null;

/**
 * Connect to the database and configure the connection.
 */
function db_connect() {
    global $lwt_db;
    $lwt_db = new PDO('mysql:host=' . LWT_SERVER . ';dbname=' . LWT_DB_NAME . ';charset=UTF-8',
                      LWT_DB_USER, LWT_DB_PASSWORD);

    // Set error level based on LWT_DEBUG value
    $lwt_db->setAttribute(PDO::ATTR_ERRMODE,
                          ( LWT_DEBUG
                            ? PDO::ERRMODE_EXCEPTION
                            : PDO::ERRMODE_EXCEPTION ) );

    // check/update db
    check_update_db();
}

function runsql($sql, $m) {
    global $lwt_db;

    $affected = $lwt_db->exec($sql);
		$message = ( ( $m == '' ) ? $affected : ($m . ": " . $affected ) );

    return $message;
}

function optimizedb() {
	adjust_autoincr('archivedtexts','AtID');
	adjust_autoincr('languages','LgID');
	adjust_autoincr('sentences','SeID');
	adjust_autoincr('textitems','TiID');
	adjust_autoincr('texts','TxID');
	adjust_autoincr('words','WoID');
	adjust_autoincr('tags','TgID');
	adjust_autoincr('tags2','T2ID');
	$dummy = runsql('OPTIMIZE TABLE archivedtexts,languages,sentences,textitems,texts,words,settings,tags,wordtags,tags2,texttags,archtexttags', '');
}

function convert_string_to_sqlsyntax($data) {
    $data = trim(prepare_textdata($data));
    if($data != "") $result = "'" . sanitize($data) . "'";

    return $result;
}

function convert_string_to_sqlsyntax_nonull($data) {
    $data = trim(prepare_textdata($data));
    return  "'" . sanitize($data) . "'";
}

function convert_string_to_sqlsyntax_notrim_nonull($data) {
    return "'" . sanitize(prepare_textdata($data)) . "'";
}

function get_first_value($sql) {
    global $lwt_db;

    $stmt = $lwt_db->query($sql);
    if ( $stmt == FALSE )
        die("Invalid query: $sql");

    $result = $stmt->fetchColumn();
    $stmt->closeCursor();

    return $result;
}

function get_last_key() {
	return get_first_value('SELECT LAST_INSERT_ID() as value');
}

function check_update_db() {
	$tables = array();

	$res = mysql_query("SHOW TABLES");
	if ($res == FALSE) die("SHOW TABLES error");
  while ($row = mysql_fetch_row($res))
  	$tables[] = $row[0];
	mysql_free_result($res);

	$count = 0;  // counter for cache rebuild

	// Rebuild Tables if missing

	if (in_array('archivedtexts', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding archivedtexts</p>';
		runsql("CREATE TABLE IF NOT EXISTS archivedtexts ( AtID int(11) unsigned NOT NULL AUTO_INCREMENT, AtLgID int(11) unsigned NOT NULL, AtTitle varchar(200) NOT NULL, AtText text NOT NULL, AtAudioURI varchar(200) DEFAULT NULL, PRIMARY KEY (AtID), KEY AtLgID (AtLgID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('languages', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding languages</p>';
		runsql("CREATE TABLE IF NOT EXISTS languages ( LgID int(11) unsigned NOT NULL AUTO_INCREMENT, LgName varchar(40) NOT NULL, LgDict1URI varchar(200) NOT NULL, LgDict2URI varchar(200) DEFAULT NULL, LgGoogleTranslateURI varchar(200) DEFAULT NULL, LgGoogleTTSURI varchar(200) DEFAULT NULL, LgTextSize int(5) unsigned NOT NULL DEFAULT '100', LgCharacterSubstitutions varchar(500) NOT NULL, LgRegexpSplitSentences varchar(500) NOT NULL, LgExceptionsSplitSentences varchar(500) NOT NULL, LgRegexpWordCharacters varchar(500) NOT NULL, LgRemoveSpaces int(1) unsigned NOT NULL DEFAULT '0', LgSplitEachChar int(1) unsigned NOT NULL DEFAULT '0', PRIMARY KEY (LgID), UNIQUE KEY LgName (LgName) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('sentences', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding sentences</p>';
		runsql("CREATE TABLE IF NOT EXISTS sentences ( SeID int(11) unsigned NOT NULL AUTO_INCREMENT, SeLgID int(11) unsigned NOT NULL, SeTxID int(11) unsigned NOT NULL, SeOrder int(11) unsigned NOT NULL, SeText text, PRIMARY KEY (SeID), KEY SeLgID (SeLgID), KEY SeTxID (SeTxID), KEY SeOrder (SeOrder) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
		$count++;
	}

	if (in_array('settings', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding settings</p>';
		runsql("CREATE TABLE IF NOT EXISTS settings ( StKey varchar(40) NOT NULL, StValue varchar(40) DEFAULT NULL, PRIMARY KEY (StKey) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('textitems', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding textitems</p>';
		runsql("CREATE TABLE IF NOT EXISTS textitems ( TiID int(11) unsigned NOT NULL AUTO_INCREMENT, TiLgID int(11) unsigned NOT NULL, TiTxID int(11) unsigned NOT NULL, TiSeID int(11) unsigned NOT NULL, TiOrder int(11) unsigned NOT NULL, TiWordCount int(1) unsigned NOT NULL, TiText varchar(250) NOT NULL, TiTextLC varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, TiIsNotWord tinyint(1) NOT NULL, PRIMARY KEY (TiID), KEY TiLgID (TiLgID), KEY TiTxID (TiTxID), KEY TiSeID (TiSeID), KEY TiOrder (TiOrder), KEY TiTextLC (TiTextLC), KEY TiIsNotWord (TiIsNotWord) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
		$count++;
	}

	if (in_array('texts', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding texts</p>';
		runsql("CREATE TABLE IF NOT EXISTS texts ( TxID int(11) unsigned NOT NULL AUTO_INCREMENT, TxLgID int(11) unsigned NOT NULL, TxTitle varchar(200) NOT NULL, TxText text NOT NULL, TxAudioURI varchar(200) DEFAULT NULL, PRIMARY KEY (TxID), KEY TxLgID (TxLgID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('words', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding words</p>';
		runsql("CREATE TABLE IF NOT EXISTS words ( WoID int(11) unsigned NOT NULL AUTO_INCREMENT, WoLgID int(11) unsigned NOT NULL, WoText varchar(250) NOT NULL, WoTextLC varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, WoStatus tinyint(4) NOT NULL, WoTranslation varchar(500) NOT NULL DEFAULT '*', WoRomanization varchar(100) DEFAULT NULL, WoSentence varchar(1000) DEFAULT NULL, WoCreated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, WoStatusChanged timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (WoID), UNIQUE KEY WoLgIDTextLC (WoLgID,WoTextLC), KEY WoLgID (WoLgID), KEY WoStatus (WoStatus), KEY WoTextLC (WoTextLC), KEY WoTranslation (WoTranslation(333)), KEY WoCreated (WoCreated), KEY WoStatusChanged (WoStatusChanged) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('tags', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding tags</p>';
		runsql("CREATE TABLE IF NOT EXISTS tags ( TgID int(11) unsigned NOT NULL AUTO_INCREMENT, TgText varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, TgComment varchar(200) NOT NULL, PRIMARY KEY (TgID), UNIQUE KEY TgText (TgText) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('wordtags', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding wordtags</p>';
		runsql("CREATE TABLE IF NOT EXISTS wordtags ( WtWoID int(11) unsigned NOT NULL, WtTgID int(11) unsigned NOT NULL, PRIMARY KEY (WtWoID,WtTgID), KEY WtTgID (WtTgID), KEY WtWoID (WtWoID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('tags2', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding tags2</p>';
		runsql("CREATE TABLE IF NOT EXISTS tags2 ( T2ID int(11) unsigned NOT NULL AUTO_INCREMENT, T2Text varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, T2Comment varchar(200) NOT NULL, PRIMARY KEY (T2ID), UNIQUE KEY T2Text (T2Text) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('texttags', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding texttags</p>';
		runsql("CREATE TABLE IF NOT EXISTS texttags ( TtTxID int(11) unsigned NOT NULL, TtT2ID int(11) unsigned NOT NULL, PRIMARY KEY (TtTxID,TtT2ID), KEY TtTxID (TtTxID), KEY TtT2ID (TtT2ID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if (in_array('archtexttags', $tables) == FALSE) {
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding archtexttags</p>';
		runsql("CREATE TABLE IF NOT EXISTS archtexttags ( AgAtID int(11) unsigned NOT NULL, AgT2ID int(11) unsigned NOT NULL, PRIMARY KEY (AgAtID,AgT2ID), KEY AgAtID (AgAtID), KEY AgT2ID (AgT2ID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8",'');
	}

	if ($count > 0) {
		// Rebuild Text Cache if cache tables new
		if (LWT_DEBUG) echo '<p>DEBUG: rebuilding cache tables</p>';
		$sql = "select TxID, TxLgID from texts";
		$res = mysql_query($sql);
		if ($res == FALSE) die("Invalid Query: $sql");
		while ($record = mysql_fetch_assoc($res)) {
			$id = $record['TxID'];
			runsql('delete from sentences where SeTxID = ' . $id, "");
			runsql('delete from textitems where TiTxID = ' . $id, "");
			adjust_autoincr('sentences','SeID');
			adjust_autoincr('textitems','TiID');
			splitText(
				get_first_value('select TxText as value from texts where TxID = ' . $id), $record['TxLgID'], $id );
		}
		mysql_free_result($res);
	}

	// Version

	$res = mysql_query("select StValue as value from settings where StKey = 'dbversion'");
	if (mysql_errno() != 0) die('There is something wrong with your database ' . LWT_DB_NAME . '. Please reinstall.');
	$record = mysql_fetch_assoc($res);
	if ($record) {
		$dbversion = $record["value"];
	} else {
		$dbversion = 'v001000000';
		saveSetting('dbversion',$dbversion);
		if (LWT_DEBUG) echo '<p>DEBUG: DB version not found, set to: ' . $dbversion . '</p>';
	}
	mysql_free_result($res);

	// Do DB Updates

	$currversion = get_version_number();
	if ( $currversion > $dbversion ) {
		if ($currversion > 'v001000000') {
			// updates for all versions > 1.0.0
			if (LWT_DEBUG) echo '<p>DEBUG: Doing db-upgrade ' . $currversion . ' &gt; v001000000</p>';
			runsql("ALTER TABLE words ADD WoTodayScore DOUBLE NOT NULL DEFAULT 0, ADD WoTomorrowScore DOUBLE NOT NULL DEFAULT 0, ADD WoRandom DOUBLE NOT NULL DEFAULT 0",'');
			runsql("ALTER TABLE words ADD INDEX WoTodayScore (WoTodayScore), ADD INDEX WoTomorrowScore (WoTomorrowScore), ADD INDEX WoRandom (WoRandom)",'');
			runsql("UPDATE words SET " . make_score_random_insert_update('u'),'');
		}
		if ($currversion > 'v001001001') {
			if (LWT_DEBUG) echo '<p>DEBUG: Doing db-upgrade ' . $currversion . ' &gt; v001001001</p>';
			// updates for all versions > 1.1.1 :
			// New: Table "tags", created above
			// New: Table "wordtags", created above
		}
		if ($currversion > 'v001002002') {
			if (LWT_DEBUG) echo '<p>DEBUG: Doing db-upgrade ' . $currversion . ' &gt; v001002002</p>';
			// updates for all versions > 1.2.2 :
			// New: Table "tags2", created above
			// New: Table "texttags", created above
			// New: Table "archtexttags", created above
			runsql("ALTER TABLE languages ADD LgRightToLeft INT(1) UNSIGNED NOT NULL DEFAULT  0",'');
		}
		// set to current.
		saveSetting('dbversion',$currversion);
	}

	// Do Scoring once per day, clean Word/Texttags, and optimize db

	$lastscorecalc = getSetting('lastscorecalc');
	$today = date('Y-m-d');
	if ($lastscorecalc != $today) {
		if (LWT_DEBUG) echo '<p>DEBUG: Doing score recalc. Today: ' . $today . ' / Last: ' . $lastscorecalc . '</p>';
		runsql("UPDATE words SET " . make_score_random_insert_update('u'),'');
		runsql("DELETE wordtags FROM (wordtags LEFT JOIN tags on WtTgID = TgID) WHERE TgID IS NULL",'');
		runsql("DELETE wordtags FROM (wordtags LEFT JOIN words on WtWoID = WoID) WHERE WoID IS NULL",'');
		runsql("DELETE texttags FROM (texttags LEFT JOIN tags2 on TtT2ID = T2ID) WHERE T2ID IS NULL",'');
		runsql("DELETE texttags FROM (texttags LEFT JOIN texts on TtTxID = TxID) WHERE TxID IS NULL",'');
		runsql("DELETE archtexttags FROM (archtexttags LEFT JOIN tags2 on AgT2ID = T2ID) WHERE T2ID IS NULL",'');
		runsql("DELETE archtexttags FROM (archtexttags LEFT JOIN archivedtexts on AgAtID = AtID) WHERE AtID IS NULL",'');
		optimizedb();
		saveSetting('lastscorecalc',$today);
	}
}

?>