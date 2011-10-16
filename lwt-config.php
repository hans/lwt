<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions,
unless such conditions are required by law.


Developed by J. Pierre in 2011.
***************************************************************/

define('LWT_SERVER', 'localhost');
define('LWT_DB_USER', 'root');
define('LWT_DB_PASSWORD', '');
define('LWT_DB_NAME', 'learning-with-texts');

define('LWT_BASE', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('LWT_INCLUDE', LWT_BASE . DS . 'lwt-include');
define('LWT_VIEW', LWT_BASE . DS . 'lwt-view');

?>