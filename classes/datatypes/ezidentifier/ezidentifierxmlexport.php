<?php
/**
 * File containing the eZIdentifierXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type declaration for this datatype
 * No complex type, only a ezstring with a specific pattern
 */

class eZIdentifierXMLExport extends eZXMLExportDatatype
{
    protected function defaultValue()
    {
        return false;
    }

    protected function toXMLSchema()
    {
        $prefix       = $this->contentClassAttribute->attribute( 'data_text1' );
        $suffix       = $this->contentClassAttribute->attribute( 'data_text2' );
        $startValue   = $this->contentClassAttribute->attribute( 'data_int1' );
        /*
        $digitsNumber = $this->contentClassAttribute->attribute( 'data_int2' );

        $digitRange = '{1,' . $digitsNumber . '}';

        if( $digitsNumber == 1 )
        {
            $digitRange = '{1}';
        }
        */

        $digitRange = '+';

        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:restriction base="ezstring">
                            <xs:pattern value="'. $prefix . '[' . $startValue . '-9]' . $digitRange . $suffix . '"/>
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