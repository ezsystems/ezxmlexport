<?php
include_once( "kernel/common/template.php" );

$Module = $Params['Module'];

/* Possible values :
 * - contentlist
 * - realtime
 */
$Type   = $Params['Type'];

// exportID
$exportID = $Params['ID'];


if( ( $Type != 'contentlist' and $Type != 'realtime' ) and (int)$ID > 0 )
{
    $Module->redirectToView( 'menu' );
    return;
}

$tpl = templateInit();
$tpl->setVariable( 'export_id', $exportID );

if( $Type == 'contentlist' )
{
    $offset = 0;

    if( $Params['Offset'] )
    {
        $offset = $Params['Offset'];
    }

    $ini = eZINI::instance( 'ezxmlexport.ini' );
    $fetchLimit = $ini->variable( 'ExportSettings', 'FetchLimit' );

    $eZXMLExporter = new eZXMLExportExporter( $exportID, false, false, false );
    $fetchableNodeTotal = $eZXMLExporter->fetchNodeTotal();

    /*
    if( $fetchableNodeTotal <= 0 )
    {
        return;
    }
    */

    if( $eZXMLExporter->ExportLimit > 0 )
    {
        $fetchableNodeTotal = $eZXMLExporter->ExportLimit;
    }

    if( abs( $fetchableNodeTotal - $offset ) <= $fetchLimit )
    {
        $fetchLimit = abs( $fetchableNodeTotal - $offset );
    }

    $exportableNodeList = $eZXMLExporter->fetchExportableNodes( $offset, $fetchLimit );

    $tpl->setVariable( 'content_list', $exportableNodeList);
    $tpl->setVariable( 'total_nodes' , $fetchableNodeTotal );
    $tpl->setVariable( 'offset'      , $offset );
    $tpl->setVariable( 'fetch_limit' , $fetchLimit );

    $templateName = 'contentlist';

    $Result = array();
    $Result['content'] = $tpl->fetch( 'design:xmlexport/test/' . $templateName . '.tpl' );
}

?>
