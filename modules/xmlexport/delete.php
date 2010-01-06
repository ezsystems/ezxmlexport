<?php
include_once( 'kernel/common/template.php' );

$Module = $Params['Module'];

if( $Module->isCurrentAction( 'DeleteExport' ) )
{
    if( $Module->hasActionParameter( 'DeleteIDArray' ) )
    {
        foreach( $Module->actionParameter( 'DeleteIDArray' ) as $exportID )
        {
            eZXMLExportExports::removeExport( $exportID );
        }
    }

    $http = eZHTTPTool::instance();
    $Module->redirectTo( $http->postVariable( 'RedirectURI' ) );
}

if( $Module->isCurrentAction( 'DeleteCustomer' ) )
{
    if( $Module->hasActionParameter( 'DeleteIDArray' ) )
    {
        foreach( $Module->actionParameter( 'DeleteIDArray' ) as $customerID )
        {
            eZXMLExportCustomers::removeCustomer( $customerID );
        }
    }

    $http = eZHTTPTool::instance();
    $Module->redirectTo( $http->postVariable( 'RedirectURI' ) );
}

$Module->redirectToView( 'menu' );

?>
