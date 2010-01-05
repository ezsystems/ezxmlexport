<?php
/**
 * File containing the eZXMLExportTestSuite class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class eZXMLExportTestSuite extends ezpTestSuite
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
    }

    public static function suite()
    {
        return new self();
    }
}

?>