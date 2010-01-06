<?php
/**
 * File containing the eZISBNXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 * No ComplexType for this datatype
 * only an ezstring with a specific
 * pattern
 */

class eZISBNXMLExport extends eZXMLExportDatatype
{
    protected function defaultValue()
    {
        return false;
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="ezstring"/>

                        <!--
                        <xs:restriction base="ezstring">
                            <xs:pattern value="^[0-9]{1,2}\-[0-9]+\-[0-9]+\-[0-9X]{1}$"/>
                        </xs:restriction>
                        -->
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $attributeContents = $this->contentObjectAttribute->content();

        return $attributeContents['value'];
    }
}
?>