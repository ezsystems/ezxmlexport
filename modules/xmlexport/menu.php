<?php
include_once( "kernel/common/template.php" );

$module = $Params['Module'];
$tpl = templateInit();

$Result = array();
$Result ['content'] = $tpl->fetch( 'design:xmlexport/menu.tpl' );

?>
