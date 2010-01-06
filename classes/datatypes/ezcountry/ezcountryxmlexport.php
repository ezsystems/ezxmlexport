<?php
/**
 * File containing the eZCountryXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- country -->
 *  <xs:complexType name="ezcountry">
 *      <xs:simpleContent>
 *         <xs:extension base="xs:string"/>
 *      </xs:simpleContent>
 *   </xs:complexType>
 */

class eZCountryXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- country -->
                <xs:complexType name="ezcountry">
                    <xs:simpleContent>
                        <xs:extension base="xs:string"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        $defaultCountryList = $this->contentClassAttribute->content();

        foreach( $defaultCountryList['default_countries'] as $defaultCountry )
        {
            return $defaultCountry['Name'];
        }

        return false;
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="ezcountry"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $countryList = $this->contentObjectAttribute->content();

        if(!$countryList['value'])
        {
            return array();
        }

        $selectedCountries = array();

        if( is_array( $countryList['value'] ) )
        {
            foreach( $countryList['value'] as $country )
            {
                $selectedCountries[] = $country;
            }
        }
        else
        {
            $selectedCountries[] = $countryList['value'];
        }

        return $selectedCountries;
    }
}
?>