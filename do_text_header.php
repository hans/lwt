<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Call: do_text_header.php?text=[textid]
Show text header frame
***************************************************************/

include "connect.inc.php";
include "settings.inc.php";
include "utilities.inc.php";

$textid = getreq('text');
$sql = 'select TxLgID, TxTitle, TxAudioURI from texts where TxID = ' . $textid;
$res = mysql_query($sql);		
if ($res == FALSE) die("Invalid Query: $sql");
$record = mysql_fetch_assoc($res);

$audio = $record['TxAudioURI'];
if(!isset($audio)) $audio='';
$audio=trim($audio);

$title = $record['TxTitle'];
$langid = $record['TxLgID'];
mysql_free_result($res); 

saveSetting('currenttext',$textid);

pagestart_nobody(tohtml($title));
echo '<h4>';
echo '<a href="edit_texts.php" target="_top">';
echo '<img src="img/lwt_icon.png" class="lwtlogo" alt="Logo" />Learning with Texts';
echo '</a>&nbsp; | &nbsp;';
quickMenu();
echo '&nbsp; | &nbsp;<a href="do_test.php?text=' . $textid . '" target="_top"><img src="icn/question-balloon.png" title="Test" alt="Test" /></a> &nbsp;<a href="print_text.php?text=' . $textid . '" target="_top"><img src="icn/printer.png" title="Print" alt="Print" /> &nbsp;<a target="_top" href="edit_texts.php?chg=' . $textid . '"><img src="icn/document--pencil.png" title="Edit Text" alt="Edit Text" /></a> &nbsp;<a href="new_word.php?text=' . $textid . '&amp;lang=' . $langid . '" target="ro"><img src="icn/sticky-note--plus.png" title="New Term" alt="New Term" /></a>';
echo '</h4><table><tr><td><h3>READ&nbsp;▶</h3></td><td class="width99pc"><h3>' . tohtml($title) . '</h3></td></tr></table>';

$showAll = getSetting('showallwords');
$showAll = ($showAll == '' ? 1 : (((int) $showAll != 0) ? 1 : 0));

?>
<table class="width99pc"><tr><td class="center" colspan="7" style="padding:10px;" nowrap="nowrap">TO DO: <span id="learnstatus"><?php echo texttodocount2($_REQUEST['text']); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span title="[Show All] = ON: ALL terms are shown, and all multi-word terms are shown as superscripts before the first word. The superscript indicates the number of words in the multi-word term. 
[Show All] = OFF: Multi-word terms now hide single words and shorter or overlapping multi-word terms.">Show All&nbsp;<input type="checkbox" id="showallwords" <?php echo get_checked($showAll); ?> /></span><span id="thetextid" class="hide"><?php echo $textid; ?></span></td></tr>

<?php

// AUDIO PLAYER

if ($audio != '') {
	$playerskin = getSettingWithDefault('set-player-skin-name');
?>
<link type="text/css" href="css/jplayer_skin/<?php echo $playerskin; ?>.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery.jplayer.min.js"><!-- jPlayer © Happyworm ** http://www.jplayer.org/about/ --></script>
<tr>
<td class="width45pc">&nbsp;</td>
<td class="center">
<span id="do-single" class="click hide"><img src="icn/arrow-stop.png" alt="Do not repeat" title="Do not repeat" style="width:24px;height:24px;" /></span><span id="do-repeat" class="click"><img src="icn/arrow-repeat.png" alt="Repeat audio" title="Repeat audio" style="width:24px;height:24px;" /></span>
</td>
<td class="center">&nbsp;</td>
<td>
<div id="jquery_jplayer_1" class="jp-jplayer">
</div>
<div class="jp-audio-container">
	<div class="jp-audio">
		<div class="jp-type-single">
			<div id="jp_interface_1" class="jp-interface">
				<ul class="jp-controls">
					<li><a href="#" class="jp-play" tabindex="1">play</a></li>
					<li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
<?php if (substr($playerskin,0,13) != 'jplayer-black') { ?>
					<li><a href="#" class="jp-stop" tabindex="1">stop</a></li>
<?php } ?>
					<li><a href="#" class="jp-mute" tabindex="1">mute</a></li>
					<li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>
				</ul>
				<div class="jp-progress-container">
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar">
							</div>
						</div>
					</div>
				</div>
				<div class="jp-volume-bar-container">
					<div class="jp-volume-bar">
						<div class="jp-volume-bar-value">
						</div>
					</div>
				</div>
<?php if (substr($playerskin,0,13) != 'jplayer-black') { ?>
				<div class="jp-current-time">
				</div>
				<div class="jp-duration">
				</div>
<?php } ?>
			</div>
<?php if (substr($playerskin,0,13) != 'jplayer-black') { ?>
			<div id="jp_playlist_1" class="jp-playlist">
			</div>
<?php } ?>
		</div>
	</div>
</div>
</td>
<td class="center">&nbsp;</td>
<td class="center">
<?php
$currentplayerseconds = getSetting('currentplayerseconds');
if($currentplayerseconds == '') $currentplayerseconds = 5;
?>
<select id="backtime" name="backtime" onchange="{do_ajax_save_setting('currentplayerseconds',document.getElementById('backtime').options[document.getElementById('backtime').selectedIndex].value);}"><?php echo get_seconds_selectoptions($currentplayerseconds); ?></select><br />
<span id="backbutt" class="click"><img src="icn/arrow-circle-225-left.png" alt="Rewind n seconds" title="Rewind n seconds" /></span>&nbsp;&nbsp;<span id="forwbutt" class="click"><img src="icn/arrow-circle-315.png" alt="Forward n seconds" title="Forward n seconds" /></span>
<span id="playTime" class="hide"></span>
</td>
<td class="width45pc">&nbsp;</td>
</tr>
<script type="text/javascript">
//<![CDATA[

function new_pos(p) {
	$("#jquery_jplayer_1").jPlayer("playHead", p);
}

function click_single() {
	$("#jquery_jplayer_1").unbind($.jPlayer.event.ended + ".jp-repeat");
	$("#do-single").addClass('hide');
	$("#do-repeat").removeClass('hide');
	return false;
}

function click_repeat() {
	$("#jquery_jplayer_1").bind($.jPlayer.event.ended + ".jp-repeat", function(event) { 
		$(this).jPlayer("play"); 
	});
	$("#do-repeat").addClass('hide');
	$("#do-single").removeClass('hide');
	return false;
}

function click_back() {
	var t = parseInt($("#playTime").text(),10);
	var b = parseInt($("#backtime").val(),10);
	var nt = t - b;
	if (nt < 0) nt = 0;
	$("#jquery_jplayer_1").jPlayer("play", nt);
}

function click_forw() {
	var t = parseInt($("#playTime").text(),10);
	var b = parseInt($("#backtime").val(),10);
	var nt = t + b;
	$("#jquery_jplayer_1").jPlayer("play", nt);
}

$(document).ready(function(){
  $("#jquery_jplayer_1").jPlayer({
    ready: function () {
      $(this).jPlayer("setMedia", { 
<?php 
	$audio = trim($audio);
	if (strcasecmp(substr($audio,-4), '.mp3') == 0) { 
  	echo 'mp3: ' . prepare_textdata_js(encodeURI($audio)); 
  } elseif (strcasecmp(substr($audio,-4), '.ogg') == 0) { 
  	echo 'oga: ' . prepare_textdata_js(encodeURI($audio))  . ",\n" . 
  			 'mp3: ' . prepare_textdata_js(encodeURI($audio)); 
  } elseif (strcasecmp(substr($audio,-4), '.wav') == 0) {
  	echo 'wav: ' . prepare_textdata_js(encodeURI($audio))  . ",\n" . 
  			 'mp3: ' . prepare_textdata_js(encodeURI($audio)); 
  } else {
  	echo 'mp3: ' . prepare_textdata_js(encodeURI($audio)); 
  }
?>
      });
    },
    swfPath: "js",
  });
  
  $("#jquery_jplayer_1").bind($.jPlayer.event.timeupdate, function(event) { 
  	$("#playTime").text(Math.floor(event.jPlayer.status.currentTime));
	});
  
  $("#backbutt").click(click_back);
  $("#forwbutt").click(click_forw);
  $("#do-single").click(click_single);
  $("#do-repeat").click(click_repeat);
});
//]]>
</script>
<?php
} // if (isset($audio))

// END AUDIO

?>
</table>
<?php

pageend();

?>