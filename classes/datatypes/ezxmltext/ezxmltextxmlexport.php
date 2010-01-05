<?php
/**
 * File containing the eZXMLTextXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
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

        return "<![CDATA[\n"
             . $attributeContents->attribute( 'output' )->attribute( 'output_text')
             . "]]>\n";
    }
}
?>