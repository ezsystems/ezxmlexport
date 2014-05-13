<?php
/**
 * File containing the eZXMLExportHelpers class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
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