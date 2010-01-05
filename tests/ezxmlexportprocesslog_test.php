<?php
/**
 * File containing the eZXMLExportProcessLogTest class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class eZXMLExportProcessLogTest extends ezpDatabaseTestCase
{
    const EXPORT_ID   = 1;

    protected $insertDefaultData = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZXMLExportProcessLogTest Unit Tests" );
    }

    protected function setUp()
    {
        parent::setUp();

        $sqlFiles = array( "extension/ezxmlexport/sql/mysql/schema.sql",
                           "extension/ezxmlexport/tests/testdata.sql" );

        ezpTestDatabaseHelper::insertSqlData( $this->sharedFixture, $sqlFiles );

        $this->db = $this->sharedFixture;

        $processLog = new eZXMLExportProcessLog();
        $processLog->setAttribute( 'export_id', self::EXPORT_ID );
        $processLog->setAttribute( 'start_date', time()  );
        $processLog->setAttribute( 'end_date', time() );
        $processLog->setAttribute( 'start_transfert_date', time(  )  );
        $processLog->setAttribute( 'end_transfert_date', time(  )  );
        $processLog->setAttribute( 'status', 32  );
        $processLog->store();

    }

    public function testFetchByExportID()
    {
        $result = eZXMLExportProcessLog::fetchByExportID( self::EXPORT_ID );

        $this->assertTrue( $result instanceof eZXMLExportProcessLog );
    }
}
?>