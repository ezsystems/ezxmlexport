<?php
/**
 * File containing the eZEnumXMLExport class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- enum -->
 *  <xs:complexType name="ezenum">
 *      <xs:sequence>
 *          <xs:element name="key" type="xs:string"/>
 *          <xs:element name="value" type="xs:string"/>
 *      </xs:sequence>
 *  </xs:complexType>
 */

class eZEnumXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- enum -->
                <xs:complexType name="ezenum">
                    <xs:sequence>
                        <xs:element name="key" type="xs:string"/>
                        <xs:element name="value" type="xs:string"/>
                    </xs:sequence>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        return false;
    }

    protected function toXMLSchema()
    {
        $this->noMaxLimit = true;

        return '<xs:complexType>
                    <xs:complexContent>
                        <xs:extension base="ezenum"/>
                    </xs:complexContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $attributeContents = $this->contentObjectAttribute->content();
        $enumerationList = $attributeContents->Enumerations;

        $availableEnumerations = array();

        foreach( $enumerationList  as $enumeration )
        {
            $availableEnumeration = '<key>'   . $enumeration->EnumElement . '</key>'
                                   .'<value>' . $enumeration->EnumValue   . '</value>';

            $availableEnumerations[] = $availableEnumeration;
        }

        return $availableEnumerations;
    }
}
?>