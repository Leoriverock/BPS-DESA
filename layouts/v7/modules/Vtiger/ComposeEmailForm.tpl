{***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/ComposeEmail.php *}

{strip}
    <div class="SendEmailFormStep2 modal-dialog modal-lg" id="composeEmailContainer">
        <div class="modal-content">
            <form class="form-horizontal" id="massEmailForm" method="post" action="index.php" enctype="multipart/form-data" name="massEmailForm">
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE={vtranslate('LBL_COMPOSE_EMAIL', $MODULE)}}
                <div class="modal-body">
                    <input type="hidden" id="selected_ids" name="selected_ids" value='{ZEND_JSON::encode($SELECTED_IDS)}' />
                    <input type="hidden" name="excluded_ids" value='{ZEND_JSON::encode($EXCLUDED_IDS)}' />
                    <input type="hidden" name="viewname" value="{$VIEWNAME}" />
                    <input type="hidden" name="module" value="{$MODULE}"/>
                    <input type="hidden" name="mode" value="massSave" />
                    <input type="hidden" name="toemailinfo" value='{ZEND_JSON::encode($TOMAIL_INFO)}' />
                    <input type="hidden" name="view" value="MassSaveAjax" />
                    <input type="hidden" id="to" name="to" value='' /> <!--{ZEND_JSON::encode($TO)}-->
                    <input type="hidden"  name="toMailNamesList" value='{$TOMAIL_NAMES_LIST}'/>
                    <input type="hidden" id="flag" name="flag" value="" />
                    <input type="hidden" id="maxUploadSize" value="{$MAX_UPLOAD_SIZE}" />
                    <input type="hidden" id="documentIds" name="documentids" value="" />
                    <input type="hidden" name="emailMode" value="{$EMAIL_MODE}" />
                    <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
                    {if !empty($PARENT_EMAIL_ID)}
                        <input type="hidden" name="parent_id" id="parent_id" value="{$PARENT_EMAIL_ID}" />
                        <input type="hidden" name="parent_record_id" value="{$PARENT_RECORD}" />
                    {/if}
                    {if !empty($RECORDID)}
                        <input type="hidden" name="record" value="{$RECORDID}" />
                    {/if}
                    <input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
                    <input type="hidden" name="operator" value="{$OPERATOR}" />
                    <input type="hidden" name="search_value" value="{$ALPHABET_VALUE}" />
                    <input type="hidden" name="search_params" value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($SEARCH_PARAMS))}' />
                    
                    <div class="row toEmailField">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_TO',$MODULE)}&nbsp;<span class="redColor">*</span></span>
                            </div>
                            <div class="col-lg-6">
                                {*var_dump($TO)*}
                                {if !empty($TO)}
                                    {assign var=TO_EMAILS value=","|implode:$TO|htmlentities}
                                {/if}
                              
                                <input id="emailField" style="width:100%" name="toEmail" type="text" class="autoComplete sourceField select2" data-rule-required="true" data-rule-multiEmails="true" value= "" placeholder="{vtranslate('LBL_TYPE_AND_SEARCH',$MODULE)}">
                            </div>
                            <div class="col-lg-4 input-group">
                                <select style="width: 140px;" class="select2 emailModulesList pull-right">
                                    {foreach item=MODULE_NAME from=$RELATED_MODULES}
                                        <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $FIELD_MODULE} selected {/if}>{vtranslate($MODULE_NAME,$MODULE_NAME)}</option>
                             {/foreach}
                                </select>
                                <a href="#" class="clearReferenceSelection cursorPointer" name="clearToEmailField"> X </a>
                                <span class="input-group-addon">
                                    <span class="selectEmail cursorPointer">
                                        <i class="fa fa-search" title="{vtranslate('LBL_SELECT', $MODULE)}"></i>
                                    </span>
                                </span>
                            </div>
                            </div>
                                    
                        </div>
                    
                    <div class="row {if empty($CC)} hide {/if} ccContainer">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_CC',$MODULE)}</span>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" id="cc" name="cc" data-rule-multiEmails="true" value="{if !empty($CC)}{$CC}{/if}"/>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>

                    <div class="row {if empty($BCC)} hide {/if} bccContainer">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_BCC',$MODULE)}</span>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" name="bcc" data-rule-multiEmails="true" value="{if !empty($BCC)}{$BCC}{/if}"/>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>
                    
                    <div class="row {if (!empty($CC)) and (!empty($BCC))} hide {/if} ">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                            </div>
                            <div class="col-lg-6">
                                <a href="#" class="cursorPointer {if (!empty($CC))}hide{/if}" id="ccLink">{vtranslate('LBL_ADD_CC', $MODULE)}</a>&nbsp;&nbsp;
                                <a href="#" class="cursorPointer {if (!empty($BCC))}hide{/if}" id="bccLink">{vtranslate('LBL_ADD_BCC', $MODULE)}</a>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>
                    
                    <div class="row subjectField">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_SUBJECT',$MODULE)}&nbsp;<span class="redColor">*</span></span>
                            </div>
                            {*if $SOURCE_MODULE == 'HelpDesk' or $SOURCE_MODULE == 'Accounts'*}
                            <div class="col-lg-6"><!-- {$SELECTED_IDS[0]}-->
                                <input type="text" name="subject" value="" data-rule-required="true" id="subject" spellcheck="true" class="inputElement"/>
                            </div>
                            {*/if*}
                            <div class="col-lg-4"></div>
                        </div>
                    </div>
                            
                    <div class="row attachment">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_ATTACHMENT',$MODULE)}</span>
                            </div>
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-lg-4 browse">
                                        <input type="file" {if $FILE_ATTACHED}class="removeNoFileChosen"{/if} id="multiFile" name="file[]"/>&nbsp;
                                    </div>
                                    <div class="col-lg-4 brownseInCrm">
                                        <!--<button type="button" class="btn btn-small btn-default" id="browseCrm" data-url="{$DOCUMENTS_URL}" title="{vtranslate('LBL_BROWSE_CRM',$MODULE)}">{vtranslate('LBL_BROWSE_CRM',$MODULE)}</button>-->
                                    </div>
                                    <div class="col-lg-4 insertTemplate">
                                        <button id="selectEmailTemplate" class="btn btn-success" data-url="module=EmailTemplates&view=Popup">{vtranslate('LBL_SELECT_EMAIL_TEMPLATE',$MODULE)}</button>
                                    </div>
                                </div>
                                <div id="attachments">
                                    {foreach item=ATTACHMENT from=$ATTACHMENTS}
                                        {if ('docid'|array_key_exists:$ATTACHMENT)}
                                            {assign var=DOCUMENT_ID value=$ATTACHMENT['docid']}
                                            {assign var=FILE_TYPE value="document"}
                                        {else}
                                            {assign var=FILE_TYPE value="file"}
                                        {/if}
                                        <div class="MultiFile-label customAttachment" data-file-id="{$ATTACHMENT['fileid']}" data-file-type="{$FILE_TYPE}"  data-file-size="{$ATTACHMENT['size']}" {if $FILE_TYPE eq "document"} data-document-id="{$DOCUMENT_ID}"{/if}>
                                            {if $ATTACHMENT['nondeletable'] neq true}
                                                <a name="removeAttachment" class="cursorPointer">x </a>
                                            {/if}
                                            <span>{$ATTACHMENT['attachment']}</span>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>
                                
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_INCLUDE_SIGNATURE',$MODULE)}</span>
                            </div>
                            <div class="item col-lg-9">
                                <input class="" type="checkbox" name="signature" value="Yes" checked="checked" id="signature" disabled="">
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid hide" id='emailTemplateWarning'>
                        <div class="alert alert-warning fade in">
                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                            <p>{vtranslate('LBL_EMAILTEMPLATE_WARNING_CONTENT',$MODULE)}</p>
                        </div>
                    </div>         
                    <div class="row templateContent">
                        <div class="col-lg-12">
                            <textarea style="width:390px;height:200px;" id="description" name="description">{$DESCRIPTION}
                                
                            
                            </textarea>
                        </div>
                    </div>
                    
                    {if $RELATED_LOAD eq true}
                        <input type="hidden" name="related_load" value={$RELATED_LOAD} />
                    {/if}
                    <input type="hidden" name="attachments" value='{ZEND_JSON::encode($ATTACHMENTS)}' />
                    <div id="emailTemplateWarningContent" style="display: none;">
                        {vtranslate('LBL_EMAILTEMPLATE_WARNING_CONTENT',$MODULE)}
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="btn-group">
                    <button id="sendEmail" name="sendemail" class="btn btn-enviar btn-fixed-width" title="{vtranslate("LBL_SEND_EMAIL",$MODULE)}" type="submit"><strong>{vtranslate("LBL_SEND_EMAIL",$MODULE)}</strong></button>
                    <div class="pull-right cancelLinkContainer">
                        <a href="#" class="btn btn-cancelar btn-fixed-width cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                    </div>
                    <!--<button id="saveDraft" name="savedraft" class="btn btn-default" title="{vtranslate('LBL_SAVE_AS_DRAFT',$MODULE)}" type="submit"><strong>{vtranslate('LBL_SAVE_AS_DRAFT',$MODULE)}</strong></button>-->
                </div>
                <!--Aca guardo de donde proviene la accion, si desde enviar mail o desde la vista relacioada-->
                <div id="proviene"></div>
            </form>
        </div>
    </div>
{/strip}
{literal}
<style type="text/css">
    .btn.btn-enviar{
        background-color: #4CAF50; /* Cambia este valor al color que desees */
        color: white;
    }
    }
    .btn.btn-enviar,
    .btn.btn-cancelar {
        /* Aplicar el mismo tamaño y cualquier otro estilo que desees */
        padding: 10px 20px;
        font-size: 14px;
    }

    /* Alinear los botones horizontalmente en el contenedor */
    .btn-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

</style>
<script type="text/javascript">
    $(document).ready(function() {
        
        console.log("el valor es"+miVariableGlobal);
        if (miVariableGlobal === false){
        

                //Cargar la firma cuando carga el cuerpo de email
                let firma = '';
                jQuery.ajax({
                            async: false,
                            data: {
                                id : id
                                },
                            url:  'index.php?module=HelpDesk&action=getFirma',
                            dataType:"json",
                            success: function(data) {
                                console.log("Firma; "+data)
                                firma =$.trim(data); 

                                $("#description").html("<br><br><br><br><br><br>"+firma+"<br><br>");
                               
                                
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                              console.log(thrownError);
                            }
                });    


                var id = $('#selected_ids').val();
                var id_email = $('#parent_id').val();
                console.log("id_email");
                console.log(id_email);
                id = id.replace('[', '');
                id = id.replace(']', '');
                id = id.replace('"', '');
                id = id.replace('"', '');
                var activa = 0;

                $( "#sendEmail" ).click(function() {

                    jQuery.ajax({
                            async: false,
                            data: {
                                id : id
                                },
                            url:  'index.php?module=HelpDesk&action=getEstado',
                            dataType:"json",
                            success: function(data) {
                                //console.log("show"+data)
                                //si no hay atenciones activas mandamos la advertencia
                                activa = data;
                                
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                              console.log(thrownError);
                            }
                            }); 
                 if(activa == 0){
                     if( !confirm('No hay atenciones activas, desea continuar?')) {
                                         return false;
                    }   
                 }


                });


                //console.log("mostrame el id "+id);
                jQuery.ajax({
                            async: false,
                            data: {
                                id : id
                                },
                            url:  'index.php?module=HelpDesk&action=getTituloHelpDesk',
                            dataType:"json",
                            success: function(data) {
                                
                                retorno = data;
                                //console.log("retorno del rey"+retorno.valueOf());
                                $('#subject').val(retorno);//Aca iria nro incidencia y titulo
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                              console.log(thrownError);
                            }
                            }); 

                jQuery.ajax({

                            async: false,
                            data: {
                                id : id,
                                id_email : id_email,
                                },
                            url:  'index.php?module=HelpDesk&action=getEmail',
                            dataType:"json",
                            success: function(data) {
                                
                                retorno = data;
                                //console.log("retorno del rey2 "+retorno.valueOf());
                                $('#emailField').val(retorno);//Email
                                
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                              console.log(thrownError);
                            }
                            }); 
                 jQuery.ajax({
                            async: false,
                            data: {
                                },
                            url:  'index.php?module=HelpDesk&action=getRelacionadas',
                            dataType:"json",
                            success: function(data) {
                                

                                //console.log("retorno del rey2 "+data);
                                if(data != ""){
                                    table = "<hr>";
                                }
                                
                                
                                
                                table += "<table style='margin: 0 auto; border-collapse: collapse; width:100%;'>";
                                var tableRow = '';
                                for (var i = 0; i < data.length; i++) {
                                    var cell = data[i].contenido;
                                    tableRow += '<tr><td style="padding: 10px; color: #777; font-size: 18px;">' + cell + '</td></tr>';
                                }
                                table += tableRow;
                                table += "</table>";
                                
                               
                                var contenidoActual = $("#description").html();
                                $("#description").html(function(index, oldHtml) {
                                    return contenidoActual + table;
                                });
                                

                              
                               
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                              console.log(thrownError);
                            }
                            }); 
        } else{
            var id_email = $('#parent_id').val();
            jQuery.ajax({
                            async: false,
                            data: {
                                id : id,
                                id_email : id_email,
                                },
                            url:  'index.php?module=HelpDesk&action=getTituloHelpDeskRel',
                            dataType:"json",
                            success: function(data) {
                                
                                retorno = data;
                                //console.log("retorno del rey"+retorno.valueOf());
                                $('#subject').val(retorno);//Aca iria nro incidencia y titulo
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                              console.log(thrownError);
                            }
                            });

            jQuery.ajax({
                            async: false,
                            data: {
                                id : id,
                                id_email : id_email,
                                },
                            url:  'index.php?module=HelpDesk&action=getEmailRel',
                            dataType:"json",
                            success: function(data) {
                                
                                retorno = data;
                                console.log("retorno del rey2 "+retorno.valueOf());
                                $('#emailField').val(retorno);//Email
                                
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                              console.log(thrownError);
                            }
                            }); 
        }
    });
    


</script>
    
{/literal}