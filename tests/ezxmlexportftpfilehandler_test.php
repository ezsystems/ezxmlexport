<?php
/**
 * File containing the eZXMLExportFTPFileHandlerTest class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class eZXMLExportFTPFileHandlerTest extends ezpTestCase
{
    const TEST_FILE_NAME = 'test.txt';

    private $credentials = array( 'host'               => 'localhost',
                                  'port'               => 21,
                                  'login'              => 'jerome',
                                  'password'           => 'publish',
                                  'destination_folder' => 'ezxmlexport');

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZXMLExportFTPFileHandlerTest Unit Tests" );
    }

    protected function setUp()
    {
        parent::setUp();
    }

    public function testCheckFTPTarget()
    {
        $testResult = eZXMLExportFTPFileHandler::checkFTPTarget( $this->credentials['host'],
                                                                 $this->credentials['port'],
                                                                 $this->credentials['login'],
                                                                 $this->credentials['password'],
                                                                 $this->credentials['destination_folder'] );
        $this->assertTrue( $testResult );
    }

    public function testConnect()
    {
        $FTPHandler = eZXMLExportFTPFileHandler::instance();
        $this->assertTrue( $FTPHandler instanceof eZXMLExportFTPFileHandler );
        $this->assertTrue( $FTPHandler->connect( $this->credentials ), 'Is an FTP server running ?' );
    }

    public function testStoreFile()
    {
        $FTPHandler = eZXMLExportFTPFileHandler::instance();
        $FTPHandler->connect( $this->credentials );

        // creating a dummy file
        $filename = tempnam( 'ezxmlexport-tests', 'test-' );

        $this->assertFileExists( $filename );
        $this->assertTrue( is_readable( $filename ) );

        $uploadResult = $FTPHandler->storeFile( $this->credentials['destination_folder'], self::TEST_FILE_NAME, $filename );

        $this->assertTrue( $uploadResult, 'Unable to upload the test file, are the rights correct in tht FTP configuration ?' );

        $this->assertTrue( unlink( $filename ), 'Unable to remove the tempfile' );
    }

    public function testRemoveFile()
    {
        $FTPHandler = eZXMLExportFTPFileHandler::instance();
        $FTPHandler->connect( $this->credentials );

        $removeResult = $FTPHandler->removeFile( $this->credentials['destination_folder'], self::TEST_FILE_NAME );
        $this->assertTrue( $removeResult, 'Unable to remove the test file' );
    }

    public function testClose()
    {
        $FTPHandler = eZXMLExportFTPFileHandler::instance();
        $FTPHandler->connect( $this->credentials );

        $this->assertTrue( $FTPHandler->close(), 'Unable to close the FTP connection' );
    }

}
?>