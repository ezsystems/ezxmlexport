<?php
/**
 * File containing the eZXMLExportHelpers class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

class eZXMLExportHelpers
{
    const XSLT_STORAGE_DIR = 'extension/ezxmlexport/design/standard/xsl/';

    /**
     * Fetches the list of XSLT files
     *
     * @return array the list of XSLT files
     */
    public static function fetchXSLTFiles()
    {
        $XSLTFileList = array();
        $XSLTFileList = eZDir::findSubitems( eZXMLExportHelpers::XSLT_STORAGE_DIR );

        return $XSLTFileList;
    }
}
?>