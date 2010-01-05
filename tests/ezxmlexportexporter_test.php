<?php
/**
 * File containing the eZXMLExportExporterTest class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class eZXMLExportExporterTest extends ezpDatabaseTestCase
{
    const EXPORT_ID         = 1;
    const IS_VERBOSE        = false;
    const WRITE_LOG         = true;
    const WRITE_PROCESS_LOG = true;

    protected $insertDefaultData = true;
    private $eZXMLExporter;
    private $startTime;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZXMLExportExporterTest Unit Tests" );
    }

    protected function setUp()
    {
        parent::setUp();

        $sqlFiles = array( "extension/ezxmlexport/sql/mysql/schema.sql",
                           "extension/ezxmlexport/tests/testdata.sql" );

        ezpTestDatabaseHelper::insertSqlData( $this->sharedFixture, $sqlFiles );

        $this->db = $this->sharedFixture;

        $eZXMLExporter = new eZXMLExportExporter( eZXMLExportExporterTest::EXPORT_ID,
                                                  eZXMLExportExporterTest::IS_VERBOSE,
                                                  eZXMLExportExporterTest::WRITE_LOG,
                                                  eZXMLExportExporterTest::WRITE_PROCESS_LOG );
        $this->eZXMLExporter = $eZXMLExporter;
        $this->startTime = time();
    }

    protected function tearDown()
    {
        $eZXMLExporter = new eZXMLExportExporter( eZXMLExportExporterTest::EXPORT_ID,
                                                  eZXMLExportExporterTest::IS_VERBOSE,
                                                  eZXMLExportExporterTest::WRITE_LOG,
                                                  eZXMLExportExporterTest::WRITE_PROCESS_LOG );

        unlink( eZXMLExportExporter::LOG_FILE_DIRECTORY    . $eZXMLExporter->CleanExportName . '.log' );
        @unlink( eZXMLExportExporter::EXPORT_FILE_DIRECTORY . $eZXMLExporter->CleanExportName . '/' . $eZXMLExporter->CleanExportName . '.xml' );
        @unlink( eZXMLExportExporter::EXPORT_FILE_DIRECTORY . $eZXMLExporter->CleanExportName . '/' . $eZXMLExporter->CleanExportName . '.tar.gz' );
        @rmdir(  eZXMLExportExporter::EXPORT_FILE_DIRECTORY . $eZXMLExporter->CleanExportName );
    }

    public function testeZXMLExportExporter()
    {
        $eZXMLExport = eZXMLExportExports::fetch( eZXMLExportExporterTest::EXPORT_ID );
        $this->assertTrue( $eZXMLExport instanceof eZXMLExportExports, '$eZXMLExport should be of type eZXMLExportExports' );

        $eZXMLExporter = new eZXMLExportExporter( eZXMLExportExporterTest::EXPORT_ID,
                                                  eZXMLExportExporterTest::IS_VERBOSE,
                                                  eZXMLExportExporterTest::WRITE_LOG,
                                                  eZXMLExportExporterTest::WRITE_PROCESS_LOG );

        $this->assertTrue( $eZXMLExporter instanceof eZXMLExportExporter, '$eZXMLExporter should be of type eZXMLExportExporter' );
        $this->assertTrue( $eZXMLExporter->eZXMLExportProcessLog instanceof eZXMLExportProcessLog, 'it is not of type eZXMLExportProcessLog' );
        $this->assertTrue( is_array( $eZXMLExporter->AlreadyExportedOjectIDList ), '$AlreadyExportedOjectIDList should be an array' );
        $this->assertTrue( is_array( $eZXMLExporter->ExportableContentClasses ), '$ExportableContentClasses should be an array' );

        $this->assertEquals( 'test_xml_export', $eZXMLExporter->CleanExportName, '$CleanExportName is incorrect' );
        $this->assertEquals( -1     , $eZXMLExporter->ExportLimit, '$ExportLimit should be -1' );
    }

    public function testExportStart()
    {
        $this->eZXMLExporter->exportStart();

        $db = eZDB::instance();
        $sql= 'SELECT * FROM ezxmlexport_process_logs WHERE export_id = ' . self::EXPORT_ID;
        $rows = $db->arrayQuery( $sql );
        $this->assertGreaterThan( 0, count( $rows ) );

        $row = $rows[0];

        $this->assertEquals( $row['export_id'] , eZXMLExportExporterTest::EXPORT_ID );
        $this->assertEquals( $row['start_date'], $this->startTime );
        $this->assertEquals( $row['status']    , eZXMLExportProcessLog::STATUS_XML_GENERATION_STARTED );

        // XML log must be created
        $this->assertFileExists( eZXMLExportExporter::EXPORT_FILE_DIRECTORY . $this->eZXMLExporter->CleanExportName . '/' . $this->eZXMLExporter->CleanExportName . '.xml' );
    }

    public function testFetchNodeTotal()
    {
        $total = $this->eZXMLExporter->fetchNodeTotal();

        $this->assertTrue( is_numeric( $total ) );
        $this->assertGreaterThan( 0, count( $total ) );
    }

    public function testFetchExportableNodes( $offset = 0, $limit = 0 )
    {
        $result = $this->eZXMLExporter->fetchExportableNodes( 0, 100 );

        $this->assertTrue( is_array( $result ) );
    }
}
?>