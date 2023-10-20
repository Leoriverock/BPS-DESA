{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{literal}
	<link rel="stylesheet" href="libraries/fontawesome/css/all.min.css">
	<style type="text/css">
		.contenedor{
			margin-right: 25px;
			margin-left: 25px;
  			height: 300px;

		}
		
		.contenido{
			text-align: left;
			font-family: Arial, Helvetica, Sans-serif;
			font-size: 14px;
			font-weight: 400;
			margin:5px; 
			padding: 20px;
			color: #5b5b5f;
		}
		.seccion{
			text-align: center;
			font-family: Arial, Helvetica, Sans-serif;
			font-size: 14px;
			font-weight: bold;
			font-weight: 600;
		}
		.formato{
			font-size: 12px;
			font-weight: bold;
			font-weight: 400;
			font-family: Arial;
			color: #5b5b5f;
			text-align: center;
			padding: 0;
		}
		.formato_texto{
			font-size: 11px;
			font-family: Arial;
			color: #5b5b5f;
		}
		.table-hover tbody tr:hover{
			background: #F0F8FF;
		}
		.table-hover tbody tr td input:hover{
			background: #F0F8FF;
		}
		.table-hover tbody tr td input{
			text-align: center;
		}
		#Adicionales{
			font-size: 10px;
			font-weight: bold;
			font-weight: 400;
			font-family: Arial;
			color: #5b5b5f;
			text-align: center;
			margin-right: 5px;
			 
		}
		#tablaConsulta{
			width: 100%;

		}
		#tablaConsulta tr{
			margin:0px ;
			padding: 0px;
		}
		#tablaConsulta th{
			margin:0px ;
			padding: 0px;
		}

		.sinborde{
			background-color: #fff;
  			border: 0;
		}
		.titulo{
			font-weight: bold;
			font-size: 13px;
			font-family: Arial, Helvetica, Sans-serif;
			text-align: center;
		}
		.subtitulo{
			  color: #A9A9A9;
			  font-weight: bold;
			  font-size: 33px;
			  font-family: Arial, Helvetica, Sans-serif;
			  text-align: center;
			  text-transform: uppercase;
		}
		.boton{
			color: tomato;
			background-color: white;
			border-radius: 0px;
		    font-weight: 100;
		    cursor: pointer;
		    border-width: thin;
		    font-size: 18px;

		}
		#botones{
			display: flex;
		  	justify-content: center;
		  	align-items: center;
		}
		#botones button{
			
			font-size: 18px;
			margin-top: 10px;
			margin-bottom: 10px;
			padding: 10px 20px 10px 20px;
			
		}
		.mensaje{
			color: Tomato;
			font-family: Arial, Helvetica, Sans-serif;
			text-align: center;
			text-transform: uppercase;
		}
		/********** SELECT ************/
		#Select{
			width: 115px;
		}
		
		input[type="text"]
		{
		  	width: auto;
  			word-wrap: break-word;
  			padding: 0px;
  			margin: 0px;
		}

		#cerrarPuesto {
			margin: 1px;
			padding:15px !important;
			background-color: red; /* fondo verde claro */
			
			color: #fff; /* letras blancas */
			border: none; /* sin borde */
			display: inline-block;
			/*position: relative;*/
			overflow: hidden;
			transition: all 0.3s ease-in-out; /* transición suave para hover */
		}

		

		#cerrarPuesto:hover {
			background-color: #CC1723; /* cambio de color al hacer hover */
			color: grey !important;
		}	
		

		#abrirpuesto {
			margin: 1px;
			padding:15px !important;
			background-color: #39AD5A; /* fondo verde claro */
			
			color: #fff; /* letras blancas */
			border: none; /* sin borde */
			display: inline-block;
			/*position: relative;*/
			overflow: hidden;
			transition: all 0.3s ease-in-out; /* transición suave para hover */
		}

		

		#abrirpuesto:hover {
			background-color: #218A3F; /* cambio de color al hacer hover */
			color: grey !important;
		}	

		.cerrado{
			color: #CC1723;
			text-align: center;
			font-weight: bold;

		}	
		.abierto{
			color: #39AD5A;
			text-align: center;
			font-weight: bold;
		}

	</style>
{/literal}

{strip}
		{include file="PicklistColorMap.tpl"|vtemplate_path:$MODULE LISTVIEW_HEADERS=$RELATED_HEADERS}
		<div class="modal-dialog modal-lg" style="width: 25%;height: 250px;">
			<div class="modal-content" style="height: 250px">
			    <div class="modal-header">
				    <button type="button" class="close" id="cerrar" data-dismiss="modal" aria-hidden="true">&times;</button>
				    <h4 class="modal-title"> {if $CONECTADO}Cerrar puesto - Conectado {else} Abrir Puesto - Desconectado {/if} </h4>


			    </div>
			    
		  		<div class="modal-body" style="height: 200px">
		  			<div  id="tablaMov" >
						<div class="contenedor"> 
						<h3 {if $CONECTADO} class="abierto" {else} class="cerrado" {/if}>{if !$CONECTADO} Puesto Cerrado {else} Puesto Abierto {/if} </h3>
						<table id="tablaConsulta">
						  <tbody style="text-align: center; vertical-align: center">
						    <tr>
						      <th style="text-align: center; color:#929292; font-size: 14px;"><i class="fa-solid fa-user"></i> {$USUARIO} </th>
						    </tr>
						    <tr>
						      <th style="text-align: center; color:#929292; font-size: 14px;"><i class="fa-solid fa-desktop"></i> {$EQUIPO} </th>
						    </tr>
						    <tr>
						      <th id="sector"> </th>
						    </tr>
						  </tbody>
						</table>

						<div id="botones" style="padding-top: 25px;">
								<div id="hijo">
									{if !$CONECTADO}
									<button  type="button" id="abrirpuesto" data-dismiss="modal" aria-hidden="true" class="btn btn-success" style="margin-left:5px;"> <i class="fas fa-lock-open"></i> Abrir Puesto </button>
									{elseif $CONECTADO}
									<button  type="button" data-dismiss="modal" aria-hidden="true" id="cerrarPuesto" class="btn boton-warning" ><i class="fa-solid fa-lock"></i> Cerrar Puesto</button>
									{/if}
								</div>
								
							</div>
						
							
						</div>
					</div>
					</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
{/strip}
{literal}
<script type="text/javascript">

			/*function abrirpuesto(){
				var params = {};
			   	params['module'] = 'AtencionPresencial';
			    params['action'] = 'getAcciones';
			    params['modo'] = 'abrirPuesto';
			    console.log(params);
			    var progressIndicatorElement = jQuery.progressIndicator();
			    AppConnector.request(params).then(function(data){
			    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
			    	if (data && data.result && data.result.success) {
			    		
			    		app.helper.showSuccessNotification({title:'Bienvenido', message:'Se ha abierto el puesto'});
			    		//$("#sector").text("Sector: Banco de la republica");
			    		location.reload();
			    	}else{
			    		if(data && data.result && data.result.error){
			    			app.helper.showErrorNotification({message:data.result.error});
			    		}else{
			    			app.helper.showErrorNotification({message:'Hubo problemas al abrir puesto.'});
			    		}
			    	}
			    },function(error){
			    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
			    	app.helper.showErrorNotification({message:error.message})
			    	console.log(error);
			    });
			};*/

			$(document).ready(function() {
    			console.log( "ready!" );
    			function liberarPuesto(){
    				var params = {};
				   	params['module'] = 'AtencionPresencial';
				    params['action'] = 'getAcciones';
				    params['modo'] = 'liberarPuesto';
				    console.log(params);
				    var progressIndicatorElement = jQuery.progressIndicator();
				    AppConnector.request(params).then(function(data){
				    	//progressIndicatorElement.progressIndicator({'mode' : 'hide'});.
				    	console.log("Liberado");
				    	console.log(data);
				    });
    						
    			}

    			$('#abrirpuesto').click(function(e){
    				var params = {};
				   	params['module'] = 'AtencionPresencial';
				    params['action'] = 'getAcciones';
				    params['modo'] = 'abrirPuesto';
				    console.log(params);
				    var progressIndicatorElement = jQuery.progressIndicator();
				    AppConnector.request(params).then(function(data){
				    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				    	if (data && data.result && data.result.success) {
				    		
				    		app.helper.showSuccessNotification({title:'Bienvenido', message:'Se ha abierto el puesto'});
				    		//$("#sector").text("Sector: Banco de la republica");
				    		location.reload();
				    	}else{
				    		if(data && data.result && data.result.error){
				    			app.helper.showErrorNotification({message:data.result.error});
				    		}else{
				    			app.helper.showErrorNotification({message:'Hubo problemas al abrir puesto.'});
				    		}
				    	}
				    },function(error){
				    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
				    	app.helper.showErrorNotification({message:error.message})
				    	console.log(error);
				    });
    							
    							
    			});

    			$('#cerrarPuesto').click(function(e){
    						var params_est = {};
				    		//var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
						   	params_est['module'] = 'AtencionPresencial';
						    params_est['action'] = 'getAcciones';
						    params_est['modo'] = 'obtenerEstadoPuesto';
						    console.log(params_est);
						    app.helper.showProgress();
						    AppConnector.request(params_est).then(function(data){
						    	app.helper.hideProgress();

						    	console.log(data.result.respuesta);
						    	if (data.result.respuesta == 'D') {
						    		//cierro puesto
						    		var params = {};
								   	params['module'] = 'AtencionPresencial';
								    params['action'] = 'getAcciones';
								    params['modo'] = 'cerrarPuesto';
								    console.log(params);
								    var progressIndicatorElement = jQuery.progressIndicator();
								    AppConnector.request(params).then(function(data){
								    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
									    	if (data && data.result && data.result.success) {
									    		
									    		app.helper.showSuccessNotification({title:'Hasta luego', message:'Puesto cerrado con exito'});
									    		setTimeout(function() {
									    			console.log("entra al conteo");
												 	location.reload();
												}, 2000); 

									    		
									    	}else{
									    		if(data && data.result && data.result.error){
									    			app.helper.showErrorNotification({message:data.result.error});
									    		}else{
									    			app.helper.showErrorNotification({message:'Hubo problemas al cerrar puesto'});
									    		}
									    	}
										    },function(error){
										    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
										    	app.helper.showErrorNotification({message:error.message})
										    	console.log(error);
										    });
								    	}else{
								    		//Si se cerro el puesto por GAP entonces borro registros del crm
								    		liberarPuesto();
								    		app.helper.showSuccessNotification({title:'Hasta luego', message:'El puesto fue cerrado desde la aplicación Sistema de Gestión de Atención al Público'});
								    		setTimeout(function() {
									    			console.log("entra al conteo");
												 	location.reload();
												}, 4000); 
								    		
								    	}
								    },function(error){
								    	app.helper.hideProgress();
								    	app.helper.showErrorNotification({message:error.message})
								    	console.log(error);
								    });
    					
    			});


    			
    		});
</script>
{/literal}