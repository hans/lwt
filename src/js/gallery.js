var TEXT = '',
LANG = '',
TRANSLATIONS = '',
TERM = '',
DELIM ='',
WIDTH = parseInt($('body').width() / 3);
if(WIDTH > 178)WIDTH = 178;
if(WIDTH < 100)WIDTH = 100;
var HEIGHT = parseInt(WIDTH * 118 / 178),
ARROW_SIZE = parseInt(WIDTH /3.28);

$(document).ready( function() {
	$('body').prepend('<style>' +  "\n" + '#gallery>li{width: ' + WIDTH + 'px;}' +  "\n" + '.widget{width: ' + 3 * WIDTH + 'px;}' +  "\n" + '.shadow{height: ' + HEIGHT + 'px;}' +  "\n" + 'div.prev .prev, div.next .next {top: ' + ARROW_SIZE + 'px;font-size: ' + ARROW_SIZE + 'px;}' +  "\n" + '</style>');
	var w = window.parent.frames['ro'];
	if (typeof w == 'undefined') w = window.opener;
	$('#img_sel').on('change', function() {
		  location.href = 'ggl_img.php?q=' + $(this).val();
	});
	$('#gallery').width(6 * WIDTH);
	$('.arrowoverlay').height(HEIGHT);
	getGoogleThumbnails(TEXT, LANG, 0, 6);
	var TRANSLATIONS = $('[name="WoTranslation"]',w.document).val();
	if (typeof w != 'undefined') {
		if(TRANSLATIONS.length){
			var TERM = $('[name="WoText"]',w.document).val();
			$('#img_sel').append('<option class="red">' + TERM + '</option>');
			if(TRANSLATIONS != '' && TRANSLATIONS != '*'){
				jQuery.each(TRANSLATIONS.split(DELIM), function(k,v){
					var z=v.trim();
					if(z != TERM){
						var sel = '';
						if(z == TEXT) sel = ' selected="selected"';
						$('#img_sel').append('<option' + sel + '>' + z + '</option>');
					}
				});
			}
		}
		else $('#new_search').text('(add translation and reload for more search options)');
	}
	else {
		$('#new_search').text('');
		$('#del_image').remove();
	}
});
$( window ).load(function(){
$(document).on('click',".shadow",click_thumbnail)
.on('click',".arrowoverlay.prev",{width:WIDTH},click_prev_thumbnails)
.on('click',".arrowoverlay.next",{width:WIDTH},click_next_thumbnails);

});

function getThumbnailsFromApi(data){
	try {
		$.each(data.responseData.results,function(i,rows){
			$('#gallery').append('<li><div><div class="shadow" style="background-image: url(\'' + rows.tbUrl + '\');"></div></div></li>');
		});
	}
	catch(err) {
	    $('#gallery').addClass('endLoad');$('.next').css('z-index',0).prop("disabled",true);
	}
}
function getGoogleThumbnails(text,lang,start,limit){
	if(lang!=''){
		lang = '&hl=' + lang;
	}
	$.ajax({
		url:'https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=' + text + lang + '&rsz=' + limit + '&start=' + start + '&callback=?',
		type:"GET",
		dataType: 'jsonp',
		jsonp: 'getThumbnailsFromApi',
		jsonpCallback: 'getThumbnailsFromApi',
		async:'true'
	});
}
function click_next_thumbnails(event) {
	var l=parseInt($('#gallery').css('left'));
	if(l % event.data.width==0){
		if(l==0)$('.prev').css('z-index',6).prop("disabled",false);
		$('.next>.next').css({'color': '#F00'}).animate({color: '#EEE'},250,function() {});
		if($('#gallery>li .shadow').length + l/event.data.width < 7){
			if($('#gallery>li .shadow').length + l/event.data.width < 4){
				$('.next').css('z-index',0).prop("disabled",true);
			}
			else{
				getGoogleThumbnails(TEXT, LANG, $('li .shadow').length +1, 6);
				var width =$('#gallery').width() + 6*event.data.width;
				$('#gallery').width(width);
			}
		}
		if($('#gallery>li .shadow:visible:last').offset().left > 3.2 * WIDTH || !$('#gallery.endLoad').length)$('#gallery').animate({left: l-3*WIDTH}, 200, function() {});
		else $('.next').css('z-index',0).prop("disabled",true);
	}
}
function click_prev_thumbnails(event) {
	var l=parseInt($('#gallery').css('left'));
	if(l % event.data.width==0)
	if(l < 0){
		if(l == -3 * event.data.width)$('.prev').css('z-index',0);
		$('.prev>.prev').css({'color': '#F00'}).animate({color: '#EEE'},250,function() {});
		$('#gallery').animate({left: l + 3 * event.data.width}, 200, function() {});
		$('.next').css('z-index',6).prop("disabled",false);
	}
}
function click_thumbnail(event) {
    $(".shadowred").removeClass('shadowred');
	var w = window.parent.frames['ro'];
	if (typeof w == 'undefined') w = window.opener;
	if (typeof w == 'undefined') {
		alert ('Image can not be copied!');
		return;
	}
    $(event.target).addClass('shadowred');
    $('#thumbnail',w.document).css('background-image',$(event.target).css('background-image'));
    $('[name="WoImage"]',w.document).val($(event.target).css('background-image').replace('url(','').replace(')','')).trigger( "change" );
    w.makeDirty();
}
function deleteImage (){
	var w = window.parent.frames['ro'];
	if (typeof w == 'undefined') w = window.opener;
	$('[name="WoImage"]',w.document).val('DEL');
	$('#thumbnail',w.document).removeAttr('style');
	$(".shadowred").removeClass('shadowred');
    w.makeDirty();
}
