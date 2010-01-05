<?php
/**
 * File containing the eZStringXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- text line -->
 *  <xs:complexType name="ezstring">
 *     <xs:simpleContent>
 *       <xs:extension base="xs:string"/>
 *     </xs:simpleContent>
 *  </xs:complexType>
 */

class eZStringXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- text line -->
                <xs:complexType name="ezstring">
                    <xs:simpleContent>
                        <xs:extension base="xs:string"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        return htmlspecialchars( $this->contentClassAttribute->attribute( 'data_text1' ) );
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
        return $this->contentObjectAttribute->content();
    }
}
?>