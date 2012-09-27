<?php
function exportContentObjects( $eZXMLExporter, $exportableNodeList, $offset = null, $limit = null )
{
    foreach( $exportableNodeList as $classIdentifier => $childExportableNodeList )
    {
        foreach( $childExportableNodeList as $exportableNode )
        {
            $contentObject   = $exportableNode->object();
            $contentObjectID = $contentObject->attribute( 'id' );

            if( in_array( $contentObjectID, $eZXMLExporter->AlreadyExportedOjectIDList ) )
            {
                continue;
            }

            $eZXMLExporter->objectExportStart( $contentObject );

            $contentObjectExportStartTime = time();

            $attributeList = organizeDataMap( $eZXMLExporter, $contentObject );

            foreach( $attributeList as $contentObjectAttribute )
            {
                $eZXMLExporter->exportAttribute( $contentObjectAttribute );
            }

            $eZXMLExporter->objectExportEnd( $contentObjectID,
                                             $contentObjectExportStartTime,
                                             time() );
        }
    }
}

function organizeDataMap( $eZXMLExporter, $contentObject )
{
    // I can not use the native dataMap directly
    // as attribute must be ordered the way they
    // have been in the XML Schema definition
    $originalDataMap   = $contentObject->dataMap();
    $rearrangedDataMap = array();

    foreach( $eZXMLExporter->ExportableContentClasses as $exportableContentClass )
    {
        if( $exportableContentClass['contentclass_id'] != $contentObject->attribute( 'contentclass_id' ) )
        {
            continue;
        }

        foreach( $exportableContentClass['attribute_id_list'] as $attributeID )
        {
            $rearrangedDataMap[] = fetchAttribute( $originalDataMap, $attributeID );
        }
    }

    return $rearrangedDataMap;
}

function fetchAttribute( $dataMap, $contentClassAttributeID )
{
    foreach( $dataMap as $eZContentObjectAttribute )
    {
        if( $eZContentObjectAttribute->attribute( 'contentclassattribute_id' ) == $contentClassAttributeID )
        {
            return $eZContentObjectAttribute;
        }
    }

    // there is almost no chances
    // to reach this line
    return false;
}

function storePersistentVariable( $variable, $pid )
{
    eZFile::create( 'ezxmlexport.' . $pid . '.cache', 'var/cache/', serialize( $variable ) );
}

function getPersistentVariable( $pid )
{
    $serializedVariable = file_get_contents( 'var/cache/ezxmlexport.' . $pid . '.cache' );
    return unserialize( $serializedVariable );
}

function removePersistentVariable( $pid )
{
    unlink( 'var/cache/ezxmlexport.' . $pid . '.cache' );
}

function showMemoryUsage( $message )
{
    return $message . ' : ' . number_format( memory_get_usage() / ( 1024 * 1024 ), 2, '.', '') . " Mo \n";
}
?>
