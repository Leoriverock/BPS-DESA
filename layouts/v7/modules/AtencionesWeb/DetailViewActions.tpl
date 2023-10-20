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
            {if $RECORD->get('aw_estado') == 'Asignado' && $RECORD->get('assigned_user_id') == $CURRENT_USER_MODEL->getId()}
                <button class="btn btn-success" style="margin-left: 50%" name="fin_atencion">Finalizar</button>
                <button class="btn btn-success" style="margin-left: 50%" name="pausa_atencion">Pausar</button>
            {/if}
            {if $RECORD->get('aw_estado') == 'Pausado' && $RECORD->get('assigned_user_id') == $CURRENT_USER_MODEL->getId()}
             
             <button class="btn btn-success" style="margin-left: 50%" name="reanudar_atencion">Reanudar</button>
             {/if}
            {assign var=STARRED value=$RECORD->get('starred')}

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
    </div>
{strip}
{literal}
<script type="text/javascript">
    $(document).ready(function() {
            console.log( "ready!" );
            jQuery.ajax({
            url: 'index.php?module=HelpDesk&action=controlActivas',
            dataType: 'json',
            success: function (data) {
                console.log(data);
                llamada = data.result.llamada.callsid; 
                atweb = data.result.atencionWeb; 
                atpre = data.result.AtencionPresencial.atencionpresencialid;
                console.log(atweb + " " + atpre + " " + llamada );
                error = 0;
                if(llamada){ error =error + 1; }
                //if(atweb ){ error = error + 1; }
                if(atpre){ error = error + 1; }
                 console.log("error "+ error);
                if(error > 0){
                    
                        app.helper.showAlertNotification({'message' : 'Debe finalizar o pausar la llamada o atención para continuar'});
                        jQuery('[name="reanudar_atencion"]').attr('disabled',true);
                    }
                }
        });
    });
</script>
{/literal}