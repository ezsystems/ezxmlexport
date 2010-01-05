<?php
include_once( "kernel/common/template.php" );

$Module = $Params['Module'];

$http = eZHTTPTool::instance();

if( $Module->isCurrentAction( 'RelaunchFTPTransfert' ) )
{
    var_dump( $Module->actionParameter( 'SelectedExportIDArray' ) );

    if( $Module->hasActionParameter( 'SelectedExportIDArray' ) )
    {
        foreach( $Module->actionParameter( 'SelectedExportIDArray' ) as $exportID )
        {
            $eZXMLExport = new eZXMLExportExporter( $exportID );

            $dirPath     = eZXMLExportExporter::EXPORT_FILE_DIRECTORY
                           . $eZXMLExport->CleanExportName;

            $fileList    = eZDir::findSubitems( $dirPath );
            // $eZXMLExport->sendOverFTPIfNeeded( $fileList );
        }
    }
}

$Module->redirectToView( 'runningexports' );
?>
