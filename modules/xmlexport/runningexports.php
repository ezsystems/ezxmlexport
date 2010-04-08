<?php
include_once( "kernel/common/template.php" );

$module = $Params['Module'];
$tpl = templateInit();

$exportList = eZXMLExportExports::fetchByStatus( eZXMLExportProcessLog::STATUS_XML_GENERATION_DONE );

$tpl->setVariable( 'exportList', $exportList );

$Result = array();
$Result ['content'] = $tpl->fetch( 'design:xmlexport/runningexports.tpl' );

?>
