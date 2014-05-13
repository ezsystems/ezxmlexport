<?php
/**
 * File containing the eZXMLTextXMLExport class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- text block -->
 *  <xs:complexType name="eztext">
 *     <xs:simpleContent>
 *         <xs:extension base="xs:string"/>
 *     </xs:simpleContent>
 *  </xs:complexType>
 */

class eZXMLTextXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- text block -->
                <xs:complexType name="eztext">
                    <xs:simpleContent>
                        <xs:extension base="xs:string"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        return false;
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="ezstring"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $attributeContents = $this->contentObjectAttribute->content();
        $xmlExportIni = eZINI::instance( 'ezxmlexport.ini' );

        if ( $xmlExportIni->variable( 'ExportSettings', 'UseXHTMLOutput' ) === 'enabled' )
        {
            $output = $attributeContents->attribute( 'output' )->attribute( 'output_text');
        }
        else
        {
            $doc = new DOMDocument('1.0');
            $doc->loadXML( $attributeContents->attribute( 'xml_data' ) );

            $xpath = new DOMXPath($doc);

            $output = $doc->saveXML( $xpath->query( '/*' )->item( 0 ) );
        }

        if ( $xmlExportIni->variable( 'ExportSettings', 'UseCDATA' ) === 'enabled' )
            return "<![CDATA[\n$output]]>\n";

        return "\n$output\n";
    }
}
?>