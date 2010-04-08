<script type="text/javascript">
    YAHOO.util.Event.addListener( "FTPTestButton", "click", testFTP );

    // super globals !!
    var serverRoot = {'/'|ezurl( 'single', 'full' )};
</script>

<div class="block">
        <label>{'FTP target'|i18n('design/admin/xmlexport')} :</label>
{if ezini( 'FTPSettings', 'FTPShipment', 'ezxmlexport.ini' )|eq( 'enabled' )}
            <br/>
            <label for="FTPHost">{'Host'|i18n( 'design/admin/xmlexport' )}</label> <input type="text" name="FTPHost" id="FTPHost" value="{$FTPHostValue|wash}"/>
            <label for="FTPPort">{'Port'|i18n( 'design/admin/xmlexport' )}</label> <input type="text" name="FTPPort" id="FTPPort" value="{$FTPPortValue|wash}"/>
            <label for="FTPLogin">{'Login'|i18n( 'design/admin/xmlexport' )}</label> <input type="text" name="FTPLogin" id="FTPLogin" value="{$FTPLoginValue|wash}"/>
            <label for="FTPPassword">{'Password'|i18n( 'design/admin/xmlexport' )}</label> <input type="text" name="FTPPassword" id="FTPPassword" value="{$FTPPasswordValue|wash}"/>
            <label for="FTPPath">{'Path'|i18n( 'design/admin/xmlexport' )}</label> <input type="text" name="FTPPath" id="FTPPath" value="{$FTPPathValue|wash}"/>
            <p><input type="button" name="FTPTestButton" id="FTPTestButton" class="button" value="{'Test'|i18n('design/admin/xmlexport')}"/></p>
            <div id="FTPSuccess" style="display:none;color:green">{'Connection test successful'|i18n( 'design/admin/xmlexport' )}</div>
            <div id="FTPFailure" style="display:none;color:red">{'Connection test failed'|i18n( 'design/admin/xmlexport' )}</div>
{else}
    <p>{'FTP settings are disabled, you can enable them in ezxmlexport.ini if you want.'|i18n( 'design/admin/xmlexport' )}</p>
{/if}
</div>