<?php
$Module = $Params['Module'];

/* Possible values :
 * - customer
 * - export
 */
$Type       = $Params['Type'];
$CustomerID = $Params['CustomerID'];
$ExportID   = $Params['ExportID'];
$Ini        = eZINI::instance( 'ezxmlexport.ini' );

if( $Type != 'customer' and $Type != 'export' )
{
    $Module->redirectToView( 'menu' );
    return;
}

$tpl = eZTemplate::factory();

$viewParameters = array( 'type' => $Type);

if( $CustomerID )
{
    $viewParameters['customer'] = $CustomerID ;
}

if( $ExportID )
{
    $viewParameters['export'] = $ExportID ;
}

$tpl->setVariable( 'view_parameters', $viewParameters );

$errorMessageList = array();

$canPublish = true;

if( $Type == 'customer' )
{
    $templateName = 'customer';
    $tpl->setVariable( 'CustomerNameValue', '' );
    $tpl->setVariable( 'CustomerSlicingModeValue', '-1' );
    $tpl->setVariable( 'FTPHostValue'    , '' );
    $tpl->setVariable( 'FTPPortValue'    , '21' );
    $tpl->setVariable( 'FTPLoginValue'   , '' );
    $tpl->setVariable( 'FTPPasswordValue', '' );
    $tpl->setVariable( 'FTPPathValue'    , '/' );

    if( $Module->isCurrentAction( 'PublishCustomer' ) )
    {
        // checking customer's name
        if( !$Module->hasActionParameter( 'CustomerName' )
            or strlen( $Module->actionParameter( 'CustomerName' ) ) == 0
            or strlen( $Module->actionParameter( 'CustomerName' ) ) > 200 )
        {
            $errorMessageList[] = 'The name must be 200 char maximum';
            $canPublish = false;
        }
        else
        {
            $tpl->setVariable( 'CustomerNameValue', $Module->actionParameter( 'CustomerName' ) );
        }

        // checking customer's FTP target
        $FTPInfos = array();

        if( $Ini->variable( 'FTPSettings', 'FTPShipment' ) == 'enabled' )
        {
            $FTPFieldList = array( 'Host', 'Port', 'Login', 'Password', 'Path' );

            foreach( $FTPFieldList as $FTPField )
            {
                $tpl->setVariable( 'FTP' . $FTPField . 'Value', $Module->actionParameter( 'FTP' . $FTPField ) );
            }

            // if there is a value for host this means the user entered a new FTP
            // target, so we need to check it it will be ignored otherwise
            if( $Module->hasActionParameter( 'FTPHost' ) and $Module->actionParameter( 'FTPHost' ) != '' )
            {
                $FTPHost     = $Module->actionParameter( 'FTPHost' );
                $FTPPort     = $Module->actionParameter( 'FTPPort' );
                $FTPLogin    = $Module->actionParameter( 'FTPLogin' );
                $FTPPassword = $Module->actionParameter( 'FTPPassword' );
                $FTPPath     = $Module->actionParameter( 'FTPPath' );

                if( empty( $FTPPath ) )
                {
                    $FTPPath = '/';
                }

                if( !eZXMLExportFTPFileHandler::checkFTPTarget( $FTPHost, $FTPPort, $FTPLogin, $FTPPassword, $FTPPath ) )
                {
                    eZDebug::writeError( 'Please choose a correct FTP target', 'ezxmlexport/edit :: FTPTarget');
                    $errorMessageList[] = 'Incorrect FTP informations, please check your login credentials';
                    $canPublish = false;
                }
                else
                {
                    $FTPInfos = array( 'host'     => $FTPHost,
                                    'port'     => $FTPPort,
                                    'login'    => $FTPLogin,
                                    'password' => $FTPPassword,
                                    'path'     => $FTPPath );
                }
            }
        }

        // checking slicing mode
        if( !checkSlicingMode( 'CustomerSlicingMode', $Module, $tpl, $errorMessageList ) )
        {
            $canPublish = false;
        }
        else
        {
            $tpl->setVariable( 'ExportSlicingModeValue', $Module->actionParameter( 'CustomerSlicingMode' ) );
        }

        if( $canPublish )
        {
            if( isset( $CustomerID ) and $CustomerID > 0 )
            {
                $eZXMLExportCustomer = eZXMLExportCustomers::fetch( $CustomerID );
            }
            else
            {
                $eZXMLExportCustomer = new eZXMLExportCustomers();
            }

            $eZXMLExportCustomer->setAttribute( 'name'        , $Module->actionParameter( 'CustomerName' ) );
            $eZXMLExportCustomer->setAttribute( 'ftp_target'  , serialize( $FTPInfos ) );
            $eZXMLExportCustomer->setAttribute( 'slicing_mode', $Module->actionParameter( 'CustomerSlicingMode' ) );
            $eZXMLExportCustomer->store();

            $Module->redirectTo( 'xmlexport/view/customer/' . $eZXMLExportCustomer->attribute( 'id' ) );
            return;
        }
    }
    elseif( isset( $CustomerID ) and $CustomerID > 0 )
    {
        $eZXMLExportCustomer = eZXMLExportCustomers::fetch( $CustomerID );

        if( $eZXMLExportCustomer )
        {
            $FTPInformations  = unserialize( $eZXMLExportCustomer->attribute( 'ftp_target' ) );

            $FTPHostValue     = ( isset( $FTPInformations['host'] )     ? $FTPInformations['host']     : '' );
            $FTPPortValue     = ( isset( $FTPInformations['port'] )     ? $FTPInformations['port']     : '21' );
            $FTPLoginValue    = ( isset( $FTPInformations['login'] )    ? $FTPInformations['login']    : '' );
            $FTPPasswordValue = ( isset( $FTPInformations['password'] ) ? $FTPInformations['password'] : '' );
            $FTPPathValue     = ( isset( $FTPInformations['path'] )     ? $FTPInformations['path']     : '' );

            $tpl->setVariable( 'CustomerNameValue'       , $eZXMLExportCustomer->attribute( 'name' ) );
            $tpl->setVariable( 'CustomerSlicingModeValue', $eZXMLExportCustomer->attribute( 'slicing_mode' ) );

            $tpl->setVariable( 'FTPHostValue'    , $FTPHostValue     );
            $tpl->setVariable( 'FTPPortValue'    , $FTPPortValue     );
            $tpl->setVariable( 'FTPLoginValue'   , $FTPLoginValue    );
            $tpl->setVariable( 'FTPPasswordValue', $FTPPasswordValue );
            $tpl->setVariable( 'FTPPathValue'    , $FTPPathValue     );
        }
    }
}

if( $Type == 'export' )
{
    // this part triggers content/browse
    if( $Module->isCurrentAction( 'BrowseForNodes' ) )
    {
        $postActionParamList = $Module->Functions['edit']['post_action_parameters']['PublishExport'];
        $ezhttp = eZHTTPTool::instance();

        $persistentDataArray = array();
        $persistentDataArray['HasObjectInput'] = 0;

        foreach( $postActionParamList as $postActionParam )
        {
            $value = null;

            if( $ezhttp->hasPostVariable( $postActionParam ) )
            {
                $value = $ezhttp->postVariable( $postActionParam );
            }

            $persistentDataArray[$postActionParam] = $value;
        }

        $fromCancelPagePrefix = 'xmlexport/edit/(type)/export';
        $fromCancelPage       = '';

        if( $CustomerID )
        {
            $fromCancelPage = $fromCancelPagePrefix . '/(customer)/' . $CustomerID;
        }

        if( $ExportID )
        {
            $fromCancelPage = $fromCancelPagePrefix . '/(export)/' . $ExportID;
        }

        eZContentBrowse::browse( array( 'action_name' => 'BrowseXMLExport',
                                        //'type' =>  'AddBookmark',
                                        //'browse_custom_action' => array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_set_object_relation]',
                                        //                                 'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                        //'persistent_data' => array( 'HasObjectInput' => 0 ),
                                        'persistent_data' => $persistentDataArray,
                                        'from_page'       => $fromCancelPage,
                                        'cancel_page'     => $fromCancelPage),
                                        $Module );
        return;
    }

    // this part fetch data from content/browse
    $http = eZHTTPTool::instance();

    $tpl->setVariable( 'selected_node_id_list', array() );

    if ( $http->hasPostVariable( 'BrowseActionName' )
         and
         $http->postVariable( 'BrowseActionName' ) == 'BrowseXMLExport'
         and
         $http->hasPostVariable( 'SelectedNodeIDArray' ) )
    {
        if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
        {
            $selectedNodeIDArray = $http->postVariable( 'SelectedNodeIDArray' );
            $tpl->setVariable( 'selected_node_id_list', $selectedNodeIDArray  );
        }
    }

    $templateName = 'export';

    $defaultSlicingMode = 1;

    if( $CustomerID )
    {
        $eZXMLExportCustomer = eZXMLExportCustomers::fetch( $CustomerID );
        $defaultSlicingMode = $eZXMLExportCustomer->attribute( 'slicing_mode' );
    }

    $templateVariableList = array(
        // templateVariableName               => defaultValue
        'ExportCustomerIDValue'               => -1,
        'ExportNameValue'                     => '',
        'ExportDescriptionValue'              => '',
        'ExportXSLTFileValue'                 => '-1',
        'ExportSlicingModeValue'              => $defaultSlicingMode,
        'ExportStartDateValue'                => date( 'd/m/Y' , time() ),
        'ExportEndDateValue'                  => 'dd/mm/YYYY',
        'ExportRecurrenceValueValue'          => 0,
        'ExportRecurrenceUnitValue'           => 'day',
        'ExportObjectNumberLimitValue'        => '',
        'ExportAllObjectsFromLastExportValue' => '',
        'ExportCompressionValue'              => '',
        'ExportHiddenNodesValue'              => '',
        'ExportRelatedObjectHandlingValue'    => 1,
        'FTPHostValue'                        => '',
        'FTPPortValue'                        => '21',
        'FTPLoginValue'                       => '',
        'FTPPasswordValue'                    => '',
        'FTPPathValue'                        => '/');

    // crap but no other choice...
    foreach( $templateVariableList as $templateVariable => $defaultValue )
    {
        $postVariable = str_replace( 'Value', '', $templateVariable );

        $tpl->setVariable( $templateVariable, $defaultValue );

        if( $http->hasPostVariable( $postVariable )
            and
            ( $http->postVariable( $postVariable ) != $defaultValue ) )
        {
            $tpl->setVariable( $templateVariable, $http->postVariable( $postVariable ) );
        }
    }

    // finally the real insert
    if( $Module->isCurrentAction( 'PublishExport' ) )
    {
        // checking export node list
        if( !$Module->hasActionParameter( 'ExportSources' ) or count( $Module->actionParameter( 'ExportSources' ) ) == 0 )
        {
            eZDebug::writeError( 'Please choose some content to export', 'ezxmlexport/edit :: ExportSources' );
            $errorMessageList[] = 'Please choose some content to export';
            $canPublish = false;
        }
        else
        {
            $tpl->setVariable( 'selected_node_id_list', $Module->actionParameter( 'ExportSources' ) );
        }

        // checking customer ID
        if( (int)$Module->actionParameter( 'ExportCustomerID' ) <= 0 )
        {
            eZDebug::writeError( 'Please choose a valid customer ID', 'ezxmlexport/edit :: ExportCustomerID' );
            $errorMessageList[] = 'Please choose a valid customer ID';
            $canPublish = false;
        }
        else
        {
            $tpl->setVariable( 'ExportCustomerIDValue', $Module->actionParameter( 'ExportCustomerID' ) );
        }

        // checking export's name
        if( !$Module->hasActionParameter( 'ExportName' )
            or strlen( $Module->actionParameter( 'ExportName' ) ) == 0
            or strlen( $Module->actionParameter( 'ExportName' ) ) > 200 )
        {
            eZDebug::writeError( 'The name must be 200 char maximum', 'ezxmlexport/edit :: ExportName' );
            $errorMessageList[] = 'The name must be 200 char maximum';
            $canPublish = false;
        }
        else
        {
            $tpl->setVariable( 'ExportNameValue', $Module->actionParameter( 'ExportName' ) );
        }

        if( $Module->hasActionParameter( 'ExportDescription' ) and $Module->actionParameter( 'ExportDescription' ) != '' )
        {
            $tpl->setVariable( 'ExportDescriptionValue', $Module->actionParameter( 'ExportDescription' ) );
        }

        // checking customer's FTP target
        $FTPInfos = array();
        if( $Ini->variable( 'FTPSettings', 'FTPShipment' ) == 'enabled' )
        {
            $FTPFieldList = array( 'Host', 'Port', 'Login', 'Password', 'Path' );

            foreach( $FTPFieldList as $FTPField )
            {
                $tpl->setVariable( 'FTP' . $FTPField . 'Value', $Module->actionParameter( 'FTP' . $FTPField ) );
            }

            if( $Module->hasActionParameter( 'FTPHost' ) and $Module->actionParameter( 'FTPHost' ) != '' )
            {
                $FTPHost     = $Module->actionParameter( 'FTPHost' );
                $FTPPort     = $Module->actionParameter( 'FTPPort' );
                $FTPLogin    = $Module->actionParameter( 'FTPLogin' );
                $FTPPassword = $Module->actionParameter( 'FTPPassword' );
                $FTPPath     = $Module->actionParameter( 'FTPPath' );

                if( empty( $FTPPath ) )
                {
                    $FTPPath = '/';
                }

                if( $FTPPath[0] != '/' )
                {
                    $FTPPath = '/' . $FTPPath;
                }

                if( !eZXMLExportFTPFileHandler::checkFTPTarget( $FTPHost, $FTPPort, $FTPLogin, $FTPPassword, $FTPPath ) )
                {
                    eZDebug::writeError( 'Please choose a correct FTP target', 'ezxmlexport/edit :: FTPTarget');
                    $errorMessageList[] = 'Incorrect FTP informations';
                    $canPublish = false;
                }
                else
                {
                    $FTPInfos = array( 'host'     => $FTPHost,
                                    'port'     => $FTPPort,
                                    'login'    => $FTPLogin,
                                    'password' => $FTPPassword,
                                    'path'     => $FTPPath );
                }
            }
        }

        if( $Module->hasActionParameter( 'ExportXSLTFile' )  )
        {
            if( $Module->actionParameter( 'ExportXSLTFile' ) == '-1' )
            {
                eZDebug::writeError( 'Please choose an XSLT file', 'ezxmlexport/edit :: ExportXSLTFile' );
                $errorMessageList[] = 'Please choose an XSLT File';

                $canPublish = false;
            }
            else
            {
                $tpl->setVariable( 'ExportXSLTFileValue', $Module->actionParameter( 'ExportXSLTFile' ) );
            }
        }

        if( !checkSlicingMode( 'ExportSlicingMode', $Module, $tpl, $errorMessageList ) )
        {
            $canPublish = false;
        }
        else
        {
            $tpl->setVariable( 'ExportSlicingModeValue', $Module->actionParameter( 'ExportSlicingMode' ) );
        }

        /*
         * checking time schedule
         * if start date and end date are present then take both
         * if start date and recurrence are present, then ignore end date
         *
         */
        $exportEndDate   = 0;
        $exportStartDate = 0;
        if( $Module->hasActionParameter( 'ExportStartDate' )
            and
            checkThisDate( $Module->actionParameter( 'ExportStartDate' ) ) )
        {
            $exportSchedule = array();

            list( $day, $month, $year ) = explode( '/', $Module->actionParameter( 'ExportStartDate' ) );
            $exportStartDate = mktime( 0, 0, 0, $month, $day, $year );
            $tpl->setVariable( 'ExportStartDateValue', $Module->actionParameter( 'ExportStartDate' ) );

            if( $Module->hasActionParameter( 'ExportEndDate' )
                and
                $Module->actionParameter( 'ExportEndDate' ) != ''
                and
                $Module->actionParameter( 'ExportEndDate' ) != 'dd/mm/YYYY' )
            {
                $exportSchedule = array(  );

                if( !checkThisDate( $Module->actionParameter( 'ExportEndDate' ) ) )
                {
                    eZDebug::writeError( 'Please give a valid end date', 'ezxmlexport/edit :: ExportEndDate' );
                    $errorMessageList[] = 'Please give a valid end date';
                    $canPublish = false;
                }
                else
                {
                    list( $day, $month, $year ) = explode( '/', $Module->actionParameter( 'ExportEndDate' ) );
                    $exportEndDate = mktime( 0, 0, 0, $month, $day, $year );
                    $tpl->setVariable( 'ExportEndDateValue', $Module->actionParameter( 'ExportEndDate' ) );
                }
            }
            else
            {
                $exportEndDate = 0;

                // need to check recurrence
                if( $Module->hasActionParameter( 'ExportRecurrenceValue' )
                    and
                    (int)$Module->actionParameter( 'ExportRecurrenceValue' ) > 0 )
                {
                    $tpl->setVariable( 'ExportRecurrenceValueValue', $Module->actionParameter( 'ExportRecurrenceValue' ) );

                    // checking recurrence unit after that
                    if( $Module->hasActionParameter( 'ExportRecurrenceUnit' ) )
                    {
                        $allowedValues = array( 'day', 'week', 'month' );
                        if( in_array( $Module->actionParameter( 'ExportRecurrenceUnit' ), $allowedValues ) )
                        {
                            $exportSchedule['schedule']['value'] = $Module->actionParameter( 'ExportRecurrenceValue' );
                            $exportSchedule['schedule']['unit']  = $Module->actionParameter( 'ExportRecurrenceUnit' );
                            $tpl->setVariable( 'ExportRecurrenceUnitValue', $Module->actionParameter( 'ExportRecurrenceUnit' ) );
                        }
                        else
                        {
                            eZDebug::writeError( 'Please give a valid recurrence unit', 'ezxmlexport/edit :: ExportRecurrenceUnit' );
                            $errorMessageList[] = 'Please give a valid recurrence unit';
                            $canPublish = false;
                        }
                    }
                }
                else
                {
                    eZDebug::writeError( 'Please give a valid recurrence value', 'ezxmlexport/edit :: ExportRecurrenceValue' );
                    $errorMessageList[] = 'Please give a valid recurrence value';
                    $canPublish = false;
                }
            }
        }
        else
        {
            eZDebug::writeError( 'Please give a start date', 'ezxmlexport/edit :: ExportStartDate' );
            $errorMessageList[] = 'Please give a start date';
            $canPublish = false;
        }

        // checking export limit
        $exportLimit = '';
        if( $Module->hasActionParameter( 'ExportObjectNumberLimit' ) )
        {
            if( $Module->actionParameter( 'ExportObjectNumberLimit' ) != '' )
            {
                if( $Module->actionParameter( 'ExportObjectNumberLimit' ) >= 0 )
                {
                    $tpl->setVariable( 'ExportObjectNumberLimitValue', $Module->actionParameter( 'ExportObjectNumberLimit' ) );
                    $exportLimit = $Module->actionParameter( 'ExportObjectNumberLimit' );
                }
                else
                {
                    eZDebug::writeError( 'Please choose a valid number', 'ezxmlexport/edit :: ExportObjectNumberLimit' );
                    $errorMessageList[] = 'Please choose a valid number for the export limit';
                    $canPublish = false;

                }
            }
        }

        // checking export from last export field
        $exportFromLast = 0;
        if( $Module->hasActionParameter( 'ExportAllObjectsFromLastExport' ) )
        {
            $tpl->setVariable( 'ExportAllObjectsFromLastExportValue', $Module->actionParameter( 'ExportAllObjectsFromLastExport' ) );
            $exportLimit    = -1;
            $exportFromLast = 1;
            if( (int)$Module->actionParameter( 'ExportAllObjectsFromLastExport' ) != 1 )
            {
                eZDebug::writeError( 'Please check a valid checkbox', 'ezxmlexport/edit :: ExportAllObjectsFromLastExport' );
                $errorMessageList[] = 'Please check a valid checkbox';
                $canPublish = false;
            }
        }

        // checking compression
        $exportCompression = 0;
        if( $Module->hasActionParameter( 'ExportCompression' ) )
        {
            if( $Module->actionParameter( 'ExportCompression' ) == 'enabled' )
            {
                $exportCompression = 1;
                $tpl->setVariable( 'ExportCompressionValue', 'checked="checked"');
            }
        }

        // checking hidden nodes export
        $exportHiddenNodes = 0;
        if( $Module->hasActionParameter( 'ExportHiddenNodes' ) )
        {
            if( $Module->actionParameter( 'ExportHiddenNodes'  ) == 'enabled' )
            {
                $exportHiddenNodes = 1;
                $tpl->setVariable( 'ExportHiddenNodesValue', 'checked="checked"' );
            }
        }

        // checking related objets handling
        $relatedObjectHandling = 1;
        if( $Module->hasActionParameter( 'ExportRelatedObjectHandling' ) )
        {
            $allowedValues = array( 1, 2 );
            if( !in_array( $Module->actionParameter( 'ExportRelatedObjectHandling' ), $allowedValues ) )
            {
                eZDebug::writeError( 'Please choose a correct value', 'ezxmlexport/edit :: ExportRelatedObjectHandling' );
                $errorMessageList[] = 'Please choose a correct value';
                $canPublish = false;
            }
            else
            {
                $relatedObjectHandling = $Module->actionParameter( 'ExportRelatedObjectHandling');
            }
        }
        else
        {
            $tpl->setVariable( 'ExportRelatedObjectHandlingValue', $Module->actionParameter( 'ExportRelatedObjectHandling' ) );
        }

        if( $canPublish )
        {
            if( isset( $ExportID ) and $ExportID > 0 )
            {
                $eZXMLExportExport = eZXMLExportExports::fetch( $ExportID );
                $CustomerID = $eZXMLExportExport->attribute( 'customer_id' );
            }
            else
            {
                $eZXMLExportExport = new eZXMLExportExports();
            }

            $xsltFile = '';
            if( $Ini->variable( 'XSLTSettings', 'XSLTTransformation' ) == 'enabled' )
            {
                $xsltFile = $Module->actionParameter( 'ExportXSLTFile' );
            }

            $eZXMLExportExport->setAttribute( 'customer_id'            , $Module->actionParameter( 'ExportCustomerID' ) );
            $eZXMLExportExport->setAttribute( 'name'                   , $Module->actionParameter( 'ExportName' ) );
            $eZXMLExportExport->setAttribute( 'description'            , $Module->actionParameter( 'ExportDescription' ) );
            $eZXMLExportExport->setAttribute( 'sources'                , serialize( $Module->actionParameter( 'ExportSources' ) ) );
            $eZXMLExportExport->setAttribute( 'ftp_target'             , serialize( $FTPInfos ) );
            $eZXMLExportExport->setAttribute( 'slicing_mode'           , $Module->actionParameter( 'ExportSlicingMode' ));
            $eZXMLExportExport->setAttribute( 'start_date'             , $exportStartDate );
            $eZXMLExportExport->setAttribute( 'end_date'               , $exportEndDate );
            $eZXMLExportExport->setAttribute( 'export_schedule'        , serialize( $exportSchedule ) );
            $eZXMLExportExport->setAttribute( 'export_limit'           , $exportLimit );
            $eZXMLExportExport->setAttribute( 'compression'            , $exportCompression );
            $eZXMLExportExport->setAttribute( 'related_object_handling', $relatedObjectHandling );
            $eZXMLExportExport->setAttribute( 'xslt_file'              , $xsltFile );
            $eZXMLExportExport->setAttribute( 'export_hidden_nodes'    , $exportHiddenNodes );
            $eZXMLExportExport->setAttribute( 'export_from_last'       , $exportFromLast );

            $eZXMLExportExport->store();

            $Module->redirectTo( 'xmlexport/view/customer/' . $CustomerID );
            return;
        }
    }
    elseif( isset( $ExportID ) and $ExportID > 0 )
    {
        $eZXMLExportExport = eZXMLExportExports::fetch( $ExportID );

        if( $eZXMLExportExport )
        {
            if ( $http->hasPostVariable( 'BrowseActionName' )
                 and
                 $http->postVariable( 'BrowseActionName' ) == 'BrowseXMLExport'
                 and
                 $http->hasPostVariable( 'SelectedNodeIDArray' ) )
                {
                    if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                    {
                        $selectedNodeIDArray = $http->postVariable( 'SelectedNodeIDArray' );
                        $tpl->setVariable( 'selected_node_id_list', $selectedNodeIDArray  );
                    }
                }
                else
                {
                    $tpl->setVariable( 'selected_node_id_list', unserialize( $eZXMLExportExport->attribute( 'sources' ) ) );
                }

            $FTPInformations  = unserialize( $eZXMLExportExport->attribute( 'ftp_target' ) );

            $FTPHostValue     = ( isset( $FTPInformations['host'] )     ? $FTPInformations['host']     : '' );
            $FTPPortValue     = ( isset( $FTPInformations['port'] )     ? $FTPInformations['port']     : '21' );
            $FTPLoginValue    = ( isset( $FTPInformations['login'] )    ? $FTPInformations['login']    : '' );
            $FTPPasswordValue = ( isset( $FTPInformations['password'] ) ? $FTPInformations['password'] : '' );
            $FTPPathValue     = ( isset( $FTPInformations['path'] )     ? $FTPInformations['path']     : '' );

            $tpl->setVariable( 'ExportCustomerIDValue'           , $eZXMLExportExport->attribute( 'customer_id' ) );
            $tpl->setVariable( 'ExportNameValue'                 , $eZXMLExportExport->attribute( 'name' ) );
            $tpl->setVariable( 'ExportDescriptionValue'          , $eZXMLExportExport->attribute( 'description' ) );
            $tpl->setVariable( 'ExportSlicingModeValue'          , $eZXMLExportExport->attribute( 'slicing_mode' ) );
            $tpl->setVariable( 'ExportRelatedObjectHandlingValue', $eZXMLExportExport->attribute( 'related_object_handling' ) );

            $exportSchedule = unserialize( $eZXMLExportExport->attribute( 'export_schedule' ) );

            // $startDate = ( isset( $exportSchedule['start_date'] ) ? date( 'd/m/Y', $exportSchedule['start_date'] ) : 'dd/mm/YYYY' );
            // $endDate   = ( isset( $exportSchedule['end_date'] )   ? date( 'd/m/Y', $exportSchedule['end_date'] )   : 'dd/mm/YYYY' );

            $startDate = ( $eZXMLExportExport->attribute( 'start_date' ) != '' ) ? date( 'd/m/Y', $eZXMLExportExport->attribute( 'start_date' ) ) : 'dd/mm/YYYY';
            $endDate   = ( $eZXMLExportExport->attribute( 'end_date' )   > 0 )   ? date( 'd/m/Y', $eZXMLExportExport->attribute( 'end_date'   ) ) : 'dd/mm/YYYY';

            $tpl->setVariable( 'ExportStartDateValue', $startDate );
            $tpl->setVariable( 'ExportEndDateValue'  , $endDate );

            if( isset( $exportSchedule['schedule'] ) )
            {
                $recurrence      = $exportSchedule['schedule'];
                $recurrenceValue = $recurrence['value'];
                $recurrenceUnit  = $recurrence['unit'];

                $tpl->setVariable( 'ExportRecurrenceValueValue', $recurrenceValue );
                $tpl->setVariable( 'ExportRecurrenceUnitValue' , $recurrenceUnit );
            }

            $tpl->setVariable( 'ExportObjectNumberLimitValue'       , $eZXMLExportExport->attribute( 'export_limit' ) );
            $tpl->setVariable( 'ExportAllObjectsFromLastExportValue', '' );

            if( $eZXMLExportExport->attribute( 'export_limit' ) == -1 )
            {
                $tpl->setVariable( 'ExportObjectNumberLimitValue'       , '' );
                $tpl->setVariable( 'ExportAllObjectsFromLastExportValue', 'checked="checked"' );
            }

            $tpl->setVariable( 'ExportCompressionValue', '' );
            if( $eZXMLExportExport->attribute( 'compression' ) == '1' )
            {
                $tpl->setVariable( 'ExportCompressionValue', 'checked="checked"' );
            }

            $tpl->setVariable( 'FTPHostValue'    , $FTPHostValue     );
            $tpl->setVariable( 'FTPPortValue'    , $FTPPortValue     );
            $tpl->setVariable( 'FTPLoginValue'   , $FTPLoginValue    );
            $tpl->setVariable( 'FTPPasswordValue', $FTPPasswordValue );
            $tpl->setVariable( 'FTPPathValue'    , $FTPPathValue     );

            $tpl->setVariable( 'ExportXSLTFileValue'   , $eZXMLExportExport->attribute( 'xslt_file' ) );

            $tpl->setVariable( 'ExportHiddenNodesValue', '' );
            if( $eZXMLExportExport->attribute( 'export_hidden_nodes' ) == 1 )
            {
                $tpl->setVariable( 'ExportHiddenNodesValue', 'checked="checked"' );
            }
        }
    }
}

$tpl->setVariable( 'errorMessageList', $errorMessageList );

$Result = array();
$Result ['content'] = $tpl->fetch( 'design:xmlexport/edit/' . $templateName . '.tpl' );

function checkSlicingMode( $actionParameter, $Module, $tpl, &$errorMessageList )
{
    $allowedSlicingModeValues = array( '1', 'n' );

    if( !$Module->hasActionParameter( $actionParameter ) or
        !in_array( $Module->actionParameter( $actionParameter ), $allowedSlicingModeValues ) )
    {
        $errorMessageList[] = 'Please choose a correct slicing mode';
        eZDebug::writeError( 'Please choose a correct slicing mode', 'ezxmlexport/edit :: ' . $actionParameter );
        return false;
    }

    return true;
}

function checkThisDate( $dateString )
{
    $pattern = '#[\d]{1,2}/[\d]{1,2}/[\d]{1,4}#';

    if( preg_match( $pattern, $dateString, $matches ) )
    {
        list( $day, $month, $year ) = explode( '/', $dateString );
        return checkDate( $month, $day, $year );
    }
    return false;
}
?>