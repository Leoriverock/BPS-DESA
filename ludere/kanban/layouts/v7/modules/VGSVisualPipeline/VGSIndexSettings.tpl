{**
 * VGS Visual Pipeline Module
 *
 *
 * @package        VGSVisualPipeline Module
 * @author         Curto Francisco - www.vgsglobal.com
 * @license        vTiger Public License.
 * @version        Release: 1.0
 *}

<div>
    <div style="width: 65%;margin: auto;margin-top: 2em;padding: 2em;">
        <h3 style="padding-bottom: 1em;text-align: center">{vtranslate('LBL_MODULE_NAME', $MODULE)}</h3>
        <div class="row" style="margin: 1em;">


            <div class="alert alert-warning" style="float: left;margin-left:1em !important; margin-bottom: 0px !important;margin-top: 0px !important;width: 80%; display:none;">
                {vtranslate('notice', $MODULE)}
            </div>

        </div>

    </div>
    <div>

        <div style="width: 80%;margin: auto;margin-top: 2%;">
            <div style="width:100%; height: 1%;margin-bottom: 2%;"><button class="btn pull-right" style="margin-bottom: 0.5em;" id="Contacts_detailView_basicAction_LBL_EDIT" onclick="window.location.href = 'index.php?module=VGSVisualPipeline&view=VGSAddNew&parent=Settings'">{vtranslate('ADD_NEW', $MODULE)}</button></div>

            <table class="table table-bordered listViewEntriesTable">
                <thead>
                    <tr class="listViewHeaders">
                        <th>{vtranslate('SOURCE_MODULE_NAME', $MODULE)}</th>
                        <th>{vtranslate('SOURCE_FIELD_LABEL', $MODULE)}</th>
                        <th>{vtranslate('FILTER_FIELD_LABEL', $MODULE)}</th>
                        <th>{vtranslate('ACTIONS', $MODULE)}</th>

                    </tr>
                </thead>
                {foreach item=RMU_FIELDS from=$RMU_FIELDS_ARRAY}
                    <tr class="listViewEntries">
                        <td class="listViewEntryValue" nowrap>{$RMU_FIELDS.source_module}</td>
                        <td class="listViewEntryValue" nowrap>{$RMU_FIELDS.source_field_name}</td>
                        <td class="listViewEntryValue" nowrap>{$RMU_FIELDS.filter_fields_name}</td>
                        <td class="listViewEntryValue" nowrap>
                            <a class="deleteRecordButton fa fa-trash" id="{$RMU_FIELDS.id}" data-sourcemodule="{$RMU_FIELDS.source_module}"></a>
                            <a class="editRecordButton fa fa-pencil" href = "index.php?module=VGSVisualPipeline&view=VGSAddNew&parent=Settings&id={$RMU_FIELDS.id}"></a>
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>      
    </div>
</div>
