/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Check for unsaved changes when unloading window
***************************************************************/

var DIRTY = 0;

function askConfirmIfDirty(){  
	if (DIRTY) { 
		return '** You have unsaved changes! **'; 
	}
}

function makeDirty() {
	DIRTY = 1; 
}

function resetDirty() {
	DIRTY = 0; 
}

function bindForChange(){    
	$('input,checkbox,textarea,radio,select').bind('change',makeDirty);
	$(':reset,:submit').bind('click',resetDirty);
}

window.onbeforeunload = askConfirmIfDirty;
window.onload = bindForChange;
