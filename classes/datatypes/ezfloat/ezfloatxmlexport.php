<?php
/**
 * File containing the eZFloatXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- float -->
 *  <xs:complexType name="ezfloat">
 *      <xs:simpleContent>
 *          <xs:extension base="xs:float"/>
 *      </xs:simpleContent>
 *  </xs:complexType>
 */

class eZFloatXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- float -->
                <xs:complexType name="ezfloat">
                    <xs:simpleContent>
                        <xs:extension base="xs:float"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }
    protected function defaultValue()
    {
        return $this->contentClassAttribute->attribute( 'data_float3' );
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:restriction base="ezfloat">
                            <xs:minInclusive value="' . $this->contentClassAttribute->attribute( 'data_float1' ). '"/>
                            <xs:maxInclusive value="' . $this->contentClassAttribute->attribute( 'data_float2' ). '"/>
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