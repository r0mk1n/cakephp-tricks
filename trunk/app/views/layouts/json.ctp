<?php
    Configure::write( 'debug', 0 );
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    echo $content_for_layout;
?>
