<?php
/**
 * File containing the eZBooleanXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *
 *  <!-- ezboolean -->
 *  <xs:complexType name="ezboolean">
 *      <xs:simpleContent>
 *          <xs:extension base="xs:boolean"/>
 *      </xs:simpleContent>
 *  </xs:complexType>
 */

class eZBooleanXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- ezboolean -->
                <xs:complexType name="ezboolean">
                    <xs:simpleContent>
                        <xs:extension base="xs:boolean"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        if( $this->contentClassAttribute->attribute( 'data_int3' ) == 0 )
        {
            return 0;
        }

        return 1;
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="ezboolean"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        return $this->contentObjectAttribute->content();
    }
}
?>