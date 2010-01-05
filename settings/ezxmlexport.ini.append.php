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

[FTPSettings]
FTPShipment=disabled
DeleteSourceFileAfterShipment=enabled

[XSLTSettings]
XSLTTransformation=disabled
DeleteXMLSourceAfterXSLTTransformation=disabled

[CompressionSettings]
TarBinaryPath=/usr/bin/tar

*/ ?>