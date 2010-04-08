<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Xml export'|i18n('design/admin/xmlexport')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<form name="CustomerListForm" action="{'xmlexport/delete/'|ezurl( 'no', 'full' )}" method="post">

    <div class="context-attributes">

        <ul>
            <li><a href="{'xmlexport/runningexports'|ezurl('no', 'full')}">{'Show running export'|i18n('design/admin/xmlexport')}</a></li>
            <li><a href="{'xmlexport/edit/(type)/customer/'|ezurl('no', 'full')}">{'Add an export channel'|i18n('design/admin/xmlexport')}</a></li>
        </ul>

        <h3>{'Export channel list'|i18n('design/admin/xmlexport')}</h3>
        {def $customerList = fetch( 'xmlexport', 'customers' )}
        <table class="list" cellspacing="0">
            <tr>
                <th class="remove">
                    <img onclick="ezjs_toggleCheckboxes( document.CustomerListForm, 'DeleteIDArray[]' ); return false;" title="Invert selection." alt="Invert selection." src="{'toggle-button-16x16.gif'|ezimage( 'no' )}"/>
                </th>
                <th>{'Export channel ID'|i18n('design/admin/ezxmlexport')}</th>
                <th>{'Name'|i18n('design/admin/ezxmlexport')}</th>
                <th>{'FTP'|i18n('design/admin/ezxmlexport')}</th>
                <th>{'Slicing mode'|i18n('design/admin/ezxmlexport')}</th>
                <th>{'Action'|i18n('design/admin/ezxmlexport')}</th>
            </tr>
            {def $ftp_target = ''}
            {def $view_link = ''}
            {def $edit_link = ''}
            {foreach $customerList as $customer sequence array( bglight, bgdark ) as $tr_class}
                {set $ftp_target = unserialize( $customer.ftp_target )}
                {set $view_link = concat( 'xmlexport/view/customer/', $customer.id)|ezurl('no', 'full')}
                {set $edit_link = concat( 'xmlexport/edit/(type)/customer/(customer)/', $customer.id)|ezurl('no', 'full')}
                <tr class={$tr_class}>
                    <td>
                        <input type="checkbox" value="{$customer.id|wash( xhtml )}" name="DeleteIDArray[]"/>
                    </td>
                    <td><a href="{$view_link}">{$customer.id|wash( xhtml )}</a></td>
                    <td><a href="{$view_link}">{$customer.name|wash( xhtml )}</a></td>
                    {if is_set( $ftp_target.host )}
                        <td>ftp://{$ftp_target.login|wash( xhtml )}:xxx@{$ftp_target.host|wash( xhtml )}:{$ftp_target.port|wash( xhtml )}{$ftp_target.path|wash( xhtml )}</td>
                    {else}
                        <td>{'No FTP informations available'|i18n( 'design/admin/xmlexport' )}</td>
                    {/if}
                    <td>{$customer.slicing_mode|wash( xhtml )}</td>
                    <td>
                        [<a href="{$view_link}">{'View'|i18n( 'design/admin/xmlexport' )}</a>]&nbsp;
                        [<a href="{$edit_link}">{'Edit'|i18n( 'design/admin/xmlexport' )}</a>]
                    </td>
                </tr>
            {/foreach}
            {undef $ftp_target $view_link $edit_link}
        </table>

    </div>
    {* DESIGN: Content END *}</div></div></div>

    <div class="controlbar">
    {* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

        <div class="block">
            <input type="hidden" name="RedirectURI" value="xmlexport/menu"/>
            <input class="button" type="submit" value="Remove selected" name="DeleteCustomerButton"/>
        </div>

    {* DESIGN: Control bar END *}</div></div></div></div></div></div>
    </div>

</form>

</div>