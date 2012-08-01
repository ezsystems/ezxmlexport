<?php
/*
[ExportSettings]
Verbosity=disabled
# while exporting a huge number of
# content objects, the cronjob
# will use an offset to be able
# to export everything without
# crashing or crushing the server
# This value must be greater than 0
FetchLimit=250

PHPBinaryPath=/usr/bin/php

# ezxmltext fields can be exported as XHTML (default) or XML (recommended).
# It is recommended to set it to 'disabled' as it creates a more flexible
# output but is enabled by default for backward compatibility reasons.
UseXHTMLOutput=enabled

# ezxmltext fields can be encapsulated in a CDATA section while exported.
# This could however prevent an easy processing of the content and might
# therefor be disabled
UseCDATA=enabled

# If "enabled", values of file paths will contain the URL of the source
# website ( http://example.com/var/plain_site/... ) so you'll not have
# to copy files on your destination server. If "disabled" file paths will be
# relative paths from the root directory (var/plain_site/storage/...)
UseRemoteFiles=enabled

# Filter exports on a (number of) specified content state ID's
StateFilterList[]
#StateFilterList[]=7

[FTPSettings]
FTPShipment=disabled
DeleteSourceFileAfterShipment=enabled

[XSLTSettings]
XSLTTransformation=disabled
DeleteXMLSourceAfterXSLTTransformation=disabled

[CompressionSettings]
TarBinaryPath=/usr/bin/tar

*/ ?>
