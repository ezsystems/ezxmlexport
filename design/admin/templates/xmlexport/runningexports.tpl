<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Running exports'|i18n('design/admin/xmlexport')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<form name="runing-export-forms" action="{'xmlexport/relaunchfttransfert'|ezurl( 'no', 'full' )}" method="post">
    <div class="context-attributes">
        <h3>{'Running exports'|i18n('design/admin/xmlexport')}</h3>
            <table class="list" cellspacing="0">
                <tr>
                    <th>{'Relaunch FTP transfert'|i18n('design/admin/ezxmlexport')}</th>
                    <th>{'Export ID'|i18n('design/admin/ezxmlexport')}</th>
                    <th>{'Name'|i18n('design/admin/ezxmlexport')}</th>
                    <th>{'FTP'|i18n('design/admin/ezxmlexport')}</th>
                    <th>{'Started at'|i18n('design/admin/ezxmlexport')}</th>
                    <th>{'Status'|i18n('design/admin/ezxmlexport')}</th>
                </tr>

                {foreach $exportList as $export sequence array( bglight, bgdark ) as $tr_class}
                    {def $ftp_target = unserialize( $export.ftp_target )}
                    <tr class={$tr_class}>
                        <td>
                            {if eq($export.status, 32)}
                                <input type="checkbox" value="{$export.id|wash( xhtml )}" name="SelectedExportIDArray[]"/>
                            {/if}
                        </td>
                        <td>{$export.id|wash( xhtml )}</td>
                        <td>{$export.name|wash( xhtml )}</td>
                        <td>ftp://
                            {if is_set( $ftp_target.host )}
                                {$ftp_target.login|wash( xhtml )}:xxxx@{$ftp_target.host}:{$ftp_target.path}
                            {/if}
                        </td>
                        <td>{$export.start_date|l10n( 'shortdate' )}</td>
                        <td>{$export.status}</td>
                    </tr>
                    {undef $ftp_target}
                {/foreach}

            </table>
    </div>
    {* DESIGN: Content END *}</div></div></div>

    <div class="controlbar">
    {* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

        <div class="block">
            <input class="button" type="submit" value="{'Relaunch FTP transfert'|i18n('design/admin/ezxmlexport')}" name="RelaunchFTPTransfertButton"/>
        </div>

    {* DESIGN: Control bar END *}</div></div></div></div></div></div>
    </div>
</form>

</div>
