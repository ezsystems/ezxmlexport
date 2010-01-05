<?php
/**
 * File containing the eZSelectionXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *  <!-- selection -->
 *  <xs:complexType name="ezselection">
 *      <xs:sequence>
 *          <xs:element name="key" type="xs:string"/>
 *          <xs:element name="value" type="xs:string"/>
 *      </xs:sequence>
 *  </xs:complexType>
 */

class eZSelectionXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- selection -->
                <xs:complexType name="ezselection">
                    <xs:sequence>
                        <xs:element name="key" type="xs:string"/>
                        <xs:element name="value" type="xs:string"/>
                    </xs:sequence>
                </xs:complexType>';
    }

    protected function defaultValue()
    {
        return false;
    }

    protected function toXMLSchema()
    {
        $this->noMaxLimit = true;

        return '<xs:complexType>
                    <xs:complexContent>
                        <xs:extension base="ezselection"/>
                    </xs:complexContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        // yes this is the hard way
        // have a look at
        // ezselection.tpl to understand why
        $selectedOptionsList   = $this->contentObjectAttribute->content();
        $availableOptionsArray = $this->contentObjectAttribute->attribute( 'class_content' );
        $availableOptionsList  = $availableOptionsArray['options'];

        $finalAvailableOptions = array();

        foreach( $availableOptionsList as $availableOption )
        {
            if( in_array( $availableOption['id'], $selectedOptionsList ) )
            {
                $finalAvailableOptions[] = '<key>'   . $availableOption['name'] .'</key>'
                                          .'<value>' . $availableOption['id']   . '</value>';
            }
        }

        return $finalAvailableOptions;
    }
}
?>