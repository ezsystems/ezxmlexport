.. -*- coding: utf-8 -*-

===================================
eZXMLExport extension documentation
===================================

:Date: 2008/10/02

.. contents:: Table of contents


What is this extension for ?
============================
Its goal is to export content class definitions in XML Schema format and to export
content objects according to this schema.

Installation
============

Declaring the extension
-----------------------
Extract the extension in the ``extension`` folder of your eZ Publish installation directory.
Enable it either by adding the following line in your site.in file

::

    ActiveExtensions[]=ezxmlexport

Creating SQL schema
-------------------
This extension use special tables.
You have to apply the SQL script delivered with this extension
in order to get the extension working :

::

    mysql -u<user> -p<password> -h<host> <database_name> < extension/ezxmlexport/sql/mysql/schema.sql

Regenerating the autoload array
-------------------------------
You should run the following command after that :

::

    php bin/php/ezpgenerateautoloads.php -e

Using the extension
===================

In order to use this extension you have to create a customer and
an export. All forms are located at the following adress :
http://yoursite.com/admin_siteaccess/xmlexport/menu

You may also use the eZ Publish' interface to go to the same location:

::

    'Setup' -> 'XML Export'

Using the cronjob
=================
This extension is shipped with only one cronjob.
Defining the correct schedule is up to you.

The only line you have to use is the following

::

    php runcronjobs.php ezxmlexport


Configuration directives
========================

ExportSettings
--------------

Verbosity
'''''''''
Possible values : ``enabled`` or disabled
This options makes it possible to have a verbose
output whenever an export starts. Here is an example output :

::

    Running cronjob part 'ezxmlexport'
    Running extension/ezxmlexport/cronjobs/ezxmlexport.php
    Exporting export 'export' ( 10 )
    [...]
    Export is finishing
    Applying XSLT
    Compressing export.1223303012.110.xml
    Compressing export.1223303012.82.xml
    Compressing export.1223303012.67.xml
    Compressing export.1223303012.60.xml
    Compressing export.1223303012.109.xml
    Compressing export.1223303012.88.xml
    Compressing export.1223303012.114.xml
    Sending over FTP
    Connexion attempt #1
    Connected
    Sending [...]
    Export 'export' done

The important parts of the output are the following :

::

    Exporting export 'export' ( 10 )

Where 'export' is the export's name and '10' is the exportID.

FetchLimit
''''''''''
This value is here to limit the amount of fetched nodes and makes it
possible to slice the export in several parts so it can run for long
period of times without crashing nor stopping

PHPBinaryPath
'''''''''''''
This values is necessary to be able to run the export.
If the value is empty the cronjob will automatically use
the following binary

::

    /usr/bin/php

FTPSettings
-----------

FTPShipment
'''''''''''
Possible values : ``enabled`` or ``disabled``

No login credentials are required in the configuration files as
they are required in the customer or project definition.

DeleteSourceFileAfterShipment
'''''''''''''''''''''''''''''
Possible values : ``enabled`` or ``disabled``
Whether to delete source file after they have been uploaded

XSLTSettings
------------

XSLTTransformation
''''''''''''''''''
Possible values : ``enabled`` or ``disabled``

No XSLT file is defined in the configuration.
In order to use custom XSLT files you have to manually upload
your file in the following folder :

::

    extension/ezxmlexport/design/standard/xsl

Use ascii characters and no special nor diacritic chars as
they will all be removed at runtime. If not the XSLT
file may not be applied.

DeleteXMLSourceAfterXSLTTransformation
''''''''''''''''''''''''''''''''''''''
Possible values : ``enabled`` or ``disabled``

Whether to delete the XML source files after they have
been processed by an XSLT stylesheet.

CompressionSettings
-------------------

This configuration group make it possible to compress files
before they be sent by FTP.
Only GZip format is available so far and there is a few chance
to add other compression formats in the future.

ActivateCompression
'''''''''''''''''''
Possible values : ``enabled`` or ``disabled``

Extensibility
=============

This extension is easily extendable for custom
datatypes. All available datatypes are located
under the following directory

::

    extension/ezxmlexport/classes/datatypes/

Creating code for custome datatypes
-----------------------------------

Directory structure
'''''''''''''''''''

The code for the new datatype must be stored
under the directory described above. Its directory
name must be the datatype name and there must be a PHP
file under this directory which name is :

::

    <datatypename>xmlexport.php

You should have the following result :

::

    extension/ezxmlexport/classes/datatypes
    |-- <datatypename>
    |   `-- <datatypename>xmlexport.php


This file must extend the mother abstract class called ``eZXMLExportDatatype``.
This file must also implement the three following methods :

- ``definition()``:
  this function must return the compex type definition for the datatype
  if there is no complex type you do not need to implement this method

- ``defaultValue()``:
  this function must return the default value for an attribute

- ``toXMLSchema()``:
  this function must return the XML Schema code corresponding
  to its XML Schema definition

- ``toXML()``:
  this function must return the XML code corresponding to the
  previously declared XML Schema markup

Useful examples
---------------

In order to know what is possible with datatype extensibility you can
read the source code (few lines of code) of the following datatypes:

- ezstring ``extension/ezxmlexport/classes/datatypes/ezstring/ezstringxmlexport.php``
- ezdate ``extension/ezxmlexport/classes/datatypes/ezdate/ezdatexmlexport.php``
- ezkeyword ``extension/ezxmlexport/classes/datatypes/ezkeyword/ezkeywordxmlexport.php``
- ezobjectrelationlist ``extension/ezxmlexport/classes/datatypes/ezobjectrelationlist/ezobjectrelationlistxmlexport.php``

Important points
================

ID and IDRef values
-------------------

Using the remote_id for an ID or IDRef attribute is a good idea
to get a unique object ID however the XML specification states that
the value of an ID(Ref) attribute must start with a letter.
This is why there is an 'id' prefix for ID and IDRef values as eZ
Publish's remote_id sometime starts with a number and not a letter.

FAQ
===

Is there any multithreading available ?
---------------------------------------
It was specified that this export should be multithreaded (forked, is more appropriate).
However once I implemented the code and tested I get more than 1000 generated
locks in MySQL in less than a second for a small export. This is why I decided
to remove all code related to multithreading as it simply kills the database.

Why are my content object not exported ?
----------------------------------------
Check that the related content class is defined as exportable.
If it is not then no content objects of this content class will
be exported.

Why is this extension so slow ?
-------------------------------
It may take 8 hours to export 250 000 objects.
This might happen if you have a lot of object relations as foreach
object relation the exporter will check if it is exportable and if yes
will export it as well. This requires heavy processes on the content node
tree.