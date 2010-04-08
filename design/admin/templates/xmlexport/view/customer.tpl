<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$customer.name}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<form name="ExportListForm" action="{'xmlexport/delete/'|ezurl( 'no', 'full' )}" method="post">

    <div class="context-attributes">

        <div class="block">
            <label>{'Name'|i18n( 'design/admin/xmlexport' )}:</label>
            {$customer.name|wash}
        </div>

        <div class="block">
            <label>{'Identifier'|i18n( 'design/admin/xmlexport' )}:</label>
            {$customer.id|wash}
        </div>

        <div class="block">
            {def $ftp_target = unserialize( $customer.ftp_target )}

            <label>{'FTP informations'|i18n( 'design/admin/xmlexport' )}:</label>

            {if count($ftp_target)|eq( 0 )}
                {'No FTP informations available'|i18n( 'design/admin/xmlexport' )}
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
            {$customer.slicing_mode|wash}
        </div>

        <hr/>

        <h3>{'Exports for this export channel'|i18n('design/admin/xmlexport')}</h3>

        {def $exportList = fetch( 'xmlexport', 'exports', hash( 'customer_id', $customer.id ) )}

        <table class="list" cellspacing="0">
            <tr>
                <th class="remove">
                    <img onclick="ezjs_toggleCheckboxes( document.ExportListForm, 'DeleteIDArray[]' ); return false;" title="Invert selection." alt="Invert selection." src="{'toggle-button-16x16.gif'|ezimage( 'no' )}"/>
                </th>
                <th>{'Export ID'|i18n('design/admin/ezxmlexport')}</th>
                <th>{'Name'|i18n('design/admin/ezxmlexport')}</th>
                <th>{'FTP'|i18n('design/admin/ezxmlexport')}</th>
                <th>{'Slicing mode'|i18n('design/admin/ezxmlexport')}</th>
                <th>{'Action'|i18n('design/admin/ezxmlexport')}</th>
            </tr>

            {set $ftp_target = ''}
            {def $view_link  = ''}
            {def $edit_link  = ''}
            {foreach $exportList as $export sequence array( bglight, bgdark ) as $tr_class}
                {set $ftp_target = unserialize( $export.ftp_target )}
                {set $view_link  = concat( 'xmlexport/view/export/', $export.id)|ezurl('no', 'full')}
                {set $edit_link = concat( 'xmlexport/edit/(type)/export/(export)/', $export.id)|ezurl('no', 'full')}
                <tr class={$tr_class}>
                    <td>
                        <input type="checkbox" value="{$export.id|wash( xhtml )}" name="DeleteIDArray[]"/>
                    </td>
                    <td><a href="{$view_link}">{$export.id|wash( xhtml )}</a></td>
                    <td><a href="{$view_link}">{$export.name|wash( xhtml )}</a></td>
                    {if is_set( $ftp_target.host )}
                        <td>ftp://{$ftp_target.login|wash( xhtml )}:xxx@{$ftp_target.host|wash( xhtml )}:{$ftp_target.port|wash( xhtml )}{$ftp_target.path|wash( xhtml )}</td>
                    {else}
                        <td>&nbsp;</td>
                    {/if}
                    <td>{$export.slicing_mode|wash( xhtml )}</td>
                    <td>
                        [<a href="{$view_link}">{'View'|i18n( 'design/admin/xmlexport' )}</a>]&nbsp;
                        [<a href="{$edit_link}">{'Edit'|i18n( 'design/admin/xmlexport' )}</a>]
                    </td>
                </tr>
            {/foreach}
            {undef $ftp_target $view_link}
        </table>

    </div>

    {* DESIGN: Content END *}</div></div></div>

    <div class="controlbar">
    {* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

        <div class="block">
            <input type="hidden" name="RedirectURI" value="{concat( 'xmlexport/view/customer/', $customer.id)}"/>
            <input class="button" type="submit" value="Remove selected" name="DeleteExportButton"/>
            <input class="button" type="button" value="{'Create an export for this channel'|i18n( 'design/admin/xmlexport' )}" name="AddExportButton" onclick="javascript:window.location ={concat( 'xmlexport/edit/(type)/export/(customer)/', $customer.id)|ezurl( 'single', 'full' )}"/>
            <input class="button" type="button" value="{'Back to channel list'|i18n( 'design/admin/xmlexport' )}" name="BackCustomerListButton"onclick="javascript:window.location ={'xmlexport/menu/'|ezurl( 'single', 'full' )}"/>
        </div>

    {* DESIGN: Control bar END *}</div></div></div></div></div></div>
    </div>

</form>

</div>