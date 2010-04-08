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

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Add an export channel'|i18n('design/admin/xmlexport')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{def $formAction = 'xmlexport/edit/'}
{if is_set( $view_parameters.type )}
    {set $formAction = concat( $formAction, '(type)/', $view_parameters.type, '/' )}
{/if}
{if and( is_set( $view_parameters.customer ), $view_parameters.customer|gt( 0 ))}
    {set $formAction = concat( $formAction, '(customer)/', $view_parameters.customer, '/' )}
{/if}

<div class="context-attributes">
    <form id="xmlexportaddcustomer" action="{$formAction|ezurl('no', 'full')}" method="post" enctype="multipart/form-data">

        <div class="block">
            <label>{'Name'|i18n('design/admin/xmlexport')}</label> <input type="text" name="CustomerName" value="{$CustomerNameValue|wash}"/><br/>
        </div>

        {include uri="design:xmlexport/ftpfield.tpl"}

        <div class="block">
            <label>{'Slicing mode'|i18n('design/admin/xmlexport')}</label>
            <select name="CustomerSlicingMode">
                <option value="-1">{'Choose an option below'|i18n('design/admin/xmlexport')}</option>
                {def $possibleValueList = array( 1, 'n' )}
                {foreach $possibleValueList as $possibleValue}
                    {if eq($possibleValue, $CustomerSlicingModeValue)}
                        <option value="{$possibleValue}" selected="selected">{$possibleValue|wash}</option>
                    {else}
                        <option value="{$possibleValue}">{$possibleValue|wash}</option>
                    {/if}
                {/foreach}
                {undef $possibleValueList}
            </select>
        </div>

        <input type="submit" name="PublishCustomerButton" value="{'OK'|i18n('design/admin/class/edit')}" class="button"/>
        <input type="reset" name="ResetCustomerButton" value="{'Reset'|i18n('design/admin/class/edit')}" class="button"/>
        <input type="button" name="CancelCustomerButton" value="{'Cancel'|i18n('design/admin/class/edit')}" class="button" onclick="javascript:window.location = {'xmlexport/menu/'|ezurl( 'single', 'full' )}"/>
    </form>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>