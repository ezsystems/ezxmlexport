<?php
/**
 * File containing the eZMediaXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- media -->
 *  <xs:complexType name="ezmedia">
 *      <xs:simpleContent>
 *          <xs:extension base="xs:string"/>
 *      </xs:simpleContent>
 *  </xs:complexType>
 */

class eZMediaXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- media -->
                <xs:complexType name="ezmedia">
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
                        <xs:extension base="ezmedia"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        /*
         * {concat( "content/download/",
         *          $attribute.contentobject_id,
         *          "/",
         *          $attribute.content.contentobject_attribute_id,
         *          "/",
         *          $attribute.content.original_filename)|ezurl}
         */
        $url =  'content/download/'
                . $this->contentObjectAttribute->attribute( 'contentobject_id' )
                . '/'
                . $this->contentObjectAttribute->attribute( 'id' )
                . '/'
                . urlencode( $this->contentObjectAttribute->content()->attribute( 'original_filename' ) );

        eZURI::transformURI( $url, false, 'full' );

        return $url;
    }
}
?>