<?php
class eZXMLExportCustomers extends eZPersistentObject
{
    function eZXMLExportCustomers( $row = null )
    {
        $this->eZPersistentObject( $row );
    }

    static function definition()
    {
        $def = array( 'fields' => array( 'id'           => array( 'name'     => 'ID',
                                                                  'datatype' => 'integer',
                                                                  'default'  => 0,
                                                                  'required' => false ),

                                         'name'         => array( 'name'     => 'Name',
                                                                  'datatype' => 'string',
                                                                  'required' => true ),

                                         'ftp_target'   => array( 'name'     => 'FTPTarget',
                                                                  'datatype' => 'string',
                                                                  'required' => true,
                                                                  'default'  => '' ),

                                         'slicing_mode' => array( 'name'     => 'SlicingMode',
                                                                  'datatype' => 'string',
                                                                  'required' => true,
                                                                  'default'  => 1 ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array(),
                      'increment_key' => 'id',
                      'class_name' => 'eZXMLExportCustomers',
                      'sort' => array(),
                      'name' => 'ezxmlexport_customers' );
        return $def;
    }

    public static function fetchAll( $offset = null, $limit = null )
    {
        return eZPersistentObject::fetchObjectList( self::definition() );
    }

    public static function fetch( $customerID )
    {
        $objectList = array();

        $objectList = eZPersistentObject::fetchObjectList( self::definition(),
                                                           null,
                                                           array( 'id' => $customerID ) );
        if( $objectList )
        {
            return $objectList[0];
        }

        return false;
    }

    public static function removeCustomer( $customerID )
    {
        eZPersistentObject::removeObject( eZXMLExportCustomers::definition(),
                                          array( 'id' => $customerID ) );

        // removing related exports as well
        eZPersistentObject::removeObject( eZXMLExportExports::definition(),
                                          array( 'customer_id' => $customerID ) );
    }

    public $Name;
    public $FTPTarget;
    public $SlicingMode;
    public $ID;
}
?>