<?php
$FunctionList = array();

$FunctionList['customers'] = array(
    'name'            => 'customers',
    'operation_types' => array( 'read' ),
    'call_method'     => array( 'include_file' => 'extension/ezxmlexport/modules/xmlexport/xmlexportfunctioncollection.php',
                                'class'        => 'eZXMLExportFunctionCollection',
                                'method'       => 'fetchCustomerList' ),
    'parameter_type' => 'standard',
    'parameters'     => array( ) );

$FunctionList['exports'] = array(
    'name'            => 'exports',
    'operation_types' => array( 'read' ),
    'call_method'     => array( 'include_file' => 'extension/ezxmlexport/modules/xmlexport/xmlexportfunctioncollection.php',
                                'class'        => 'eZXMLExportFunctionCollection',
                                'method'       => 'fetchExportList' ),
    'parameter_type' => 'standard',
    'parameters' => array( array( 'name' => 'customer_id',
                                  'type' => 'integer',
                                  'required' => true ) ) );

$FunctionList['class'] = array(
    'name'            => 'name',
    'operation_types' => array( 'read' ),
    'call_method'     => array( 'include_file' => 'extension/ezxmlexport/modules/xmlexport/xmlexportfunctioncollection.php',
                                'class'        => 'eZXMLExportFunctionCollection',
                                'method'       => 'fetchClass' ),
    'parameter_type' => 'standard',
    'parameters' => array( array( 'name' => 'class_id',
                                  'type' => 'integer',
                                  'required' => true ) ) );

$FunctionList['class_availability'] = array(
    'name'            => 'class_availability',
    'operation_types' => array( 'read' ),
    'call_method'     => array( 'include_file' => 'extension/ezxmlexport/modules/xmlexport/xmlexportfunctioncollection.php',
                                'class'        => 'eZXMLExportFunctionCollection',
                                'method'       => 'fetchClassAvailability' ),
    'parameter_type' => 'standard',
    'parameters' => array( array( 'name' => 'class_id',
                                  'type' => 'integer',
                                  'required' => true ) ) );

$FunctionList['xsltfiles'] = array(
    'name'            => 'xsltfiles',
    'operation_types' => array( 'read' ),
    'call_method'     => array( 'include_file' => 'extension/ezxmlexport/modules/xmlexport/xmlexportfunctioncollection.php',
                                'class'        => 'eZXMLExportFunctionCollection',
                                'method'       => 'fetchXSLTFiles' ),
    'parameter_type' => 'standard',
    'parameters' => array( ) );
?>
