<?php
include_once( "kernel/common/template.php" );

$Module = $Params['Module'];

/* Possible values :
 * - customer
 * - export
 */
$Type   = $Params['Type'];

// customerID or exportID
$ID     = $Params['ID'];

if( ( $Type != 'customer' and $Type != 'export' ) and (int)$ID > 0 )
{
    $Module->redirectToView( 'menu' );
    return;
}

$tpl = eZTemplate::factory();

if( $Type == 'customer' )
{
    $customer = eZXMLExportCustomers::fetch( $ID );
    if( !$customer )
    {
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
    }

    $tpl->setVariable( 'customer', $customer );
    $templateName = 'customer';
}

if( $Type == 'export' )
{
    $export = eZXMLExportExports::fetch( $ID );
    if( !$export )
    {
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
    }

    $tpl->setVariable( 'export', $export );
    $templateName = 'export';
}

$Result = array();
$Result ['content'] = $tpl->fetch( 'design:xmlexport/view/' . $templateName . '.tpl' );

?>