<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Content list'|i18n( 'design/admin/ezxmlexport' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">


<div class="context-attributes">

    <table class="list" cellspacing="0">
        <tr>
            <th>{'Name'|i18n('design/admin/ezxmlexport')}</th>
            <th>{'NodeID'|i18n('design/admin/ezxmlexport')}</th>
            <th>{'ObjectID'|i18n('design/admin/ezxmlexport')}</th>
        </tr>
        {if is_set( $content_list )}
            {foreach $content_list as $content_class_identifier => $content_node_list}
                {foreach $content_node_list as $content_node sequence array( bglight, bgdark ) as $tr_class}
                    <tr class={$tr_class}>
                        <td><a href="{$content_node.url_alias|ezurl( 'no', 'full' )}">{$content_node.name}</a></td>
                        <td>{$content_node.node_id}</td>
                        <td>{$content_node.object.id}</td>
                    </tr>
                {/foreach}
            {/foreach}
        {/if}
    </table>

</div>

<div>
    {def $view_parameters = hash( 'offset', $offset )}

    {include name            = navigator
             uri             = 'design:navigator/google.tpl'
             page_uri        = concat('xmlexport/test/contentlist/', $export_id)
             item_count      = $total_nodes
             view_parameters = $view_parameters
             item_limit      = $fetch_limit}
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

    <div class="block">
        <input class="button" type="button" value="{'Back to export'|i18n( 'design/admin/xmlexport' )}" name="BackToExportButton"onclick="javascript:window.location ={concat( 'xmlexport/view/export/', $export_id )|ezurl( 'single', 'full' )}"/>
    </div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>
