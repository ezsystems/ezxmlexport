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
}
?>
