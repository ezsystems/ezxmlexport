<?php
class eZXMLExportExports extends eZPersistentObject
{
    function eZXMLExportExports( $row = null )
    {
        $this->eZPersistentObject( $row );
    }

    public static function definition()
    {
        $def = array( 'fields' => array( 'id'              => array( 'name'              => 'ID',
                                                                     'datatype'          => 'integer',
                                                                     'required'          => true,
                                                                     'foreign_class'     => 'eZXMLExportCustomers',
                                                                     'foreign_attribute' => 'id',
                                                                     'multiplicity'      => '1..*' ),

                                         'customer_id'     => array( 'name'     => 'customer_id',
                                                                     'datatype' => 'integer',
                                                                     'required' => true ),

                                         'name'            => array( 'name'     => 'name',
                                                                     'datatype' => 'string',
                                                                     'required' => true ),

                                         'description'     => array( 'name'     => 'description',
                                                                     'datatype' => 'string',
                                                                     'required' => false,
                                                                     'default'  => '' ),

                                         'sources'         => array( 'name'     => 'sources',
                                                                     'datatype' => 'string',
                                                                     'required' => true ),

                                         'ftp_target'      => array( 'name'     => 'ftp_target',
                                                                     'datatype' => 'string',
                                                                     'required' => false ),

                                         'slicing_mode'    => array( 'name'     => 'slicing_mode',
                                                                     'datatype' => 'string',
                                                                     'required' => false,
                                                                     'default'  => 1),

                                         'start_date'      => array( 'name'     => 'start_date',
                                                                     'datatype' => 'integer',
                                                                     'required' => true,
                                                                     'default'  => 1),

                                         'end_date'        => array( 'name'     => 'end_date',
                                                                     'datatype' => 'integer',
                                                                     'required' => true,
                                                                     'default'  => 1),

                                         'export_schedule' => array( 'name'     => 'export_schedule',
                                                                     'datatype' => 'string',
                                                                     'required' => true ),

                                         'export_limit'    => array( 'name'     => 'export_limit',
                                                                     'datatype' => 'integer',
                                                                     'required' => false,
                                                                     'default'  => '' ),

                                         'export_from_last'=> array( 'name'     => 'export_from_last',
                                                                     'datatype' => 'integer',
                                                                     'required' => false,
                                                                     'default'  => 0 ),

                                         'compression'     => array( 'name'     => 'compression',
                                                                     'datatype' => 'integer',
                                                                     'required' => false,
                                                                     'default' => '0' ),

                                         'related_object_handling' => array( 'name'     => 'related_object_handling',
                                                                             'datatype' => 'integer',
                                                                             'required' => false,
                                                                             'default'  => 1 ),

                                         'xslt_file'               => array( 'name'     => 'xslt_file',
                                                                             'datatype' => 'string',
                                                                             'required' => true,
                                                                             'default'  => '' ),

                                         'export_hidden_nodes'     => array( 'name'     => 'export_hidden_nodes',
                                                                             'datatype' => 'integer',
                                                                             'required' => false,
                                                                             'default'  => 0 ) ),

                      'keys' => array( 'id' ),
                      'function_attributes' => array(),
                      'increment_key' => 'id',
                      'class_name' => 'eZXMLExportExports',
                      'sort' => array(),
                      'name' => 'ezxmlexport_exports' );
        return $def;
    }

    public static function fetchAll( $offset = null, $limit = null )
    {
        return eZPersistentObject::fetchObjectList( self::definition() );
    }

    public static function fetchByCustomerID( $customerID )
    {
        return eZPersistentObject::fetchObjectList( self::definition(),
                                                    null,
                                                    array( 'customer_id' => $customerID ) );
    }

    public static function fetch( $exportID )
    {
        $objectList = array();

        $objectList = eZPersistentObject::fetchObjectList( self::definition(),
                                                           null,
                                                           array( 'id' => $exportID ) );
        if( $objectList )
        {
            return $objectList[0];
        }

        return false;
    }

    public static function fetchAvailableExports()
    {
        $objectList = array();

        $now = time();
        $currentDay   = date( 'j', $now );
        $currentWeek  = date( 'w', $now );
        $currentMonth = date( 'n', $now );

        $objectList = eZPersistentObject::fetchObjectList( self::definition(),
                                                           null,
                                                           array( 'start_date' => array( '<=', $now ) ),
                                                                  //'end_date'   => array( '>=', $now ) ),
                                                           array( 'start_date' => 'desc' ) );
        $availableExportList = array();

        if( $objectList )
        {
            foreach( $objectList as $export )
            {
                if( eZXMLExportExports::exportIsAvailable( $export ) )
                {
                    $availableExportList[] = $export;
                }
            }
        }

        return $availableExportList;
    }

    public static function exportIsAvailable( $export )
    {
        $exportEndDate = $export->attribute( 'end_date' );

        // using an end_date simply disables recurrence
        if( $exportEndDate > time() )
        {
            return true;
        }

        $latestRunExport = eZXMLExportProcessLog::fetchByExportID( $export->attribute( 'id' ),
                                                                   array( 'end_date' => 'desc' ),
                                                                   array( 'offset'   => 0,
                                                                          'limit'    => 1 ) );

        // no log means this is
        // the first time the export is run
        if( !$latestRunExport )
        {
            return true;
        }

        $latestExecutionDate = $latestRunExport->attribute( 'end_date' );

        $now = time();

        if( !$latestExecutionDate )
        {
            return false;
        }

        $exportSchedule = unserialize( $export->attribute( 'export_schedule' ) );

        /* test purpose only
        $month = 2;
        $day   = 29;
        $latestExecutionDate = gmmktime(0, 0, 0, $month, $day, 2008);
        print( 'latestExecutionDate : ' . date( 'Y/m/d', $latestExecutionDate ) . "\n" );

        $month = 3;
        $day   = 3;
        $now = gmmktime(0, 0, 0, $month, $day, 2008);
        print( 'now : ' . date( 'Y/m/d', $now ) . "\n" );
        */

        $scheduleUnit   = $exportSchedule['schedule']['unit'];
        $scheduleValue  = $exportSchedule['schedule']['value'];

        // schedule conversion
        switch( $scheduleUnit )
        {
            case 'day' :

                // 60 seconds * 60 minutes * 24 hours
                $dayRepresentation = 86400;
                $timeDifference = $now - $latestExecutionDate;

                eZDebug::writeNotice( 'TimeDifference : ' . $timeDifference . ' => ' . floor( $timeDifference / $dayRepresentation ) . " day(s)" );

                if( $timeDifference >= ( $scheduleValue * $dayRepresentation ) )
                {
                    return true;
                }

            break;

            case 'week' :

                /* possibility 1 : every X week the day does not matter
                $latestExecutionWeek = date( 'W', $latestExecutionDate );
                $currentWeek         = date( 'W', $now );

                eZDebug::writeNotice( 'latestExecutionWeek : ' . $latestExecutionWeek . ", currentWeek : " . $currentWeek);
                eZDebug::writeNotice( 'timeDifference : ' . abs( $currentWeek - $latestExecutionWeek ) . " week(s)" );

                if( abs( $currentWeek - $latestExecutionWeek ) >= $scheduleValue )
                {
                    return true;
                }
                */

                /* possibility 2 : every 7 days */
                // 86400 * 7 days
                $weekRepresentation = 604800;
                $timeDifference = $now - $latestExecutionDate;
                eZDebug::writeNotice( 'TimeDifference : ' . $timeDifference . ' => ' . floor( $timeDifference / $weekRepresentation ) . " week(s)" );

                if( $timeDifference >= ( $scheduleValue * $weekRepresentation ) )
                {
                    return true;
                }

            break;

            case 'month' :

                $latestExecutionMonth = date( 'n', $latestExecutionDate );
                $currentMonth         = date( 'n', $now );

                // thanks Bin for pointing this out ;)
                if(date( 'Y', $now ) > date( 'Y', $latestExecutionDate ))
                {
                    $currentMonth = $currentMonth + 12;
                }

                eZDebug::writeNotice( 'latestExecutionMonth : ' . $latestExecutionMonth . ", currentMonth : " . $currentMonth );
                eZDebug::writeNotice( 'time Difference : ' .  abs( $currentMonth - $latestExecutionMonth ) . " month(s)" );

                if( abs( $currentMonth - $latestExecutionMonth ) >= $scheduleValue )
                {
                    return true;
                }

            break;

            default : return false;
        }

        return false;
    }

    public static function removeExport( $exportID )
    {
        eZPersistentObject::removeObject( eZXMLExportExports::definition(),
                                          array( 'id' => $exportID ) );
    }

    public static function fetchByStatus( $status )
    {
        $db = eZDB::instance();

        $sql = 'SELECT exports.id,
                       exports.name,
                       exports.ftp_target,
                       exports.slicing_mode,
                       logs.start_date,
                       logs.status

                FROM ezxmlexport_exports AS exports,
                     ezxmlexport_process_logs as logs

                WHERE logs.export_id = exports.id
                   AND status <= ' . (int)$status;

        return $db->arrayQuery( $sql );
    }

    public $ID;
}
?>
