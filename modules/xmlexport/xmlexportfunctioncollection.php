<?php
class eZXMLExportFunctionCollection
{
    public function eZXMLExportFunctionCollection()
    {
    }

    public static function fetchCustomerList()
    {
        return array( 'result' => eZXMLExportCustomers::fetchAll() );
    }

    public static function fetchExportList( $customerID )
    {
        return array( 'result' => eZXMLExportExports::fetchByCustomerID( $customerID ) );
    }

    public static function fetchClass( $classID )
    {
        return array( 'result' => eZXMLExportAvailableContentClasses::fetchAll( $classID ) );
    }

    public static function fetchXSLTFiles()
    {
        return array( 'result' => eZXMLExportHelpers::fetchXSLTFiles() );
    }

    /**
     * Returns the eZXMLExportAvailableContentClasses object corresponding to 
     * the $classID or false if it does not exist
     * 
     * @param int $classID 
     * @return eZXMLExportAvailableContentClasses or false
     */
    public static function fetchClassAvailability( $classID )
    {
        $objects = eZXMLExportAvailableContentClasses::fetchList( null, array( 'contentclass_id' => $classID ) );
        return array( "result" => isset( $objects[0] ) ? $objects[0] : false );
    }
}
?>
