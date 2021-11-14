/**
 * \file
 * \brief General file to control dynamic interactions with the user.
 * 
 */

/**
 * Redirect the user to a specific page depending on the value
 */
function quickMenuRedirection(value) {
    var qm = document.getElementById('quickmenu');
    qm.selectedIndex=0;
    if (value == '')
        return; 
    if (value == 'INFO') {
        top.location.href = 'info.php';
    } else if (value == 'rss_import') {
        top.location.href = 'do_feeds.php?check_autoupdate=1';
    } else {
        top.location.href = value + '.php';
    }
}

/**
 * Ceate an interactable to add a new expression.
 * 
 * WARNING! This function was not properly tested!
 * 
 * @param {string} text Text to append 
 * @param {string} attrs A group of attributes to add 
 * @param {int} length SHoud correspond to WoWordCount
 * @param {*} hex 
 * @param {bool} showallwords Whether you want to show all words
 */
function newExpressionInteractable(text, attrs, length, hex, showallwords=false) {
    alert("HERE");
    alert(text);
    alert(attrs);
    alert(length);
    var attrs2 =  ' class="click mword ' + (showallwords ? 'm':'') + 'wsty TERM' + hex + 
    ' word' + woid + ' status' + status + '" data_trans="' + trans + 
    '" data_rom="' + roman + '" data_code="' + length + 
    '" data_status="' + status + '" data_wid="' + woid + 
    '" title="' + title + '"';

    for (key in text) {
        var text_refresh = 0;
        if($('span[id^="ID-'+ key +'-"]', context).not(".hide").length ) {
            if(!($('span[id^="ID-'+ key +'-"]', context).not(".hide").attr('data_code')>length)){
                text_refresh = 1;
            }
        }
        $('#ID-' + key + '-' + length, context).remove();
        var i = '';
        for (let j = parseInt(length, 10) - 1; j > 0; j = j-1){
            if (j==1)
                i='#ID-' + key + '-1';
            if($('#ID-' + key + '-' + j,context).length){
                i = '#ID-' + key + '-' + j;
                break;
            }
        }
        var ord_class='order' + key;
        $(i, context).before('<span id="ID-' + key + '-' + length + '"' + attrs + '>' + text[ key ] + '</span>');
        el = $('#ID-' + key + '-' + parseInt(length, 10), context);
        el.addClass(ord_class).attr('data_order',key);
        var txt = el.nextUntil($('#ID-' + (parseInt(key) + parseInt(length, 10) * 2 - 1) + '-1', context),'[id$="-1"]')
            .map(function() {
                return $( this ).text();
            })
            .get().join( "" );
        var pos = $('#ID-' + key + '-1', context).attr('data_pos');
        el.attr('data_text',txt).attr('data_pos',pos);
        if (showallwords) {
            if(text_refresh == 1){
                refresh_text(el);
            } else {
                el.addClass('hide');
            }
        }
    }
}