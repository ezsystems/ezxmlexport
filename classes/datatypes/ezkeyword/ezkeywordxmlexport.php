<?php
/**
 * File containing the eZKeywordXMLExport class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
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