<?php
/**
 * File containing the eZXMLExportProcessLog class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

class eZXMLExportProcessLog extends eZPersistentObject
{
    const STATUS_XML_GENERATION_STARTED      = 2;
    const STATUS_XSLT_TRANSFORMATION_STARTED = 2;
    const STATUS_XSLT_TRANSFORMATION_DONE    = 4;
    const STATUS_FTP_CONNECTION_FAILED       = 8;
    const STATUS_FTP_TRANSFERT_STARTED       = 16;
    const STATUS_FTP_TRANSFERT_FAILED        = 32;
    const STATUS_FTP_TRANSFERT_DONE          = 64;
    const STATUS_XML_GENERATION_DONE         = 128;

    function eZXMLExportProcessLog( $row = null )
    {
        $this->eZPersistentObject( $row );
    }

    static function definition()
    {
        $def = array( 'fields' => array( 'id'                   => array( 'name'     => 'ID',
                                                                          'datatype' => 'integer',
                                                                          'required' => true ),

                                         'export_id'            => array( 'name'     => 'ExportID',
                                                                          'datatype' => 'integer',
                                                                          'required' => true ),

                                         'start_date'           => array( 'name'     => 'StartDate',
                                                                          'datatype' => 'string',
                                                                          'required' => true,
                                                                          'default'  => '' ),

                                         'end_date'             => array( 'name'     => 'EndDate',
                                                                          'datatype' => 'string',
                                                                          'required' => true,
                                                                          'default'  => '' ),

                                         'start_transfert_date' => array( 'name'     => 'StartTransfertDate',
                                                                          'datatype' => 'string',
                                                                          'required' => true,
                                                                          'default'  => '' ),

                                         'end_transfert_date'   => array( 'name'     => 'EndTransfertDate',
                                                                          'datatype' => 'string',
                                                                          'required' => true,
                                                                          'default'  => '' ),

                                         'status'               => array( 'name'     => 'Status',
                                                                          'datatype' => 'integer',
                                                                          'required' => true,
                                                                          'default'  => 1),

                                         /*'object_id_list'       => array( 'name'     => 'ObjectIDList',
                                                                          'datatype' => 'string',
                                                                          'required' => true )*/ ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array(),
                      'set_functions' => array( 'object_id' => 'setObjectID' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZXMLExportProcessLog',
                      'sort' => array(),
                      'name' => 'ezxmlexport_process_logs' );
        return $def;
    }

    public static function fetchByExportID( $exportID, $sortBy = null, $limit = null )
    {
        $objectList = array();

        $objectList = eZPersistentObject::fetchObjectList( self::definition(),
                                                           null,
                                                           array( 'export_id' => $exportID,
                                                                  // I do not want unterminated
                                                                  // export's logs
                                                                  'end_date'  => array( '!=', '' ) ),
                                                           $sortBy,
                                                           $limit );
        if( $objectList )
        {
            return $objectList[0];
        }

        return false;
    }

    public function setObjectID( $contentObjectID )
    {
        $db = eZDB::instance();
        $sql = 'INSERT INTO ezxmlexport_export_object_log ( process_log_id , contentobject_id )
                VALUES( ' . $db->escapeString( $this->ID ) . ', '
                          . $db->escapeString( $contentObjectID ) . ')';

        if( !$db->query( $sql ) )
        {
            eZLog::write( 'Unable to store exported object ID for the current process logger in xml export' );
        }
    }

    public $ID;
    public $StartDate;
    public $EndDate;
    public $ExportID;
    public $StartTransfertDate;
    public $EndTransfertDate;
    public $Status;
    public $ObjectIDList;
}
?>