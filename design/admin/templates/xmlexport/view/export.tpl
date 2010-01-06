<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$export.name}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

    <div class="block">
        <label>{'Name'|i18n( 'design/admin/xmlexport' )}:</label>
        {$export.name|wash}
    </div>

    <div class="block">
        <label>{'Identifier'|i18n( 'design/admin/xmlexport' )}:</label>
        {$export.id|wash}
    </div>

    <div class="block">
        <label>{'FTP informations'|i18n( 'design/admin/xmlexport' )}:</label>
        {def $ftp_target = unserialize( $export.ftp_target )}

        {if count($ftp_target)|eq( 0 )}
            {'No FTP informations available, the export channel\'s ones will be used'|i18n( 'design/admin/xmlexport' )}
        {else}
        <table class="list" cellspacing="0">
            <tr>
                <th>{'Host'|i18n( 'design/admin/xmlexport' )}</th>
                <th>{'Port'|i18n( 'design/admin/xmlexport' )}</th>
                <th>{'Login'|i18n( 'design/admin/xmlexport' )}</th>
                <th>{'Password'|i18n( 'design/admin/xmlexport' )}</th>
                <th>{'Path'|i18n( 'design/admin/xmlexport' )}</th>
            </tr>
            <tr>
                <td>{$ftp_target.host|wash}</td>
                <td>{$ftp_target.port|wash}</td>
                <td>{$ftp_target.login|wash}</td>
                <td>xxxx</td>
                <td>{$ftp_target.path|wash}</td>
            </tr>
        </table>
        {/if}
    </div>

    <div class="block">
        <label>{'Slicing mode'|i18n( 'design/admin/xmlexport' )}:</label>
        {$export.slicing_mode|wash}
    </div>

    <div class="block">
        <label>{'Export schedule'|i18n( 'design/admin/xmlexport' )}:</label>
        {def $export_schedule = unserialize($export.export_schedule)}
        {if and( $export.start_date, $export.end_date )}
            {'From'|i18n( 'design/admin/xmlexport' )}
            {$export.start_date|l10n( 'shortdate' )}
            {'To'|i18n( 'design/admin/xmlexport' )}
            {$export.end_date|l10n( 'shortdate' )}
        {elseif and( $export.start_date, is_set( $export_schedule.schedule ) )}
            {'From'|i18n( 'design/admin/xmlexport' )}
            {$export.start_date|l10n( 'shortdate' )}
            {'Every'|i18n( 'design/admin/xmlexport' )}
            {$export_schedule.schedule.value} {$export_schedule.schedule.unit|i18n( 'design/admin/xmlexport' )}
        {/if}
        {undef $export_schedule}
    </div>

    <div class="block">
        <label>{'Limit of exportable objects'|i18n( 'design/admin/xmlexport' )}:</label>
        {$export.export_limit|wash}
    </div>

    <div class="block">
        <label>{'Compression'|i18n( 'design/admin/xmlexport' )}:</label>
        {$export.compression|wash|choose( 'Disabled', 'Enabled' )|i18n( 'design/admin/xmlexport' )}
    </div>

    <div class="block">
        <label>{'Related object handling'|i18n( 'design/admin/xmlexport' )}:</label>
        {$export.related_object_handling|wash|choose( 'Disabled', 'Enabled' )|i18n( 'design/admin/xmlexport' )}
    </div>
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

    <div class="block">
        <input class="button" type="button" value="{'Show content that will be exported'|i18n( 'design/admin/xmlexport' )}" name="TextContentListExportButton" onclick="javascript:window.location ={concat('xmlexport/test/contentlist/', $export.id)|ezurl( 'single', 'full' )}"/>
        {*<input class="button" type="button" value="{'Realtime export'|i18n( 'design/admin/xmlexport' )}" name="TestRealTimeExportButton"onclick="javascript:window.location ={concat('xmlexport/test/realtime/', $export.id)|ezurl( 'single', 'full' )}"/>*}
        <input class="button" type="button" value="{'Back to this export channel'|i18n( 'design/admin/xmlexport' )}" name="TextContentBackCutomerButton" onclick="javascript:window.location ={concat('xmlexport/view/customer/', $export.customer_id)|ezurl( 'single', 'full' )}"/>
    </div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>