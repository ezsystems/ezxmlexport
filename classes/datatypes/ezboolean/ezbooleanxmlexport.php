<?php
/**
 * File containing the eZBooleanXMLExport class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
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