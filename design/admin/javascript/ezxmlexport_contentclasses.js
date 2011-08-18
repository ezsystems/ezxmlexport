function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

function storeAvailableContentClassAttributesForExport( e )
{
    var serverURL = encodeURI( ezxmlexport.serverRoot + '/xmlexport/storeavailability' );
    var postData = "";
    var classID = document.getElementById( 'eZXMLExportContentClassID' ).value;
    var contentClassAttributeList = '';

    if( document.getElementById( 'ContentClassAvailableForXMLExport' ).checked == true )
    {
        classAction = "insert";

        for( var i =0; i < ezxmlexport.contentClassAttributeIDList.length; i++ )
        {
            if( document.getElementById( 'ContentAttribute_available_xml_export_' + ezxmlexport.contentClassAttributeIDList[i] ).checked == true )
            {
                contentClassAttributeList += "ContentClassAttributeIDList[]=" + ezxmlexport.contentClassAttributeIDList[i] + '&';
            }
        }
    }
    else
    {
        classAction = "remove";
    }

    postData = encodeURI( "ContentClassID=" + classID + '&Action=' + classAction + '&' + contentClassAttributeList );

    var _tokenNode = document.getElementById('ezxform_token_js');
    if ( _tokenNode )
        postData += '&ezxform_token=' + _tokenNode.getAttribute('title');

    var handleSuccess = function(o){
        /* if(o.responseText !== undefined){ console.log('success !'); } */
    }

    var handleFailure = function(o){
        /* if(o.responseText !== undefined){ console.log('failure !'); } */
    }

    var callback =
    {
        success:handleSuccess,
        failure:handleFailure
    };

    var request = YAHOO.util.Connect.syncRequest( 'POST', serverURL, callback, postData );
}

function testFTP()
{
    var FTPHost     = document.getElementById( 'FTPHost' ).value;
    var FTPPort     = document.getElementById( 'FTPPort' ).value;
    var FTPLogin    = document.getElementById( 'FTPLogin' ).value;
    var FTPPassword = document.getElementById( 'FTPPassword' ).value;
    var FTPPath     = document.getElementById( 'FTPPath' ).value;

    var postString = 'FTPHost=' + FTPHost + '&FTPPort=' + FTPPort + '&FTPLogin=' + FTPLogin + '&FTPPassword=' + FTPPassword + '&FTPPath=' + FTPPath;
    var postData   = encodeURI( postString );
    var serverURL  = encodeURI( ezxmlexport.serverRoot + '/xmlexport/ftptest' );

    var _tokenNode = document.getElementById('ezxform_token_js');
    if ( _tokenNode )
        postData += '&ezxform_token=' + _tokenNode.getAttribute('title');

    var handleSuccess = function(o){
        /* console.log( o.responseText ); */
        if( o.responseText == 'OK' )
        {
            document.getElementById( 'FTPTestButton' ).className = 'ftptestok';
            document.getElementById( 'FTPSuccess' ).style.display = 'block';
            document.getElementById( 'FTPFailure' ).style.display = 'none';
        }
        else
        {
            document.getElementById( 'FTPTestButton' ).className = 'ftptestko';
            document.getElementById( 'FTPSuccess' ).style.display = 'none';
            document.getElementById( 'FTPFailure' ).style.display = 'block';
        }
    }

    var handleFailure = function(o){
            document.getElementById( 'FTPTestButton' ).className = 'ftptestko';
            document.getElementById( 'FTPSuccess' ).style.display = 'none';
            document.getElementById( 'FTPFailure' ).style.display = 'block';
    }

    var callback =
    {
        success:handleSuccess,
        failure:handleFailure
    };

    var request = YAHOO.util.Connect.asyncRequest( 'POST', serverURL, callback, postData );
}

YAHOO.namespace('ezexalead');
YAHOO.ezexalead.YUIConnectExtension = function () {

    // public methods
    return {
        /**
         * @description Method for initiating a synchronous request via the XHR object.
         * @method syncRequest
         * @public
         * @static
         * @param {string} method HTTP transaction method
         * @param {string} uri Fully qualified path of resource
         * @param {callback} callback User-defined callback function or object
         * @param {string} postData POST body
         * @return {object} Returns the connection object
         */
        syncRequest : function ( method, uri, callback, postData ) {
            var o = (this._isFileUpload)?this.getConnectionObject(true):this.getConnectionObject();

            if(!o){
                YAHOO.log('Unable to create connection object.', 'error', 'Connection');
                return null;
            }
            else{

                // Intialize any transaction-specific custom events, if provided.
                if(callback && callback.customevents){
                    this.initCustomEvents(o, callback);
                }

                if(this._isFormSubmit){
                    if(this._isFileUpload){
                        this.uploadFile(o, callback, uri, postData);
                        return o;
                    }

                    // If the specified HTTP method is GET, setForm() will return an
                    // encoded string that is concatenated to the uri to
                    // create a querystring.
                    if(method.toUpperCase() == 'GET'){
                        if(this._sFormData.length !== 0){
                            // If the URI already contains a querystring, append an ampersand
                            // and then concatenate _sFormData to the URI.
                            uri += ((uri.indexOf('?') == -1)?'?':'&') + this._sFormData;
                        }
                        else{
                            uri += "?" + this._sFormData;
                        }
                    }
                    else if(method.toUpperCase() == 'POST'){
                        // If POST data exist in addition to the HTML form data,
                        // it will be concatenated to the form data.
                        postData = postData?this._sFormData + "&" + postData:this._sFormData;
                    }
                }

                // a 'false' value here as 3rd parameter makes this XHR synchronous, meaning that the script will not be processed on until the XHR is completed
                o.conn.open(method, uri, false );
                //this.processTransactionHeaders(o);

                // Each transaction will automatically include a custom header of
                // "X-Requested-With: XMLHttpRequest" to identify the request as
                // having originated from Connection Manager.
                if(this._use_default_xhr_header){
                    if(!this._default_headers['X-Requested-With']){
                        this.initHeader('X-Requested-With', this._default_xhr_header, true);
                        YAHOO.log('Initialize transaction header X-Request-Header to XMLHttpRequest.', 'info', 'Connection');
                    }
                }

                if(this._isFormSubmit || (postData && this._use_default_post_header)){
                    this.initHeader('Content-Type', this._default_post_header);
                    YAHOO.log('Initialize header Content-Type to application/x-www-form-urlencoded for POST transaction.', 'info', 'Connection');
                    if(this._isFormSubmit){
                        this.resetFormState();
                    }
                }

                if(this._has_default_headers || this._has_http_headers){
                    this.setHeader(o);
                }

                this.handleReadyState(o, callback);
                o.conn.send(postData || null);

                // Fire global custom event -- startEvent
                this.startEvent.fire(o);

                if(o.startEvent){
                    // Fire transaction custom event -- startEvent
                    o.startEvent.fire(o);
                }

                return o;
            }
        }
    }
}(); // Execute the function, returning the object literal

// Augment YAHOO.util.Connect with the YUIConnectExtension methods
YAHOO.lang.augmentObject(
    YAHOO.util.Connect,
    YAHOO.ezexalead.YUIConnectExtension );
