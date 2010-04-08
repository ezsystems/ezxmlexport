{* Warnings *}
{if $errorMessageList}
    <div class="message-warning">
        <h2>
            <span class="time">[{currentdate()|l10n( shortdatetime )}]</span>
        {'The export definition could not be stored.'|i18n( 'design/admin/xmlexport' )}
        </h2>
        <p>{'The following information is either missing or invalid'|i18n( 'design/admin/class/edit' )}:</p>

        <ul>
            {foreach $errorMessageList as $errorMessage}
                <li>{$errorMessage}</li>
            {/foreach}
        </ul>
    </div>
{/if}

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Add an export'|i18n('design/admin/xmlexport')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{def $formAction = 'xmlexport/edit/'}
{foreach $view_parameters as $key => $value}
    {set $formAction = concat( $formAction, '(', $key, ')/', $value, '/' )}
{/foreach}

<div class="context-attributes">
    <form id="xmlexportaddexport" action="{$formAction|ezurl('no', 'full')}" method="post" enctype="multipart/form-data">

        {* hope this should not be so heavy, will find another way to do the job otherwise *}
        {if is_set( $selected_node_id_list )}
            <ul>
            {foreach $selected_node_id_list as $selected_node_id}
                {def $content_node = fetch( 'content', 'node', hash( 'node_id', $selected_node_id ) )}
                    <input type="hidden" name="ExportSources[]" value="{$selected_node_id}"/>
                    <li>{$content_node.name|wash}</li>
                {undef $content_node}
            {/foreach}
            </ul>
        {/if}
        <div class="block">
            <label>{'Choose contents to export'|i18n('design/admin/ezxmlexport')}</label>
            <input type="submit" name="BrowseExportNodes" value="{'Choose contents'|i18n('design/admin/xmlexport')}" class="button"/><br/>
        </div>

        {def $customer_id = ''}
        {if and( is_set( $view_parameters.customer ), $view_parameters.customer|gt( 0 ) )}
            {set $customer_id = $view_parameters.customer}
            <input type="hidden" value="{$view_parameters.customer}" name="ExportCustomerID"/>
        {elseif and( is_set( $ExportCustomerIDValue ), $ExportCustomerIDValue|gt( 0 ) )}
            {set $customer_id = $ExportCustomerIDValue}
            <input type="hidden" value="{$ExportCustomerIDValue}" name="ExportCustomerID"/>
        {else}
            {def $customerList = fetch( 'xmlexport', 'customers' )}
            <div class="block">
                    <label>{'Choose a customer'|i18n('design/admin/xmlexport')}</label>
                    <select name="ExportCustomerID">
                        <option value="-1">{'Choose a customer below'|i18n('design/admin/xmlexport')}</option>
                        {foreach $customerList as $customer}
                            {if eq($customer.id, $ExportCustomerIDValue)}
                                <option value="{$customer.id|wash}" selected="selected">{$customer.name|wash}</option>
                            {else}
                                <option value="{$customer.id|wash}">{$customer.name|wash}</option>
                            {/if}
                        {/foreach}
                    </select>
            </div>
        {/if}

        <div class="block">
            <label>{'Name'|i18n('design/admin/xmlexport')}</label> <input type="text" name="ExportName" value="{$ExportNameValue|wash}"/>
        </div>

        <div class="block">
            <label>{'Description'|i18n('design/admin/xmlexport')}</label> <input type="text" name="ExportDescription" value="{$ExportDescriptionValue|wash}"/>
        </div>

        {include uri="design:xmlexport/ftpfield.tpl"}

        <div class="block">
            <label>{'XSLT file to use'|i18n('design/admin/xmlexport')}</label>
            {if ezini( 'XSLTSettings', 'XSLTTransformation', 'ezxmlexport.ini' )|eq( 'enabled' )}
                <select name="ExportXSLTFile">
                    <option value="-1">{'Choose an option below'|i18n('design/admin/xmlexport')}</option>
                    {def $xslt_file_list = fetch( 'xmlexport', 'xsltfiles' )}
                    {foreach $xslt_file_list as $xslt_file}
                        {if eq($xslt_file, $ExportXSLTFileValue)}
                            <option value="{$xslt_file}" selected="selected">{$xslt_file|wash}</option>
                        {else}
                            <option value="{$xslt_file}">{$xslt_file|wash}</option>
                        {/if}
                    {/foreach}
                </select>
            {else}
                {'XSLT tranformations are disabled, you can enable them in ezxmlexport.ini if you want'|i18n( 'design/admin/xmlexport' )}
            {/if}
        </div>

        <div class="block">
            <label>{'Slicing mode'|i18n('design/admin/xmlexport')}</label>
            <select name="ExportSlicingMode">
                <option value="-1">{'Choose an option below'|i18n('design/admin/xmlexport')}</option>
                {def $possibleValueList = array( 1, 'n' )}
                {foreach $possibleValueList as $possibleValue}
                    {if eq($possibleValue, $ExportSlicingModeValue)}
                        <option value="{$possibleValue}" selected="selected">{$possibleValue}</option>
                    {else}
                        <option value="{$possibleValue}">{$possibleValue}</option>
                    {/if}
                {/foreach}
                {undef $possibleValueList}
            </select>
        </div>

        <div class="block">
            <label>{'Enable compression'|i18n('design/admin/xmlexport')}</label>
            <input type="checkbox" name="ExportCompression" {$ExportCompressionValue} value="enabled"/><br/>
        </div>

        <div class="block">
            <label>{'Export hidden nodes'|i18n('design/admin/xmlexport')}</label>
            <input type="checkbox" name="ExportHiddenNodes" {$ExportHiddenNodesValue} value="enabled"/><br/>
        </div>

        <div class="block">
            <label>{'Related object handling'|i18n('design/admin/xmlexport')}</label>
            <select name="ExportRelatedObjectHandling">
                <option value="-1">{'Choose an option below'|i18n('design/admin/xmlexport')}</option>
                {def $possibleValueList = array( 1, '2' )}
                {foreach $possibleValueList as $possibleValue}
                    {if eq($possibleValue, $ExportRelatedObjectHandlingValue)}
                        <option value="{$possibleValue}" selected="selected">{$possibleValue}</option>
                    {else}
                        <option value="{$possibleValue}">{$possibleValue}</option>
                    {/if}
                {/foreach}
                {undef $possibleValueList}
            </select>
        </div>

        <div class="block">
            <label>{'Start date'|i18n('design/admin/ezxmlexport')}</label>
            <input type="text" name="ExportStartDate" value="{$ExportStartDateValue|wash}" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)"/><br/>
        </div>

        <div class="block">
            <label>{'End date'|i18n('design/admin/ezxmlexport')}</label>
            <input type="text" name="ExportEndDate" value="{$ExportEndDateValue|wash}" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)"/>
            {'Leave the field empty if you plan to use recurrence'|i18n('design/admin/ezxmlexport')}
        </div>

        <div class="block">
            <label>{'Every'|i18n('design/admin/ezxmlexport')}</label><input type="text" name="ExportRecurrenceValue" value="{$ExportRecurrenceValueValue}" size="3"/>
            <select name="ExportRecurrenceUnit">
                <option value="-1">{'Choose an option below'|i18n('design/admin/ezxmlexport')}</option>
                {def $possibleValueList = array( 'day', 'week', 'month' )}
                {foreach $possibleValueList as $possibleValue}
                    {if eq( $possibleValue, $ExportRecurrenceUnitValue )}
                        <option value="{$possibleValue}" selected="selected">{$possibleValue|upfirst|i18n('design/admin/ezxmlexport')|wash}</option>
                    {else}
                        <option value="{$possibleValue}">{$possibleValue|upfirst|i18n('design/admin/ezxmlexport')|wash}</option>
                    {/if}
                {/foreach}
                {undef $possibleValueList}
            </select>
        </div>

        <div class="block">
            <label>{'Only export'|i18n('design/admin/ezxmlexport')}</label> <input type="text" name="ExportObjectNumberLimit" value="{$ExportObjectNumberLimitValue|wash}" size="3"/>
            {'content objects for this export, starting from their publication date'|i18n('design/admin/ezxmlexport')}
            <br/>
            <input type="checkbox" name="ExportAllObjectsFromLastExport" {$ExportAllObjectsFromLastExportValue} value="1"/>
            {'or export all content object from the last export'|i18n('design/admin/ezxmlexport')}
        </div>

        <input type="submit" name="PublishExportButton" value="{'OK'|i18n('design/admin/class/edit')}" class="button"/>
        <input type="reset" name="ResetExportButton" value="{'Reset'|i18n('design/admin/class/edit')}" class="button"/>
        <input type="button" name="CancelExportButton" value="{'Cancel'|i18n('design/admin/class/edit')}" class="button" onclick="javascript:window.location = {concat( 'xmlexport/view/customer/', $customer_id )|ezurl( 'single', 'full' )}"/>
    </form>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>