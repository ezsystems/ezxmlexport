<?php
/**
 * File containing the eZXMLExportExporter class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

class eZXMLExportExporter
{

    /**
     * Holds a list of already exported node IDs
     *
     * @var array
     */
    public $AlreadyExportedOjectIDList;

    /**
     * The name of the export after it has been cleaned
     *
     * @var string
     */
    public  $CleanExportName;

    /**
     * The current object key
     *
     * @var string
     */
    private $CurrentObjectKey;

    /**
     * The list of all exportable nodes
     *
     * @var array
     */
    private $ExportableNodeList;

    /**
     * The list of exported object ID
     *
     * @var array
     */
    private $ExportedObjectIDList;

    /**
     * The eZXMLExport instance
     *
     * @var object
     */
    private $eZXMLExport;

    /**
     * The eZXMLExportLogger instance
     *
     * @var object
     */
    private $eZXMLExportLogger;

    /**
     * The eZXMLExportProcessLog instance
     *
     * @var object
     */
    public  $eZXMLExportProcessLog;

    /**
     * The chosen slicing mode for the export
     * Possible values are 1 or n.
     *
     * @var string
     */
    private $eZXMLExportSlicingMode;

    /**
     * Flag to set the export in verbose
     * mode or not
     *
     * @var bool
     */
    private $IsVerbose;

    /**
     * The list of related objects for this export
     *
     * @var array
     */
    private $RelatedObjectList;

    /**
     * The name of the generated XML File
     *
     * @var string
     */
    private $XMLFile;

    /**
     * The XML result for an export
     *
     * @var array
     */
    private $XMLResultArray;

    /**
     * The timestamp when the export started
     *
     * @var int
     */
    private $StartTime;

    /**
     * The list of sources fo this export
     *
     * @var array
     */
    private $SourceList;

    /**
     * The list of classes defined as exportable
     *
     * @var array
     */
    public  $ExportableContentClasses;

    /**
     * The exportable object limit
     *
     * @var int
     */
    public  $ExportLimit;

    // those values could be configurable
    const EXPORT_FILE_DIRECTORY  = 'extension/ezxmlexport/exports/xml/';
    const LOG_FILE_DIRECTORY     = 'extension/ezxmlexport/logs/';
    const XSLT_STORAGE_DIRECTORY = 'extension/ezxmlexport/design/standard/xsl/';

    const ID_REF_PREFIX          = 'id';

    /**
     * Creates a new eZXMLExportExporter instance
     *
     * @param int  $exportID        The export identifier
     * @paran bool $isVerbose       Wether to enable verbosity or not, default true
     * @param bool $writeLogFile    Wether to write the log file or not, default true
     * @param bool $writeProcessLog Wether to write the process log or not, default true
     */
    public function eZXMLExportExporter( $exportID, $isVerbose = true , $writeLogFile = true, $writeProcessLog = true )
    {
        $this->eZXMLExport                = eZXMLExportExports::fetch( $exportID );

        $this->AlreadyExportedOjectIDList = array();
        $this->CleanExportName            = $this->cleanExportName();
        $this->CurrentObjectKey           = 0;
        $this->ExportableContentClasses   = eZXMLExportAvailableContentClasses::fetchExportableClasses();
        $this->ExportLimit                = $this->eZXMLExport->attribute( 'export_limit' );
        $this->eZXMLExportSlicingMode     = $this->getSlicingMode();
        $this->FTPInfo                    = $this->fetchFTPInfo();
        $this->IsVerbose                  = $isVerbose;
        $this->RelatedObjectList          = array();
        $this->XMLFile                    = '';
        $this->XMLResultArray             = array();
        $this->StartTime                  = time();
        $this->SourceList                 = unserialize( $this->eZXMLExport->attribute( 'sources' ) );


        // those test are only used for a particular context, the "test" view
        // it is useless anywhere else
        if( $writeLogFile )
        {
            $this->eZXMLExportProcessLog = new eZXMLExportProcessLog();
        }

        if( $writeProcessLog )
        {
            $this->eZXMLExportLogger = new eZXMLExportLogger( 'utf-8',
                                                              self::LOG_FILE_DIRECTORY,
                                                              $this->CleanExportName . '.log' );

            $ini = eZINI::instance( 'ezxmlexport.ini' );
            if( ( $ini->variable( 'XSLTSettings', 'XSLTTransformation' ) == 'enabled' )
                and
                $this->eZXMLExport->attribute( 'xslt_file' ) != '' )
            {
                $this->eZXMLExportLogger->setAppliedXSLT( $this->eZXMLExport->attribute( 'xslt_file' ) );
            }
        }
    }

    /**
     * Takes the export name and clean it
     *
     * @see eZCharTransform::instance()
     * @return string The cleaned export name
     */
    private function cleanExportName()
    {
        $trans = eZCharTransform::instance();
        $exportName = $this->eZXMLExport->attribute( 'name' );
        return $trans->transformByGroup( $exportName , 'identifier' );
    }

    /**
     * Returns the slicing mode defined by the user for this export
     *
     * It is possible to define a slicing mode for either a customer
     * or an export the one defines for the export has a higher priority
     *
     * @return string The slicing mode
     */
    private function getSlicingMode()
    {
        if( $this->eZXMLExport->attribute( 'slicing_mode' ) != '' )
        {
            return $this->eZXMLExport->attribute( 'slicing_mode' );
        }

        $customer = eZXMLExportCustomers::fetch( $this->eZXMLExport->attribute( 'customer_id' ) );

        return $customer->attribute( 'slicing_mode' );
    }

    /**
     * Fetches the FTP configuration and returns it
     *
     * If an FTP has been configured for a customer this
     * one will be returned.
     *
     * If an FTP has been configured per export it will
     * be returned.
     *
     * @return array The table with FTP informations
     */
    private function fetchFTPInfo()
    {
        // a customer may provide one FTP login
        // or one per export the one defined
        // in an export overrides the client's one
        // if any

        // FTP target for the export : top priority
        $FTPTarget = unserialize( $this->eZXMLExport->attribute( 'ftp_target' ) );

        if( count( $FTPTarget ) > 0 )
        {
            return $FTPTarget;
        }

        // customer's FTP target : low priority
        $customer = eZXMLExportCustomers::fetch( $this->eZXMLExport->attribute( 'customer_id' ) );
        $FTPTarget = unserialize( $customer->attribute( 'ftp_target' ) );

        if( count( $FTPTarget ) > 0 )
        {
            return $FTPTarget;
        }

        return false;
    }

    /**
     * Creates the basic XML export headers
     *
     * @return void
     */
    private function exportHeaders()
    {
        $ini      = eZINI::instance('i18n.ini');
        $encoding = $ini->variable( 'CharacterSettings', 'Charset' );

        $this->XMLResultArray['header'][] = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $this->XMLResultArray['header'][] = '<ezpublish xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
        $this->XMLResultArray['header'][] = ' xsi:noNamespaceSchemaLocation="file:'
                                            . realpath( 'extension/ezxmlexport/exports/xsd/contentclassdefinition.xsd' )
                                            . '">';

        $this->generateMetaData();
    }

    /**
     * Generates the meta data for this export
     *
     * @return void
     */
    public function generateMetaData()
    {
        $ini        = eZINI::instance( 'datetime.ini' );
        $dateFormat = $ini->variable( 'ClassSettings', 'Formats' );

        $locale      = eZLocale::instance();
        $exportDate  = $locale->formatDateTimeType( $dateFormat['datetimexmlschema'], $this->StartTime );

        $this->XMLResultArray['header'][] = '<AdministrativeMetadata>';
        $this->XMLResultArray['header'][] = '<name>' . $this->eZXMLExport->attribute( 'name' ). '</name>';
        $this->XMLResultArray['header'][] = '<export_date>' . $exportDate . '</export_date>';
        $this->XMLResultArray['header'][] = '<country>France</country>';
        $this->XMLResultArray['header'][] = '</AdministrativeMetadata>';
    }

    /**
     * Instruct the export to start the export
     *
     * @return void
     */
    public function exportStart()
    {
        $message = 'Exporting export \''
                 . $this->eZXMLExport->attribute( 'name' )
                 . '\' ( '
                 . $this->eZXMLExport->attribute( 'id' )
                 . ' )';

        $this->outputMessage( $message );

        $exportDirectory = eZXMLExportExporter::EXPORT_FILE_DIRECTORY . $this->CleanExportName;

        if( !file_exists( $exportDirectory ) and is_writable( $exportDirectory ) )
        {
            $this->outputMessage( 'Creating dir ' . $exportDirectory );
            eZDir::mkDir( $exportDirectory );
        }

        // log that export process starts
        $this->eZXMLExportProcessLog->setAttribute( 'export_id' , $this->eZXMLExport->attribute( 'id' ) );
        $this->eZXMLExportProcessLog->setAttribute( 'start_date', $this->StartTime );
        $this->eZXMLExportProcessLog->setAttribute( 'status', eZXMLExportProcessLog::STATUS_XML_GENERATION_STARTED );
        $this->eZXMLExportProcessLog->store();

        $this->exportHeaders();

        if( $this->eZXMLExportSlicingMode == '1' )
        {
            $this->generateFileName();
            $this->writeResultFile( false,   // do not append contents => create the file
                                    true,    // add the XML header
                                    false ); // do not add the footer
        }
    }

    /**
     * Instruct the export to start the export for a
     * content object
     *
     * @param  object $contentObject The eZContentObject to export
     * @return void
     */
    public function objectExportStart( $contentObject )
    {
        if( !$contentObject instanceof eZContentObject )
        {
            return false;
        }

        if( $this->eZXMLExportSlicingMode == 'n' )
        {
            $suffix  = $contentObject->attribute( 'class_identifier' );
            $suffix .= '.';
            $suffix .= $contentObject->attribute( 'id' );

            $this->generateFileName( $suffix );
        }

        $ini        = eZINI::instance( 'datetime.ini' );
        $dateFormat = $ini->variable( 'ClassSettings', 'Formats' );

        $locale    = eZLocale::instance();
        $modified  = $locale->formatDateTimeType( $dateFormat['datetimexmlschema'],
                                                  $contentObject->attribute( 'modified' ) );
        $published = $locale->formatDateTimeType( $dateFormat['datetimexmlschema'],
                                                  $contentObject->attribute( 'published' ) );

        $objectInfoArray = array( 'ID'                => eZXMLExportExporter::ID_REF_PREFIX . $contentObject->attribute( 'remote_id' ),
                                  'contentobject_id'  => $contentObject->attribute( 'id' ),
                                  'creation_date'     => $modified,
                                  'modification_date' => $modified,
                                  'publication_date'  => $published,
                                  'lang'              => $contentObject->attribute( 'current_language' ),
                                  'version'           => $contentObject->attribute( 'current_version' ),
                                  'creator_id'        => $contentObject->attribute( 'owner_id' ),
                                  'remote_id'         => $contentObject->attribute( 'remote_id' ) );

        $externalMetaData = array( 'contentobject_id' => $contentObject->attribute( 'id' ),
                                   'class_identifier' => $contentObject->attribute( 'class_identifier' ) );


        // I need to to this for the 'object_metadata' custom attribute
        // see the generated XML Schema to understand know how it works
        // as this is not an eZ Publish attribute, I can not use the
        // API I built before

        $section     = eZSection::fetch( $contentObject->attribute( 'section_id' ) );
        $sectionName = $section->attribute( 'name' );

        $objectCustomMetaData = array( 'section'           => array( 'id'   => $contentObject->attribute( 'section_id' ),
                                                                     'name' => $sectionName ),
                                       'draft_count'       => $this->fetchDraftCount( $contentObject->attribute( 'id' ) ),
                                       'translation_count' => $this->fetchTranslationCount( $contentObject->attribute( 'id' ) ),
                                       'locations'         => array());

        // feching object's locations to define IDRefs is needed
        $objectLocationList = $contentObject->assignedNodes();

        $objectCustomMetaData['locations'][] = array( 'id'           => $objectLocationList[0]->attribute( 'node_id' ),
                                                      'is_main_node' => 1,
                                                      'name'         => $objectLocationList[0]->attribute( 'path_identification_string' ) );

        // if count( $objectLocation ) is 1 this means
        // we only have one assigned node which is the main
        // location and this is not what we want for the IDRef
        if( count( $objectLocationList ) > 1 )
        {
            $IDRefAlreadyDefined = false;
            foreach( $objectLocationList as $assignedNode )
            {
                $objectCustomMetaData['locations'][] = array( 'id'   => $assignedNode->attribute( 'node_id' ),
                                                              'is_main_node' => 0,
                                                              'name' => $assignedNode->attribute( 'path_identification_string' ) );

                if( !$IDRefAlreadyDefined
                    and
                    $this->isExportable( $assignedNode->object() ) )
                {
                    $tempObject = $assignedNode->object();
                    $objectInfoArray['IDRef'] = eZXMLExportExporter::ID_REF_PREFIX . $tempObject->attribute( 'remote_id' );

                    // I can only push one IDRef value in the XML target
                    // as far as I know, this is a limitation of the XML
                    // standard, if I wrong then it should be easy to
                    // add other IDRefs value
                    $IDRefAlreadyDefined = true;
                }
            }
        }

        $this->XMLResultArray['objects'][$this->CurrentObjectKey] = array( 'object_info'               => $objectInfoArray,
                                                                           'external_meta_data'        => $externalMetaData,
                                                                           'ezobject_custom_meta_data' => $objectCustomMetaData );
    }

    /**
     * Instruct the export to export an content object attribute
     *
     * @param  object $contentObjectAttribute The eZContentObjectAttribute to export
     * @return void
     */
    public function exportAttribute( $contentObjectAttribute )
    {
        if( !$contentObjectAttribute instanceof eZContentObjectAttribute )
        {
            return false;
        }

        if( eZXMLExportAvailableContentClassAttributes::isExportable( $contentObjectAttribute->attribute( 'contentclassattribute_id' ) ) )
        {
            $className = $contentObjectAttribute->attribute('data_type_string') . 'xmlexport';

            $fileToInclude = 'extension/ezxmlexport/classes/datatypes/'
                            . $contentObjectAttribute->attribute( 'data_type_string')
                            . '/'
                            . $className . '.php';

            if( !file_exists( $fileToInclude ) )
            {
                return;
            }

            include_once( $fileToInclude );

            $contentClassAttribute = eZContentClassAttribute::fetch( $contentObjectAttribute->attribute( 'contentclassattribute_id' ) );
            $xmlSchemaDatatype     = new $className( $contentClassAttribute );

            $this->XMLResultArray['objects'][$this->CurrentObjectKey]['attributes'][] = $xmlSchemaDatatype->xmlize( $contentObjectAttribute );
        }
    }

    /**
     * Instruct the export to end a contentobject export
     *
     * @param  int $contentObjectID              An eZContentObject::contentObjectID
     * @params int $contentObjectExportStartTime The eZContentObject export start time
     * @params int $contentObjectExportEndTime   The eZContentObject export end time
     */
    public function objectExportEnd( $contentObjectID, $contentObjectExportStartTime, $contentObjectExportEndTime )
    {
        $this->CurrentObjectKey++;

        $appendContents = true;
        $addHeader      = false;
        $addFooter      = false;

        if( $this->eZXMLExportSlicingMode == 'n' )
        {
            $appendContents = false;
            $addHeader      = true;
            $addFooter      = true;
        }

        $this->writeResultFile( $appendContents, $addHeader, $addFooter );
        $this->resetObjectList();

        $this->eZXMLExportLogger->addExportedObject( $contentObjectID,
                                                     $contentObjectExportStartTime,
                                                     $contentObjectExportEndTime,
                                                     $this->XMLFile  );

        $this->eZXMLExportProcessLog->setAttribute( 'object_id', $contentObjectID );
        $this->eZXMLExportProcessLog->store();

        $this->AlreadyExportedOjectIDList[] = $contentObjectID;

        eZContentObject::clearCache( $contentObjectID );
    }


    /**
     * Instruct the export to stop
     *
     * @return void
     */
    public function exportEnd()
    {
        $this->outputMessage( 'Export is finishing' );

        if( $this->eZXMLExportSlicingMode == 1 )
        {
            // it is now time to write the footer
            // in the XML result file
            $this->writeResultFile( true,   // append contents, the footer is contents
                                    false,  // no header
                                    true ); // add footer
        }

        $dirPath = eZXMLExportExporter::EXPORT_FILE_DIRECTORY . $this->CleanExportName;

        $fileList = eZDir::findSubitems( $dirPath, false, false, false, '#^(?:.*).gz|.transformed.xml$#i' );

        $this->applyXSLTIfNeeded( $fileList );

        $fileList = eZDir::findSubitems( $dirPath, false, false, false, '#^(?:.*).gz$#i' );

        $compressionResult = $this->compressFilesIfNeeded( $fileList );

        if( $this->eZXMLExport->attribute( 'compression' ) == 1
            and
            $compressionResult == true )
        {
            $prependExportDir = false;
            $fileList = array( $this->CleanExportName . '.tar.gz' );
        }
        else
        {
            // I have to refetch the file list as name
            // may have changed
            $fileList = eZDir::findSubitems( $dirPath );
        }

        $this->sendOverFTPIfNeeded( $fileList );

        $this->eZXMLExportLogger->finalizeLog();

        // log that export process ends
        $this->eZXMLExportProcessLog->setAttribute( 'end_date', time() );
        $this->eZXMLExportProcessLog->setAttribute( 'status', eZXMLExportProcessLog::STATUS_XML_GENERATION_DONE);
        $this->eZXMLExportProcessLog->store();

        $message = 'Export \''
                 . $this->eZXMLExport->attribute( 'name' )
                 . '\' done';

        $this->outputMessage( $message );
    }

    /**
     * Resets the list of exported objects
     *
     * @return void
     */
    private function resetObjectList()
    {
        $this->XMLResultArray['objects'] = array();

        $this->resetCurrentObjectKey();
    }

    /**
     * Reset the eZXMLExportExporter::CurrentObjectKey value
     *
     * @return void
     */
    private function resetCurrentObjectKey()
    {
        $this->CurrentObjectKey = 0;
    }

    /**
     * Apply an XSLT stylesheet on the list of files
     * given in $fileList
     *
     * @param  array $fileList The list of files which to apply the XSLT on
     * @return void
     */
    private function applyXSLTIfNeeded( $fileList )
    {
        // applying XSLT if needed
        $ini = eZINI::instance( 'ezxmlexport.ini' );

        if( ( $ini->variable( 'XSLTSettings', 'XSLTTransformation' ) == 'enabled' )
            and
            $this->eZXMLExport->attribute( 'xslt_file' ) != '' )
        {

            $this->eZXMLExportProcessLog->setAttribute( 'status', eZXMLExportProcessLog::STATUS_XSLT_TRANSFORMATION_STARTED );
            $this->eZXMLExportProcessLog->store();

            $this->outputMessage( 'Applying XSLT' );

            $XSLTPath = eZXMLExportExporter::XSLT_STORAGE_DIRECTORY . $this->eZXMLExport->attribute( 'xslt_file' );

            // sanity check : no empty files
            if( eZFile::getContents( $XSLTPath ) == '' )
            {
                $message = 'XLST File is empty';
                $this->outputMessage( $message );
                eZDebug::writeError( $message );
                return false;
            }

            //foreach( $this->WrittenFileList as $key => $generatedXMLFile )
            foreach( $fileList as $key => $generatedXMLFile )
            {
                $directory = eZXMLExportExporter::EXPORT_FILE_DIRECTORY
                             . $this->CleanExportName
                             . '/';

                $filePath =  $directory . $generatedXMLFile;

                $DOMDocument = new DOMDocument();
                $DOMDocument->load( $filePath );


                $XSLDocument = new DOMDocument();
                $XSLDocument->load( $XSLTPath );

                $XSLTProc = new XSLTProcessor();

                if( !@$XSLTProc->importStyleSheet( $XSLDocument ) )
                {
                    eZDebug::writeError( 'Unable to import XSLT file :' . $this->eZXMLExport->attribute( 'xslt_file' ) );
                }

                $transformedDoc = $XSLTProc->transformToXML( $DOMDocument );

                if( $transformedDoc === false )
                {
                    eZDebug::writeError( 'Unable to apply XSLT file : ' . $this->eZXMLExport->attribute( 'xslt_file' ) );
                }
                else
                {
                    $transformedDocFilename = str_replace( '.xml', '.transformed.xml', $generatedXMLFile );

                    eZFile::create( $directory . $transformedDocFilename, false, $transformedDoc );

                    if( $ini->variable( 'XSLTSettings', 'DeleteXMLSourceAfterXSLTTransformation' ) == 'enabled' )
                    {
                        // unset( $this->WrittenFileList[$key] );
                        $this->deleteSourceFile( $filePath );
                    }
                }
            }
        }
        else
        {
            $this->outputMessage( 'No XSLT transformation available' );
        }

        $this->eZXMLExportProcessLog->setAttribute( 'status', eZXMLExportProcessLog::STATUS_XSLT_TRANSFORMATION_DONE );
        $this->eZXMLExportProcessLog->store();
    }

    /**
     * Sends the export result over FTP
     *
     * @param  array $readyToSendFileList The list of files to send
     * @return void
     */
    private function sendOverFTPIfNeeded( $readyToSendFileList )
    {
        $ini = eZINI::instance( 'ezxmlexport.ini' );

        if( ( $ini->variable( 'FTPSettings', 'FTPShipment' ) == 'enabled' )
            and
            $this->FTPInfo != false )
        {
            $this->outputMessage( 'Sending over FTP' );

            $connectionAttempt = 1;
            $maxConnectionAttempt = 5;
            $isConnected = false;

            $ftpHandler = eZXMLExportFTPFileHandler::instance();

            $FTPLoginCredentials = array();
            $FTPLoginCredentials['host']               = $this->FTPInfo['host'];
            $FTPLoginCredentials['port']               = $this->FTPInfo['port'];
            $FTPLoginCredentials['login']              = $this->FTPInfo['login'];
            $FTPLoginCredentials['password']           = $this->FTPInfo['password'];
            $FTPLoginCredentials['destination_folder'] = $this->FTPInfo['path'];

            while( $connectionAttempt < $maxConnectionAttempt )
            {
                $this->outputMessage( 'Connexion attempt #' . $connectionAttempt );

                if( $ftpHandler->connect( $FTPLoginCredentials ) )
                {
                    $this->outputMessage( 'Connected' );

                    // send contents by FTP
                    $this->eZXMLExportProcessLog->setAttribute( 'status', eZXMLExportProcessLog::STATUS_FTP_TRANSFERT_STARTED );
                    $this->eZXMLExportProcessLog->store();

                    foreach( $readyToSendFileList as $readyToSendFile )
                    {
                        $this->outputMessage( 'Sending file ' . $readyToSendFile . ' ', false );

                        $sourceFile = eZXMLExportExporter::EXPORT_FILE_DIRECTORY
                                     . $this->CleanExportName
                                     . '/'
                                     . $readyToSendFile;

                        $storeResult = $ftpHandler->storeFile( $FTPLoginCredentials['destination_folder'],
                                                               $readyToSendFile,
                                                               $sourceFile );

                        if( $storeResult
                            and
                            $ini->variable( 'FTPSettings', 'DeleteSourceFileAfterShipment' ) == 'enabled' )
                        {
                            $this->outputMessage( 'SUCCESS' );
                            $this->deleteSourceFile( $sourceFile );
                        }
                        else
                        {
                            $this->outputMessage( 'FAILED' );
                            eZDebug::writeError( 'Unable to store ' . $sourceFile . ' in ' . $FTPLoginCredentials['destination_folder'] . ', does it exists ?',
                                                 'eZXMLExport FTP sender' );
                        }
                    }

                    $isConnected = true;
                    break;
                }

                $connectionAttempt++;
            }

            if( !$isConnected )
            {
                $this->eZXMLExportProcessLog->setAttribute( 'status', eZXMLExportProcessLog::STATUS_FTP_TRANSFERT_FAILED );
                $this->eZXMLExportProcessLog->store();

                $message = 'Unable to send files over FTP, connection failed';
                $this->outputMessage( $message );
                eZDebug::writeError( $message );
            }
        }

        $this->eZXMLExportProcessLog->setAttribute( 'status', eZXMLExportProcessLog::STATUS_FTP_TRANSFERT_DONE );
        $this->eZXMLExportProcessLog->store();
    }

    /**
     * Returns the final XML result for an export
     *
     * @param  bool  $addHeader Wether to add the XML header or not
     * @param  bool  $addFooter Wether to add the XML footer or not
     * @return string The final XML string
     */
    public function getXMLResult( $addHeader = true, $addFooter = true )
    {
        $header     = '';
        $objectList = '';
        $footer     = '';

        if( $addHeader )
        {
            $header = join( "\n", $this->XMLResultArray['header'] );
        }

        $objectXMLList = $this->extractObjectList();

        if( $objectXMLList != '' )
        {
            $objectList = '<objects>' . $objectXMLList . '</objects>';
        }

        if( $addFooter )
        {
            $footer = '</ezpublish>';
        }

        $finalResult = $header . $objectList . $footer;

        return $finalResult;
    }

    /**
     * Extracts the object list from the XML result array
     *
     * @return array The object list
     */
    private function extractObjectList()
    {
        $objectList = '';

        if( !isset( $this->XMLResultArray['objects'] ) )
        {
            return $objectList;
        }

        foreach( $this->XMLResultArray['objects'] as $contentObjectDataList => $contentObjectData )
        {
            $objectInfoAttributeGroup = '';

            foreach( $contentObjectData['object_info'] as $attributeName => $attributeValue )
            {
                $objectInfoAttributeGroup .= $attributeName . '="' . $attributeValue . '" ';
            }

            $contentObjectID = $contentObjectData['external_meta_data']['contentobject_id'];

            $tagName = $contentObjectData['external_meta_data']['class_identifier'];

            $objectMetaDataCustomAttribute = $this->getObjectMetaDataCustomTag( $contentObjectData['ezobject_custom_meta_data'], $contentObjectID );

            $objectList .= '<' . $tagName . ' ' . $objectInfoAttributeGroup. '>'
                           . $objectMetaDataCustomAttribute
                           . join( "\n", $contentObjectData['attributes'] )
                           . '</' . $tagName . '>';
        }

        $this->RelatedObjectList = array();

        return $objectList;
    }

    /**
     * Generates the <object_metadata> custom tag
     * based on the $contentObjectData provided for
     * a specific $contentObjectID
     *
     * @param  array $contentObjectData The content object data
     * @param  int   $contentObjectID   The eZContentObjectID
     * @return string the <object_metadata> tag
     */
    private function getObjectMetaDataCustomTag( $contentObjectData, $contentObjectID )
    {
        $XMLString = '<object_metadata>';

        $XMLString .= '<section ID="' . $contentObjectData['section']['id'] . '">'
                      . $contentObjectData['section']['name']
                      .'</section>';

        $XMLString .= '<draft_count>' . $contentObjectData['draft_count']
                      . '</draft_count>';

        $XMLString .= '<translation_count>'
                      . $contentObjectData['translation_count']
                      . '</translation_count>';

        // adding locations
        $XMLString .= '<locations>';

        foreach( $contentObjectData['locations'] as $key => $location )
        {
            $XMLString .= '<ezlocation ID="'. $location['id']
                                . '" name="'. $location['name']
                                . '" is_main_node="' . $location['is_main_node']. '"/>';
        }

        $XMLString .= '</locations>';

        // adding object relations
        if( isset( $this->RelatedObjectList[$contentObjectID] ) )
        {
            $XMLString .= '<object_relation_list>';

            foreach( $this->RelatedObjectList[$contentObjectID] as  $relatedObjectInfos )
            {
                $XMLString .= '<object_relation>'
                              . $relatedObjectInfos['related_object_id']
                              . '</object_relation>';
            }

            $XMLString .= '</object_relation_list>';
        }

        $XMLString .= '</object_metadata>';

        return $XMLString;
    }

    /**
     * Fetches the number of nodes to export
     *
     * @return int The number of nodes to export
     */
    public function fetchNodeTotal()
    {
        $params = $this->generateFetchParameters();
        $params['Limitation'] = false;
        unset( $params['SortBy'] );

        $total = 0;

        foreach( $this->SourceList as $parentNodeID )
        {
            $total += eZContentObjectTreeNode::subtreeCountByNodeID( $params, $parentNodeID );
        }

        return $total;
    }

    /**
     * Fetches the list of exportable nodes
     *
     * @param  int $offset The offset to start to, default 0
     * @param  int $limit  The limit to stop to, default 0
     * @return array The list of exportable nodes
     */
    public function fetchExportableNodes( $offset = 0, $limit = 0 )
    {
        if( !$this->eZXMLExport )
        {
            return array();
        }

        $exportableNodes = array();

        $params = $this->generateFetchParameters();

        $params['Offset'] = $offset;
        $params['LoadDataMap'] = false;

        if( $limit > 0)
        {
            $params['Limit']  = $limit;
        }

        foreach( $this->SourceList as $parentNodeID )
        {
            $exportableNodes[] = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentNodeID );
        }

        $exportableNodes = $exportableNodes[0];

        $exportableNodes = $this->addRelatedObjectsIfNeeded( $exportableNodes );

        // since GroupBy will not work for me, I have to create it
        $exportableNodes = $this->groupByClassIdentifier( $exportableNodes );

        return $exportableNodes;
    }

    /**
     * Generates the fetchParameters for an
     * eZContentObjectTreeNode::subtreeCountByNodeID method
     *
     * @return array The fetch parameters
     */
    private function generateFetchParameters()
    {
        $maxDepth = $this->getMaxContentTreeDepth();

        if( !$maxDepth )
        {
            eZDebug::writeError( 'Unable to get max depth for this content tree' );
            return false;
        }

        // I only want class_id
        $classFilterArray = array();

        foreach( $this->ExportableContentClasses as $exportableClass )
        {
            $classFilterArray[] = $exportableClass['contentclass_id'];
        }

        if( !$classFilterArray )
        {
            eZDebug::writeError( 'Unable to get class filter array' );
            return false;
        }

        $params = array( 'Depth'            => $maxDepth,
                         'ClassFilterType'  => 'include',
                         'ClassFilterArray' => $classFilterArray,
                         'SortBy'           => array( array( 'class_identifier', true ),
                                                      array( 'published'       , false ) ) );

        if( $this->eZXMLExport->attribute( 'export_hidden_nodes' ) == '1' )
        {
            $params['IgnoreVisibility'] = true;
        }

        // "export all content object from the last export" is checked
        if( $this->eZXMLExport->attribute( 'export_from_last' ) == 1 )
        {
            $latestRunExport = eZXMLExportProcessLog::fetchByExportID( $this->eZXMLExport->attribute( 'id' ),
                                                                    array( 'end_date' => 'desc' ),
                                                                    array( 'offset'   => 0,
                                                                           'limit'    => 1 ) );
            if( $latestRunExport != false
                and
                $latestRunExport->attribute( 'end_date' ) )
            {
                $params['AttributeFilter'] = array( array( 'published', '>=', $latestRunExport->attribute( 'end_date' ) ) );
            }
        }

        return $params;
    }

    /**
     * Reorder the list of exportable nodes and group them
     * by class identifier
     *
     * @param  array  $exportableNodeList the list of exportable nodes
     * @return array The reordered list
     */
    private function groupByClassIdentifier( $exportableNodeList )
    {
        $resultArray = array();

        // the list passed in argument is supposed
        // to be sorted by class_identifier so
        // things should be easy to group data
        foreach( $exportableNodeList as $exportableNode )
        {
            $object = $exportableNode->object();
            $currentClassIdentifier = $object->attribute( 'class_identifier' );

            $resultArray[$currentClassIdentifier][] = $exportableNode;
        }

        return $resultArray;
    }

    /**
     * Adds related objects to the list of exportable node if needed
     *
     * @param  array $exportableNodeList The $exportableNodeList
     * @return array The new $exportableNodeList
     */
    private function addRelatedObjectsIfNeeded( $exportableNodeList )
    {
        $relatedObjectHandling = $this->eZXMLExport->attribute( 'related_object_handling' );

        foreach( $exportableNodeList as $exportableNode )
        {
            $object = $exportableNode->object();
            $contentObjectID = $object->attribute( 'id' );

            $eZContentFunctionCollection = new eZContentFunctionCollection();
            $relatedObjectIDList = $eZContentFunctionCollection->fetchRelatedObjectsID( $contentObjectID,
                                                                                        0,
                                                                                        array( 'attribute' ) );
            $relatedObjectIDList = $relatedObjectIDList['result']['attribute'];

            foreach( $relatedObjectIDList as $relatedObjectID )
            {
                $relatedObject = eZContentObject::fetch( $relatedObjectID );

                // related object level 1 :
                // related objects are only referenced by
                // their nodeIDs
                if( $relatedObjectHandling == 1 )
                {
                    if( $this->isExportable( $relatedObject ) )
                    {
                        $this->RelatedObjectList[ $contentObjectID ][] =  array( 'related_object_id' => $relatedObjectID,
                                                                                 'remote_id'         => $relatedObject->attribute( 'remote_id' ) );
                    }
                }

                // related object level 2:
                // objects are exported as well
                // if their main_location is under
                // the sourceList's parentNodes
                // if not they will only be referenced
                // by their nodeID
                if( $relatedObjectHandling == 2 )
                {
                    if( $this->isExportable( $relatedObject ) )
                    {
                        $this->RelatedObjectList[ $contentObjectID ][] =  array( 'related_object_id' => $relatedObjectID,
                                                                                 'remote_id'         => $relatedObject->attribute( 'remote_id' ) );

                        $nodeToAdd = eZContentObjectTreeNode::fetch( $relatedObject->attribute( 'main_node_id' ) );
                        if( $nodeToAdd instanceof eZContentObjectTreeNode )
                        {
                            $exportableNodeList[] = $nodeToAdd;
                        }
                        else
                        {
                            eZLog::write( 'Node  can not be exported due to a PHP issue, this object was not an eZContentObjectTreeNode', 'error.log' );
                        }
                    }
                }
            }
        }

        return $exportableNodeList;
    }

    /**
     * Checks if a content object is exportable or not
     *
     * @return bool false if the content object is not exportable, true otherwise
     */
    private function isExportable( $contentObject )
    {
        if( !$this->contentClassIsExportable( $contentObject->attribute( 'contentclass_id' ) ) )
        {
            return false;
        }

        return true;

    }

    /**
     * Checks if a content class is exportable or not
     *
     * @param  int $contentClassID The $contentClassID
     * @return bool false if the content class is not exportable, true otherwise
     */
    private function contentClassIsExportable( $contentClassID )
    {
        foreach( $this->ExportableContentClasses as $exportableContentClass )
        {
            if( $exportableContentClass['contentclass_id'] == $contentClassID )
            {
                return true;
            }

        }

        return false;
    }

    /**
     * Fetches the current max depth for the content tree
     *
     * @return int|bool The max depth for the content tree, false otherwise
     */
    private function getMaxContentTreeDepth()
    {
        $db   = eZDB::instance();
        $sql  = 'SELECT MAX( depth ) as max_depth FROM ezcontentobject_tree';
        $rows = $db->arrayQuery( $sql );

        if( isset( $rows[0] ) )
        {
            return $rows[0]['max_depth'];
        }

        return false;
    }

    /**
     * Writes the XML contents to a file
     *
     * @param  bool $appendContents wether to append the XML contents or not
     * @param  bool $addFileHeader  wether to add the XML header or not
     * @param  bool $addFileFooter  wether to add the XML footer or not
     * @return bool true if success, false otherwise
     */
    private function writeResultFile( $appendContents, $addFileHeader, $addFileFooter )
    {
        if( $appendContents != true )
        {
           return eZFile::create( $this->XMLFile,
                                  eZXMLExportExporter::EXPORT_FILE_DIRECTORY . $this->CleanExportName,
                                  $this->getXMLResult( $addFileHeader,
                                                       $addFileFooter ) );
        }

        if( !$fp = @fopen( eZXMLExportExporter::EXPORT_FILE_DIRECTORY
                          . $this->CleanExportName
                          . '/'
                          . $this->XMLFile, 'a' ) )
        {
            eZLog::write( 'Unable to append contents to the XML result file' );
            return false;
        }

        fwrite( $fp, $this->getXMLResult( $addFileHeader,
                                          $addFileFooter ) );
        fclose( $fp );

        return true;
    }

    /**
     * Generates a filename based on the $suffix provided
     *
     * @param string $suffix The suffix to append to the filename, default false
     */
    private function generateFileName( $suffix = false )
    {
        $resultFileName = $this->CleanExportName;

        if( $suffix )
        {
            $resultFileName .= '.' . $suffix;
        }

        $resultFileName .= '.xml';

        $this->XMLFile = $resultFileName;
    }

    /**
     * Compresses a file list if needed
     *
     * @param  array $fileList the file list to compress
     * @return bool true if success, false otherwise
     */
    private function compressFilesIfNeeded( $fileList )
    {
        $ini = eZINI::instance( 'ezxmlexport.ini' );

        if( $this->eZXMLExport->attribute( 'compression' ) == 1 )
        {
            $tarBinaryPath = $ini->variable( 'CompressionSettings', 'TarBinaryPath' );

            if( !is_executable( $tarBinaryPath ) )
            {
                eZLog::writeError( 'The following tar binary : ' . $tarBinaryPath . ' is not executable' );
                return false;
            }

            $exportDirectory = eZXMLExportExporter::EXPORT_FILE_DIRECTORY . $this->CleanExportName;

            $shellCommand = $tarBinaryPath . ' czf ' . $exportDirectory . '/' . $this->CleanExportName . '.tar.gz ' . $exportDirectory;

            exec( $shellCommand, $output, $return_var );

            if( $return_var != 0 )
            {
                eZLog::writeError( 'Unable to compress exported files' );
                return false;
            }

            foreach( $fileList as $fileToUnlink )
            {
                $this->deleteSourceFile( $exportDirectory . '/' . $fileToUnlink );
            }
        }

        return true;
    }

    /**
     * Outputs a debug message
     *
     * @param string $message The debug message
     * @param bool   $eol     Wether to add EOL or not
     */
    public function outputMessage( $message, $eol = true )
    {
        if( $this->IsVerbose and strtolower( php_sapi_name() ) == 'cli')
        {
            $cli = eZCLI::instance();
            $cli->output( $message, $eol );
        }
    }

    /**
     * Deletes a source file identified by its $filePath
     *
     * @param  string $filePath The source file to delete
     * @return bool true if success, false otherwise
     */
    private function deleteSourceFile( $filePath )
    {
        $this->outputMessage( 'Deleting source file : ' . $filePath , false );

        if( @unlink( $filePath ) )
        {
            $this->outputMessage( ' SUCCESS' );
            return true;
        }
        else
        {
            $this->outputMessage( ' FAILED' );
            return false;
        }

        return false;
    }

    /**
     * Fetches the number of drafts for a given content object
     *
     * @param  int $contentObjectID The contentobject identified by its $contentObjectID
     * @return int the number of drafts for thi object
     */
    private function fetchDraftCount( $contentObjectID )
    {
        $db = eZDB::instance();

        $sql = 'SELECT COUNT( id ) AS draft_total
                FROM ezcontentobject
                WHERE id = '. $contentObjectID
            . ' AND status = ' . eZContentObject::STATUS_DRAFT;

        $rows = $db->arrayQuery( $sql );

        return $rows[0]['draft_total'];
    }

    /**
     * Fetches the number of translations for a contentObject
     *
     * @param  int $contentObjectID The contentObjectID
     * @return int the number of translations for this object
     */
    private function fetchTranslationCount( $contentObjectID )
    {
        $db = eZDB::instance();

        $sql = 'SELECT COUNT(DISTINCT(language_id)) as translation_count
                FROM ezcontentobject_attribute
                WHERE contentobject_id = ' . $contentObjectID;

        $rows = $db->arrayQuery( $sql );

        return $rows[0]['translation_count'];
    }
}
?>