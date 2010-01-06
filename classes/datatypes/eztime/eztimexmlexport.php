<?php
/**
 * File containing the eZTimeXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 * <!-- time -->
 * <xs:complexType name="eztime">
 *     <xs:simpleContent>
 *         <xs:extension base="xs:time"/>
 *     </xs:simpleContent>
 * </xs:complexType>
 */

class eZTimeXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- time -->
                <xs:complexType name="eztime">
                    <xs:simpleContent>
                        <xs:extension base="xs:time"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        if( $this->contentClassAttribute->attribute( 'data_int1' ) == 1 )
        {
            return date( 'H:i:s', time());
        }

        return false;
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="eztime"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $ini        = eZINI::instance( 'datetime.ini' );
        $dateFormat = $ini->variable( 'ClassSettings', 'Formats' );

        $attributeContents = $this->contentObjectAttribute->content();

        if( $attributeContents instanceof eZTime )
        {
            $date              = $attributeContents->Time;

            $locale = eZLocale::instance();
            return  $locale->formatTimeType( $dateFormat['timexmlschema'], $date );
        }

        // this line should never be called !
        return false;
    }
}
?>