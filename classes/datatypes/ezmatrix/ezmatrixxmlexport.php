<?php
/**
 * File containing the eZMatrixXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- matrix -->
 *  <xs:complexType name="ezmatrix">
 *      <xs:sequence>
 *          <xs:element name="key" type="xs:string"/>
 *          <xs:element name="value" type="xs:string"/>
 *      </xs:sequence>
 *  </xs:complexType>
 */

class eZMatrixXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
    return '<!-- matrix -->
            <xs:complexType name="ezmatrix">
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
                        <xs:extension base="ezmatrix"/>
                    </xs:complexContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $attributeContents = $this->contentObjectAttribute->content();
        $cellList          = $attributeContents->attribute( 'cells' );

        $availableCells = array();

        for( $i = 0; $i < count( $cellList ); $i++ )
        {
            $keyValuePair = '<key>'    . $cellList[$i] . '</key>'
                            .'<value>' . $cellList[++$i]. '</value>';

            $availableCells[] = $keyValuePair;
        }

        return $availableCells;
    }
}
?>