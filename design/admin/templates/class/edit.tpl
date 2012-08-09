<script type="text/javascript">
    // Namespace
    var ezxmlexport = window.ezxmlexport || {ldelim}{rdelim};
    ezxmlexport.contentClassAttributeIDList = ezxmlexport.contentClassAttributeIDList || [];

    ezxmlexport.serverRoot = {'/'|ezurl( 'single', 'full' )};

    YAHOO.util.Event.addListener( "eZXMLExportStoreButton"  , "click", storeAvailableContentClassAttributesForExport );
    YAHOO.util.Event.addListener( "eZXMLExportApplyButton"  , "click", storeAvailableContentClassAttributesForExport );

    {foreach $attributes as $attribute}
        ezxmlexport.contentClassAttributeIDList.push( {$attribute.id} );
    {/foreach}
</script>
{* Warnings *}

{def $class_xml_availability = fetch( 'xmlexport', 'class', hash( 'class_id',  $class.id ) )}
{def $class_available_for_xml_export = false()}

{if $class_xml_availability}
    {set $class_available_for_xml_export = true()}
{/if}

{def $attribute_xml_availability = array()}
{foreach $class_xml_availability as $item}
    {set $attribute_xml_availability = $attribute_xml_availability|append( $item.contentclass_attribute_id )}
{/foreach}

{section show=$validation.processed}
{* handle attribute validation errors *}
{section show=$validation.attributes}
<div class="message-warning">
<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The class definition could not be stored.'|i18n( 'design/admin/class/edit' )}</h2>
<p>{'The following information is either missing or invalid'|i18n( 'design/admin/class/edit' )}:</p>
<ul>
    {section var=UnvalidatedAttributes loop=$validation.attributes}
    {section show=is_set( $UnvalidatedAttributes.item.reason )}
        <li>attribute '{$UnvalidatedAttributes.item.identifier}': ({$UnvalidatedAttributes.item.id})
            {$UnvalidatedAttributes.item.reason.text|wash}
        <ul>
        {section var=subitem loop=$UnvalidatedAttributes.item.reason.list}
            <li>{section show=is_set( $subitem.identifier )}{$subitem.identifier|wash}: {/section}{$subitem.text|wash}</li>
        {/section}
        </ul>
        </li>
    {section-else}
        <li>attribute '{$UnvalidatedAttributes.item.identifier}': {$UnvalidatedAttributes.item.name|wash} ({$UnvalidatedAttributes.item.id})</li>
    {/section}
    {/section}
</ul>
</div>
{section-else}
{* no attribute validation errors *}
<div class="message-feedback">
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The class definition was successfully stored.'|i18n( 'design/admin/class/edit' )}</h2>
</div>
{/section}

{section-else} {* !$validation|processed *}
{* we're about to store the class, so let's handle basic class properties errors (name, identifier, presence of attributes) *}
    {section show=or( $validation.class_errors )}
    <div class="message-warning">
    <h2>{"The class definition contains the following errors"|i18n("design/admin/class/edit")}:</h2>
    <ul>
    {section var=ClassErrors loop=$validation.class_errors}
        <li>{$ClassErrors.item.text}</li>
    {/section}
    </ul>
    </div>
    {/section}
{/section}

{* Main window *}
<form action={concat( $module.functions.edit.uri, '/', $class.id, '/(language)/', $language_code )|ezurl} method="post" id="ClassEditForm" name="ClassEdit">
<input type="hidden" name="ContentClassHasInput" value="1" />

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$class.identifier|class_icon( 'normal', $class.name|wash )}&nbsp;{'Edit <%class_name> [Class]'|i18n( 'design/admin/class/edit',, hash( '%class_name', $class.nameList[$language_code] ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-information">
<p class="modified">{'Last modified'|i18n( 'design/admin/class/edit' )}:&nbsp;{$class.modified|l10n( shortdatetime )},&nbsp;{$class.modifier.contentobject.name|wash}</p>
{def $locale = fetch( 'content', 'locale', hash( 'locale_code', $language_code ) )}
<p class="translation">{$locale.intl_language_name}&nbsp;<img src="{$language_code|flag_icon}" alt="{$language_code}" style="vertical-align: middle;" /></p>
{undef $locale}
</div>

<div class="context-attributes">

    {* Name. *}
    <div class="block">
    <label>{'Name'|i18n( 'design/admin/class/edit' )}:</label>
    <input class="box" type="text" id="className" name="ContentClass_name" size="30" value="{$class.nameList[$language_code]|wash}" title="{'Use this field to set the informal name of the class. The name field can contain whitespaces and special characters.'|i18n( 'design/admin/class/edit' )}" />
    </div>

    {* Identifier. *}
    <div class="block">
    <label>{'Identifier'|i18n( 'design/admin/class/edit' )}:</label>
    <input class="box" type="text" name="ContentClass_identifier" size="30" value="{$class.identifier|wash}" title="{'Use this field to set the internal name of the class. The identifier will be used in templates and in PHP code. Allowed characters are letters, numbers and underscores.'|i18n( 'design/admin/class/edit' )}" maxlength="50" />
    </div>

    {* Object name pattern. *}
    <div class="block">
    <label>{'Object name pattern'|i18n( 'design/admin/class/edit' )}:</label>
    <input class="box" type="text" name="ContentClass_contentobject_name" size="30" value="{$class.contentobject_name|wash}" title="{'Use this field to configure how the name of the objects are generated. Type in the identifiers of the attributes that should be used. The identifiers must be enclosed in angle brackets. Text outside angle brackets will be included as it is shown here.'|i18n( 'design/admin/class/edit' )}" />
    </div>

    {* URL alias name pattern. *}
    <div class="block">
    <label>{'URL alias name pattern'|i18n( 'design/admin/class/edit' )}:</label>
    <input class="box" type="text" name="ContentClass_url_alias_name" size="30" value="{$class.url_alias_name|wash}" title="{'Use this field to configure how the url alias of the objects are generated (applies to nice URLs). Type in the identifiers of the attributes that should be used. The identifiers must be enclosed in angle brackets. Text outside angle brackets will be included as is.'|i18n( 'design/admin/class/edit' )}" />
    </div>

    {* Container. *}
    <div class="block">
    <label>{'Container'|i18n( 'design/admin/class/edit' )}:</label>
    <input type="hidden" name="ContentClass_is_container_exists" value="1" />
    <input type="checkbox" name="ContentClass_is_container_checked" value="{$class.is_container}" {section show=$class.is_container|eq( 1 )}checked="checked"{/section} title="{'Use this checkbox to allow instances of the class to have sub items. If checked, it will be possible to create new sub items. If not checked, the sub items will not be displayed.'|i18n( 'design/admin/class/edit' )}" />
    </div>

    {* Class Default Sorting *}
    <div class="block">
    <label>{'Default sorting of children'|i18n( 'design/admin/class/edit' )}:</label>
    {def $sort_fields=fetch( content, available_sort_fields )
         $title='Use these controls to set the default sorting method for the sub items of instances of the content class.'|i18n( 'design/admin/class/edit' ) }
    <input type="hidden" name="ContentClass_default_sorting_exists" value="1" />
    <select name="ContentClass_default_sorting_field" title="{$title}">
    {foreach $sort_fields as $sf_key => $sf_item}
        <option value="{$sf_key}" {if eq( $sf_key, $class.sort_field )}selected="selected"{/if}>{$sf_item}</option>
    {/foreach}
    </select>
    <select name="ContentClass_default_sorting_order" title="{$title}">
        <option value="0"{if eq($class.sort_order, 0)} selected="selected"{/if}>{'Descending'|i18n( 'design/admin/class/edit' )}</option>
        <option value="1"{if eq($class.sort_order, 1)} selected="selected"{/if}>{'Ascending'|i18n( 'design/admin/class/edit' )}</option>
    </select>
    </div>

    {* Object availablility. *}
    <div class="block">
    <label>{'Default object availability'|i18n( 'design/standard/class/edit' )}:</label>
    <input type="hidden" name="ContentClass_always_available_exists" value="1" />
    <input type="checkbox" name="ContentClass_always_available"{if $class.always_available|eq(1)} checked="checked"{/if} title="{'Use this checkbox to set the default availability for the objects of this class. The availability controls whether an object should be shown even if it does not exist in one of the languages specified by the "SiteLanguageList" setting. If this is the case, the system will use the main language of the object.'|i18n( 'design/admin/class/edit' )|wash}" />
    </div>

    {* XML export availability *}
    <div class="block">
    <label>{'Class available for XML export'|i18n( 'design/admin/xmlexport' )}:</label>
    <input type="checkbox" name="ContentClass_available_xml_export" id="ContentClassAvailableForXMLExport" {if $class_available_for_xml_export}checked="checked"{/if} title="{'Use this checkbox to set this class as available for XML export.'|i18n( 'design/admin/class/edit' )|wash}" />
    </div>

<div class="block">
<label>{'Class attributes'|i18n( 'design/admin/class/edit' )}:</label>
</div>
{section show=$attributes}

<table class="list" cellspacing="0">
{section var=Attributes loop=$attributes}

<tr>
    <th class="tight"><input type="checkbox" name="ContentAttribute_id_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}" title="{'Select attribute for removal. Click the "Remove selected attributes" button to remove the selected attributes.'|i18n( 'design/admin/class/edit' )|wash}" /></th>
    <th class="wide">{$Attributes.number}. {$Attributes.item.name|wash} [{$Attributes.item.data_type.information.name|wash}] (id:{$Attributes.item.id})</th>
    <th class="tight">
      <div class="listbutton">
          <input type="image" src={'button-move_down.gif'|ezimage} alt="{'Down'|i18n( 'design/admin/class/edit' )}" name="MoveDown_{$Attributes.item.id}" title="{'Use the order buttons to set the order of the class attributes. The up arrow moves the attribute one place up. The down arrow moves the attribute one place down.'|i18n( 'design/admin/class/edit' )}" />&nbsp;
          <input type="image" src={'button-move_up.gif'|ezimage} alt="{'Up'|i18n( 'design/admin/class/edit' )}" name="MoveUp_{$Attributes.item.id}" title="{'Use the order buttons to set the order of the class attributes. The up arrow moves the attribute one place up. The down arrow moves the attribute one place down.'|i18n( 'design/admin/class/edit' )}" />
<input size="2" type="text" name="ContentAttribute_priority[{$Attributes.item.id}]" value="{$Attributes.number}" />
      </div>
    </th>
</tr>

<tr>
<td>&nbsp;</td>
<!-- Attribute input Start -->
<td colspan="2">
<input type="hidden" name="ContentAttribute_id[{$Attributes.item.id}]" value="{$Attributes.item.id}" />
<input type="hidden" name="ContentAttribute_position[{$Attributes.item.id}]" value="{$Attributes.item.placement}" />


{* Attribute name. *}
<div class="block">
<label>{'Name'|i18n( 'design/admin/class/edit' )}:</label>
<input class="box" type="text" name="ContentAttribute_name[{$Attributes.item.id}]" value="{$Attributes.item.nameList[$language_code]|wash}" title="{'Use this field to set the informal name of the attribute. This field can contain whitespaces and special characters.'|i18n( 'design/admin/class/edit' )}" />
</div>

{* Attribute identifier. *}
<div class="block">
<label>{'Identifier'|i18n( 'design/admin/class/edit' )}:</label>
<input class="box" type="text" name="ContentAttribute_identifier[{$Attributes.item.id}]" value="{$Attributes.item.identifier}" title="{'Use this field to set the internal name of the attribute. The identifier will be used in templates and in PHP code. Allowed characters are letters, numbers and underscores.'|i18n( 'design/admin/class/edit' )}" maxlength="50" />
</div>

<!-- Attribute input End -->

<!-- Attribute flags Start -->
<div class="block inline">

{* Required. *}
<label>
<input type="checkbox" name="ContentAttribute_is_required_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}"  {section show=$Attributes.item.is_required}checked="checked"{/section} title="{'Use this checkbox to specify whether the user should be forced to enter information into the attribute.'|i18n( 'design/admin/class/edit' )}" />
{'Required'|i18n( 'design/admin/class/edit' )}
</label>

{* Searchable. *}

<label>
{section show=$Attributes.item.data_type.is_indexable}
<input type="checkbox" name="ContentAttribute_is_searchable_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}"  {section show=$Attributes.item.is_searchable}checked="checked"{/section} title="{'Use this checkbox to specify whether the contents of the attribute should be indexed by the search engine.'|i18n( 'design/admin/class/edit' )}" />
{section-else}
<input type="checkbox" name="ContentAttribute_is_searchable_checked[{$Attributes.item.id}]" value="" disabled="disabled" title="{'The <%datatype_name> datatype does not support search indexing.'|i18n( 'design/admin/class/edit',, hash( '%datatype_name', $Attributes.item.data_type.information.name ) )|wash}" />
{/section}
{'Searchable'|i18n( 'design/admin/class/edit' )}
</label>

{* Information collector. *}
<label>
{section show=$Attributes.item.data_type.is_information_collector}
<input type="checkbox" name="ContentAttribute_is_information_collector_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}"  {section show=$Attributes.item.is_information_collector}checked="checked"{/section} title="{'Use this checkbox to specify whether the attribute should collect input from users.'|i18n( 'design/admin/class/edit' )}" />
{section-else}
<input type="checkbox" name="ContentAttribute_is_information_collector_checked[{$Attributes.item.id}]" value="" disabled="disabled" title="{'The <%datatype_name> datatype cannot be used as an information collector.'|i18n( 'design/admin/class/edit',, hash( '%datatype_name', $Attributes.item.data_type.information.name ) )|wash}" />
{/section}
{'Information collector'|i18n( 'design/admin/class/edit' )}
</label>


{* Disable translation. *}
<label>
<input type="checkbox" name="ContentAttribute_can_translate_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}" {section show=$Attributes.item.can_translate|eq(0)}checked="checked"{/section} title="{'Use this checkbox for attributes that contain non-translatable content.'|i18n( 'design/admin/class/edit' )}" />
{'Disable translation'|i18n( 'design/admin/class/edit' )}
</label>

{* Available for XML export *}
<label>
<input type="checkbox" name="ContentAttribute_available_xml_export[{$Attributes.item.id}]" id="ContentAttribute_available_xml_export_{$Attributes.item.id}" value="{$Attributes.item.id}" {section show=$Attributes.item.can_translate|eq(0)}checked="checked"{/section} title="{'Use this checkbox to set this attribute available for XML export.'|i18n( 'design/admin/xmlexport' )}" {if $attribute_xml_availability|contains($Attributes.item.id)}checked="checked"{/if} />
{'Available for XML export'|i18n( 'design/admin/xmlexport' )}
</label>

</div>

{class_attribute_edit_gui class_attribute=$Attributes.item}

</td>
<!-- Attribute flags End -->

</tr>

{/section}

</table>

{section-else}

<div class="block">
<p>{'This class does not have any attributes.'|i18n( 'design/admin/class/edit' )}</p>
</div>
{/section}




<div class="controlbar">

{* Remove selected attributes button *}
<div class="block">
{section show=$attributes}
<input class="button" type="submit" name="RemoveButton" value="{'Remove selected attributes'|i18n( 'design/admin/class/edit' )}" title="{'Remove the selected attributes.'|i18n( 'design/admin/class/edit' )}" />
{section-else}
<input class="button-disabled" type="submit" name="RemoveButton" value="{'Remove selected attributes'|i18n( 'design/admin/class/edit' )}" title="{'Remove the selected attributes.'|i18n( 'design/admin/class/edit' )}" disabled="disabled" />
{/section}
</div>

<div class="block">
{include uri="design:class/datatypes.tpl" name=DataTypes id_name=DataTypeString datatypes=$datatypes current=$datatype}
<input class="button" type="submit" name="NewButton" value="{'Add attribute'|i18n( 'design/admin/class/edit' )}" title="{'Add a new attribute to the class. Use the menu on the left to select the attribute type.'|i18n( 'design/admin/class/edit' )}" />
</div>

</div>

</div>

{* DESIGN: Content END *}</div></div></div>


<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
    <div class="block">
        <input type="hidden" id="eZXMLExportContentClassID" value="{$class.id}"/>
        <div id="eZXMLExportStoreButton" style="display:inline;">
            <input class="button" type="submit" name="StoreButton"   value="{'OK'|i18n( 'design/admin/class/edit' )}" title="{'Store changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )}" />
        </div>
    {section show=eq( ezini( 'ClassSettings', 'ApplyButton', 'content.ini' ), 'enabled' )}
        <div id="eZXMLExportApplyButton" style="display:inline;">
            <input class="button" type="submit" name="ApplyButton"   value="{'Apply'|i18n( 'design/admin/class/edit' )}" title="{'Store changes and continue editing.'|i18n( 'design/admin/class/edit' )}" />
        </div>
    {/section}

        <div id="eZXMLExportDiscardButton" style="display:inline;">
            <input class="button" type="submit" name="DiscardButton" value="{'Cancel'|i18n( 'design/admin/class/edit' )}" title="{'Discard all changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )}" />
        </div>
    </div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>

</form>
