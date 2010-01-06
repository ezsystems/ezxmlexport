<?php
/**
 * File containing the eZObjectRelationListXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- object relations -->
 *  <xs:complexType name="ezobjectrelationlist">
 *     <xs:sequence>
 *         <xs:element name="object_id" type="xs:integer" maxOccurs="unbounded"/>
 *     </xs:sequence>
 *  </xs:complexType>
 */

class eZObjectRelationListXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- object relations -->
                <xs:complexType name="ezobjectrelationlist">
                    <xs:sequence>
                        <xs:element name="object_id" type="xs:integer" maxOccurs="unbounded"/>
                    </xs:sequence>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        return false;
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:complexContent>
                        <xs:extension base="ezobjectrelationlist"/>
                    </xs:complexContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $relatedObjectList = $this->contentObjectAttribute->content();

        $availableObjectIDList = array();

        foreach( $relatedObjectList['relation_list'] as $relatedObject )
        {
            $availableObjectIDList[] = '<object_id>'
                                      . $relatedObject['contentobject_id']
                                      . '</object_id>';
        }

        return $availableObjectIDList;
    }
}
?>