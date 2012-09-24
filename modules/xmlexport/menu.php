<?php
$module = $Params['Module'];
$tpl = eZTemplate::factory();

$Result = array();
$Result ['content'] = $tpl->fetch( 'design:xmlexport/menu.tpl' );

?>
