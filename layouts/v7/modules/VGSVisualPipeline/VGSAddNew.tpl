{**
 * VGS Visual Pipeline Module
 *
 *
 * @package        VGSVisualPipeline Module
 * @author         Curto Francisco - www.vgsglobal.com
 * @license        vTiger Public License.
 * @version        Release: 1.0
 *}
<style type="text/css">
    #divcoloreador{
        height: 18px;
        margin-top: 7px;
    }

    #icoloreador{
        border: 1px solid black;
        display: inline-block;
        width: 32px;
        height: 16px;
    }

    #segunda td{
        text-align: center;
    }

    #segunda th{
        text-align: center;
    }

    #segunda td input.negrita{
        margin-top: 7px;
    }
</style>
<div style="width: 65%;margin: auto;margin-top: 2em;padding: 2em;">
    <input type="hidden" name="sourcemodule" id="sourcemodule" value="{$SOURCEMODULE}">
    <input type="hidden" name="sourcefieldname" id="sourcefieldname" value="{$SOURCEFIELDNAME}">
    {foreach item=ITEM key=KEY from=$CAMPOS}
        <input type="hidden" name="fieldname{$KEY+1}" id="fieldname{$KEY+1}" value="{$ITEM[0]}">
        <input type="hidden" name="negrita{$KEY+1}" id="negrita{$KEY+1}" value="{$ITEM[1]}">
        <input type="hidden" name="color{$KEY+1}" id="color{$KEY+1}" value="{$ITEM[2]}">
    {/foreach}
    <input type="hidden" name="vgsid" id="vgsid" value="{$VGSID}">
    <h3 style="padding-bottom: 1em;text-align: center">{vtranslate('LBL_MODULE_NAME', $MODULE)}</h3>
    <div>
        <h4 style="margin-top: 1em;margin-bottom: 0.5em;">{vtranslate('ADD_NEW_PIPELINE', $MODULE)}</h4>
        <p>{vtranslate('ADD_NEW_UPDATE_EXPLAIN', $MODULE)}</p>
        <table class="table table-bordered table-condensed themeTableColor" style="margin-top: 1em;">
            <thead>
                <tr class="blockHeader">
                    <th colspan="4" class="mediumWidthType"><span class="alignMiddle">{vtranslate('ADD_NEW_PIPELINE', $MODULE)}</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="50%" colspan="2"><label class="muted pull-right marginRight10px">{vtranslate('SOURCE_MODULE_NAME', $MODULE)}</label></td>
                    <td colspan="2" style="border-left: none;">
                        <select name="module1"  class="chzn-select" id="module1">
                            <option value="-">--</option>
                            {foreach from=$ENTITY_MODULES item=MODULE1}
                                <option value="{$MODULE1}">{vtranslate($MODULE1)}</option>
                            {/foreach}
                        </select>    
                    </td>
                </tr>
                <tr>
                    <td width="50%" colspan="2"><label class="muted pull-right marginRight10px">{vtranslate('SOURCE_FIELD_LABEL', $MODULE)}</label></td>
                    <td colspan="2" style="border-left: none;">
                        <select name="picklist1"  class="picklist chzn-select" id="picklist1">
                            <option value="-">--</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <table id="segunda" class="table table-bordered table-condensed themeTableColor" style="margin-top: 1em;">
            <thead>
                <tr class="blockHeader">
                    <th style="width: 50%;">
                        Campo
                    </th>
                    <th style="width: 25%;">
                        &iquest;Negrita?
                    </th>
                    <th style="width: 25%;">
                        Color de letra
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border-left: none;">
                        <select name="amostrar1"  class="picklist chzn-select" id="amostrar1">
                            <option value="-">--</option>
                        </select>
                    </td>
                    <td>
                        <input type="checkbox" class="negrita" name="negrita1">
                    </td>
                    <td>
                        <div id="divcoloreador" name="divcoloreador1" class="input-group colorpicker-component colorpicker-element">
                            <i name="icoloreador1" id="icoloreador" class="input-group-addon"></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="border-left: none;">
                        <select name="amostrar2"  class="picklist chzn-select" id="amostrar2">
                            <option value="-">--</option>
                        </select>
                    </td>
                    <td>
                        <input type="checkbox" class="negrita" name="negrita2">
                    </td>
                    <td>
                        <div id="divcoloreador" name="divcoloreador2" class="input-group colorpicker-component colorpicker-element">
                            <i name="icoloreador2" id="icoloreador" class="input-group-addon"></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="border-left: none;">
                        <select name="amostrar3"  class="picklist chzn-select" id="amostrar3">
                            <option value="-">--</option>
                        </select>
                    </td>
                    <td>
                        <input type="checkbox" class="negrita" name="negrita3">
                    </td>
                    <td>
                        <div id="divcoloreador" name="divcoloreador3" class="input-group colorpicker-component colorpicker-element">
                            <i name="icoloreador3" id="icoloreador" class="input-group-addon"></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="border-left: none;">
                        <select name="amostrar4"  class="picklist chzn-select" id="amostrar4">
                            <option value="-">--</option>
                        </select>
                    </td>
                    <td>
                        <input type="checkbox" class="negrita" name="negrita4">
                    </td>
                    <td>
                        <div id="divcoloreador" name="divcoloreador4" class="input-group colorpicker-component colorpicker-element">
                            <i name="icoloreador4" id="icoloreador" class="input-group-addon"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
       
        <br><br>
        <button class="btn btn-success pull-right" style="margin-bottom: 0.5em;" id="add_entry">{vtranslate('SAVE', $MODULE)}</button>
        <a class="btn btn-danger pull-right" style="margin-right: 2%;" href="javascript:history.go(-1)">{vtranslate('Cancel', $MODULE)}</a>
     
    </div>
</div>