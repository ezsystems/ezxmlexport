<?php
/**
 * File containing the eZBinaryFileXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *
 * <!-- binaryfile -->
 * <xs:complexType name="ezbinaryfile">
 *     <xs:simpleContent>
 *         <xs:extension base="xs:string"/>
 *     </xs:simpleContent>
 * </xs:complexType>
 *
 */

class eZBinaryFileXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- binaryfile -->
                <xs:complexType name="ezbinaryfile">
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
                        <xs:extension base="ezbinaryfile"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        /* URL for download
        $url =  'content/download/'
                . $contentObjectAttribute->attribute( 'contentobject_id' )
                . '/'
                . $contentObjectAttribute->attribute( 'id' )
                . '/version/'
                . $contentObjectAttribute->attribute( 'version' )
                . '/file/'
                . urlencode( $contentObjectAttribute->attribute( 'original_filename' ) );
         */

        if( !$this->contentObjectAttribute->hasContent() )
        {
            return false;
        }

        // direct access URL
        $url = $this->contentObjectAttribute->content()->filePath();

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

            $url = 'http://' . $siteURL . '/' . $url;
        }
        else
        {
            // warning : reference usage here see the prototype
            // transformURI( &$href, $ignoreIndexDir = false, $serverURL = 'relative' )
            // if ever you want to display the "content/download" URL change the second
            // parameter to 'true' instead of false
            eZURI::transformURI( $url, false, 'full' );
        }

        return $url;
    }
}
?>