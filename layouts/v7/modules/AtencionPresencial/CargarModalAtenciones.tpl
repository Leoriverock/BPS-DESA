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
		width: 100%;
		max-height: 400px;
		overflow-y: auto;

		overflow-x: hidden;


			flex-wrap: wrap;
			margin: 20px;
			/*width: 65%;*/
	}
	.contenedor::-webkit-scrollbar {
	        width: 0; /* Oculta la barra de desplazamiento en navegadores basados en WebKit */
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
		background-color: #fbf4f3 !important;
	}
	.formato_texto{
		font-size: 11px;
		font-family: Arial;
		color: #5b5b5f;
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
		font-family: Arial, Helvetica, Sans-serif;
		text-align: center;
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
		color: #4CB630;
		
		text-align: center;
	}
	#tablaMov {
		height: 400px;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		margin: 20px;
		/*margin-top: 130px;*/
		font-family: Arial, Helvetica, Sans-serif;
	}

	#tablaMov i {
		font-size: 3rem;
	}

	#tablaMov h2.mensaje {
		text-align: center;
	}


	.theaders{
		text-align: center;
		background-color: black;
		color: white;
	}

	.definido{
		width: 20%;
	}

	.tablaatrasadas, .tablapendientes {
		border: 1px solid !important;
    	border-color: #aba4a4 !important;
	}

	.tablaatrasadas th, .tablaatrasadas td{
		border: 1px solid !important;
    	border-color: #aba4a4 !important;
	}

	.tablapendientes th, .tablapendientes td{
		border: 1px solid !important;
    	border-color: #aba4a4 !important;
	}

	.modal-dialog .table td{
		padding: 4px !important;
	}

	.tablapendientes{
		width: 96%;
		table-layout: fixed;
	  	margin-top: 10px;
	  	border-top: 8px solid green !important;
	  
	}

	.tablaatrasadas{
		width: 96%;
		table-layout: fixed;
		margin-top: 10px;
		border-top: 8px solid #ea2626 !important;
		
	}

	.modal-atenciones{
		min-width: 800px;
	}

	.numero:hover{
		opacity: 60% !important;

		background-color: grey;
		/*linear-gradient(to right, rgba(200, 200, 200, 0.9), rgba(255, 0, 0, 0.9));*/
		color: black;

	}


	.tabladetalle{
		width: 80%;
    	margin-left: 10%;
	}

	.contenedor .nav-item{
		width: 49%;
		height: 55px;
	    padding: 0px 10px 0px 10px !important;
	    background: #e6e6e6;
	    /*border-radius: 5px 5px 0px 0px;*/
	}

	.contenedor .nav-item{
		width: 49%;
	    padding: 0px 10px 0px 10px !important;
	    background: #e6e6e6;
	    /*border-radius: 5px 5px 0px 0px;*/
	}

	.contenedor .nav-item{
		width: 49%;
	    padding: 0px 10px 0px 10px !important;
	    background: #e6e6e6;
	    /*border-radius: 5px 5px 0px 0px;*/
	}

	.item-pendientes.active{
		background: green;
	}

	.item-atrasadas.active{
		background: #ea2626;
	}

	.nav-item.active .subtitulo{
		text-align: left;
		color: white;
	}

	.subtitulo{
		font-size: 2em;
	}
	.nav-item .subtitulo{
		text-align: left;
		color: white;
	}
	#lista{
		margin-top: 10px;
	}
	#icon{
		font-size:0.8em !important;
	}

	#aptabs {
		position: fixed;
		top: 50px;
		left: 22px;
		width: 97%;
		background-color: #fff; /* Puedes establecer el color de fondo que desees */
		z-index: -999; /* Asegura que las pestañas queden por encima del resto de los elementos */
	}
	.contenedor{
		position: fixed;
		top:110px;
		width: 100%;
		left: 25px;
		margin: 0;
		padding: 0;
	}
	.sector-td {
		overflow: hidden;
		white-space: nowrap;
	}

	.sector-td.long {
		white-space: nowrap;
		font-size: 12px !important; /* Tamaño de letra para contenido largo */
		margin:0px !important;
		padding: 0px !important;

	}
	.numero:nth-child(even) {
		background-color: #c7d4cb; /* Color de fondo para filas pares */
	}

  	.numero:nth-child(odd) {
		background-color: #ffffff; /* Color de fondo para filas impares */
	}
	.numero:nth-child(even):hover {
		
		background-color: #718878; /* Color de fondo para filas pares */
	}

  	.numero:nth-child(odd):hover {
  		
  		
		background-color: #718878; /* Color de fondo para filas pares */
	}
	.numero:hover {
		cursor: pointer;
		
	}

  	/*******************************************/
  	.left-column {
  			margin:0 !important;
  			padding: 0 !important;
			flex-basis: 50%;
			height: 100%;
			width: 48%;
			float: left;
			/*margin-left: 85px !important;*/
			/*align-items: flex-end;*/
		
		}
		.right-column {
			margin:0 !important;
  			padding: 0 !important;
			width: 48%;
			float: left;
			align-items: center;
			height: 100%;
			text-align: center;
		}
		
		.call-number {
			text-align: center;
			font-size: 148px;
			font-weight: bold;
			border: none;
			background-color: transparent;
			width: 200px;
			height: 100%;
			margin: 0 auto;
		}
		.call-button {
			font-size: 18px;
			color: #fff;
			background-color: #333;
			border: none;
			padding: 10px 20px;
			font-family: Arial, Helvetica, Sans-serif;
		}
		.back-button {
			font-size: 18px;
			color: #fff;
			background-color: #333;
			border: none;
			padding: 10px 20px;
			cursor: pointer;
			margin-top: auto;
			font-family: Arial, Helvetica, Sans-serif;
		}
		.table-container {
			flex-basis: 100%;
			height: 100%;
		}
		.button-container {
			flex-basis: 100%;
			justify-content: space-between;
			align-items: center;
			margin: 50px !important;
			text-align: center;
		}
		.black{
			font-family: Arial, Helvetica, Sans-serif;
			font-size: 18px;
			font-weight: bold;
			margin: 0;
			padding: 0;
		}
		.td{
			font-family: Arial, Helvetica, Sans-serif;
			font-size: 14px;
			margin: 0;
			padding: 0;
		}

		.a {
			color: #000;
			color: #4775DE !important; 
			font-size: 16px;
			text-decoration: underline !important; 
		}

		.a:hover {
			color: black !important; 
		}
		.icono {
		  	font-size: 14px; /* Tamaño del icono */
		  	color: white; /* Color del icono */
		}
		
		.tablapendientes thead {
			  position: sticky;
			  top: 0;
			  background-color: #f5f5f5;
		
		}
		.tablaatrasadas thead {
			  position: sticky;
			  top: 0;
			  background-color: #f5f5f5;
			 
		}

	


  
</style>
{/literal}

{strip}
{include file="PicklistColorMap.tpl"|vtemplate_path:$MODULE LISTVIEW_HEADERS=$RELATED_HEADERS}
<div class="modal-dialog modal-lg modal-atenciones" style="width: 85%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" {if $ESTADO neq "D"} id="cerrado"{else} id="cerrar" {/if} data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">Atenciones Presenciales </h4>


		</div>
		{if !$APACTIVA}
		<div class="modal-body lista" id="lista">
			{*var_dump($ESTADO)*}
			{if $ESTADO neq "D"}
			<div  id="tablaMov" class="" role="alert" style = "margin-top: 30px;">
				<!--<i class="fa-solid fa-calendar-check" style="color: red;"></i>-->
				<i class="fa-solid fa-calendar-xmark" style="color: red; font-size: 45px;"></i>

				<h2 class="mensaje" style="margin-top:100px; color: red;" >Se ha perdido el puesto, vuelva a conectarse.</h2>
			</div>

			
			
			

			{elseif $ATENCIONES eq NULL and $ATENCIONESATR eq NULL}

			<div  id="tablaMov" class="" role="alert" style = "margin-top: 30px;">
				<i class="fa-solid fa-calendar-check" style="color: #4CB630;"></i>
				<h2 class="mensaje" >No existen atenciones pendientes ni atrasadas.</h2>
			</div>


			{else}	


			<div  id="tablaMov">
				<div class="contenedor"> 
					<ul class="nav nav-tabs" role="tablist" id="aptabs">
						<li class="nav-item item-pendientes active" role="presentation" name="ap-pendientes" style="display: flex; align-items: center; margin-left:5px">
							<h2 class="subtitulo text-left" style="padding: 0px; margin-right: auto;">Pendientes</h2>
							<i class="fa-regular fa-clock icono"></i>
						</li>
						<li class="nav-item item-atrasadas" role="presentation" name="ap-atrasadas" style="display: flex; align-items: center;">
							<h2 class="subtitulo text-left" style="margin-right: auto;">Atrasadas</h2>
							<i class="fa-solid fa-clock-rotate-left icono"></i>

						</li>
					</ul>




					<!--center>
						<h2 class="subtitulo">Pendientes</h2>
					</center-->

					<table class="table table-striped tablapendientes">
						<thead>
							<tr class="theaders">
								<th class="definido">Sector</th>
								<th>Serie</th>
								<th>N°</th>
								<th>Fecha</th>
								<th>Hora</th>
								<th class="sector-td">Tipo doc.</th>
								<th class="sector-td">Documento</th>
								<th class="definido">Nombre</th>
								<th class="definido">Trámite</th>
							</tr>
							</thead>
							<tbody >
						{if $ATENCIONES}
							
							{foreach from=$ATENCIONES item=a}
							<tr class="numero" data-json='{ZEND_JSON::encode($a.data)}'>
								<td class="sector-td">{$a.sector}</td>
								<td class="sector-td">{$a.data->serie}</td>
								<td class="sector-td">{$a.numero}</td>
								<td class="sector-td">{$a.fecha}</td>
								<td class="sector-td">{$a.hora}</td>
								<td class="sector-td">{$a.tipo}</td>
								<td class="sector-td">{if $a.datosPersona && $a.datosPersona.doc}{$a.datosPersona.doc}{else}Sin datos{/if}</td>
								<td class="sector-td">{if $a.datosPersona && $a.datosPersona.nombre}{$a.datosPersona.nombre}{else}Sin datos{/if}</td>
								<td class="sector-td">{$a.data->tramite}</td>
							</tr>
							{/foreach}
							{else}
							<tr align="center" class="formato"   >


								<td align="center" class="formato" colspan="9" style="border: 1px solid">
										<i class="fa-solid fa-calendar-check" style="color: #4CB630;"></i>
										<h2 class="mensaje" >No existen atenciones pendientes.</h2>
								</td>

							</tr>

							{/if}
						</tbody> 
					</table>
					<!--center>
						<h2 class="subtitulo">Atrasadas</h2>
					</center-->
					<table class="table table-striped tablaatrasadas hide">
						<thead>
							<tr class="theaders">
								<th class="definido">Sector</th>
								<th>Serie</th>
								<th>N°</th>
								<th>Fecha</th>
								<th>Hora</th>
								<th class="sector-td">Tipo doc.</th>
								<th class="sector-td">Documento</th>
								<th class="definido">Nombre</th>
								<th class="definido">Trámite</th>
							</tr>
							</thead>
							<tbody >
							{if $ATENCIONESATR}
							{foreach from=$ATENCIONESATR item = b}
							{*var_dump($ATENCIONESATR )*}


							<tr class="numero" data-json='{ZEND_JSON::encode($b.data)}'>
								<td class="sector-td">{$b.sector}</td>
								<td class="sector-td">{$b.data->serie}</td>
								<td class="sector-td">{$b.numero}</td>
								<td class="sector-td">{$b.fecha}</td>
								<td class="sector-td">{$b.hora}</td>
								<td class="sector-td">{$b.tipo}</td>
								<td class="sector-td">{if $b.datosPersona && $b.datosPersona.doc}{$b.datosPersona.doc}{else}Sin datos{/if}</td>
								<td class="sector-td">{if $b.datosPersona && $b.datosPersona.nombre}{$b.datosPersona.nombre}{else}Sin datos{/if}</td>
								<td class="sector-td">{$b.data->tramite}</td>
							</tr>
							{/foreach}
							{else}
							<tr align="center" class="formato"   >


								<td align="center" class="formato" colspan="9" style="border: 1px solid">
									<i class="fa-solid fa-calendar-check" style="color: #4CB630;"></i>
									<h2 class="mensaje" >No existen atenciones atrasadas.</h2>	
								</td>

							</tr>
							{/if}
						</tbody>  
					</table>

				</div>
			</div>
			{/if}
		</div>
		<div class="modal-body transicion" style="display: none;">
			<div  id="tablaMov">
				<div class="contenedor"> 
					<input type="hidden" name="datajson">
					<!--<center>
						<h2 class="subtitulo">Detalle de número a llamar</h2>
					</center>-->
         			<div class="left-column">
					<table id="tablaConsulta" class="tabladetalle">
						<tbody style="border: 1px #848180;margin-top: 5px">
							  <tr>
		                      <th class="black">Sector:</th>
		                      <td class="td" name='datoSector'></td>
			                  </tr>
			                  <tr>
			                     <th class="black">Trámite:</th>
			                     <td class="td" name='datoTramite'></td>
			                  </tr>
			                  <tr>
			                     <th class="black">Fecha:</th>
			                     <td class="td" name='datoFecha'></td>
			                  </tr>
			                  <tr>
			                     <th class="black">Hora:</th>
			                     <td class="td" name='datoHora'></td>
			                  </tr>
			                  <!--<tr>
			                     <th>Tiempo de espera:</th>
			                     <td>5 minutos</td>
			                  </tr>-->
			                  <tr>
			                     <th class="black">Serie:</th>
			                     <td class="td" name='datoSerie'></td>
			                  </tr>
			                  <tr>
			                     <th colspan="6" ></th>
			                  </tr>
			                  <tr>
			                     <th class="black">Tipo doc.:</th>
			                     <td class="td" name='datoTipoDoc'></td>
			                  </tr>
			                  <tr>
			                     <th class="black">Documento:</th>
			                     <td class="td" name='datoDocumento'></td>
			                  </tr>
			                  <tr>
			                     <th class="black">Nombre:</th>
			                     <td class="td"name='datoNombre'></td>
			                  </tr>
			                  <tr>
			                     <th class="black">Apellido:</th>
			                     <td class="td" name='datoApellido'></td>
			                  </tr>
							<!--<tr>
								<th align="center" colspan="2">
									<button name="llamar" style="margin-top: 10px;" class="btn btn-success">Llamar número</button>
									<button name="cancelar" style="margin-top: 10px; margin-left: 10px;" class="btn btn-success">Volver</button>
									<button name="atender" style="margin-top: 10px;" class="btn btn-success" style="display: none;">Atender</button>
									<button name="liberar" style="margin-top: 10px; margin-left: 10px;" class="btn btn-success" style="display: none;">Liberar</button>
								</th>
							</tr>-->
						</tbody>
					</table>
					</div>
					<div class="right-column">
						<h2 align="center" id="texto" class="black" style="font-size: 22px;">Número a llamar:</h2>
						<br><br>
						<label class="call-number" name='datoNumero'></label>  
					</div>
					<div class="button-container" style="float: none;">
							
						<button name="cancelar" style="margin-top: 10px; margin-left: 85px; float: left; background: #EA9341;" class="back-button btn btn-success"> Volver</button>
						<button name="llamar" style="margin-top: 10px; float: right; margin-right: 18%;" class="call-button btn btn-primary">Llamar número</button>
						<button name="atender" style="margin-top: 10px; float: right; margin-right: 18%;" class="call-button btn btn-success" style="display: none;">Atender número</button>
						<button name="liberar" style="margin-top: 10px; margin-left: 85px; float: left; background: #EA9341;" class="back-button btn btn-success" style="display: none;">Liberar número</button>

					</div>
				</div>
			</div>
		</div>
		{else}
		<div class="modal-body">
			<div  id="tablaMov" class="" role="alert" style = "margin-top: 30px;">
				<i class="fa-solid fa-calendar-check" style="color: red;"></i>
				<h2 class="mensaje" style="color: red;" >Tiene una atención sin finalizar.</h2>
				<center><a href="{$APACTIVADETAIL}"><p class="a">Haz clic aquí para redireccionar a ella</p></a></center>
			</div>

			<!--<h2 class="subtitulo" >Atenciones Presenciales </h2>

			<div  id="tablaMov" class="alert alert-danger" role="alert" style = "margin-top: 30px;">
				<h2 class="mensaje" >Tiene una atención presencial activa</h2>
				<center><a href="{$APACTIVADETAIL}"><p>Haz clic aquí para redireccionar a ella</p></a></center>
			</div>-->
			{/if}
		</div>
	</div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
{/strip}
{literal}

<script type="text/javascript">
	function CargarModalAtenciones(){
		localStorage.removeItem('numeroLlamado');
		    		var params = {};
			        params['module'] = 'AtencionPresencial';
			        params['view'] = 'CargarModalAtenciones';
			        AppConnector.request(params).then(
			            function(data) {
			            		jQuery('[name="datajson"]').val(undefined);
							    		jQuery('.modal-body.lista').show();
							    		jQuery('.modal-body.transicion').hide();
							    		jQuery('[name="datoNombre"]').text('');
							    		jQuery('[name="datoApellido"]').text('');
							    		jQuery('[name="datoDocumento"]').text('');
							    		jQuery('[name="datoTipoDoc"]').text('');
							    		jQuery('[name="datoSector"]').text('');
							    		jQuery('[name="datoTramite"]').text('');
							    		jQuery('[name="datoSerie"]').text('');
							    		jQuery('[name="datoNumero"]').text('');
							    		jQuery('[name="datoFecha"]').text('');
							    		jQuery('[name="datoHora"]').text('');
							    		jQuery('[name="liberar"]').hide();
							    		jQuery('[name="atender"]').hide();
							    		jQuery('[name="llamar"]').show();
							    		jQuery('[name="cancelar"]').show();
							    		//muestro cerrar
										$('#cerrar').show();
			                app.helper.hideProgress();
			                app.helper.showErrorNotification({title:'Error', message:'Se ha desconectado el puesto'});
			                var modal = jQuery(data.result);
			                jQuery('.modal-body.lista').html(jQuery('.modal-body.lista', modal).html());
			                eventSeleccionar();
			            },
			    		function(error) {
			                console.log("error");
			                app.helper.hideProgress();
			            });
	}

	$(document).ready(function() {
		const sectorTds = document.querySelectorAll('.sector-td');
		sectorTds.forEach(td => {
			if (td.textContent.length >= 25) {
				td.classList.add('long');
			} else {
				td.classList.remove('long');
			}
		});

		$('#cerrado').click(function(){
			location.reload();

		});

		$('#cerrar').click(function(){
			location.reload();

		});
		function eventSeleccionar(){
			jQuery('.numero').click(function(e){

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
	                    if(atweb ){ error = error + 1; }
	                    if(atpre){ error = error + 1; }
	                     console.log("error "+ error);
	                    if(error > 0){
	                    	app.helper.showAlertNotification({'message' : 'Debe finalizar o pausar la llamada o atención para continuar'});
	                        //alert("Debe finalizar o pausar la llamada o atención para continuar");
	                    }else{

	                    	var params = $(e.currentTarget).data('json');
										   	params['module'] = 'AtencionPresencial';
										    params['action'] = 'AtenderAtencion';
										    var progressIndicatorElement = jQuery.progressIndicator();
										    AppConnector.request(params).then(function(response){
										    	console.log("El error cuando es apostrofe");
										    	console.log(response);
										    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
										    	if (response && response.result && response.result.success) {
											    		var clone = params; delete(clone.module); delete(clone.action); 
											    		jQuery('[name="datajson"]').val(JSON.stringify(clone));
											    		jQuery('.modal-body.lista').hide();
											    		jQuery('.modal-body.transicion').show();
											    		jQuery('[name="datoNombre"]').text(params.nombre);
											    		jQuery('[name="datoApellido"]').text(params.apellido);
											    		jQuery('[name="datoDocumento"]').text(params.documento);
											    		jQuery('[name="datoTipoDoc"]').text(params.tipodoc.nombre);
											    		jQuery('[name="datoSector"]').text(params.sector);
											    		jQuery('[name="datoTramite"]').text(params.tramite);
											    		jQuery('[name="datoSerie"]').text(params.serie);
											    		jQuery('[name="datoNumero"]').text(params.numerocod);
											    		jQuery('[name="datoFecha"]').text(params.numerofecha);
											    		jQuery('[name="datoHora"]').text(params.hora);
											    		jQuery('[name="liberar"]').hide();
											    		jQuery('[name="atender"]').hide();
											    		jQuery('[name="llamar"]').show();
											    		jQuery('[name="cancelar"]').show();
											    		//oculto cerrar 
															$('#cerrar').hide();
										    	}else{
											    		if(response && response.result && response.result.error){
											    				app.helper.showErrorNotification({message:response.result.error});
											    		}else{
											    				app.helper.showErrorNotification({message:'Hubo problemas al seleccionar la atención'});
													    	}
									    		}
											    },function(error){
											    	progressIndicatorElement.progressIndicator({'mode' : 'hide'});
											    	app.helper.showErrorNotification({message:error.message});
											    });

	                   		}
	                }
	            });
	    	});

				jQuery('#aptabs .nav-item').click((e) => {
					var tabla = e.currentTarget.attributes.name.nodeValue.replace('ap-', '');
					jQuery('.contenedor .table-striped').addClass('hide');
					jQuery('#aptabs .nav-item').removeClass('active');
					jQuery(e.currentTarget).addClass('active');
					jQuery('.tabla'+tabla).removeClass('hide');
				});
	    }

    	jQuery('[name="llamar"]').click(function(e) {
    		var params_est = {};
    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
		   	params_est['module'] = 'AtencionPresencial';
		    params_est['action'] = 'getAcciones';
		    params_est['modo'] = 'obtenerEstadoPuesto';
		    console.log(params_est);
		    app.helper.showProgress();
		    AppConnector.request(params_est).then(function(data){
		    	app.helper.hideProgress();

		    	console.log(data.result.respuesta);
		    	if (data.result.respuesta == 'D') {
		    		var params = {};
		    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
				   	params['module'] = 'AtencionPresencial';
				    params['action'] = 'getAcciones';
				    params['modo'] = 'llamarNumero';
				    params['sectorid'] = datosnumero.sectorId;
				    params['numerocod'] = datosnumero.numerocod;
				    params['fecha'] = datosnumero.numerofecha;
				    params['hora'] = datosnumero.hora;
				    console.log(params);
				    app.helper.showProgress();
				    AppConnector.request(params).then(function(data){
				    	app.helper.hideProgress();
				    	if (data && data.result && data.result.success) {
				    		jQuery('[name="liberar"]').show();
				    		jQuery('[name="atender"]').show();
				    		jQuery('[name="llamar"]').hide();
				    		jQuery('[name="cancelar"]').hide();
				    		 $('#texto').text('Número llamado');
				    		app.helper.showSuccessNotification({title:'Exito', message:'El número ha sido llamado'});
				    		localStorage.setItem('numeroLlamado', true);
				    	}else{
				    		if(data && data.result && data.result.error){
				    			app.helper.showErrorNotification({message:data.result.error});
				    		}else{
				    			app.helper.showErrorNotification({message:'Hubo problemas al llamar al número'});
				    		}
				    	}
				    },function(error){
				    	app.helper.hideProgress();
				    	app.helper.showErrorNotification({message:error.message})
				    	console.log(error);
				    });
		    	}else{
		    		//$('#cerrar').attr('id', 'cerrado');
		    		CargarModalAtenciones();
		    		/*localStorage.removeItem('numeroLlamado');
		    		var params = {};
			        params['module'] = 'AtencionPresencial';
			        params['view'] = 'CargarModalAtenciones';
			        AppConnector.request(params).then(
			            function(data) {
			            		jQuery('[name="datajson"]').val(undefined);
							    		jQuery('.modal-body.lista').show();
							    		jQuery('.modal-body.transicion').hide();
							    		jQuery('[name="datoNombre"]').text('');
							    		jQuery('[name="datoApellido"]').text('');
							    		jQuery('[name="datoDocumento"]').text('');
							    		jQuery('[name="datoSector"]').text('');
							    		jQuery('[name="datoTramite"]').text('');
							    		jQuery('[name="datoSerie"]').text('');
							    		jQuery('[name="datoNumero"]').text('');
							    		jQuery('[name="datoFecha"]').text('');
							    		jQuery('[name="datoHora"]').text('');
							    		jQuery('[name="liberar"]').hide();
							    		jQuery('[name="atender"]').hide();
							    		jQuery('[name="llamar"]').show();
							    		jQuery('[name="cancelar"]').show();
							    		//muestro cerrar
										$('#cerrar').show();
			                app.helper.hideProgress();
			                app.helper.showErrorNotification({title:'Error', message:'Se ha desconectado el puesto'});
			                var modal = jQuery(data.result);
			                jQuery('.modal-body.lista').html(jQuery('.modal-body.lista', modal).html());
			                eventSeleccionar();
			            },
			    		function(error) {
			                console.log("error");
			                app.helper.hideProgress();
			            });*/
		    	}
		    },function(error){
		    	app.helper.hideProgress();
		    	app.helper.showErrorNotification({message:error.message})
		    	console.log(error);
		    });

    		/**/
    	});
    	jQuery('[name="atender"]').click(function(e) {
    		var params_est = {};
    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
		   	params_est['module'] = 'AtencionPresencial';
		    params_est['action'] = 'getAcciones';
		    params_est['modo'] = 'obtenerEstadoPuesto';
		    console.log(params_est);
		    app.helper.showProgress();
		    AppConnector.request(params_est).then(function(data){
		    	app.helper.hideProgress();

		    	console.log(data.result.respuesta);
		    	if (data.result.respuesta == 'D') {
		    		var params = {};
		    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
				   	params['module'] = 'AtencionPresencial';
				    params['action'] = 'getAcciones';
				    params['modo'] = 'atenderNumero';
				    params['sectorid'] = datosnumero.sectorId;
				    params['numerocod'] = datosnumero.numerocod;
				    params['fecha'] = datosnumero.numerofecha;
				    params['hora'] = datosnumero.hora;
				    console.log(params);
				    app.helper.showProgress();
				    AppConnector.request(params).then(function(data){
				    	app.helper.hideProgress();
				    	if (data && data.result && data.result.success) {
				    		console.log(data);
				    		localStorage.removeItem('numeroLlamado');
				    		app.helper.showSuccessNotification({title:'Exito', message:'El número ha sido atendido'});
				    		location.href = data.result.url;
				    	}else{
				    		if(data && data.result && data.result.error){
				    			app.helper.showErrorNotification({message:data.result.error});
				    		}else{
				    			app.helper.showErrorNotification({message:'Hubo problemas al atender al número'});
				    		}
				    	}
				    },function(error){
				    	app.helper.hideProgress();
				    	app.helper.showErrorNotification({message:error.message})
				    	console.log(error);
				    });
				}else{
						var params = {};
			    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
					   	params['module'] = 'AtencionPresencial';
					    params['action'] = 'getAcciones';
					    params['modo'] = 'liberarNumero';
					    params['sectorid'] = datosnumero.sectorId;
					    params['numerocod'] = datosnumero.numerocod;
					    params['fecha'] = datosnumero.numerofecha;
					    params['hora'] = datosnumero.hora;
					    console.log(params);
					    app.helper.showProgress();
					    AppConnector.request(params).then(function(data){
					    		
						     console.log("El puesto esta desconectado igual libero la atencion");
					    	},function(error){
						    	app.helper.hideProgress();
						    	app.helper.showErrorNotification({message:error.message})
						    	console.log(error);
						    }
				    	);
				    	//$('#cerrar').attr('id', 'cerrado');
				   		CargarModalAtenciones();
				}
		    },function(error){
		    	app.helper.hideProgress();
		    	app.helper.showErrorNotification({message:error.message})
		    	console.log(error);
		    });
    		
    	});

    	///////////////////////////////////////
    	jQuery('[name="liberar"]').click(function(e) {
    		var params_est = {};
    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
		   	params_est['module'] = 'AtencionPresencial';
		    params_est['action'] = 'getAcciones';
		    params_est['modo'] = 'obtenerEstadoPuesto';
		    console.log(params_est);
		    app.helper.showProgress();
		    AppConnector.request(params_est).then(function(data){
		    	app.helper.hideProgress();

		    	console.log(data.result.respuesta);
		    	if (data.result.respuesta == 'D') {
		    		var params = {};
		    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
				   	params['module'] = 'AtencionPresencial';
				    params['action'] = 'getAcciones';
				    params['modo'] = 'liberarNumero';
				    params['sectorid'] = datosnumero.sectorId;
				    params['numerocod'] = datosnumero.numerocod;
				    params['fecha'] = datosnumero.numerofecha;
				    params['hora'] = datosnumero.hora;
				    console.log(params);
				    app.helper.showProgress();
				    AppConnector.request(params).then(function(data){
				    	if (data && data.result && data.result.success) {
				    		localStorage.removeItem('numeroLlamado');
				    		var params = {};
					        params['module'] = 'AtencionPresencial';
					        params['view'] = 'CargarModalAtenciones';
					        AppConnector.request(params).then(
					            function(data) {
					            		jQuery('[name="datajson"]').val(undefined);
									    		jQuery('.modal-body.lista').show();
									    		jQuery('.modal-body.transicion').hide();
									    		jQuery('[name="datoNombre"]').text('');
									    		jQuery('[name="datoApellido"]').text('');
									    		jQuery('[name="datoDocumento"]').text('');
									    		jQuery('[name="datoTipoDoc"]').text('');
									    		jQuery('[name="datoSector"]').text('');
									    		jQuery('[name="datoTramite"]').text('');
									    		jQuery('[name="datoSerie"]').text('');
									    		jQuery('[name="datoNumero"]').text('');
									    		jQuery('[name="datoFecha"]').text('');
									    		jQuery('[name="datoHora"]').text('');
									    		jQuery('[name="liberar"]').hide();
									    		jQuery('[name="atender"]').hide();
									    		jQuery('[name="llamar"]').show();
									    		jQuery('[name="cancelar"]').show();
									    		$('#texto').text('Número a llamar');
									    		//muestro cerrar
												$('#cerrar').show();
					                app.helper.hideProgress();
					                app.helper.showSuccessNotification({title:'Exito', message:'El número ha sido liberado'});
					                var modal = jQuery(data.result);
					                jQuery('.modal-body.lista').html(jQuery('.modal-body.lista', modal).html());
					                eventSeleccionar();
					            },
					    		function(error) {
					                console.log("error");
					                app.helper.hideProgress();
					            }
					        	);
				    		}else{
					    		app.helper.hideProgress();
					    		if(data && data.result && data.result.error){
					    			app.helper.showErrorNotification({message:data.result.error});
					    		}else{
					    			app.helper.showErrorNotification({message:'Hubo problemas al liberar al número'});
				    		}
				    	}
			    	},function(error){
				    	app.helper.hideProgress();
				    	app.helper.showErrorNotification({message:error.message})
				    	console.log(error);
				    }
				    );
		    		}else{
			    		var params = {};
			    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
					   	params['module'] = 'AtencionPresencial';
					    params['action'] = 'getAcciones';
					    params['modo'] = 'liberarNumero';
					    params['sectorid'] = datosnumero.sectorId;
					    params['numerocod'] = datosnumero.numerocod;
					    params['fecha'] = datosnumero.numerofecha;
					    params['hora'] = datosnumero.hora;
					    console.log(params);
					    app.helper.showProgress();
					    AppConnector.request(params).then(function(data){
					    		
						     console.log("El puesto esta desconectado igual libero la atencion");
				    	},function(error){
					    	app.helper.hideProgress();
					    	app.helper.showErrorNotification({message:error.message})
					    	console.log(error);
					    }
				    );
					//$('#cerrar').attr('id', 'cerrado');
		    		CargarModalAtenciones();
		    	}
		    },function(error){
		    	app.helper.hideProgress();
		    	app.helper.showErrorNotification({message:error.message})
		    	console.log(error);
		    });
    		/*var params = {};
    		var datosnumero = JSON.parse(jQuery('[name="datajson"]').val());
		   	params['module'] = 'AtencionPresencial';
		    params['action'] = 'getAcciones';
		    params['modo'] = 'liberarNumero';
		    params['sectorid'] = datosnumero.sectorId;
		    params['numerocod'] = datosnumero.numerocod;
		    params['fecha'] = datosnumero.numerofecha;
		    params['hora'] = datosnumero.hora;
		    console.log(params);
		    app.helper.showProgress();
		    AppConnector.request(params).then(function(data){
		    	if (data && data.result && data.result.success) {
		    		localStorage.removeItem('numeroLlamado');
		    		var params = {};
			        params['module'] = 'AtencionPresencial';
			        params['view'] = 'CargarModalAtenciones';
			        AppConnector.request(params).then(
			            function(data) {
			            		jQuery('[name="datajson"]').val(undefined);
							    		jQuery('.modal-body.lista').show();
							    		jQuery('.modal-body.transicion').hide();
							    		jQuery('[name="datoNombre"]').text('');
							    		jQuery('[name="datoApellido"]').text('');
							    		jQuery('[name="datoDocumento"]').text('');
							    		jQuery('[name="datoSector"]').text('');
							    		jQuery('[name="datoTramite"]').text('');
							    		jQuery('[name="datoSerie"]').text('');
							    		jQuery('[name="datoNumero"]').text('');
							    		jQuery('[name="datoFecha"]').text('');
							    		jQuery('[name="datoHora"]').text('');
							    		jQuery('[name="liberar"]').hide();
							    		jQuery('[name="atender"]').hide();
							    		jQuery('[name="llamar"]').show();
							    		jQuery('[name="cancelar"]').show();
							    		//muestro cerrar
										$('#cerrar').show();
			                app.helper.hideProgress();
			                app.helper.showSuccessNotification({title:'Exito', message:'El número ha sido liberado'});
			                var modal = jQuery(data.result);
			                jQuery('.modal-body.lista').html(jQuery('.modal-body.lista', modal).html());
			                eventSeleccionar();
			            },
			    		function(error) {
			                console.log("error");
			                app.helper.hideProgress();
			            }
			        	);
			    	}else{
			    		app.helper.hideProgress();
			    		if(data && data.result && data.result.error){
			    			app.helper.showErrorNotification({message:data.result.error});
			    		}else{
			    			app.helper.showErrorNotification({message:'Hubo problemas al liberar al número'});
			    		}
			    	}
			    },function(error){
			    	app.helper.hideProgress();
			    	app.helper.showErrorNotification({message:error.message})
			    	console.log(error);
			    });*/
		});

    jQuery('[name="cancelar"]').click(function(e) {
		    app.helper.showProgress();
		    var params = {};
			  params['module'] = 'AtencionPresencial';
			  params['view'] = 'CargarModalAtenciones';
			  AppConnector.request(params).then(
			    function(data) {
			   		jQuery('[name="datajson"]').val(undefined);
				 		jQuery('.modal-body.lista').show();
				 		jQuery('.modal-body.transicion').hide();
				 		jQuery('[name="datoNombre"]').text('');
				 		jQuery('[name="datoApellido"]').text('');
				 		jQuery('[name="datoTipoDoc"]').text('');
				 		jQuery('[name="datoDocumento"]').text('');
				 		jQuery('[name="datoSector"]').text('');
				 		jQuery('[name="datoTramite"]').text('');
				 		jQuery('[name="datoSerie"]').text('');
				 		jQuery('[name="datoNumero"]').text('');
						jQuery('[name="datoFecha"]').text('');
				 		jQuery('[name="datoHora"]').text('');
						jQuery('[name="liberar"]').hide();
				 		jQuery('[name="atender"]').hide();
				 		jQuery('[name="llamar"]').show();
				 		jQuery('[name="cancelar"]').show();
				 		//muestro cerrar
						$('#cerrar').show();
			      app.helper.hideProgress();
			      var modal = jQuery(data.result);
			      jQuery('.modal-body.lista').html(jQuery('.modal-body.lista', modal).html());
			      eventSeleccionar();
			    },
			    function(error) {
			      console.log("error");
			    	app.helper.hideProgress();
			    }
			  );
		});
		eventSeleccionar();

  });
</script>
{/literal}