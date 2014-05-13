<?php
/**
 * File containing the eZTextXMLExport class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ezxmlexport
 *
 */

/*
 * Complex type for this datatype
 */

class eZTextXMLExport extends eZXMLExportDatatype
{
    protected function defaultValue()
    {
        return false;
    }

    protected function toXMLSchema()
    {
        return '<xs:complexType>
                    <xs:simpleContent>
                        <xs:extension base="ezstring"/>
                    </xs:simpleContent>
                </xs:complexType>';
    }

    protected function toXML()
    {
        return htmlspecialchars( $this->contentObjectAttribute->content() );
    }
}
?>
