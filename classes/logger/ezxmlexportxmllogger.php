<?php
/**
 * File containing the eZXMLExportLogger class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

class eZXMLExportLogger
{
    /**
     * Holds the start date of the export
     * @var int
     */
    private $startDate;

    /**
     * Holds the end date of the export
     * @var int
     */
    private $endDate;

    /**
     * Holds the name of the XSLT file applied
     * @var string
     */
    public  $appliedXSLT;

    /**
     * Holds the list of exported content objects
     * @var array
     */
    private $exportedObjects;

    /**
     * XML contents of the log file
     * @var array
     */
    private $XMLContents;

    /**
     * Encoding used in the XML log file
     * @var string
     */
    private $encoding;

    /**
     * The directory in which the log files will be stored
     * @var string
     */
    private $directory;

    /**
     * The name of the log file
     * @var string
     */
    private $filename;

    /**
     * Creates a new eZXMLExportLogger instance
     *
     * @params string the chosen encoding, default utf-8
     * @params string the directory in which the log files will be stored
     * @params string the log filename
     */
    public function eZXMLExportLogger( $encoding = 'utf-8', $directory, $filename )
    {
        $this->startDate       = time();
        $this->endDate         = null;
        $this->appliedXSLT     = null;
        $this->exportedObjects = array();
        $this->encoding        = $encoding;
        $this->XMLContents     = array();
        $this->directory       = $directory;
        $this->filename        = $filename;

        $this->generateAndStoreHeader();
    }

    /**
     * Stores the name of the XSLT applied on the XML export
     */
    public function setAppliedXSLT( $XSLTStylesheet )
    {
        $this->appliedXSLT = $XSLTStylesheet;
    }

    /**
     * Adds informations about the exported object in the log file
     *
     * @param int the content object ID
     * @param int when the content object export started
     * @param int when the content object export ended
     * @param string the name of the generated XML file
     */
    public function addExportedObject( $objectID, $exportStartTime, $exportEndTime, $generatedXMLFile )
    {
        $contentObjectstring = '<object id="' . $objectID . '" '
                                . 'exportstarttime="' . $exportStartTime . '" '
                                . 'exportendtime="' . $exportEndTime . '" '
                                . 'generatedxmlfile="' . $generatedXMLFile . '"/>';

        $this->storeContents( $contentObjectstring );
    }

    /**
     * Generates the XML header for the log file
     *
     * @return string the generated header
     */
    private function generateHeader()
    {
        $headerString  = '<?xml version="1.0" encoding="' . strtoupper( $this->encoding ). '"?>';
        $headerString .= '<export>';
        $headerString .= '<generalinformations>';
        $headerString .= '<startdate>' . $this->startDate . '</startdate>';
        $headerString .= '<enddate>' . time() . '</enddate>';
        $headerString .= '<appliedxslttransformation>' . $this->appliedXSLT . '</appliedxslttransformation>';
        $headerString .= '</generalinformations>';
        $headerString .= '<exportedobjects>';

        return $headerString;
    }

    /**
     * Generates the XML header for the log file
     * and stores it in the XML log file
     */
    private function generateAndStoreHeader()
    {
        $header = $this->generateHeader();
        $this->storeContents( $header, false );
    }

    /**
     * Generates the XML footer of the log file
     *
     * @return string the XML footer
     */
    private function generateFooter()
    {
        $footerString  = '</exportedobjects>';
        $footerString .= '</export>';

        return $footerString;
    }

    /**
     * Generates the XML footer of the log file
     * and stores it in the XML log file
     */
    private function generateAndStoreFooter()
    {
        $header = $this->generateFooter();

        $this->storeContents( $header );
    }

    /**
     * Finalizes the XML log file generation
     */
    public function finalizeLog()
    {
        $this->generateAndStoreFooter();
    }

    /**
     * Store the contents of the XML log file
     *
     * @return bool true if success, false otherwise
     */
    public function storeContents( $contents, $appendContents = true )
    {
        if( $appendContents != true )
        {
            return eZFile::create( $this->filename , $this->directory, $contents );
        }

        if( !$fp = fopen( $this->directory . $this->filename, 'a' ) )
        {
            eZLog::write( 'Unable to append contents to the XML index' );
            return false;
        }

        fwrite( $fp, $contents );
        fclose( $fp );

        return true;
    }
}
?>