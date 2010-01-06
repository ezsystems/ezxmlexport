<?php
/**
 * File containing the eZXMLExportFileHandler abstract class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 * @abstract
 *
 */

abstract class eZXMLExportFileHandler
{
    /**
     * Stores a file
     *
     * @abstract
     * @param string the directory in which to store the file
     * @param string the filename
     * @param string the file's contents
     */
    abstract public function storeFile( $directory, $fileName, $fileContents );

    /**
     * Removes a file
     *
     * @abstract
     * @param string the directory in which the file is stored
     * @param string the filename
     */
    abstract public function removeFile( $directory, $fileName );
}

?>