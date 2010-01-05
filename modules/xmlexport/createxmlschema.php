<?php
$availableClassList = eZXMLExportAvailableContentClasses::fetchExportableClasses();

$classDefinitionList = array();

foreach( $availableClassList as $availableClass )
{
    foreach( $availableClass['attribute_id_list'] as $attributeID )
    {
        $contentClassAttribute = eZContentClassAttribute::fetch( $attributeID );

        $flags = array( 'is_required'           => $contentClassAttribute->attribute( 'is_required' ),
                        'is_searchable'         => $contentClassAttribute->attribute( 'is_searchable' ),
                        'is_translatable'       => $contentClassAttribute->attribute( 'can_translate' ),
                        'information_collector' => $contentClassAttribute->attribute( 'is_information_collector' ) );

        $attributeInformations = array( 'id'         => $attributeID,
                                        'datatype'   => $contentClassAttribute->attribute( 'data_type_string' ),
                                        'identifier' => $contentClassAttribute->attribute( 'identifier' ),
                                        'flags'      => $flags);

        $contentClass = eZContentClass::fetch( $availableClass['contentclass_id'] );

        $classDefinitionList[ $availableClass['contentclass_id'] ]['attributes'][]        = $attributeInformations;
        $classDefinitionList[ $availableClass['contentclass_id'] ]['class']['identifier'] = $contentClass->attribute( 'identifier' );
        $classDefinitionList[ $availableClass['contentclass_id'] ]['class']['name']       = $contentClass->attribute( 'name' );
    }
}

// generating content class list summary
$rootElementStart = <<<EOT
    <xs:element name="ezpublish">
        <xs:complexType>
            <xs:sequence>
                <xs:element ref="AdministrativeMetadata"/>
                <xs:element name="objects">
                    <xs:complexType>
                        <xs:sequence>

EOT;

$rootElementItems = '';
foreach( $classDefinitionList as $classID => $classDefinition )
{
    //$contentClass = eZContentClass::fetch( $classID );
    $rootElementItems .= '                          ';
    $rootElementItems .= '<xs:element ref="' . $classDefinition['class']['identifier'] . '" minOccurs="0" maxOccurs="unbounded"/>' . chr( 10 );
}

$rootElementEnd = <<<EOT
                        </xs:sequence>
                    </xs:complexType>
                </xs:element>
            </xs:sequence>
        </xs:complexType>
    </xs:element>

EOT;

// adding administrative meta data
$administrativeMetaData = <<<EOT
    <xs:element name="object_relation" type="xs:integer">
    </xs:element>
    <xs:element name="ezlocation">
        <xs:complexType>
            <xs:attribute name="ID" use="required"/>
            <xs:attribute name="name" use="required"/>
            <xs:attribute name="is_main_node" use="required"/>
        </xs:complexType>
    </xs:element>
    <xs:element name="AdministrativeMetadata">
        <xs:complexType>
            <xs:all>
                <xs:element name="name" type="xs:string"/>
                <xs:element name="export_date" type="xs:dateTime"/>
                <xs:element name="country" type="xs:string"/>
            </xs:all>
        </xs:complexType>
    </xs:element>

EOT;

// adding attribute flags
$attributeFlags = <<<EOT
    <xs:attributeGroup name="flags">
        <xs:attribute name="is_searchable" type="xs:boolean"/>
        <xs:attribute name="is_information_collector" type="xs:boolean"/>
        <xs:attribute name="can_translate" type="xs:boolean"/>
    </xs:attributeGroup>

EOT;

// now comes the real job, adding description for any content class
$contentClassDescription = '';

foreach( $classDefinitionList as $classID => $classDefinition )
{
    $contentClassDescription .= "   <xs:element name=\"" . $classDefinition['class']['identifier'] . "\" id=\"" . $classDefinition['class']['identifier'] . "\">\n";
    $contentClassDescription .= "       <xs:complexType>\n";
    $contentClassDescription .= "           <xs:sequence>\n";
    $contentClassDescription .= "              <xs:element name=\"object_metadata\" type=\"ezobjectmetadata\"/>\n";

    // looping over attributes
    foreach( $classDefinition['attributes'] as $attribute )
    {
        if( !isset( $attribute['datatype'] ) )
        {
            continue;
        }

        $className = $attribute['datatype'] . 'xmlexport';

        $fileToInclude = 'extension/ezxmlexport/classes/datatypes/'
                         . $attribute['datatype']
                         . '/'
                         . $className . '.php';

        if( !file_exists( $fileToInclude ) )
        {
            continue;
        }

        include_once( $fileToInclude );
        $xmlSchemaDatatype = new $className( $attribute );
        $XMLString = $xmlSchemaDatatype->schematize();

        $contentClassDescription .= $XMLString . "\n";
        //$contentClassDescription .= "           </xs:element>\n";
    }

    $contentClassDescription .= "       </xs:sequence>\n";
    $contentClassDescription .= "       <xs:attribute name='ID' type='xs:ID' use='required'/>\n";
    $contentClassDescription .= "       <xs:attributeGroup ref='objectinfo'/>\n";
    $contentClassDescription .= "   </xs:complexType>\n";
    $contentClassDescription .= "</xs:element>\n";
}

$attributeGroup = <<<EOT
    <xs:attributeGroup name="objectinfo">
        <xs:attribute name="contentobject_id" type="xs:integer" use="required"/>
        <xs:attribute name="creation_date" type="xs:dateTime" use="required"/>
        <xs:attribute name="modification_date" type="xs:dateTime" use="required"/>
        <xs:attribute name="publication_date" type="xs:dateTime" use="required"/>
        <xs:attribute name="lang" type="xs:string" use="optional"/>
        <xs:attribute name="version" type="xs:integer" use="required"/>
        <xs:attribute name="creator_id" type="xs:integer" use="required"/>
        <xs:attribute name="remote_id" use="required">
            <xs:simpleType>
                <xs:restriction base="xs:string">
                    <xs:pattern value="[a-z0-9]{32}"/>
                </xs:restriction>
            </xs:simpleType>
        </xs:attribute>
        <xs:attribute name="related_object_id" type="xs:integer"/>
        <xs:attribute name="related_object_remote_id">
            <xs:simpleType>
                <xs:restriction base="xs:string">
                    <xs:pattern value="[a-z0-9]{32}"/>
                </xs:restriction>
            </xs:simpleType>
        </xs:attribute>
    </xs:attributeGroup>
EOT;

// adding custom types definition
$dataTypes = '';
$alreadyExportedDatatypes = array();
$datatypeList = eZDir::findSubdirs( 'extension/ezxmlexport/classes/datatypes/' );

foreach( $datatypeList as $datatype )
{
    $className = $datatype . 'xmlexport';

    $fileToInclude = 'extension/ezxmlexport/classes/datatypes/'
                        . $datatype
                        . '/'
                        . $className . '.php';

    if( !file_exists( $fileToInclude ) )
    {
        continue;
    }

    include_once( $fileToInclude );
    $xmlSchemaDatatype = new $className( $attribute );
    $XMLString = $xmlSchemaDatatype->definition();

    $dataTypes .= $XMLString . "\n";
}

// adding custom datatypes
$dataTypes .= '<xs:complexType name="section">
               <xs:simpleContent>
                   <xs:extension base="xs:string">
                       <xs:attribute name="ID" type="xs:integer"/>
                   </xs:extension>
               </xs:simpleContent>
               </xs:complexType>
               <xs:complexType name="ezobjectmetadata">
               <xs:sequence>
                   <xs:element name="section" type="section"/>
                   <xs:element name="draft_count"/>
                   <xs:element name="translation_count"/>
                   <xs:element name="locations">
                      <xs:complexType>
                           <xs:sequence>
                               <xs:element ref="ezlocation" maxOccurs="unbounded"/>
                           </xs:sequence>
                       </xs:complexType>
                   </xs:element>
                   <xs:element name="object_relation_list" minOccurs="0">
                      <xs:complexType>
                            <xs:sequence>
                                <xs:element ref="object_relation" maxOccurs="unbounded"/>
                            </xs:sequence>
                      </xs:complexType>
                   </xs:element>
               </xs:sequence>
               </xs:complexType>';

$headers = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<xs:schema xmlns:xs=\"http://www.w3.org/2001/XMLSchema\">\n";

$footer = '</xs:schema>';

ob_start();

echo( $headers
    . $rootElementStart
    . $rootElementItems
    . $rootElementEnd
    . $administrativeMetaData
    . $contentClassDescription
    . $attributeGroup
    . $dataTypes
    . $attributeFlags
    . $footer );


// creating a copy of this file in a specific directory
$directory = 'extension/ezxmlexport/exports/xsd';
$filename  = 'contentclassdefinition.xsd';

if( file_exists( $directory ) and is_writable( $directory ) )
{
    $fileContents = ob_get_contents();

    if( extension_loaded( 'dom' ) )
    {
        $DOMDocument = new DOMDocument();
        $DOMDocument->preserveWhiteSpace = false;
        $DOMDocument->formatOutput       = true;

        $DOMDocument->loadXML( $fileContents );
        $DOMDocument->save( $directory . '/' . $filename );
    }
    else
    {
        eZFile::create( $filename, $directory, $fileContents );
    }
}
else
{
    eZDebug::writeError( 'Unable to store a copy of the XSD file', 'xmlexport/createxmlschema' );
}


ob_end_flush();

header( 'Cache-Control: no-cache, must-revalidate' );
header( 'Content-Length: ' . ob_get_length() );
header( 'Content-Type: text/xml' );
header( 'Content-disposition: attachment; filename=' . $filename );
header( 'Content-Transfer-Encoding: binary' );
header( 'Accept-Ranges: bytes' );

eZExecution::cleanExit();
?>