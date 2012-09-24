<?php
$module = $Params['Module'];
$tpl = eZTemplate::factory();

$exportList = eZXMLExportExports::fetchByStatus( eZXMLExportProcessLog::STATUS_XML_GENERATION_DONE );

$tpl->setVariable( 'exportList', $exportList );

$Result = array();
$Result ['content'] = $tpl->fetch( 'design:xmlexport/runningexports.tpl' );

?>
