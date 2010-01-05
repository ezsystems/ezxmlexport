<?php
/**
 * File containing the eZXMLExportFTPFileHandler class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

class eZXMLExportFTPFileHandler extends eZXMLExportFileHandler
{
    /**
     * The FTP connexion resource
     *
     * @var resource
     */
    private $ConnectionResource = false;

    /**
     * Void constructor
     */
    private function eZXMLExportFTPFileHandler()
    {
    }

    /**
     * Connects to the FTP
     *
     * @params array $loginCredentials The login credentials
     * @return bool true if success, false otherwise
     */
    public function connect( $loginCredentials )
    {
        if( is_resource( $this->ConnectionResource ) )
        {
            eZDebug::writeError( 'No Connexion Resource available', 'eZXMLExportFTPFileHandler::connect' );
            return false;
        }

        $host              = $loginCredentials['host'];
        $port              = $loginCredentials['port'];
        $timeout           = 10;
        $login             = $loginCredentials['login'];
        $password          = $loginCredentials['password'];
        $destinationFolder = $loginCredentials['destination_folder'];

        if( $cr = @ftp_connect( $host, $port, $timeout ) and ftp_login( $cr, $login, $password ) )
        {
            eZDebug::writeNotice( 'Connecting to FTP server', 'eZXMLExportFTPFileHandler' );

            $this->ConnectionResource = $cr;
            $GLOBALS['eZXMLExportFTPFileHandler'] = $this;
            unset( $cr );

            // creating basic stucture if does not exists
            // the directory does not exists
            if( !@ftp_chdir( $this->ConnectionResource, $destinationFolder ) )
            {
                // create it
                if( !$this->mkDir( $destinationFolder ) )
                {
                    eZDebug::writeError( 'Unable to create dir ' . $destinationFolder, 'eZXMLExportFTPFileHandler::eZXMLExportFTPFileHandler' );
                }

                // dir should exists now
                eZDebug::writeNotice( 'CWD : ' . ftp_pwd( $this->ConnectionResource), 'eZXMLExportFTPFileHandler::eZXMLExportFTPFileHandler' );
                ftp_chdir( $this->ConnectionResource, $destinationFolder );
            }


            // make sure the connection is closed at the
            // end of the script
            eZExecution::addCleanupHandler( 'eZXMLExportFTPCloseConnexion' );

            return true;
        }
        else
        {
            eZDebug::writeError( 'Unable to connect to FTP server', 'eZXMLExportFTPFileHandler' );

            return false;
        }
    }

    /**
     * Creates a directory
     *
     * @param string $path The directory path
     * @return bool, true if success, false otherwise
     */
    private function mkDir( $path )
    {
        $dirList = explode( "/", $path );
        $path = "";

        foreach( $dirList as $dir )
        {
            $path .= "/" . $dir;

            if(!@ftp_chdir( $this->ConnectionResource, $path) )
            {
                @ftp_chdir( $this->ConnectionResource, "/" );

                if( !@ftp_mkdir( $this->ConnectionResource, $path ) )
                {
                    return false;
                }

                eZDebug::writeNotice( 'Creating ' . $path, 'eZXMLExportFTPFileHandler::mkDir' );
            }
        }

        // returning to root folder : lots of moves but cleaner
        ftp_chdir( $this->ConnectionResource, "/" );

        return true;
    }

    /**
     * Returns the FTP singleton
     *
     * @return object the eZXMLExportFTPFileHandler instance
     */
    public static function instance()
    {
        if( isset( $GLOBALS['eZXMLExportFTPFileHandler'] ) and is_object( $GLOBALS['eZXMLExportFTPFileHandler'] ) )
        {
            return $GLOBALS['eZXMLExportFTPFileHandler'];
        }
        else
        {
            return new eZXMLExportFTPFileHandler();
        }
    }

    /**
     * Stores a file
     *
     * @param string $targetDirectory The directory in which to store the file
     * @param string $targetFileName  The filename
     * @param string $sourceFile      The file's contents
     * @return bool true if success, false otherwise
     */
    public function storeFile( $targetDirectory, $targetFileName, $sourceFile )
    {
        ftp_chdir( $this->ConnectionResource, '/' );

        if( !@ftp_put( $this->ConnectionResource,
                      $targetDirectory . '/' . $targetFileName,
                      $sourceFile,
                      FTP_BINARY  ) )
        {
            eZDebug::writeError( 'Unable to upload the file', 'eZXMLExportFTPFileHandler::storeFile' );
            return false;
        }

        return true;
    }

    /**
     * Removes a file
     *
     * @param string $directory The directory in which the file is stored
     * @param string $fileName  The filename
     */
    public function removeFile( $directory, $fileName )
    {
        if( !$this->ConnectionResource )
        {
            return false;
        }

        if( !@ftp_delete( $this->ConnectionResource, $fileName ) )
        {
            eZDebug::writeError( 'Unable to delete file', 'eZXMLExportFTPFileHandler::removeFile' );
            return false;
        }

        return true;
    }

    /**
     * Closes the FTP connexion
     *
     * @return bool true if success, false otherwise
     */
    public function close()
    {
        return @ftp_close( $this->ConnectionResource );
    }

    /**
     * Check if the FTP is accessible
     *
     * @param string $FTPHost     The FTP host
     * @param string $FTPPort     The FTP port
     * @param string $FTPLogin    The FTP login
     * @param string $FTPPassword The FTP password
     * @param string $FTPPath     The FTP path
     * @return bool true if success, false otherwise
     */
    public static function checkFTPTarget( $FTPHost, $FTPPort, $FTPLogin, $FTPPassword, $FTPPath )
    {
        if( $FTPPath == '' )
        {
            $FTPPath = '/';
        }

        if( $cr = @ftp_connect( $FTPHost, $FTPPort, 3 )
            and @ftp_login( $cr, $FTPLogin, $FTPPassword ) )
            //and is_array( ftp_nlist( $cr, $FTPPath ) ) )
        {
            return true;
        }

        return false;
    }

}

function eZXMLExportFTPCloseConnexion()
{
    $eZXMLExportFTP = eZXMLExportFTPFileHandler::instance();
    $eZXMLExportFTP->close();
}
?>