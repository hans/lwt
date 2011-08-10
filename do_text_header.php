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
if ($res == FALSE) die("<p>Invalid query: $sql</p>");
$dsatz = mysql_fetch_assoc($res);

$audio = $dsatz['TxAudioURI'];
if(!isset($audio)) $audio='';
$audio=trim($audio);

$titel = $dsatz['TxTitle'];
$sprid = $dsatz['TxLgID'];
mysql_free_result($res); 

saveSetting('currenttext',$textid);

pagestart_nobody(tohtml($titel));
echo '<h4>';
echo '<a href="edit_texts.php" target="_top">';
echo '<img src="img/lwt_icon.png" class="lwtlogo" alt="Logo" />Learning with Texts';
echo '</a>&nbsp; | &nbsp;';
quickMenu();
echo '&nbsp; | &nbsp;<a href="do_test.php?text=' . $textid . '" target="_top"><img src="icn/question-balloon.png" title="Test" alt="Test" /></a>&nbsp; &nbsp;<a href="print_text.php?text=' . $textid . '" target="_top"><img src="icn/printer.png" title="Print" alt="Print" /></a>';
echo '</h4><table><tr><td><h3>READ&nbsp;▶</h3></td><td class="width99pc"><h3>' . tohtml($titel) . '</h3></td></tr></table>';

$showAll = getSetting('showallwords');
$showAll = ($showAll == '' ? 1 : (((int) $showAll != 0) ? 1 : 0));

?>
<table class="width99pc"><tr><td class="center" colspan="3" style="padding:10px;" nowrap="nowrap">TO DO: <span id="learnstatus"><?php echo texttodocount2($_REQUEST['text']); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span title="[Show All] = ON: ALL terms are shown, and all multi-word terms are shown as superscripts before the first word. The superscript indicates the number of words in the multi-word term. 
[Show All] = OFF: Multi-word terms now hide single words and shorter or overlapping multi-word terms.">Show All&nbsp;<input type="checkbox" id="showallwords" <?php echo get_checked($showAll); ?> /></span><span id="thetextid" class="hide"><?php echo $textid; ?></span></td></tr>

<?php

// AUDIO PLAYER

if ($audio != '') {
	$playerskin = getSettingWithDefault('set-player-skin-name');
?>
<link type="text/css" href="css/jplayer_skin/<?php echo $playerskin; ?>.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery.jplayer.min.js"><!-- jPlayer © Happyworm ** http://www.jplayer.org/about/ --></script>
<tr><td class="width45pc">&nbsp;</td><td>
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
</td><td class="width45pc">&nbsp;</td></tr>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
  $("#jquery_jplayer_1").jPlayer({
    ready: function () {
      $(this).jPlayer("setMedia", {
        mp3: "<?php echo trim($audio); ?>",
      });
    },
    swfPath: "js",
  });
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