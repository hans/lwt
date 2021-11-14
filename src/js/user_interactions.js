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