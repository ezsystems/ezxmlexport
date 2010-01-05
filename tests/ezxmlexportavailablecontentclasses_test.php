<?php
/**
 * File containing the eZXMLExportAvailableContentClassesTest class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class eZXMLExportAvailableContentClassesTest extends ezpDatabaseTestCase
{
    const CLASS_ID = 1;

    protected $insertDefaultData = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZXMLExportAvailableContentClasses Unit Tests" );
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
        $result = eZXMLExportAvailableContentClasses::fetchAll( self::CLASS_ID );

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }

    public function testFetchExportableClasses()
    {
        $result = eZXMLExportAvailableContentClasses::fetchExportableClasses();

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }

    public function fetchList()
    {
        $result = eZXMLExportAvailableContentClasses::fetchList();

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }
}
?>
