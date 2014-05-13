<?php
/**
 * File containing the eZXMLExportCustomersTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZXMLExportCustomersTest extends ezpDatabaseTestCase
{
    const CUSTOMER_ID = 2;

    protected $insertDefaultData = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZXMLExportCustomersTest Unit Tests" );
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
        $result = eZXMLExportCustomers::fetchAll();

        $this->assertTrue( is_array( $result ) );
        $this->assertGreaterThan( 0, count( $result ) );
    }

    public function testFetch()
    {
        $result = eZXMLExportCustomers::fetch( self::CUSTOMER_ID );

        $this->assertTrue( $result instanceof eZXMLExportCustomers );
        $this->assertGreaterThan( 0, count( $result ) );
    }
}

?>