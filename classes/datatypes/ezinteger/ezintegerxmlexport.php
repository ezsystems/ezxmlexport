<?php
/**
 * File containing the eZIntegerXMLExport class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *
 *  <!-- ezinteger -->
 *  <xs:complexType name="ezinteger">
 *     <xs:simpleContent>
 *         <xs:extension base="xs:integer"/>
 *     </xs:simpleContent>
 *  </xs:complexType>
 */

class eZIntegerXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- ezinteger -->
                <xs:complexType name="ezinteger">
                    <xs:simpleContent>
                        <xs:extension base="xs:integer"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        return $this->contentClassAttribute->attribute( 'data_int3' );
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:restriction base="ezinteger">
                            <xs:minInclusive value="' . $this->contentClassAttribute->attribute( 'data_int1' ) . '"/>
                            <xs:maxInclusive value="' . $this->contentClassAttribute->attribute( 'data_int2' ) . '"/>
                        </xs:restriction>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        return $this->contentObjectAttribute->content();
    }
}
?>