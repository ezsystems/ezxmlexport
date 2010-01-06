<?php
/**
 * File containing the eZDateTimeXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 * <!-- date and time -->
 * <xs:complexType name="ezdatetime">
 *    <xs:simpleContent>
 *       <xs:extension base="xs:time"/>
 *    </xs:simpleContent>
 * </xs:complexType>
 */

class eZDateTimeXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- date and time -->
                <xs:complexType name="ezdatetime">
                    <xs:simpleContent>
                        <xs:extension base="xs:dateTime"/>
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
                        <xs:extension base="ezdatetime"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $ini        = eZINI::instance( 'datetime.ini' );
        $dateFormat = $ini->variable( 'ClassSettings', 'Formats' );

        $attributeContents = $this->contentObjectAttribute->content();
        $date              = $attributeContents->DateTime;

        $locale = eZLocale::instance();
        $output = $locale->formatDateTimeType( $dateFormat['datetimexmlschema'], $date );

        return $output;
    }
}
?>