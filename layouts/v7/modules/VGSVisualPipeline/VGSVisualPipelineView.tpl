{**
* VGS Visual Pipeline Module
*
*
* @package        VGSVisualPipeline Module
* @author         Curto Francisco, Conrado Maggi - www.vgsglobal.com
* @license        vTiger Public License.
* @version        Release: 1.0
*}
{strip}
<style>
    .tilt.right {
        transform: rotate(3deg);
        -moz-transform: rotate(3deg);
        -webkit-transform: rotate(3deg);
    }
    .tilt.left {
        transform: rotate(-3deg);
        -moz-transform: rotate(-3deg);
        -webkit-transform: rotate(-3deg);
    }
    body {
        min-width: 520px;
    }
    .vgs-visual-pipeline{
        width: 100%;
        margin: 0 auto;
        height: 500px;
        min-height: 500px;
        white-space: nowrap;
        overflow-x: scroll;
        overflow-y: hidden;
    }

    .columnTitle{
        text-align: center;
        margin-bottom: 5%;
        word-wrap: break-word;


    }    
    .vgs-visual-pipeline .column {
        width: 250px;
        padding-bottom: 100px;
        display: inline-block;
        height: 500px;
        margin-right: 5.7px;
    }

    .vgs-visual-pipeline .column-list {
        overflow-y: scroll;
        width: 250px;
        padding-bottom: 100px;
        display: inline-block;
        height: 80%;
        margin-right: 5.7px;
    }


    .vgs-visual-pipeline .quickLinksDiv p.selectedQuickLink a:after{
        border-bottom: 20px solid rgba(0, 0, 0, 0);
    }
    .vgs-visual-pipeline .quickLinksDiv {
        margin: 10px auto 10px 1%;
        width: 90%;
    }

    .vgs-visual-pipeline  .quickLinksDiv p {
        font-size: 1em;
        padding: 5% 0 0 2%;

    }

    .vgs-visual-pipeline .table th, .table td {
        padding: 3%;   
        font-size: 80%;
        word-wrap: break-word;
        white-space: normal; 
    }

    .vgs-visual-pipeline .table {
    }

    .portlet {
        margin: 0 1em 0.5em 0;
        padding: 0.1em;
    }
    .portlet-header {
        padding: 0.2em 0.3em;
        margin-bottom: 0.5em;
        position: relative;
        border-bottom: 1px solid #acacac;
        color: rgb(68, 68, 68);
        overflow: hidden;
    }
    .portlet-header a {
        display: block;
        overflow: hidden;
        width: 85%;
    }
    .portlet-toggle {
        position: absolute;
        top: 50%;
        right: 0;
        margin-top: -8px;
        display: block;
    }
    .portlet-settings {
        position: absolute;
        top: 50%;
        right: 16px;
        margin-top: -8px;
        display: block;
    }
    .portlet-content {
        padding: 0.4em;
    }
    .portlet-placeholder {
        border: 1px dotted black;
        margin: 0 1em 1em 0;
        height: 50px;
    }
    .portlet.ui-widget.ui-widget-content.ui-helper-clearfix.ui-corner-all{
        height: auto;
    }

    .ui-widget-content {
        border-radius: 1px;
        border-color: #ffffff;
        box-shadow: 0 0 3px -1px inset;
        margin-top: 2px;
        margin-left: 5px;
        height: 12px;
        cursor: unset;
    }
    #tablaajustes>tbody>tr>td{
        text-align: center;
        padding: 10px;
    }
    #tablaajustes>tbody>tr>td.nombreprop{
        font-size: 15px;
        font-weight: bold;
    }
    #tablaajustes>tbody>tr>td.aclaracionprop{
        font-size: 10px;
    }
    .colorpicker.dropdown-menu.colorpicker-with-alpha.colorpicker-right.colorpicker-visible{
        z-index: 10000 !important;
    }
    #divcoloreador{
        height: 18px;
    }
    #icoloreador{
        border: 1px solid black;
        display: inline-block;
        width: 32px;
        height: 16px;
    }
    div.portlet-content > div{
        white-space: normal;
    }
    div.ultimos{
        width: 72%;
        display: inline-block;
    }
    div.imagen{
        width: 26%;
        display: inline-block;
    }
    div.imagen > img{
        border-radius: 50%;
        height: auto;
        max-width: 50px;
    }
    div.portlet-content > div > div > a {
        color: inherit;
    }
    div.portlet-content > div > div > div > a {
        color: inherit;
    }
</style>
<div id="modalSettings" class="modal hide fade">
    <input type="hidden" id="idregistro" value="">
    <div class="modal-header">
        <div class="titleHolder" style="text-align: center;">
            <h3>Ajuste el registro a su gusto</h3>
        </div>
    </div>
    <div class="modal-body">
        <table id="tablaajustes" class="table table-bordered equalSplit detailview-table">
            <tbody>
                <tr>
                    <td class="nombreprop">
                        Color
                    </td>
                    <td class="aclaracionprop">
                        (Dejar en blanco o en negro para eliminar color)
                    </td>
                    <td>
                        <div id="divcoloreador" class="input-group colorpicker-component colorpicker-element">
                            <i id="icoloreador" class="input-group-addon"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button id="saveSettings" class="btn btn-success">Guardar</button>
        <button id="closeSettings" class="btn btn-danger" style="float: left;">Cerrar</button>
    </div>
</div>
<input type="hidden" id="columna_filtro" value="{$COLUMNA}">
<input type="hidden" id="modulo" value="{$MODULENAME}">
{assign var=NOMBREMODULO value='SINGLE_'|cat:$MODULENAME}
{if $PUEDECREAR}
<div class="listViewActionsDiv row-fluid">
    <span class="btn-toolbar span4">
        <span class="btn-group listViewMassActions">
            <span class="btn-group">
                <button id="HelpDesk_listView_basicAction_LBL_ADD_RECORD" class="btn addButton" onclick="window.location.href='index.php?module={$MODULENAME}&view=Edit'">
                    <i class="icon-plus"></i>&nbsp;<strong>Añadir {vtranslate($NOMBREMODULO, $MODULENAME)}</strong>
                </button>
            </span>
            <span class="btn-group">
                <button id="alterarToggle" class="btn addButton" data-actual="contraer">
                    <strong>Contraer tareas</strong>
                </button>
            </span>
        </span>
    </span>
</div>
{/if}
<div class="vgs-visual-pipeline">
    {foreach $RECORDS_ARRAY as $order => $RECORDS}
        {foreach $RECORDS as $llave => $otro}
            <div class="column">
                {if $llave neq ''}
                    <div class="quickLinksDiv"><p class="columnTitle selectedQuickLink "><a class="quickLinks"><strong>{vtranslate($llave,$MODULENAME)}</strong></a></p></div>
                {else}
                    <div class="quickLinksDiv"><p class="columnTitle selectedQuickLink "><a class="quickLinks"><strong>Sin {$FILTERFIELDLABEL}</strong></a></p></div>
                {/if}
                <div class="column-list" id="{$llave}">
                    {if $otro|@count > 0}
                        {foreach $otro as $key => $RECORD_INFO}
                            <div class="portlet" id="{$RECORD_INFO.RECORD}" data-key="{$llave}" style="border: 2px ridge #00000052;{if !!$RECORDS_SETTINGS[$RECORD_INFO.RECORD]}border-left: 10px solid {$RECORDS_SETTINGS[$RECORD_INFO.RECORD]}{/if}">
                                <div class="portlet-header" style="border: none;">
                                    <a href="index.php?module={$MODULENAME}&record={$RECORD_INFO.RECORD}&view=Detail" target="_blank" title="{$RECORD_INFO.RECORD_LABEL}">{$RECORD_INFO.RECORD_LABEL}</a>
                                </div>
                                <div class="portlet-content">
                                    <div>
                                        {foreach item=FIELD_MODEL from=$RECORD_INFO.TOOLTIP_FIELDS name=fieldsCount}
                                            <div class="row-fluid 
                                                {if $smarty.foreach.fieldsCount.index < count($RECORD_INFO.TOOLTIP_FIELDS)-2}
                                                    " title="{vtranslate($FIELD_MODEL->get('label'), $MODULENAME)}" style="{if $RECORD_INFO.NEGRITAS[$smarty.foreach.fieldsCount.index]} font-weight: bold; {/if}{if $RECORD_INFO.COLORES[$smarty.foreach.fieldsCount.index]}color:{$RECORD_INFO.COLORES[$smarty.foreach.fieldsCount.index]}; {/if}">
                                                    {$FIELD_MODEL->getDisplayValue($RECORD_INFO.RECORD_MODEL->get($FIELD_MODEL->get('name')),$RECORD_INFO.RECORD)}
                                                {else}
                                                    {" "}ultimos">
                                                {assign var=indexActual value=count($RECORD_INFO.TOOLTIP_FIELDS)-2}
                                                {if count($RECORD_INFO.TOOLTIP_FIELDS) == 1}
                                                    {assign var=indexActual value=$indexActual+1}
                                                {/if}
                                                <div title="{vtranslate($FIELD_MODEL->get('label'), $MODULENAME)}" style="{if $RECORD_INFO.NEGRITAS[$indexActual]} font-weight: bold; {/if}{if $RECORD_INFO.COLORES[$indexActual]}color:{$RECORD_INFO.COLORES[$indexActual]}; {/if}">
                                                    {$FIELD_MODEL->getDisplayValue($RECORD_INFO.RECORD_MODEL->get($FIELD_MODEL->get('name')),$RECORD_INFO.RECORD)}
                                                </div>
                                                {if count($RECORD_INFO.TOOLTIP_FIELDS) != 1}
                                                    {assign var=indexActual value=$indexActual+1}
                                                    {assign var=FIELD_MODEL value=$RECORD_INFO.TOOLTIP_FIELDS.$indexActual}
                                                    <div title="{vtranslate($FIELD_MODEL->get('label'), $MODULENAME)}" style="{if $RECORD_INFO.NEGRITAS[$indexActual]} font-weight: bold; {/if}{if $RECORD_INFO.COLORES[$indexActual]}color:{$RECORD_INFO.COLORES[$indexActual]}; {/if}">
                                                        {$FIELD_MODEL->getDisplayValue($RECORD_INFO.RECORD_MODEL->get($FIELD_MODEL->get('name')),$RECORD_INFO.RECORD)}
                                                    </div>
                                                    {/if}
                                                {/if}
                                            </div>
                                            {if $smarty.foreach.fieldsCount.index >= count($RECORD_INFO.TOOLTIP_FIELDS)-2}
                                                <div class="value imagen">
                                                    <img src="{$RECORD_INFO.PATHIMAGEN}" class="pull-right">
                                                </div>
                                                {break}
                                            {/if}
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        {/foreach}

    {/foreach}

</div>
<script>
    {literal}
        jQuery(document).ready(function() {
            jQuery('.vgs-visual-pipeline').height(jQuery(window).height()-jQuery('.navbar-fixed-top').height())
        });

    {/literal}    
</script>
{/strip}