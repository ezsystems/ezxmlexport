<?php
class eZXMLExportAvailableContentClasses extends eZPersistentObject
{
    function eZXMLExportAvailableContentClasses( $row = null )
    {
        $this->eZPersistentObject( $row );
    }

    static function definition()
    {
        $def = array( 'fields' => array( 'contentclass_id' => array( 'name'     => 'contentclass_id',
                                                                     'datatype' => 'integer',
                                                                     'required' => true ) ),
                      'keys' => array(),
                      'function_attributes' => array(),
                      'class_name' => 'eZXMLExportAvailableContentClasses',
                      'sort' => array(),
                      'name' => 'ezxmlexport_available_contentclasses' );
        return $def;
    }

    public static function fetchAll( $classID = null )
    {
        $db = eZDB::instance();

        $sql = 'SELECT ezxmlexport_available_contentclasses.contentclass_id,
                       ezxmlexport_available_contentclass_attributes.contentclass_attribute_id
                FROM ezxmlexport_available_contentclasses,
                     ezxmlexport_available_contentclass_attributes';

        $whereClause = '';

        if( $classID != null )
        {
            $whereClause = ' WHERE ezxmlexport_available_contentclasses.contentclass_id = ezxmlexport_available_contentclass_attributes.contentclass_id
                               AND ezxmlexport_available_contentclasses.contentclass_id = ' . $db->escapeString( $classID );
        }

        $sql .= $whereClause;

        return $db->arrayQuery( $sql );
    }

    public static function fetchExportableClasses()
    {
        $exportableClasses = array();

        $objectList = eZPersistentObject::fetchObjectList( eZXMLExportAvailableContentClasses::definition(),
                                                           null,
                                                           array(),
                                                           null,
                                                           null,
                                                           false );
        foreach( $objectList as $exportableClass )
        {
            $availableAttributeList = eZXMLExportAvailableContentClassAttributes::fetchFromClassID( $exportableClass['contentclass_id'] );

            $simplifiedAttributeIDList = array();

            foreach( $availableAttributeList as $availableAttribute )
            {
                $simplifiedAttributeIDList[] = $availableAttribute['contentclass_attribute_id'];
            }

            sort( $simplifiedAttributeIDList );

            $tempArray = array( 'contentclass_id' => $exportableClass['contentclass_id'],
                                'attribute_id_list' => $simplifiedAttributeIDList);
            //$exportableClasses['content_classes'][] = $tempArray;
            $exportableClasses[] = $tempArray;
        }

        return $exportableClasses;
    }

    public static function fetchList( $fieldList = null, $conditions = null, $offset = null, $limit = null )
    {
        return eZPersistentObject::fetchObjectList( self::definition(),
                                                    $fieldList,
                                                    $conditions );
    }

    public $ID;
}
?>
