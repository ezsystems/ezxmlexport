<?php

$Module = array( 'name' => 'xmlexport' );

$ViewList['test'] = array(
    'script'                  => 'test.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'ui_context'              => 'administration',
    'params'                  => array( 'Type', 'ID' ),
    'unordered_params'        => array( 'offset' => 'Offset') );

$ViewList['view'] = array(
    'script'                  => 'view.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'ui_context'              => 'administration',
    'params'                  => array( 'Type', 'ID' ) );

$ViewList['menu'] = array(
    'script'                  => 'menu.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'ui_context'              => 'administration',
    'params'                  => array() );

$ViewList['ftptest'] = array(
    'script'                  => 'ftptest.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'ui_context'              => 'administration',
    'params'                  => array() );

$ViewList['runningexports'] = array(
    'script'                  => 'runningexports.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'ui_context'              => 'administration',
    'params'                  => array() );

$ViewList['storeavailability'] = array(
    'script'                  => 'storeavailability.php',
    'params'                  => array() );

$ViewList['createxmlschema'] = array(
    'script'                  => 'createxmlschema.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'ui_context'              => 'administration',
    'params'                  => array() );

$ViewList['relaunchfttransfert'] = array(
    'script'                  => 'relaunchftptransfert.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'ui_context'              => 'administration',

    'single_post_actions'     => array( 'RelaunchFTPTransfertButton' => 'RelaunchFTPTransfert' ),

    'post_action_parameters'  => array( 'RelaunchFTPTransfert' => array( 'SelectedExportIDArray' => 'SelectedExportIDArray' ) ) );

$ViewList['delete'] = array(
     'script'                  => 'delete.php',
     'default_navigation_part' => 'ezsetupnavigationpart',
     'ui_context'              => 'administration',

     'single_post_actions'     => array( 'DeleteExportButton'   => 'DeleteExport',
                                         'DeleteCustomerButton' => 'DeleteCustomer' ),

     'post_action_parameters'  => array( 'DeleteExport'   => array( 'DeleteIDArray' => 'DeleteIDArray' ),
                                         'DeleteCustomer' => array( 'DeleteIDArray' => 'DeleteIDArray' ) ) );

$ViewList['edit'] = array(
    'script'                  => 'edit.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'ui_context'              => 'administration',

    'single_post_actions'     => array( 'PublishCustomerButton' => 'PublishCustomer',
                                        'PublishExportButton'   => 'PublishExport',
                                        'BrowseExportNodes'     => 'BrowseForNodes' ),

    'post_action_parameters'  => array( 'PublishCustomer' => array( 'CustomerName'        => 'CustomerName',
                                                                    'FTPHost'             => 'FTPHost',
                                                                    'FTPPort'             => 'FTPPort',
                                                                    'FTPLogin'            => 'FTPLogin',
                                                                    'FTPPassword'         => 'FTPPassword',
                                                                    'FTPPath'             => 'FTPPath',
                                                                    'CustomerSlicingMode' => 'CustomerSlicingMode' ),

                                        'PublishExport'   => array( 'ExportSources'                  => 'ExportSources',
                                                                    'ExportCustomerID'               => 'ExportCustomerID',
                                                                    'ExportName'                     => 'ExportName',
                                                                    'ExportDescription'              => 'ExportDescription',
                                                                    'ExportContentList'              => 'ExportContentList',
                                                                    'FTPHost'                        => 'FTPHost',
                                                                    'FTPPort'                        => 'FTPPort',
                                                                    'FTPLogin'                       => 'FTPLogin',
                                                                    'FTPPassword'                    => 'FTPPassword',
                                                                    'FTPPath'                        => 'FTPPath',
                                                                    'ExportSlicingMode'              => 'ExportSlicingMode',
                                                                    'ExportXSLTFile'                 => 'ExportXSLTFile',
                                                                    'ExportSchedule'                 => 'ExportSchedule',
                                                                    'ExportCompression'              => 'ExportCompression',
                                                                    'ExportHiddenNodes'              => 'ExportHiddenNodes',
                                                                    'ExportRelatedObjectHandling'    => 'ExportRelatedObjectHandling',
                                                                    'ExportStartDate'                => 'ExportStartDate',
                                                                    'ExportEndDate'                  => 'ExportEndDate',
                                                                    'ExportRecurrenceValue'          => 'ExportRecurrenceValue',
                                                                    'ExportRecurrenceUnit'           => 'ExportRecurrenceUnit',
                                                                    'ExportObjectNumberLimit'        => 'ExportObjectNumberLimit',
                                                                    'ExportAllObjectsFromLastExport' => 'ExportAllObjectsFromLastExport' ),

                                        'BrowseForNodes'  => array() ),

    'post_actions'            => array( 'BrowseActionName' ),

    'unordered_params' => array( 'type'     => 'Type',
                                 'customer' => 'CustomerID',
                                 'export'   => 'ExportID' ) );

    // 'params'                  => array( 'Type', 'ID' ) );

?>
