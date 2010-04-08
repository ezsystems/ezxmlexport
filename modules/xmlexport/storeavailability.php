<?php
$Module = $Params['Module'];
$http = eZHTTPTool::instance();

// eZLog::write( 'postVariable' . print_r($_POST,1), 'error.log');

$ContentClassID              = $http->postVariable( 'ContentClassID' );
$Action                      = $http->postVariable( 'Action' );
$ContentClassAttributeIDList = $http->postvariable( 'ContentClassAttributeIDList' );

if( $ContentClassID != null )
{
    $eZXMLExportAvailableContentClasses = new eZXMLExportAvailableContentClasses();
    $eZXMLExportAvailableContentClasses->setAttribute( 'contentclass_id', $ContentClassID );

    if( $Action == 'insert' )
    {
        $fields = array( 'contentclass_id' );
        $conds  = array( 'contentclass_id' => $ContentClassID );

        $contentClassList = eZXMLExportAvailableContentClasses::fetchList( $fields,  $conds );

        if( !$contentClassList )
        {
            eZLog::write( 'Inserting class : ' . $ContentClassID, 'error.log' );
            $eZXMLExportAvailableContentClasses->store();
        }

        // defining its attributes as exportable as well
        if( count( $ContentClassAttributeIDList ) > 0 )
        {
            // eZLog::write( 'ContenClassAttributeIDList : ' . print_r( $ContentClassAttributeIDList, 1), 'error.log' );

            $fields = array( 'contentclass_attribute_id' );
            $existingAttributeList   = eZXMLExportAvailableContentClassAttributes::fetchList( $fields, $conds, null, null, false );
            $existingAttributeIDList = array();

            foreach( $existingAttributeList as $existingAttribute )
            {
                $existingAttributeIDList[] = $existingAttribute['contentclass_attribute_id'];
            }

            // eZLog::write( 'existingAttributeList : ' . print_r( $existingAttributeList, 1 ), 'error.log' );
            // eZLog::write( 'existingAttributeIDList : ' . print_r( $existingAttributeIDList, 1 ), 'error.log' );
            unset( $existingAttributeList );

            $eZXMLExportAvailableContentClassAttributes = new eZXMLExportAvailableContentClassAttributes();

            // if stored in DB and uncheked => delete
            foreach( $existingAttributeIDList as $existingAttributeID )
            {
                if( !in_array( $existingAttributeID, $ContentClassAttributeIDList ) )
                {
                    // delete
                    eZLog::write( 'Deleting attributeID : ' . $existingAttributeID, 'error.log' );
                    $eZXMLExportAvailableContentClassAttributes->setAttribute( 'contentclass_attribute_id', $existingAttributeID );
                    $eZXMLExportAvailableContentClassAttributes->remove();
                }
            }

            // if not in DB and checked => insert
            foreach( $ContentClassAttributeIDList as $contentClassAttributeID )
            {
                $eZXMLExportAvailableContentClassAttributes->setAttribute( 'contentclass_attribute_id', $contentClassAttributeID );
                $eZXMLExportAvailableContentClassAttributes->setAttribute( 'contentclass_id'          , $ContentClassID );

                if( !in_array( $contentClassAttributeID, $existingAttributeIDList ) )
                {
                    // insert
                    eZLog::write( 'Inserting attributeID : ' . $contentClassAttributeID, 'error.log' );
                    $eZXMLExportAvailableContentClassAttributes->store();
                }
            }
        }
    }

    if( $Action == 'remove' )
    {
        eZLog::write( 'Deleting class : ' . $ContentClassID, 'error.log' );
        $eZXMLExportAvailableContentClasses->remove();

        $fields = array( 'contentclass_attribute_id' );
        $conds  = array( 'contentclass_id' => $ContentClassID );
        $relatedAttributeList = eZXMLExportAvailableContentClassAttributes::fetchList( $fields, $conds, null, null );

        foreach( $relatedAttributeList as $relatedAttribute )
        {
            $relatedAttribute->remove();
        }
    }
}

eZExecution::cleanExit();
?>