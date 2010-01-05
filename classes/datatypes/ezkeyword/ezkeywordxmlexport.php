<?php
/**
 * File containing the eZKeywordXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *
 * <!-- keywords -->
 * <xs:complexType name="ezkeyword">
 *    <xs:simpleContent>
 *        <xs:extension base="xs:string"/>
 *    </xs:simpleContent>
 * </xs:complexType>
 */

class eZKeywordXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
    return '<!-- keywords -->
            <xs:complexType name="ezkeyword">
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
        $this->noMaxLimit = true;

        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="ezstring"/>
                        <!-- This restiction is finally too ... restrictive
                        <xs:restriction base="ezstring">
                            <xs:pattern value="^[\w,]+$"/>
                        </xs:restriction>
                        -->
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $attributeContents = $this->contentObjectAttribute->content();

        return $attributeContents->KeywordArray;
    }
}
?>