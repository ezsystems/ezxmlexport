<?php
/**
 * File containing the eZAuthorXMLExport class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 *
 *  <!-- ezauthor -->
 *  <xs:complexType name="ezauthor">
 *       <xs:sequence>
 *           <xs:element name="id" type="xs:integer"/>
 *           <xs:element name="name" type="xs:string"/>
 *           <xs:element name="email" type="xs:string"/>
 *       </xs:sequence>
 *   </xs:complexType>
 */

class eZAuthorXMLExport extends eZXMLExportDatatype
{
    public function definition()
    {
        return '<!-- ezauthor -->
                <xs:complexType name="ezauthor">
                    <xs:sequence>
                        <xs:element name="id" type="xs:integer"/>
                        <xs:element name="name" type="xs:string"/>
                        <xs:element name="email" type="xs:string"/>
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
                        <xs:extension base="ezauthor"/>
                    </xs:complexContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        $authorList = $this->contentObjectAttribute->content();

        $availableAuthors = '';

        foreach( $authorList->attribute( 'author_list' ) as $author )
        {
            $authorXMLString = '<id>' . $author['id'] . '</id>'
                             . '<name>' . $author['name'] .  '</name>'
                             . '<email>' . $author['email'] . '</email>';

            $availableAuthors[] = $authorXMLString;
        }

        return $availableAuthors;
    }
}
?>