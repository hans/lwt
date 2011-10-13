<?php

function anki_export($sql) {
	// WoID, LgRightToLeft, LgRegexpWordCharacters, LgName, WoText, WoTranslation, WoRomanization, WoSentence, taglist
	$res = mysql_query($sql);
	$x = '';
	if ($res == FALSE) die("Invalid Query: $sql");
	while ($record = mysql_fetch_assoc($res)) {
		$rtlScript = $record['LgRightToLeft'];
		$span1 = ($rtlScript ? '<span dir="rtl">' : '');
		$span2 = ($rtlScript ? '</span>' : '');
		$lpar = ($rtlScript ? ']' : '[');
		$rpar = ($rtlScript ? '[' : ']');
		$sent = tohtml(repl_tab_nl($record["WoSentence"]));
		$sent1 = str_replace("{", '<span style="font-weight:600; color:#0000ff;">' . $lpar, str_replace("}", $rpar . '</span>',
			mask_term_in_sentence($sent,$record["LgRegexpWordCharacters"])
		));
		$sent2 = str_replace("{", '<span style="font-weight:600; color:#0000ff;">', str_replace("}", '</span>', $sent));
		$x .= $span1 . tohtml(repl_tab_nl($record["WoText"])) . $span2 . "\t" .
		tohtml(repl_tab_nl($record["WoTranslation"])) . "\t" .
		tohtml(repl_tab_nl($record["WoRomanization"])) . "\t" .
		$span1 . $sent1 . $span2 . "\t" .
		$span1 . $sent2 . $span2 . "\t" .
		tohtml(repl_tab_nl($record["LgName"])) . "\t" .
		tohtml($record["WoID"]) . "\t" .
		tohtml($record["taglist"]) .
		"\r\n";
	}
	mysql_free_result($res);
	header('Content-type: text/plain; charset=utf-8');
	header("Content-disposition: attachment; filename=lwt_anki_export.txt");
	echo $x;
	exit();
}

function tsv_export($sql) {
	// WoID, LgName, WoText, WoTranslation, WoRomanization, WoSentence, WoStatus, taglist
	$res = mysql_query($sql);
	$x = '';
	if ($res == FALSE) die("Invalid Query: $sql");
	while ($record = mysql_fetch_assoc($res)) {
		$x .= repl_tab_nl($record["WoText"]) . "\t" .
		repl_tab_nl($record["WoTranslation"]) . "\t" .
		repl_tab_nl($record["WoSentence"]) . "\t" .
		repl_tab_nl($record["WoRomanization"]) . "\t" .
		$record["WoStatus"] . "\t" .
		repl_tab_nl($record["LgName"]) . "\t" .
		$record["WoID"] . "\t" .
		$record["taglist"] . "\r\n";
	}
	mysql_free_result($res);
	header('Content-type: text/plain; charset=utf-8');
	header("Content-disposition: attachment; filename=lwt_tsv_export.txt");
	echo $x;
	exit();
}

?>