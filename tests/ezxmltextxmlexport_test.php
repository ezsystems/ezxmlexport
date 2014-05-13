<?php
/**
 * File containing the eZXMLTextXMLExportTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZXMLTextXMLExportTest extends ezpDatabaseTestCase
{
    protected $class;
    protected $object;
    protected $XMLAttribute;
    protected $XMLTextXMLExport;
    protected $backupGlobals = false;

    public function setUp()
    {
        parent::setUp();

        $this->class = new ezpClass( "toto", "toto", '<title>' );
        $this->class->add( 'Title', 'title', 'ezstring' );
        $this->XMLAttribute = $this->class->add( 'XML', 'xml', 'ezxmltext' );
        $this->class->store();

        $this->object = new ezpObject( "toto", 2 );
        $this->object->title = "toto";
        $this->object->xml = <<<EOT
<section>
  <header>This is a header</header>
  <paragraph>eZ Publish is a &quot;Content Management System&quot; (CMS)</paragraph>
</section>
EOT;

        $this->object->publish();

        $this->XMLTextXMLExport = new eZXMLTextXMLExport( $this->XMLAttribute );
    }

    public function tearDown()
    {
        ezpINIHelper::restoreINISettings();
        $this->object->remove();
        eZContentClassOperations::remove( $this->class->id );

        parent::tearDown();
    }

    public function testXHTMLOutput()
    {
        ezpINIHelper::setINISetting( 'ezxmlexport.ini', 'ExportSettings', 'UseXHTMLOutput', 'enabled' );
        ezpINIHelper::setINISetting( 'ezxmlexport.ini', 'ExportSettings', 'UseCDATA', 'enabled' );
        $this->assertRegExp(
            '@<xml>\s*<!\[CDATA\[\s*<a [^>]+></a><h\d>This is a header</h\d><p>eZ Publish is a &quot;Content Management System&quot; \(CMS\)</p>\]\]>\s*</xml>@',
            $this->XMLTextXMLExport->xmlize( $this->object->dataMap["xml"] )
        );
    }

    public function testXMLOutput()
    {
        ezpINIHelper::setINISetting( 'ezxmlexport.ini', 'ExportSettings', 'UseXHTMLOutput', 'disabled' );
        ezpINIHelper::setINISetting( 'ezxmlexport.ini', 'ExportSettings', 'UseCDATA', 'enabled' );
        $this->assertRegExp(
            '@<xml>\s*<!\[CDATA\[\s*<section[^>]*><section><header>This is a header</header><paragraph>eZ Publish is a "Content Management System" \(CMS\)</paragraph></section></section>\s*\]\]>\s*</xml>@',
            $this->XMLTextXMLExport->xmlize( $this->object->dataMap["xml"] )
        );
    }

    public function testXHTMLNoCDATAOutput()
    {
        ezpINIHelper::setINISetting( 'ezxmlexport.ini', 'ExportSettings', 'UseXHTMLOutput', 'enabled' );
        ezpINIHelper::setINISetting( 'ezxmlexport.ini', 'ExportSettings', 'UseCDATA', 'disabled' );
        $this->assertRegExp(
            '@<xml>\s*<a [^>]+></a><h\d>This is a header</h\d><p>eZ Publish is a &quot;Content Management System&quot; \(CMS\)</p>\s*</xml>@',
            $this->XMLTextXMLExport->xmlize( $this->object->dataMap["xml"] )
        );
    }

    public function testXMLNoCDATAOutput()
    {
        ezpINIHelper::setINISetting( 'ezxmlexport.ini', 'ExportSettings', 'UseXHTMLOutput', 'disabled' );
        ezpINIHelper::setINISetting( 'ezxmlexport.ini', 'ExportSettings', 'UseCDATA', 'disabled' );
        $this->assertRegExp(
            '@<xml>\s*<section[^>]*><section><header>This is a header</header><paragraph>eZ Publish is a "Content Management System" \(CMS\)</paragraph></section></section>\s*</xml>@',
            $this->XMLTextXMLExport->xmlize( $this->object->dataMap["xml"] )
        );
    }
}
?>
