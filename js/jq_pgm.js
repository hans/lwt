/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. Pierre in 2011.
***************************************************************/
 
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

$(document).ready( function() {
	$('form.validate').submit(check);
	$('input.markcheck').click(markClick);
	$('#showallwords').click(showallwordsClick);
	$('textarea.textarea-noreturn').keydown(textareaKeydown);
	markClick();
	setTheFocus();
	window.setTimeout(noShowAfter3Secs,3000);
} ); 
