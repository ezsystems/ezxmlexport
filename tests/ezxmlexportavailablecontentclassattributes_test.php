<?php
/**
 * File containing the eZXMLExportAvailableContentClassAttributesTest class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class eZXMLExportAvailableContentClassAttributesTest extends ezpDatabaseTestCase
{
    const CLASS_ID     = 1;
    const ATTRIBUTE_ID = 4;

    protected $insertDefaultData = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZXMLExportAvailableContentClassAttributes Unit Tests" );
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
        $result = eZXMLExportAvailableContentClassAttributes::fetchAll();

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }

    public function testFetchList()
    {
        $result = eZXMLExportAvailableContentClassAttributes::fetchList();

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }

    public function testIsExportable()
    {
        $result = eZXMLExportAvailableContentClassAttributes::isExportable( self::ATTRIBUTE_ID );

        $this->assertTrue( $result );
    }

    public function testFetchFromClassID()
    {
        $result = eZXMLExportAvailableContentClassAttributes::fetchFromClassID( self::CLASS_ID );

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }
}
?>