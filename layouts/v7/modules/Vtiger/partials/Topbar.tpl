{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}



{strip}
	{include file="modules/Vtiger/Header.tpl"}
	{*include file="config.ludere.php"*}
	{assign var=APP_IMAGE_MAP value=Vtiger_MenuStructure_Model::getAppIcons()}
	{assign var=CALLINGROUP value=$CURRENT_USER_MODEL->getCallinGroups()}
	<nav class="navbar navbar-inverse navbar-fixed-top app-fixed-navbar">
		<div class="container-fluid global-nav">
			<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-8 app-navigator-container">
					<div class="row">
						<div id="appnavigator" class="col-sm-2 col-xs-2 cursorPointer app-switcher-container" data-app-class="{if $MODULE eq 'Home' || !$MODULE}fa-dashboard{else}{$APP_IMAGE_MAP[$SELECTED_MENU_CATEGORY]}{/if}">
							<div class="row app-navigator">
								<span class="app-icon fa fa-bars"></span>
							</div>
						</div>
						<div class="logo-container col-sm-3 col-xs-9">
							<div class="row">
								<a href="index.php" class="company-logo">
									<img src="{$COMPANY_LOGO->get('imagepath')}" alt="{$COMPANY_LOGO->get('alt')}"/>
								</a>
							</div>
						</div>  
					</div>
				</div>
				<div class="navbar-header paddingTop5">
					<button type="button" class="navbar-toggle collapsed border0" data-toggle="collapse" data-target="#navbar" aria-expanded="false">
						<i class="fa fa-th"></i>
					</button>
					<button type="button" class="navbar-toggle collapsed border0" data-toggle="collapse" data-target="#search-links-container" aria-expanded="false">
						<i class="fa fa-search"></i>
					</button>
				</div>
				<div class="col-sm-3">
					<div id="search-links-container" class="search-links-container collapse navbar-collapse">
						<div class="search-link">
							<span class="fa fa-search" aria-hidden="true"></span>
							<input class="keyword-input" type="text" placeholder="{vtranslate('LBL_TYPE_SEARCH')}" value="{$GLOBAL_SEARCH_VALUE}">
							<span id="adv-search" class="adv-search fa fa-chevron-circle-down pull-right cursorPointer" aria-hidden="true"></span>
						</div>
					</div>
				</div>
				<div id="navbar" class="col-sm-6 col-xs-12 collapse navbar-collapse navbar-right global-actions">
					<ul class="nav navbar-nav">
						{if $CALLINGROUP}
						<li class = "active-call">
							<div>
								<a id = "linkToCall">
									<i class="fa fa-phone"></i>
									&nbsp;&nbsp;&nbsp;
									<label id = "lblcallphone">
										{if $ACTIVE_CALL}
											{$ACTIVE_CALL.callphonenumber}
										{else}
											Sin llamadas
										{/if}
									</label>
								</a>	
							</div>
						</li>
						{/if}
						{if $CURRENT_USER_MODEL}
						{assign var=ACTIVA value=$CURRENT_USER_MODEL->getAtencionActiva()}
						{assign var=ACTIVAP value=$CURRENT_USER_MODEL->getAtencionPActiva()}
						{assign var=CONECTADO value=$CURRENT_USER_MODEL->getConectado()}
						{assign var=ACCESO value=$CURRENT_USER_MODEL->tienePermisoPresencial()}
						{assign var=EMAIL value=$CURRENT_USER_MODEL->getEmailActiva()}
						{*assign var=DATOS value=$CURRENT_USER_MODEL->getDatosAPActiva()*}
						{assign var=moduloAP value=Vtiger_Module_Model::getInstance('AtencionPresencial')}
						{if $ACTIVA}
						<li class="attentionactiva" title="{$EMAIL}">
							<div>
								<a href="index.php?module=AtencionesWeb&view=Detail&record={$ACTIVA}&app=SUPPORT">
									<img src="layouts/v7/icons_custom_modules/AtencionesWeb-white.png" style="width:20px;" title="Atenciones Web" alt="layouts/v7/icons_custom_modules/AtencionesWeb-red.png">
									&nbsp;&nbsp;&nbsp;
									<label id = "lblatention" >
										{vtranslate('Active_Attention', 'Vtiger')}
									</label>
								</a>	
							</div>
							<style type="text/css">
								.attentionactiva{
									background: #54F148;
    								color: white;
								}
							</style>
						</li>

						{/if}
						{if $moduloAP}
						<!--Atencion Presencial Activa -->
						{if $ACTIVAP}
						<li class="attentionpactiva" >
							<div>
								<!-- lblatention-->
								<a href="index.php?module=AtencionPresencial&view=Detail&record={$ACTIVAP}&app=SUPPORT">
									<i class="fa fa-user"></i>
									&nbsp;&nbsp;&nbsp;
									<label id = "lblatention" >
										{'Atencion Presencial Activa'}
									</label>
								</a>	
							</div>
							<style type="text/css">
								.attentionpactiva{
									background: #35bfda;
    								color: white;
								}
							</style>
						</li>

						{/if}

						{if $ACCESO}

						<li class = "" {if $CONECTADO} id="abierto" {else} id="cerrado"{/if}>
							<div >
								<a id = "" {if !$ACTIVAP} onclick="cargarModalIniciar()" {/if} {if $ACTIVAP} style="background-color: gray;" {/if} {if $ACTIVAP} title="Finaliza la atención para cerrar puesto" {/if}>
									{if $CONECTADO}
									<i class="fa-solid fa-lock"></i>
									{else}
									<i class="fas fa-lock-open"></i>
									{/if}
									&nbsp;&nbsp;&nbsp;
									<label id = "" >
										{if $CONECTADO}
											Cerrar puesto
										{else}
											Abrir puesto
										{/if}
										
									</label>
								</a>	
							</div>
							<!--<style type="text/css">
								.atte{
									background: #ffffff;
    								color: white;
								}
							</style>-->
						</li>
						{/if}
						{/if}
						{/if}
						{if $CURRENT_USER_MODEL && method_exists($CURRENT_USER_MODEL, 'getGroupsPreferencesOption')}
						{assign var=CURRENT_USER_MODELAUX value=Vtiger_Record_Model::getInstanceById($CURRENT_USER_MODEL->getId(), 'Users')}
						{assign var=SELECTEDGROUPPREF value=$CURRENT_USER_MODELAUX->get('us_grupopref')}
						{assign var=MODELGROUPPREF value=Settings_Groups_Record_Model::getInstance($SELECTEDGROUPPREF)}
						{assign var=GROUPS value=$CURRENT_USER_MODELAUX->getGroupsPreferencesOption('mail')}
						{if count($GROUPS) > 1}
						<li class="grupopreferencia">
							<div class="dropdown pull-left">
								<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									<a href="#" title="{vtranslate('Groups_active', 'Vtiger')}">
										<i class="fa fa-users"></i>
										&nbsp;&nbsp;&nbsp;
										<label name="prefgroup_label" {if $MODELGROUPPREF}
												data-name='{$MODELGROUPPREF->getName()}'
												data-id='{$MODELGROUPPREF->getId()}'
											{else}
												data-name="{vtranslate('LBL_PREFGROUP_UNASSIGNED',$MODULE)}"
											{/if}>
											{if $MODELGROUPPREF}
												{$MODELGROUPPREF->getName()}
											{else}
												{vtranslate('LBL_PREFGROUP_UNASSIGNED',$MODULE)}
											{/if}
										</label>
									</a>	
								</div>
								<ul class="dropdown-menu dropdowngrouppref" role="menu" aria-labelledby="dropdownMenu1" style="width:300px;">
									<li class="title" style="padding: 5px 0 0 15px;">
										<strong>{vtranslate('LBL_PREFGROUP',$MODULE)}</strong>
									</li>
									<hr/>
									<li style="padding: 0 5px;">
										<form id="selectprefgroup">
											<input type="hidden" name="userid" value="{$CURRENT_USER_MODEL->getId()}"/>
											<div class="form-group" style="margin-top:30px">
												<label>{vtranslate('LBL_selectgroup', $MODULE)}</label>
												<select class="select2 form-control" name="selectprefgroup">
													<option {if empty($SELECTEDGROUPPREF)}select="true"{/if} value="">{vtranslate('LBL_PREFGROUP_UNASSIGNED',$MODULE)}</option>
													{foreach item=grupo from=$GROUPS}
														<option {if $SELECTEDGROUPPREF == $grupo->id}selected {/if} value="{$grupo->id}">{$grupo->label}</option>
													{/foreach}
												</select>
											</div>
											<hr>
											<button class="btn btn-success" onclick="Vtiger_Index_Js.setEventGroupPref();" type="submit">{vtranslate('LBL_SAVE', $MODULE)}</button>
										</form>
										<script type="text/javascript">
											$(document).ready(() => {
												/*setTimeout(() => {
													//Vtiger_Index_Js.setEventGroupPref();
												}, 1200)*/
											});
										</script>
									</li>
								</ul>
							</div>
						</li>
						{/if}
						{if count($GROUPS) == 1}
							<li class="grupopreferencia">
								<div>
									<a href="#">
										<i class="fa fa-users"></i>
										&nbsp;&nbsp;&nbsp;
										<label name="prefgroup_label" data-name='{$GROUPS[0]->label}'>
											{$GROUPS[0]->label}
										</label>
									</a>	
								</div>
							</li>
						{/if}
						{/if}
						{*simplemente oculto el + de quickcreate ya que el vtiger lo usa a nivel de js en otros lugares*}
						<li class="hide">
							<div class="dropdown pull-left">
								<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									<a href="#" id="menubar_quickCreate" class="qc-button fa fa-plus-circle" title="{vtranslate('LBL_QUICK_CREATE',$MODULE)}" aria-hidden="true"></a>
								</div>
								<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="width:500px;">
									<li class="title" style="padding: 5px 0 0 15px;">
										<strong>{vtranslate('LBL_QUICK_CREATE',$MODULE)}</strong>
									</li>
									<hr/>
									<li id="quickCreateModules" style="padding: 0 5px;">
										<div class="col-lg-12" style="padding-bottom:15px;">
											{foreach key=moduleName item=moduleModel from=$QUICK_CREATE_MODULES}
												{if $moduleModel->isPermitted('CreateView') || $moduleModel->isPermitted('EditView')}
													{assign var='quickCreateModule' value=$moduleModel->isQuickCreateSupported()}
													{assign var='singularLabel' value=$moduleModel->getSingularLabelKey()}
													{assign var=hideDiv value={!$moduleModel->isPermitted('CreateView') && $moduleModel->isPermitted('EditView')}}
													{if $quickCreateModule == '1'}
														{if $count % 3 == 0}
															<div class="row">
															{/if}
															{* Adding two links,Event and Task if module is Calendar *}
															{if $singularLabel == 'SINGLE_Calendar'}
																{assign var='singularLabel' value='LBL_TASK'}
																<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-lg-4 col-xs-4{/if}">
																	<a id="menubar_quickCreate_Events" class="quickCreateModule" data-name="Events"
																	   data-url="index.php?module=Events&view=QuickCreateAjax" href="javascript:void(0)">{$moduleModel->getModuleIcon('Event')}<span class="quick-create-module">{vtranslate('LBL_EVENT',$moduleName)}</span></a>
																</div>
																{if $count % 3 == 2}
																	</div>
																	<br>
																	<div class="row">
																{/if}
																<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-lg-4 col-xs-4{/if}">
																	<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModule" data-name="{$moduleModel->getName()}"
																	   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)">{$moduleModel->getModuleIcon('Task')}<span class="quick-create-module">{vtranslate($singularLabel,$moduleName)}</span></a>
																</div>
																{if !$hideDiv}
																	{assign var='count' value=$count+1}
																{/if}
															{else if $singularLabel == 'SINGLE_Documents'}
																<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-lg-4 col-xs-4{/if} dropdown">
																	<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModuleSubmenu dropdown-toggle" data-name="{$moduleModel->getName()}" data-toggle="dropdown" 
																	   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)">
																		{$moduleModel->getModuleIcon()}
																		<span class="quick-create-module">
																			{vtranslate($singularLabel,$moduleName)}
																			<i class="fa fa-caret-down quickcreateMoreDropdownAction"></i>
																		</span>
																	</a>
																	<ul class="dropdown-menu quickcreateMoreDropdown" aria-labelledby="menubar_quickCreate_{$moduleModel->getName()}">
																		<li class="dropdown-header"><i class="fa fa-upload"></i> {vtranslate('LBL_FILE_UPLOAD', $moduleName)}</li>
																		<li id="VtigerAction">
																			<a href="javascript:Documents_Index_Js.uploadTo('Vtiger')">
																				<img style="  margin-top: -3px;margin-right: 4%;" title="Vtiger" alt="Vtiger" src="layouts/v7/skins//images/Vtiger.png">
																				{vtranslate('LBL_TO_SERVICE', $moduleName, {vtranslate('LBL_VTIGER', $moduleName)})}
																			</a>
																		</li>
																		<li class="dropdown-header"><i class="fa fa-link"></i> {vtranslate('LBL_LINK_EXTERNAL_DOCUMENT', $moduleName)}</li>
																		<li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E')">&nbsp;<i class="fa fa-external-link"></i>&nbsp;&nbsp; {vtranslate('LBL_FROM_SERVICE', $moduleName, {vtranslate('LBL_FILE_URL', $moduleName)})}</a></li>
																		<li role="separator" class="divider"></li>
																		<li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W')"><i class="fa fa-file-text"></i> {vtranslate('LBL_CREATE_NEW', $moduleName, {vtranslate('SINGLE_Documents', $moduleName)})}</a></li>
																	</ul>
																</div>
															{else}
																<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-lg-4 col-xs-4{/if}">
																	<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModule" data-name="{$moduleModel->getName()}"
																	   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)">
																		{$moduleModel->getModuleIcon()}
																		<span class="quick-create-module">{vtranslate($singularLabel,$moduleName)}</span>
																	</a>
																</div>
															{/if}
															{if $count % 3 == 2}
																</div>
																<br>
															{/if}
														{if !$hideDiv}
															{assign var='count' value=$count+1}
														{/if}
													{/if}
												{/if}
											{/foreach}
										</div>
									</li>
								</ul>
							</div>
						</li>
						{assign var=USER_PRIVILEGES_MODEL value=Users_Privileges_Model::getCurrentUserPrivilegesModel()}
						{assign var=CALENDAR_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Calendar')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($CALENDAR_MODULE_MODEL->getId())}
							<li><div><a href="index.php?module=Calendar&view={$CALENDAR_MODULE_MODEL->getDefaultViewName()}" class="fa fa-calendar" title="{vtranslate('Calendar','Calendar')}" aria-hidden="true"></a></div></li>
						{/if}
						{assign var=REPORTS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Reports')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($REPORTS_MODULE_MODEL->getId())}
							<li><div><a href="index.php?module=Reports&view=List" class="fa fa-bar-chart" title="{vtranslate('Reports','Reports')}" aria-hidden="true"></a></div></li>
						{/if}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($CALENDAR_MODULE_MODEL->getId())}
							<li><div><a href="#" class="taskManagement vicon vicon-task" title="{vtranslate('Tasks','Vtiger')}" aria-hidden="true"></a></div></li>
						{/if}
						<li class="dropdown">
							<div>
								<a href="#" class="userName dropdown-toggle pull-right" data-toggle="dropdown" role="button">
									<span class="fa fa-user" aria-hidden="true" title="{$USER_MODEL->get('userlabel')}
										  ({$USER_MODEL->get('user_name')})"></span>
									<span class="link-text-xs-only hidden-lg hidden-md hidden-sm">{$USER_MODEL->getName()}</span>
								</a>
								<div class="dropdown-menu logout-content" role="menu">
									<div class="row">
										<div class="col-lg-4 col-sm-4">
											<div class="profile-img-container">
												{assign var=IMAGE_DETAILS value=$USER_MODEL->getImageDetails()}
												{if $IMAGE_DETAILS neq '' && $IMAGE_DETAILS[0] neq '' && $IMAGE_DETAILS[0].path eq ''}
													<i class='vicon-vtigeruser' style="font-size:90px"></i>
												{else}
													{foreach item=IMAGE_INFO from=$IMAGE_DETAILS}
														{if !empty($IMAGE_INFO.url)}
															<img src="{$IMAGE_INFO.url}" width="100px" height="100px">
														{/if}
													{/foreach}
												{/if}
											</div>
										</div>
										<div class="col-lg-8 col-sm-8">
											<div class="profile-container">
												<h4>{$USER_MODEL->get('first_name')} {$USER_MODEL->get('last_name')}</h4>
												<h5 class="textOverflowEllipsis" title='{$USER_MODEL->get('user_name')}'>{$USER_MODEL->get('user_name')}</h5>
												<p>{$USER_MODEL->getUserRoleName()}</p>
											</div>
										</div>
									</div>
									<div id="logout-footer" class="logout-footer clearfix">
										<hr style="margin: 10px 0 !important">
										<div class="">
											<span class="pull-left">
												<span class="fa fa-cogs"></span>
												<a id="menubar_item_right_LBL_MY_PREFERENCES" href="{$USER_MODEL->getPreferenceDetailViewUrl()}">{vtranslate('LBL_MY_PREFERENCES')}</a>
											</span>
											<span class="pull-right">
												<span class="fa fa-power-off"></span>
												<a id="menubar_item_right_LBL_SIGN_OUT" href="index.php?module=Users&action=Logout">{vtranslate('LBL_SIGN_OUT')}</a>
											</span>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
{/strip}
{literal}
<!--<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">-->


<link rel="stylesheet" href="libraries/fontawesome/css/all.min.css">


<style type="text/css" >

	
	#abierto {
		
		background-color: red; /* fondo verde claro */
		
		color: #fff; /* letras blancas */
		border: none; /* sin borde */
		display: inline-block;
		/*position: relative;*/
		overflow: hidden;
		transition: all 0.3s ease-in-out; /* transición suave para hover */
	}

	

	#abierto:hover {
		background-color: #CC1723; /* cambio de color al hacer hover */
		color: grey !important;
	}	
	

	#cerrado {
		
		background-color: #39AD5A; /* fondo verde claro */
		
		color: #fff; /* letras blancas */
		border: none; /* sin borde */
		display: inline-block;
		/*position: relative;*/
		overflow: hidden;
		transition: all 0.3s ease-in-out; /* transición suave para hover */
	}

	

	#cerrado:hover {
		background-color: #218A3F; /* cambio de color al hacer hover */
		color: grey !important;
	}	


</style>
	<script type="text/javascript">

		/*$(document).ready(function() {

			  // Llamada cada 15 minutos
			  setInterval(function() {
			    controlarAbrirPuesto();
			  }, 18000000); // 900000 milisegundos = 15 minutos
			  
		});*/

		function cargarModalIniciar() {
				var progressIndicatorElement = jQuery.progressIndicator();
		 		var params = {};
		        params['module'] = 'AtencionPresencial';
		        params['view'] = 'CargarModalAbrirPuesto';
		        AppConnector.request(params).then(
		            function(data) {
		            	
		                var callBackFunction = function(data) {
		                }
		                app.showModalWindow(data,function(data){
		                    if(typeof callBackFunction == 'function'){
		                        callBackFunction(data);
		                        console.log("Entrando");
		                    }
		                }, false);
		                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
		            },
		            function(error) {
		                console.log("error");
		                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
		            	}
		        	);
					

				}
		/*function controlarAbrirPuesto(){
				console.log("controlando este abierto el puesto");
					var params = {};
				   	params['module'] = 'AtencionPresencial';
				    params['action'] = 'getAcciones';
				    params['modo'] = 'controlarPuesto';
				   
				    console.log(params);
				    var progressIndicatorElement = jQuery.progressIndicator();
				    AppConnector.request(params).then(function(data){				    	
				    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				    	//console.log(data.result.error);
				    	if (data && data.result && data.result.success){
				    		app.helper.showSuccessNotification({title:'Exito', message:'Se ha cerrado el puesto por inactividad'});
				    	}
				    	
				    	
				    	
				    },function(error){
				    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				    	//app.helper.showErrorNotification({message:error.message})
				    	console.log(error);
				    });	
		}		*/


		

		
		
	</script>
{/literal}