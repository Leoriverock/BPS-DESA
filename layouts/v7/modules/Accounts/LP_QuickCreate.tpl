{strip}
    {foreach key=index item=jsModel from=$SCRIPTS}
        <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
    {/foreach}

    <div class="modal-dialog" style = "width: 400px">
        <div class="modal-content">
            <form class="form-horizontal recordEditView" id="QuickCreate" name="QuickCreate" method="post" action="index.php">
                {assign var=HEADER_TITLE value={vtranslate('LBL_QUICK_CREATE', $MODULE)}|cat:" "|cat:{vtranslate($SINGLE_MODULE, $MODULE)}}
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
                    
                <div class="modal-body">
                    {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
                        <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
                    {/if}
                    {if $MODULE eq 'Events'}
                        <input type="hidden" name="calendarModule" value="Events">
                        {if !empty($PICKIST_DEPENDENCY_DATASOURCE_EVENT)}
                            <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE_EVENT)}' />
                        {/if}
                    {/if}
                    {if $MODULE eq 'Events'}
                        <input type="hidden" name="module" value="Calendar">
                    {else}
                        <input type="hidden" name="module" value="{$MODULE}">
                    {/if}
                    <input type="hidden" name="action" value="SaveAjax">
                    <div class="quickCreateContent">
                        <table class="massEditTable table no-border">
                            <tr>
                                <td class='fieldLabel col-lg-4'>
                                    <label class = "muted">Tipo de usuario</label>
                                </td>
                                <td class='fieldLabel col-lg-8 tdSelect'>
                                    <span class="pull-right spanSelect">
                                        <select class="select2 referenceModulesList inputElement" id = "userType">
                                            <option value = "Persona">Persona</option>
                                            <option value = "Contribuyente">Contribuyente</option>
                                        </select>
                                    </span>
                                </td>
                            </tr>
                            <tr class = "trPersona">
                                <td class='fieldLabel col-lg-4'>
                                    <label class = "muted">Tipo de Documento</label>
                                </td>
                                <td>
                                    <span class="pull-right spanSelect">
                                        <select data-fieldname="accdocumenttype" data-fieldtype="picklist" class="inputElement select2  select2-offscreen" type="picklist" name="accdocumenttype">
                                            {foreach key=id item=value from=$DOCUMENT_TYPES}
                                                <option value = "{$id}" {if $RECORD->get('accdocumenttype') eq $value}selected{/if}>{$value}</option>
                                            {/foreach}
                                        </select>
                                    </span>
                                </td>
                            </tr>
                            <tr class = "trPersona">
                                <td class='fieldLabel col-lg-4'>
                                    <label class = "muted">Número de Documento</label>
                                </td>
                                <td>
                                    <span class="pull-right">
                                        <input type="text" class = "inputElement nameField" id = "Accounts_editView_fieldName_accdocumentnumber" name = "accdocumentnumber" value="{$RECORD->get('accdocumentnumber')}" />
                                    </span>
                                </td>
                            </tr>
                            <tr class = "trPersona">
                                <td class='fieldLabel col-lg-4'>
                                    <label class = "muted">País</label>
                                </td>
                                <td>
                                    <span class="pull-right spanSelect">
                                        <select name = "acccountry" class="select2 referenceModulesList inputElement">
                                            {foreach key=id item=value from=$COUNTRIES}
                                                <option value = "{$id}" {if $id eq 1}selected{/if}>{$value}</option>
                                            {/foreach}
                                        </select>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <center>
                        {if $BUTTON_NAME neq null}
                            {assign var=BUTTON_LABEL value=$BUTTON_NAME}
                        {else}
                            {assign var=BUTTON_LABEL value={vtranslate('LBL_SAVE', $MODULE)}}
                        {/if}
                        {assign var="EDIT_VIEW_URL" value=$MODULE_MODEL->getCreateRecordUrl()}
                        <button class="btn btn-default" id="goToFullForm" data-edit-view-url="{$EDIT_VIEW_URL}" type="button"><strong>{vtranslate('LBL_GO_TO_FULL_FORM', $MODULE)}</strong></button>
                        <button {if $BUTTON_ID neq null} id="{$BUTTON_ID}" {/if} class="btn btn-success" type="submit" name="saveButton"><strong>{$BUTTON_LABEL}</strong></button>
                        <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </center>
                </div>
            </form>
        </div>
            {if $FIELDS_INFO neq null}
                <script type="text/javascript">
                    var quickcreate_uimeta = (function() {
                        var fieldInfo  = {$FIELDS_INFO};
                        return {
                            field: {
                                get: function(name, property) {
                                    if(name && property === undefined) {
                                        return fieldInfo[name];
                                    }
                                    if(name && property) {
                                        return fieldInfo[name][property]
                                    }
                                },
                                isMandatory : function(name){
                                    if(fieldInfo[name]) {
                                        return fieldInfo[name].mandatory;
                                    }
                                    return false;
                                },
                                getType : function(name){
                                    if(fieldInfo[name]) {
                                        return fieldInfo[name].type
                                    }
                                    return false;
                                }
                            },
                        };
                    })();
                </script>
            {/if}
            <script>
                jQuery(document).ready( function(){
                    jQuery('#userType').change( function( option ){
                        if( option.val == "Persona" ){
                            jQuery('.trPersona').show();
                        }
                        else{
                            jQuery('.trPersona').hide();
                        }
                    } );
                } )
            </script>
    </div>
{/strip}