<?php

/**************************************************************
Call: save_setting_redirect.php?k=[key]&v=[value]&u=[RedirURI]
Save a Setting (k/v) and redirect to URI u
***************************************************************/

require_once 'inc/session_utility.php';

$k = getreq('k');
$v = getreq('v');
$u = getreq('u');

if($k == 'currentlanguage') {

    unset($_SESSION['currenttextpage']);
    unset($_SESSION['currenttextquery']);
    unset($_SESSION['currenttextquerymode']);
    unset($_SESSION['currenttexttag1']);
    unset($_SESSION['currenttexttag2']);
    unset($_SESSION['currenttexttag12']);
    
    unset($_SESSION['currentwordpage']);
    unset($_SESSION['currentwordquery']);
    unset($_SESSION['currentwordquerymode']);
    unset($_SESSION['currentwordstatus']);
    unset($_SESSION['currentwordtext']);
    unset($_SESSION['currentwordtag1']);
    unset($_SESSION['currentwordtag2']);
    unset($_SESSION['currentwordtag12']);
    unset($_SESSION['currentwordtextmode']);
    unset($_SESSION['currentwordtexttag']);
    
    unset($_SESSION['currentarchivepage']);
    unset($_SESSION['currentarchivequery']);
    unset($_SESSION['currentarchivequerymode']);
    unset($_SESSION['currentarchivetexttag1']);
    unset($_SESSION['currentarchivetexttag2']);
    unset($_SESSION['currentarchivetexttag12']);
    
    unset($_SESSION['currentrsspage']);
    unset($_SESSION['currentrssfeed']);
    unset($_SESSION['currentrssquery']);
    unset($_SESSION['currentrssquerymode']);
    
    unset($_SESSION['currentfeedspage']);
    unset($_SESSION['currentmanagefeedsquery']);
    
    
    saveSetting('currenttext', '');
}

saveSetting($k, $v);
header("Location: " . $u);
exit(); 
?>