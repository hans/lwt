<?php

require_once( 'settings.inc.php' );
require_once( 'connect.inc.php' );
require_once( 'dbutils.inc.php' );
require_once( 'utilities.inc.php' ); 

if(empty($_GET["hl"]))$hl='';
else $hl=$_GET["hl"];
$txt=$_GET["q"];

pagestart_nobody('');
?>
<script type="text/javascript" src="js/gallery.js" charset="utf-8"></script>
<script type="text/javascript">
$('head').append('<link rel="stylesheet" type="text/css" href="css/gallery.css" />');
TEXT = <?php echo prepare_textdata_js($txt); ?>;
LANG = <?php echo prepare_textdata_js($hl); ?>;
DELIM = new RegExp('[<?php echo tohtml(str_replace (array('\\',']','-','^'),array('\\\\','\\]','\\-','\\^'),getSettingWithDefault('set-term-translation-delimiters'))); ?>]+');
</script>

<h3><span class="red2">Google Images Search for »<?php echo $txt; ?>« <img id="del_image" src="icn/broom.png" title="Delete Image" style="cursor:pointer" onclick="deleteImage ();"></img></span></h3>
<p id="new_search">New Search: <select id="img_sel"></select></p><br />
<div class="widget"><div class="prev arrowoverlay" style="z-index:0;"><div class="prev" style="left:4px;z-index:0;">‹</div></div><ul id="gallery" style="left:0px;"></ul><div class="next arrowoverlay"><div class="next" style="right:4px;">›</div></div></div>
<?php
pageend();
?>
