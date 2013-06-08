<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Call: display_impr_text_header.php?text=[textid]
Display an improved annotated text (top frame)
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
echo '<h2 class="center" style="margin:5px;margin-top:-10px;">';

?>

<script type="text/javascript">
//<![CDATA[
	function do_hide_t() {
		$('#showt').show(); 
		$('#hidet').hide();
		$('.anntermruby', window.parent.frames['text'].document).css('color','#E5E4E2').css('background-color', '#E5E4E2');
	}
	function do_show_t() {
		$('#showt').hide(); 
		$('#hidet').show(); 
		$('.anntermruby', window.parent.frames['text'].document).css('color','black').css('background-color', 'white');
	}
	function do_hide_a() {
		$('#show').show(); 
		$('#hide').hide(); 
		$('.anntransruby2', window.parent.frames['text'].document).css('color','#C8DCF0').css('background-color', '#C8DCF0');
	}
	function do_show_a() {
		$('#show').hide(); 
		$('#hide').show(); 
		$('.anntransruby2', window.parent.frames['text'].document).css('color','#006699').css('background-color', 'white');
	}
//]]>
</script>

<?php

echo '<img id="hidet" style="margin-bottom:-5px;" class="click" src="icn/light-bulb-T.png" title="Toggle Text Display (Now ON)" alt="Toggle Text Display (Now ON)" onclick="do_hide_t();" />';
echo '<img id="showt" style="display:none; margin-bottom:-5px;" class="click" src="icn/light-bulb-off-T.png" title="Toggle Text Display (Now OFF)" alt="Toggle Text Display (Now OFF)" onclick="do_show_t();" />';
echo '<img id="hide" style="margin-bottom:-5px;" class="click" src="icn/light-bulb-A.png" title="Toggle Annotation Display (Now ON)" alt="Toggle Annotation Display (Now ON)" onclick="do_hide_a();" />';
echo '<img id="show" style="display:none; margin-bottom:-5px;" class="click" src="icn/light-bulb-off-A.png" title="Toggle Annotation Display (Now OFF)" alt="Toggle Annotation Display (Now OFF)" onclick="do_show_a();" />';
echo '&nbsp; &nbsp;';
echo tohtml($title);
echo getPreviousAndNextTextLinks($textid, 'display_impr_text.php?text=', TRUE, '&nbsp; &nbsp;');
echo ' <img class="click" src="icn/cross.png" title="Close Window" alt="Close Window" onclick="top.close();" /></span></h2>';

if ($audio != '') {
	$playerskin = getSettingWithDefault('set-player-skin-name');
?>
<link type="text/css" href="css/jplayer_skin/<?php echo $playerskin; ?>.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery.jplayer.min.js"><!-- jPlayer © Happyworm ** http://www.jplayer.org/about/ --></script>
<table class="width99pc">
<tr>
<td class="width45pc">&nbsp;</td>
<td class="center">
<span id="do-single" class="click hide"><img src="icn/arrow-repeat.png" alt="Toggle Repeat (Now ON)" title="Toogle Repeat (Now ON)" style="width:24px;height:24px;" /></span><span id="do-repeat" class="click"><img src="icn/arrow-norepeat.png" alt="Toggle Repeat (Now OFF)" title="Toggle Repeat (Now OFF)" style="width:24px;height:24px;" /></span>
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
