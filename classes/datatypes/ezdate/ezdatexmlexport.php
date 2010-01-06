<?php
/**
 * File containing the eZDateXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *   <!-- date : %Y/%m/%d but stored as a timestamp in the DB -->
 *   <xs:complexType name="ezdate">
 *       <xs:simpleContent>
 *           <xs:extension base="xs:time"/>
 *       </xs:simpleContent>
 *   </xs:complexType>
 */

class eZDateXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- date : %Y/%m/%d but stored as a timestamp in the DB -->
                <xs:complexType name="ezdate">
                    <xs:simpleContent>
                        <xs:extension base="xs:date"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        if( $this->contentClassAttribute->attribute( 'data_int1' ) == 1 )
        {
            $ini        = eZINI::instance( 'datetime.ini' );
            $dateFormat = $ini->variable( 'ClassSettings', 'Formats' );

            $locale = eZLocale::instance();
            $output = $locale->formatDateType( $dateFormat['datexmlschema'] );

            return $output;
        }

        return false;
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="ezdate"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $ini        = eZINI::instance( 'datetime.ini' );
        $dateFormat = $ini->variable( 'ClassSettings', 'Formats' );

        $attributeContents = $this->contentObjectAttribute->content();
        $date              = $attributeContents->Date;

        $locale = eZLocale::instance();
        $output = $locale->formatDateType( $dateFormat['datexmlschema'], $date );

        return $output;
    }
}
?>