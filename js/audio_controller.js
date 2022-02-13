/**
 * \file
 * \brief All the function to make an audio controller in do_text_header.php
 * 
 * @license Unlicense
 */
function new_pos(p){$("#jquery_jplayer_1").jPlayer("playHead",p)}
function set_new_playerseconds(){var newval=($("#backtime :selected").val());do_ajax_save_setting('currentplayerseconds',newval)}
function set_new_playbackrate(){var newval=($("#playbackrate :selected").val());do_ajax_save_setting('currentplaybackrate',newval);$("#jquery_jplayer_1").jPlayer("option","playbackRate",newval*0.1)}
function set_current_playbackrate(){var val=($("#playbackrate :selected").val());$("#jquery_jplayer_1").jPlayer("option","playbackRate",val*0.1)}
function click_single(){$("#jquery_jplayer_1").off('bind',$.jPlayer.event.ended+".jp-repeat");$("#do-single").addClass('hide');$("#do-repeat").removeClass('hide');do_ajax_save_setting('currentplayerrepeatmode','0');return!1}
function click_repeat(){$("#jquery_jplayer_1").on('bind',$.jPlayer.event.ended+".jp-repeat",function(event){$(this).jPlayer("play")});$("#do-repeat").addClass('hide');$("#do-single").removeClass('hide');do_ajax_save_setting('currentplayerrepeatmode','1');return!1}
function click_back(){var t=parseInt($("#playTime").text(),10);var b=parseInt($("#backtime").val(),10);var nt=t-b;var st='pause';if(nt<0)nt=0;if(!$('#jquery_jplayer_1').data().jPlayer.status.paused)st='play';$("#jquery_jplayer_1").jPlayer(st,nt)}
function click_forw(){var t=parseInt($("#playTime").text(),10);var b=parseInt($("#backtime").val(),10);var nt=t+b;var st='pause';if(!$('#jquery_jplayer_1').data().jPlayer.status.paused)st='play';$("#jquery_jplayer_1").jPlayer(st,nt)}
function click_slower(){val=parseFloat($("#pbvalue").text())-0.1;if(val>=0.5){$("#pbvalue").text(val.toFixed(1)).css({'color':'#BBB'}).animate({color:'#888'},150,function(){});$("#jquery_jplayer_1").jPlayer("playbackRate",val)}}
function click_faster(){val=parseFloat($("#pbvalue").text())+0.1;if(val<=4.0){$("#pbvalue").text(val.toFixed(1)).css({'color':'#BBB'}).animate({color:'#888'},150,function(){});$("#jquery_jplayer_1").jPlayer("playbackRate",val)}}
function click_stdspeed(){$("#playbackrate").val(10);set_new_playbackrate()}
function click_slower(){var val=($("#playbackrate :selected").val());if(val>5){val--;$("#playbackrate").val(val);set_new_playbackrate()}}
function click_faster(){var val=($("#playbackrate :selected").val());if(val<15){val++;$("#playbackrate").val(val);set_new_playbackrate()}}