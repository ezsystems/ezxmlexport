<?php
class eZXMLExportAvailableContentClassAttributes extends eZPersistentObject
{
    function eZXMLExportAvailableContentClassAttributes( $row = null )
    {
        $this->eZPersistentObject( $row );
    }

    static function definition()
    {
        $def = array( 'fields' => array( 'contentclass_attribute_id' => array( 'name' => 'contentclass_attribute_id',
                                                                               'datatype' => 'integer',
                                                                               'required' => true ),

                                         'contentclass_id'           => array( 'name' => 'contentclass_id',
                                                                               'datatype' => 'integer',
                                                                               'required'  => true) ),
                      'keys' => array( 'contentclass_attribute_id' ),
                      'function_attributes' => array(),
                      'class_name' => 'eZXMLExportAvailableContentClassAttributes',
                      'sort' => array(),
                      'name' => 'ezxmlexport_available_contentclass_attributes' );
        return $def;
    }

    public static function fetchAll( $fieldList = null, $offset = null, $limit = null )
    {
        return eZPersistentObject::fetchObjectList( self::definition(),
                                                    $fieldList );
    }

    public static function fetchList( $fieldList = null, $conditions = null, $offset = null, $limit = null, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( self::definition(),
                                                    $fieldList,
                                                    $conditions,
                                                    null,
                                                    null,
                                                    $asObject );
    }

    public static function isExportable( $attributeID )
    {
        $objectList = eZPersistentObject::fetchObjectList( self::definition(),
                                                           null,
                                                           array( 'contentclass_attribute_id' => $attributeID ),
                                                           null,
                                                           null,
                                                           false );

        return isset( $objectList[0]['contentclass_attribute_id'] );
    }

    public static function fetchFromClassID( $classID )
    {
       return eZPersistentObject::fetchObjectList( eZXMLExportAvailableContentClassAttributes::definition(),
                                                   array( 'contentclass_attribute_id' ),
                                                   array( 'contentclass_id' => $classID ),
                                                   array( 'contentclass_attribute_id' => 'asc' ),
                                                   null,
                                                   false );

    }

    public $ID;
}
?>
