<?php
/**
 * File containing the eZXMLExportTestSuite class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZXMLExportTestSuite extends ezpDatabaseTestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZXMLExport Test Suite" );

        $this->addTestSuite( 'eZXMLExportExporterTest' );
        $this->addTestSuite( 'eZXMLExportFTPFileHandlerTest' );
        $this->addTestSuite( 'eZXMLExportExportsTest' );
        $this->addTestSuite( 'eZXMLExportProcessLogTest' );
        $this->addTestSuite( 'eZXMLExportAvailableContentClassesTest' );
        $this->addTestSuite( 'eZXMLExportAvailableContentClassAttributesTest' );
        $this->addTestSuite( 'eZXMLExportCustomersTest' );
        $this->addTestSuite( 'eZXMLTextXMLExportTest' );
    }

    public static function suite()
    {
        return new self();
    }
}

?>
