<?php

include( 'extension/ezxmlexport/functions/functions.php' );

echo showMemoryUsage('Script starts');

$isVerbose = false;
$phpBinary = '/usr/bin/php';

$ini = eZINI::instance( 'ezxmlexport.ini' );

if( $ini->variable( 'ExportSettings', 'Verbosity' ) == 'enabled' )
{
    $isVerbose = true;
}

if( $ini->hasVariable( 'ExportSettings', 'PHPBinaryPath' ) )
{
    $phpBinary = $ini->variable( 'ExportSettings', 'PHPBinaryPath' );

    if( !is_executable( $phpBinary ) )
    {
        $cli->error( $phpBinary . ' is not executable' );
        $script->shutdown( 0 );
    }
}

$fetchLimit = $ini->variable( 'ExportSettings', 'FetchLimit' );

$exportList = eZXMLExportExports::fetchAvailableExports();

foreach( $exportList as $export )
{
    $eZXMLExporter = new eZXMLExportExporter( $export->attribute( 'id' ), $isVerbose );

    $fetchableNodeTotal = $eZXMLExporter->fetchNodeTotal();

    if( $fetchableNodeTotal <= 0 )
    {
        $eZXMLExporter->eZXMLExportProcessLog->setAttribute( 'export_id' , $export->attribute( 'id' ) );
        $eZXMLExporter->eZXMLExportProcessLog->setAttribute( 'start_date', time() );
        $eZXMLExporter->eZXMLExportProcessLog->setAttribute( 'end_date', time() );
        $eZXMLExporter->eZXMLExportProcessLog->setAttribute( 'status', eZXMLExportProcessLog::STATUS_XML_GENERATION_DONE);
        $eZXMLExporter->eZXMLExportProcessLog->store();

        continue;
    }

    $eZXMLExporter->exportStart();

    $pid = getmypid();
    storePersistentVariable( $eZXMLExporter, $pid );

    $offset = 0;
    $exportEnd = $fetchableNodeTotal;

    if( $eZXMLExporter->ExportLimit > 0 )
    {
        $exportEnd = $eZXMLExporter->ExportLimit;
    }

    $cli->output( 'ExportEnd : ' . $exportEnd . ' limit per fetch : ' . $fetchLimit );

    do
    {
        if( abs( $exportEnd - $offset ) <= $fetchLimit )
        {
            $fetchLimit = abs( $exportEnd - $offset );
        }

        $cli->output( 'Exporting with offset : ' . $offset . ' and Limit : ' . $fetchLimit );

        $shellCommand = $phpBinary . ' extension/ezxmlexport/bin/php/exportcontentobjects.php --offset=' . $offset
                        . ' --limit=' . $fetchLimit
                        . ' --pid=' . $pid
                        . ' --siteaccess=' . $GLOBALS['eZCurrentAccess']['name'];

        exec( $shellCommand, $output, $return_var );

        if( $return_var != 0 )
        {
            $cli->error( 'Error with the XML export, aborting' );
            $script->shutdown( 0 );
        }

        $offset += $fetchLimit;
    }
    while( $offset < $exportEnd );

    $eZXMLExporter->exportEnd();

    removePersistentVariable( $pid );
}

echo showMemoryUsage( 'export END' );

?>