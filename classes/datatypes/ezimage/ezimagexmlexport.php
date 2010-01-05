<?php
/**
 * File containing the eZImageXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- image -->
 *  <xs:complexType name="ezimage">
 *      <xs:simpleContent>
 *          <xs:extension base="xs:string"/>
 *      </xs:simpleContent>
 *  </xs:complexType>
 */

class eZImageXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- image -->
                <xs:complexType name="ezimage">
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
                        <xs:extension base="ezimage"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $attributeContents = $this->contentObjectAttribute->content();
        $originalImage     = $attributeContents->attribute('original');

        $imageURL = $originalImage['url'];

        if( PHP_SAPI == 'cli' )
        {
            $ini = eZINI::instance( 'site.ini' );
            $siteURL = trim( $ini->variable( 'SiteSettings', 'SiteURL' ) );

            // removes trailing slash
            $lastPos = strlen( $siteURL ) - 1;
            if( $siteURL[ $lastPos ] == '/' )
            {
                $siteURL = substr( $siteURL, 0, -1 );
            }

            $imageURL = 'http://' . $siteURL . '/' . $imageURL;
        }
        else
        {
            // ezroot
            eZURI::transformURI( $imageURL, false, 'full' );
        }

        return $imageURL;
    }
}
?>