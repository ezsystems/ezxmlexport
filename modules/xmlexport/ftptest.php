<?php
$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$postFieldList = array( 'Host', 'Port', 'Login', 'Password', 'Path' );

foreach( $postFieldList as $postField )
{
    if( !$http->hasPostVariable( 'FTP' . $postField ) )
    {
        eZExecution::cleanExit();
        return;
    }
}

$FTPHost     = $http->postVariable( 'FTPHost' );
$FTPPort     = $http->postVariable( 'FTPPort' );
$FTPLogin    = $http->postVariable( 'FTPLogin' );
$FTPPassword = $http->postVariable( 'FTPPassword' );
$FTPPath     = $http->postVariable( 'FTPPath' );

if( eZXMLExportFTPFileHandler::checkFTPTarget( $FTPHost, $FTPPort, $FTPLogin, $FTPPassword, $FTPPath ) )
{
    echo( 'OK' );
}

eZExecution::cleanExit();
?>
