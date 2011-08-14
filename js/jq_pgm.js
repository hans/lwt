/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/

/**************************************************************
Global variables used in LWT jQuery functions
***************************************************************/

var OPENED = 0;
var WID = 0;
var TID = 0;
var WBLINK1 = '';
var WBLINK2 = '';
var WBLINK3 = '';
var WBLINK4 = '';
var SOLUTION = '';
 
/**************************************************************
LWT jQuery functions
***************************************************************/
 
function check() {
	var count = 0;
	$('input.notempty').each( function(n) {
		if($(this).attr('value').trim()=='') count++; 
	} );
	$('textarea.notempty').each( function(n) {
		if($(this).val().trim()=='') count++; 
	} );
	$('select.notempty').each( function(n) {
		if($(this).val().trim()=='') count++; 
	} );
	if (count > 0) {
		alert('ERROR\n\n' + count + ' field(s) - marked with * - must not be empty!');
	}
	return (count == 0);
}

function markClick() {
	if($('input.markcheck:checked').length > 0) {
		$('#markaction').removeAttr('disabled');
	} else {
		$('#markaction').attr('disabled','disabled');
	}
}

function showallwordsClick() {
	var option = $('#showallwords:checked').length;
	var text = $('#thetextid').text();
	window.parent.frames['ro'].location.href = 
		'set_text_mode.php?mode=' + option +
		'&text=' + text;
}

function textareaKeydown(event) {
	if (event.keyCode && event.keyCode == '13') {
		if (check()) $('input:submit').last().click();
		return false;
	} else {
		return true;
	}
}

function noShowAfter3Secs() {
	$('#hide3').slideUp();
}

function setTheFocus() {
	$('.setfocus').focus().select();
}

function word_click_event_do_test_test() {
	run_overlib_test(
		WBLINK1, WBLINK2, WBLINK3, WBLINK4,
		$(this).attr('data_wid'),
		$(this).attr('data_text'),
		$(this).attr('data_trans'),
		$(this).attr('data_rom'),
		$(this).attr('data_status'),
		$(this).attr('data_sent'),
		$(this).attr('data_todo'));
	$('.todo').text(SOLUTION);
	return false;
}

function keydown_event_do_test_test(e) {
	if (e.which == 32 && OPENED == 0) {  // 1st space show sol.
		$('.word').click();
		cClick();
		OPENED = 1;
		return;
	}
	if (e.which == 32 && OPENED == 1) {  // space: show box
		$('.word').click();
		OPENED = 2;
		return;
	}
	if (e.which == 32 && OPENED == 2) {  // space: hide box
		cClick();
		OPENED = 1;
		return;
	}
	if (e.which == 38 && OPENED > 0) {  // up : status+1
		window.parent.frames['ro'].location.href = 
			'set_test_status.php?wid=' + WID + '&stchange=1';
		return;
	}
	if (e.which == 40 && OPENED > 0) {  // down : status-1
		window.parent.frames['ro'].location.href = 
			'set_test_status.php?wid=' + WID + '&stchange=-1';
		return;
	}
	for (var i=1; i<=5; i++) {
		if ((e.which == (48+i) || e.which == (96+i)) && OPENED > 0) {  // 1,.. : status=i
			window.parent.frames['ro'].location.href = 
				'set_test_status.php?wid=' + WID + '&status=' + i;
			return;
		}
	}
	if (e.which == 73 && OPENED > 0) {  // I : status=98
		window.parent.frames['ro'].location.href = 
			'set_test_status.php?wid=' + WID + '&status=98';
		return;
	}
	if (e.which == 87 && OPENED > 0) {  // W : status=99
		window.parent.frames['ro'].location.href = 
			'set_test_status.php?wid=' + WID + '&status=99';
		return;
	}
	if (e.which == 69 && OPENED > 0) {  // E : EDIT
		window.parent.frames['ro'].location.href = 
			'edit_tword.php?wid=' + WID;
		return;
	}
	return false;
}

function word_each_do_text_text(i) {
	this.title = make_tooltip($(this).text(), $(this).attr('data_trans'), 
		$(this).attr('data_rom'), $(this).attr('data_status'));
}

function mword_each_do_text_text(i) {
	if ($(this).attr('data_status') != '') 
		this.title = make_tooltip($(this).attr('data_text'), 
		$(this).attr('data_trans'), $(this).attr('data_rom'), 
		$(this).attr('data_status'));
}

function word_click_event_do_text_text() {
	var status = $(this).attr('data_status');
	if ( status < 1 ) {
		run_overlib_status_unknown(WBLINK1,WBLINK2,WBLINK3,WBLINK4,$(this).attr('title'),
			TID,$(this).attr('data_order'),$(this).text(),$(this).attr('data_mw2'),
			$(this).attr('data_mw3'),$(this).attr('data_mw4'),$(this).attr('data_mw5'),
			$(this).attr('data_mw6'),$(this).attr('data_mw7'),$(this).attr('data_mw8'),
			$(this).attr('data_mw9'));
		top.frames['ro'].location.href='edit_word.php?tid=' + TID + '&ord=' + 
			$(this).attr('data_order') + '&wid=';
	}
	else if ( status == 99 )
		run_overlib_status_99(WBLINK1,WBLINK2,WBLINK3,WBLINK4,$(this).attr('title'),
			TID,$(this).attr('data_order'),$(this).text(),$(this).attr('data_wid'),
			$(this).attr('data_mw2'),$(this).attr('data_mw3'),$(this).attr('data_mw4'),
			$(this).attr('data_mw5'),$(this).attr('data_mw6'),$(this).attr('data_mw7'),
			$(this).attr('data_mw8'),$(this).attr('data_mw9'));
	else if ( status == 98 )
		run_overlib_status_98(WBLINK1,WBLINK2,WBLINK3,WBLINK4,$(this).attr('title'),
			TID,$(this).attr('data_order'),$(this).text(),$(this).attr('data_wid'),
			$(this).attr('data_mw2'),$(this).attr('data_mw3'),$(this).attr('data_mw4'),
			$(this).attr('data_mw5'),$(this).attr('data_mw6'),$(this).attr('data_mw7'),
			$(this).attr('data_mw8'),$(this).attr('data_mw9'));
	else
		run_overlib_status_1_to_5(WBLINK1,WBLINK2,WBLINK3,WBLINK4,$(this).attr('title'),
			TID,$(this).attr('data_order'),$(this).text(),$(this).attr('data_wid'),status,
			$(this).attr('data_mw2'),$(this).attr('data_mw3'),$(this).attr('data_mw4'),
			$(this).attr('data_mw5'),$(this).attr('data_mw6'),$(this).attr('data_mw7'),
			$(this).attr('data_mw8'),$(this).attr('data_mw9'));
	return false;
}
	
function mword_click_event_do_text_text() {
	var status = $(this).attr('data_status');
	if (status != '') 
		run_overlib_multiword(WBLINK1,WBLINK2,WBLINK3,WBLINK4,$(this).attr('title'),TID,
		$(this).attr('data_order'),$(this).attr('data_text'),$(this).attr('data_wid'),
		status,$(this).attr('data_code'));
	return false;
}

$(document).ready( function() {
	$('form.validate').submit(check);
	$('input.markcheck').click(markClick);
	$('#showallwords').click(showallwordsClick);
	$('textarea.textarea-noreturn').keydown(textareaKeydown);
	markClick();
	setTheFocus();
	window.setTimeout(noShowAfter3Secs,3000);
} ); 
