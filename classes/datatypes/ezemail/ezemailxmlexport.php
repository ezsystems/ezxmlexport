<?php
/**
 * File containing the eZEmailXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- email -->
 *  <xs:complexType name="ezemail">
 *      <xs:simpleContent>
 *          <xs:extension base="xs:string"/>
 *      </xs:simpleContent>
 *  </xs:complexType>
 */

class eZEmailXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- email -->
                <xs:complexType name="ezemail">
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
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="ezemail"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        return $this->contentObjectAttribute->content();
    }
}
?>