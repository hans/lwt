function deleteTranslation (){
	var w = window.parent.frames['ro'];
	if (typeof w == 'undefined') w = window.opener;
	if($('[name="WoTranslation"]',w.document).val().trim().length){
		$('[name="WoTranslation"]',w.document).val('');
		w.makeDirty();
	}
}

function addTranslation (s) {
	var w = window.parent.frames['ro'];
	if (typeof w == 'undefined') w = window.opener;
	if (typeof w == 'undefined') {
		alert ('Translation can not be copied!');
		return;
	}
	var c = w.document.forms[0].WoTranslation;
	if (typeof c != 'object') {
		alert ('Translation can not be copied!');
		return;
	}
	var oldValue = c.value;
	if (oldValue.trim() == '') {
		c.value = s;
		w.makeDirty();
	}
	else {
		if (oldValue.indexOf(s) == -1) {
			c.value = oldValue + ' / ' + s;
			w.makeDirty();
		}
		else {
			if (confirm('"' + s + '" seems already to exist as a translation.\nInsert anyway?')) { 
				c.value = oldValue + ' / ' + s;
				w.makeDirty();
			}
		}
	}
}

function getGlosbeTranslation(text,lang,dest){
	$.ajax({
		url:'http://glosbe.com/gapi/translate?from=' + lang + '&dest=' + dest + '&format=json&phrase=' + text + '&callback=?',
		type:"GET",
		dataType: 'jsonp',
		jsonp: 'getTranslationFromGlosbeApi',
		jsonpCallback: 'getTranslationFromGlosbeApi',
		async:'true'
	});
}

function getTranslationFromGlosbeApi(data){
	try {
		$.each(data.tuc,function(i,rows){
			if(rows.phrase){
				$('#translations').append('<span class="click" onclick="addTranslation(\'' + rows.phrase.text + '\');"><img src="icn/tick-button.png" title="Copy" alt="Copy" /> &nbsp; ' + rows.phrase.text + '</span><br />');
			}
			else if(rows.meanings){
				$('#translations').append('<span class="click" onclick="addTranslation(' + "'(" + rows.meanings[0].text + ")'" + ');"><img src="icn/tick-button.png" title="Copy" alt="Copy" /> &nbsp; ' + "(" + rows.meanings[0].text + ")" + '</span><br />');
			}
		});
		if(!data.tuc.length){
			$('#translations').before('<p>No translations found (' + data.from + '-' + data.dest + ').</p>');
			if(data.dest!='en' && data.from!='en'){
				$('#translations').attr('id','no_trans').after('<hr /><p>&nbsp;</p><h3><a href="http://glosbe.com/' + data.from + '/en/' + data.phrase + '">Glosbe Dictionary (' + data.from + '-en):  &nbsp; <span class="red2">' + data.phrase + '</span></a></h3>&nbsp;<p id="translations"></p>');
				getGlosbeTranslation(data.phrase,data.from,'en');
			}
			else $('#translations').after('<hr />');
		}
		else $('#translations').after('<p>&nbsp;<br/>' + data.tuc.length + ' translation' + (data.tuc.length==1 ? '' : 's') + ' retrieved via <a href="http://glosbe.com/a-api" target="_blank">Glosbe API</a>.</p><hr />');
	}
	catch(err) {
		$('#translations').text('Retrieval error. Possible reason: There is a limit of Glosbe API calls that may be done from one IP address in a fixed period of time, to prevent from abuse.').after('<hr />');
	}
}
