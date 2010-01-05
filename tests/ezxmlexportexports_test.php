<?php
/**
 * File containing the eZXMLExportExportsTest class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class eZXMLExportExportsTest extends ezpDatabaseTestCase
{
    const EXPORT_ID   = 1;
    const CUSTOMER_ID = 2;

    protected $insertDefaultData = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZXMLExportExportsTest Unit Tests" );
    }

    protected function setUp()
    {
        parent::setUp();

        $sqlFiles = array( "extension/ezxmlexport/sql/mysql/schema.sql",
                           "extension/ezxmlexport/tests/testdata.sql" );

        ezpTestDatabaseHelper::insertSqlData( $this->sharedFixture, $sqlFiles );

        $this->db = $this->sharedFixture;
    }

    public function testFetchAll()
    {
        $result = eZXMLExportExports::fetchAll();

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }

    public function testFetchByCustomerID()
    {
        $result = eZXMLExportExports::fetchByCustomerID( self::CUSTOMER_ID );

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }

    public function testFetch()
    {
        $result = eZXMLExportExports::fetch( self::EXPORT_ID );

        $this->assertTrue( $result instanceof eZXMLExportExports );
        $this->assertGreaterThan( 0, count( $result ) );
    }

    public function testFetchAvailableExports()
    {
        return;
        $result = eZXMLExportExports::fetchAvailableExports(  );
    }
}
?>