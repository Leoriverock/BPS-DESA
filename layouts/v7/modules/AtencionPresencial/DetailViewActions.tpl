{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
    <div class="col-lg-6 detailViewButtoncontainer">
        <div class="pull-right btn-toolbar">
            <div class="btn-group">
            {*if $RECORD->get('ap_estado') == 'Asignado' || $RECORD->get('ap_estado') == 'Liberado' || $RECORD->get('ap_estado') == 'Llamado' && $RECORD->get('assigned_user_id') == $CURRENT_USER_MODEL->getId()*}
               <!-- 
                <button class="btn btn-success" style="margin-left: 50%" name="llamar_atencion">LLamar numero</button> 
                <button class="btn btn-success" style="margin-left: 50%" name="llamar_atender">Atender</button> 
                -->
                
            {*/if*}
            {*if $RECORD->get('ap_estado') != 'Liberado' && $RECORD->get('ap_estado') != 'Finalizado'*}    
               <!--  <button class="btn btn-success" style="margin-left: 50%" name="cola_atencion">Liberar</button>-->

            {*/if*}
           {if $RECORD->get('ap_estado') != 'Finalizado' }
           <button class="btn btn-success" style="margin-left: 50%" id="Finalizar" name="">Finalizar</button>   <!-- onclick="cargarModalFin()" -->
           {assign var=tienetickets value=$CURRENT_USER_MODEL->tienetickets($RECORD->getId())}
           <input type="hidden" name="tieneTickets" {if $tienetickets } value='tiene' {/if}>

            {assign var=STARRED value=$RECORD->get('starred')}
        {/if}
            {if $MODULE_MODEL->isStarredEnabled()}
                <button class="btn btn-default markStar {if $STARRED} active {/if}" id="starToggle" style="width:150px;">
                    <div class='starredStatus' title="{vtranslate('LBL_STARRED', $MODULE)}">
                        <div class='unfollowMessage'>
                            <i class="fa fa-star-o"></i> &nbsp;{vtranslate('LBL_UNFOLLOW',$MODULE)}
                        </div>
                        <div class='followMessage'>
                            <i class="fa fa-star active"></i> &nbsp;{vtranslate('LBL_FOLLOWING',$MODULE)}
                        </div>
                    </div>
                    <div class='unstarredStatus' title="{vtranslate('LBL_NOT_STARRED', $MODULE)}">
                        {vtranslate('LBL_FOLLOW',$MODULE)}
                    </div>
                </button>
            {/if}
            {foreach item=DETAIL_VIEW_BASIC_LINK from=$DETAILVIEW_LINKS['DETAILVIEWBASIC']}
                <button class="btn btn-default" id="{$MODULE_NAME}_detailView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($DETAIL_VIEW_BASIC_LINK->getLabel())}"
                        {if $DETAIL_VIEW_BASIC_LINK->isPageLoadLink()}
                            onclick="window.location.href = '{$DETAIL_VIEW_BASIC_LINK->getUrl()}&app={$SELECTED_MENU_CATEGORY}'"
                        {else}
                            onclick="{$DETAIL_VIEW_BASIC_LINK->getUrl()}"
                        {/if}
                        {if $MODULE_NAME eq 'Documents' && $DETAIL_VIEW_BASIC_LINK->getLabel() eq 'LBL_VIEW_FILE'}
                            data-filelocationtype="{$DETAIL_VIEW_BASIC_LINK->get('filelocationtype')}" data-filename="{$DETAIL_VIEW_BASIC_LINK->get('filename')}"
                        {/if}>
                    {vtranslate($DETAIL_VIEW_BASIC_LINK->getLabel(), $MODULE_NAME)}
                </button>
            {/foreach}

            {if !empty($DETAILVIEW_LINKS['DETAILVIEW']) && ($DETAILVIEW_LINKS['DETAILVIEW']|@count gt 0)}
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
                   {vtranslate('LBL_MORE', $MODULE_NAME)}&nbsp;&nbsp;<i class="caret"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    {foreach item=DETAIL_VIEW_LINK from=$DETAILVIEW_LINKS['DETAILVIEW']}
                        {if $DETAIL_VIEW_LINK->getLabel() eq ""} 
                            <li class="divider"></li>   
                            {else}
                            <li id="{$MODULE_NAME}_detailView_moreAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($DETAIL_VIEW_LINK->getLabel())}">
                                {if $DETAIL_VIEW_LINK->getUrl()|strstr:"javascript"} 
                                    <a href='{$DETAIL_VIEW_LINK->getUrl()}'>{vtranslate($DETAIL_VIEW_LINK->getLabel(), $MODULE_NAME)}</a>
                                {else}

                                    <a href='{$DETAIL_VIEW_LINK->getUrl()}&app={$SELECTED_MENU_CATEGORY}' >{vtranslate($DETAIL_VIEW_LINK->getLabel(), $MODULE_NAME)}</a>
                                {/if}
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {/if}
            </div>
            {if !{$NO_PAGINATION}}
            <div class="btn-group pull-right">
                <button class="btn btn-default " id="detailViewPreviousRecordButton" {if empty($PREVIOUS_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href = '{$PREVIOUS_RECORD_URL}&app={$SELECTED_MENU_CATEGORY}'" {/if} >
                    <i class="fa fa-chevron-left"></i>
                </button>
                <button class="btn btn-default  " id="detailViewNextRecordButton"{if empty($NEXT_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href = '{$NEXT_RECORD_URL}&app={$SELECTED_MENU_CATEGORY}'" {/if}>
                    <i class="fa fa-chevron-right"></i>
                </button>
                
            </div>
            {/if}        
        </div>
        <input type="hidden" name="record_id" value="{$RECORD->getId()}">
        <input type="hidden" name="record_status" value="{$RECORD->get('ap_estado')}">
    </div>
{strip}
{literal}
<script type="text/javascript">

   
function showConfirmationDialog(message, actionYes, actionNo) {
  var dialog = $("<div><div><h6>" + message + "</h6></div></div>").dialog({
    resizable: false,
    modal: true,
    title: "Advertencia",
    height: 150,
    width: 600,
    open: function(event, ui) {
      $(this).parent().find(".ui-dialog-titlebar-close").hide();
      $(this).find("span.fa-exclamation-triangle").css({
        "font-size": "40px",
        "color": "yellow"
      });
      $(this).find(".ui-dialog-buttonset button").addClass("confirm-box-btn-pad");
      $(this).find(".ui-dialog-buttonset button:contains('Sí')").addClass("btn confirm-box-ok btn-primary");
      $(this).find(".ui-dialog-buttonset button:contains('No')").addClass("btn btn-default confirm-box-btn-pad pull-right");
    },
    buttons: {
      "Sí": function() {
        $(this).dialog("close");
        actionYes();
      },
      "No": function() {
        $(this).dialog("close");
        actionNo();
      }
    },
    close: function() {
      $(this).remove();
    }
  });

  dialog.find("div.ui-dialog-titlebar").css("background-color", "yellow");
}

function hacerAccion() {
    var id = jQuery('[name="record_id"]').val();
    window.location.href = "index.php?module=AtencionPresencial&view=Edit&record=" + id + "&app=SUPPORT";
}

function hacerOtraAccion() {
    var id = jQuery('[name="record_id"]').val();
  console.log("Se realizó otra acción.");
  var progressIndicatorElement = jQuery.progressIndicator();
                           

                            if ($('[name="tieneTickets"]').val() == '') {
                                console.log("entra en el if");
                                var message = '¿Desea dar por finalizada la atención?';
                                message += ' Tenga en cuenta que no hay ningún ticket asociado a ésta.';
                                app.helper.showConfirmationBox({'message' : message})
                                .then(function() {
                                    app.helper.showProgress();

                                    var params = {};
                                    params['module'] = 'AtencionPresencial';
                                    params['view'] = 'CargarFinAtenciones';
                                    params['id'] = id;

                                    AppConnector.request(params).then(
                                    function(data) {
                                        var callBackFunction = function(data) {
                                    // Función de devolución de llamada (callback) después de mostrar la ventana modal
                                    };

                                    app.helper.hideProgress();
                                    app.showModalWindow(data, function(data) {
                                    if (typeof callBackFunction == 'function') {
                                        callBackFunction(data);
                                        console.log("Entrando");
                                        }
                                            }, false);
                                    },
                                    function(error) {
                                        console.log("error");
                                        app.helper.hideProgress();
                                      }
                                    );
                             });
                            }else{
                                    app.helper.showProgress();
                                    console.log("entra en el else");
                                    var params = {};
                                    params['module'] = 'AtencionPresencial';
                                    params['view'] = 'CargarFinAtenciones';
                                    params['id'] = id;

                                    AppConnector.request(params).then(
                                    function(data) {
                                        var callBackFunction = function(data) {
                                    // Función de devolución de llamada (callback) después de mostrar la ventana modal
                                    };

                                    app.helper.hideProgress();
                                    app.showModalWindow(data, function(data) {
                                    if (typeof callBackFunction == 'function') {
                                        callBackFunction(data);
                                        console.log("Entrando");
                                        }
                                            }, false);
                                    },
                                    function(error) {
                                        console.log("error");
                                        app.helper.hideProgress();
                                      }
                                    );
                            }

                            
}


    
$( document ).ready(function() {


        var params = {};
        var id = jQuery('[name="record_id"]').val();
        var persona = '';
        params['module'] = 'AtencionPresencial';
        params['action'] = 'getPersona';
        params['id'] = id;
        AppConnector.request(params).then(
            function(data) {
                console.log("mostrando informacion:");
                console.log(data.result.nombre); 
                persona = data.result.nombre;
               
            },
            function(error) {
                console.log("error");
                app.helper.hideProgress();
                }
            );




            $("#Finalizar").click(function(e){ 
                var id = jQuery('[name="record_id"]').val();
                
                        if (persona === 'USUARIO GENERICO') {
                               showConfirmationDialog("La atención tiene un usuario genérico ¿Desea modificarlo?", hacerAccion, hacerOtraAccion);


                        } else{
                            var progressIndicatorElement = jQuery.progressIndicator();
                            

                            if ($('[name="tieneTickets"]').val() == '') {
                                var message = '¿Desea dar por finalizada la atención?';
                                message += ' Tenga en cuenta que no hay ningún ticket asociado a ésta.';
                                app.helper.showConfirmationBox({'message' : message})
                                .then(function() {
                                    app.helper.showProgress();

                                    var params = {};

                                    params['module'] = 'AtencionPresencial';
                                    params['view'] = 'CargarFinAtenciones';
                                    params['id'] = id;

                                    AppConnector.request(params).then(
                                    function(data) {
                                        var callBackFunction = function(data) {
                                    // Función de devolución de llamada (callback) después de mostrar la ventana modal
                                    };

                                    app.helper.hideProgress();
                                    app.showModalWindow(data, function(data) {
                                    if (typeof callBackFunction == 'function') {
                                        callBackFunction(data);
                                        console.log("Entrando");
                                        }
                                            }, false);
                                    },
                                    function(error) {
                                        console.log("error");
                                        app.helper.hideProgress();
                                      }
                                    );
                                });
                            }else{
                                                                    var params = {};

                                    params['module'] = 'AtencionPresencial';
                                    params['view'] = 'CargarFinAtenciones';
                                    params['id'] = id;

                                    AppConnector.request(params).then(
                                    function(data) {
                                        var callBackFunction = function(data) {
                                    // Función de devolución de llamada (callback) después de mostrar la ventana modal
                                    };

                                    app.helper.hideProgress();
                                    app.showModalWindow(data, function(data) {
                                    if (typeof callBackFunction == 'function') {
                                        callBackFunction(data);
                                        console.log("Entrando");
                                        }
                                            }, false);
                                    },
                                    function(error) {
                                        console.log("error");
                                        app.helper.hideProgress();
                                      }
                                    );
                            }

                            
                        }
                                      
             });
            
         

    });

    


    

</script>
{/literal}