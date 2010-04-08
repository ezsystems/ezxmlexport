<?php
abstract class eZXMLExportDatatype
{
    protected $contentClassAttribute  = null;
    protected $contentObjectAttribute = null;
    protected $noMaxLimit             = null;

    public function eZXMLExportDatatype( $exportableAttribute )
    {
        $this->noMaxLimit = false;

        if( !$exportableAttribute instanceof eZContentClassAttribute )
        {
            $this->contentClassAttribute = eZContentClassAttribute::fetch( $exportableAttribute['id'] );
        }
        else
        {
            $this->contentClassAttribute = $exportableAttribute;
        }
    }

    public function schematize()
    {
        $minOccurs = 0;

        if( $this->contentClassAttribute->attribute( 'is_required' ) )
        {
            $minOccurs = 1;
        }

        // child method
        $defaultValue = $this->defaultValue();

        if( $defaultValue !== false )
        {
            $defaultValue = ' default="' . $defaultValue . '"';
        }

        $datatypeSchemaString = $this->toXMLSchema();

        $maxOccurString = '';

        if( $this->noMaxLimit == true )
        {
            $maxOccurString = ' maxOccurs="unbounded"';
        }

        return '<xs:element name="' . $this->contentClassAttribute->attribute( 'identifier' )
                .'" minOccurs="' . $minOccurs. '"'
                . $maxOccurString
                . $defaultValue . ">\n"
                . $datatypeSchemaString . "\n"
                . '</xs:element>';
    }

    public function xmlize( eZContentObjectAttribute $contentObjectAttribute )
    {
        $this->contentObjectAttribute = $contentObjectAttribute;

        $tagName = $this->contentClassAttribute->attribute( 'identifier' );

        $XMLString = '';

        $datatypeXML = $this->toXML();

        if( is_array( $datatypeXML ) )
        {
            foreach( $datatypeXML as $datatypeXMLString )
            {
                $XMLString .= '<' . $tagName . '>' . $datatypeXMLString . '</' . $tagName . '>';
            }
        }
        else
        {
            $XMLString = '<' . $tagName . '>' . $datatypeXML . '</' . $tagName . '>';
        }

        return $XMLString;
    }

    protected abstract function defaultValue();

    protected abstract function toXMLSchema();

    protected abstract function toXML();

    public function definition()
    {
        return '';
    }
}
?>
