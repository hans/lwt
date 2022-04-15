/**
 * \file
 * \brief General file to control dynamic interactions with the user.
 * 
 * @package Lwt
 * @author  HugoFara <Hugo.Farajallah@protonmail.com>
 * @license Unlicense <http://unlicense.org/>
 * @since   2.0.3-fork
 */
function quickMenuRedirection(value){var qm=document.getElementById('quickmenu');qm.selectedIndex=0;if(value=='')
return;if(value=='INFO'){top.location.href='info.php'}else if(value=='rss_import'){top.location.href='do_feeds.php?check_autoupdate=1'}else{top.location.href=value+'.php'}}
function newExpressionInteractable(text,attrs,length,hex,showallwords){console.log("HERE, text: "+text+" attrs: "+attrs+" length: "+length);var attrs2=' class="click mword '+(showallwords?'m':'')+'wsty TERM'+hex+' word'+woid+' status'+status+'" data_trans="'+trans+'" data_rom="'+roman+'" data_code="'+length+'" data_status="'+status+'" data_wid="'+woid+'" title="'+title+'"';for(key in text){let text_refresh=!1;if($('span[id^="ID-'+key+'-"]',context).not(".hide").length){if(!($('span[id^="ID-'+key+'-"]',context).not(".hide").attr('data_code')>length)){text_refresh=!0}}
$('#ID-'+key+'-'+length,context).remove();var i='';for(let j=parseInt(length,10)-1;j>0;j=j-1){if(j==1)
i='#ID-'+key+'-1';if($('#ID-'+key+'-'+j,context).length){i='#ID-'+key+'-'+j;break}}
var ord_class='order'+key;$(i,context).before('<span id="ID-'+key+'-'+length+'"'+attrs+'>'+text[key]+'</span>');el=$('#ID-'+key+'-'+parseInt(length,10),context);el.addClass(ord_class).attr('data_order',key);var txt=el.nextUntil($('#ID-'+(parseInt(key)+parseInt(length,10)*2-1)+'-1',context),'[id$="-1"]').map(function(){return $(this).text()}).get().join("");var pos=$('#ID-'+key+'-1',context).attr('data_pos');el.attr('data_text',txt).attr('data_pos',pos);if(!showallwords){if(text_refresh){refresh_text(el)}else{el.addClass('hide')}}}}
function prepareTextInteractions(){$('.word').each(word_each_do_text_text);$('.mword').each(mword_each_do_text_text);$('.word').on('click',word_click_event_do_text_text);$('#thetext').on('selectstart','span',!1).on('mousedown','.wsty',{annotation:ANNOTATIONS_MODE},mword_drag_n_drop_select);$('#thetext').on('click','.mword',mword_click_event_do_text_text);$('.word').on('dblclick',word_dblclick_event_do_text_text);$('#thetext').on('dblclick','.mword',word_dblclick_event_do_text_text);$(document).on('keydown',keydown_event_do_text_text);$('#thetext').hoverIntent({over:word_hover_over,out:word_hover_out,interval:150,selector:".wsty,.mwsty"})}
function goToLastPosition(){const lookPos=POS;let pos=0;if(lookPos>0){let posObj=$(".wsty[data_pos="+lookPos+"]").not(".hide").eq(0);if(posObj.attr("data_pos")===undefined){pos=$(".wsty").not(".hide").filter(function(){return $(this).attr("data_pos")<=lookPos}).eq(-1)}}
$(document).scrollTo(pos);window.focus();window.setTimeout('overlib()',10);window.setTimeout('cClick()',100)}
function saveCurrentPosition(){var pos=0;var top=$(window).scrollTop()-$('.wsty').not('.hide').eq(0).height();$('.wsty').not('.hide').each(function(){if($(this).offset().top>=top){pos=$(this).attr('data_pos');return!1}});$.ajax({type:"POST",url:'inc/ajax_save_text_position.php',data:{id:TID,position:pos},async:!1})}
function getPhoneticText(text,lang){let phoneticText;$.ajax({async:!1,data:{text:text,lang:lang},type:"GET",url:'inc/ajax_get_phonetic.php',}).done(function(data){phoneticText=data});return phoneticText}
function readRawTextAloud(text,lang,rate,pitch){let msg=new SpeechSynthesisUtterance();const trimmed=lang.substring(0,2);const prefix='tts['+trimmed;msg.text=text;if(lang){msg.lang=lang}else if(getCookie(prefix+'RegName]')){msg.lang=trimmed+'-'+getCookie(prefix+'RegName]')}
if(rate){msg.rate=rate}else if(getCookie(prefix+'Rate]')){msg.rate=parseInt(getCookie(prefix+'Rate]'),10)}
if(pitch){msg.pitch=pitch}else if(getCookie(prefix+'Pitch]')){msg.pitch=parseInt(getCookie(prefix+'Pitch]'),10)}
window.speechSynthesis.speak(msg)}
function readTextAloud(text,lang,rate,pitch){let msg=new SpeechSynthesisUtterance();const trimmed=lang.substring(0,2);let parsed_text;if(trimmed=='ja'){parsed_text=getPhoneticText(text,lang)}else{parsed_text=text}
readRawTextAloud(parsed_text,lang,rate,pitch)}