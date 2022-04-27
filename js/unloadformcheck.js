/**
 * \file
 * \brief Check for unsaved changes when unloading window.
 * 
 * @package Lwt
 * @license unlicense
 * @author  andreask7 <andreasks7@users.noreply.github.com>
 * @since   1.6.16-fork
 */
var DIRTY=0;function askConfirmIfDirty(){if(DIRTY){return'** You have unsaved changes! **'}}
function makeDirty(){DIRTY=1}
function resetDirty(){DIRTY=0}
function tagChanged(_,ui){if(!ui.duringInitialization){DIRTY=1}
return!0}
$(document).ready(function(){$('#termtags').tagit({afterTagAdded:tagChanged,afterTagRemoved:tagChanged});$('#texttags').tagit({afterTagAdded:tagChanged,afterTagRemoved:tagChanged});$('input,checkbox,textarea,radio,select').not('#quickmenu').on('change',makeDirty);$(':reset,:submit').on('click',resetDirty);$(window).on('beforeunload',askConfirmIfDirty)})