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
<style type="text/css">
	.contenedor {
		margin-right: 25px;
		margin-left: 25px;
		overflow-x: scroll;
	}

	.contenido {
		text-align: left;
		font-family: Arial, Helvetica, Sans-serif;
		font-size: 14px;
		font-weight: 400;
		margin: 5px;
		padding: 20px;
		color: #5b5b5f;
	}

	.seccion {
		text-align: center;
		font-family: Arial, Helvetica, Sans-serif;
		font-size: 14px;
		font-weight: bold;
		font-weight: 600;
	}

	.formato {
		font-size: 12px;
		font-weight: bold;
		font-weight: 400;
		font-family: Arial;
		color: #5b5b5f;
		text-align: center;
	}

	.formato_texto {
		font-size: 11px;
		font-family: Arial;
		color: #5b5b5f;
	}

	.table-hover tbody tr:hover {
		background: #F0F8FF;
	}

	.table-hover tbody tr td input:hover {
		background: #F0F8FF;
	}

	.table-hover tbody tr td input {
		text-align: center;
	}

	#tablaConsulta {
		width: 100%;

	}

	#tablaConsulta tr {
		margin: 0px;
		padding: 0px;
	}

	#tablaConsulta th {
		margin: 0px;
		padding: 0px;
	}

	.sinborde {
		background-color: #fff;
		border: 0;
	}

	.titulo {
		font-weight: bold;
		font-size: 13px;
		font-family: Arial, Helvetica, Sans-serif;
		text-align: center;
	}

	.subtitulo {
		color: #A9A9A9;
		font-weight: bold;
		font-size: 33px;
		font-family: Arial, Helvetica, Sans-serif;
		text-align: center;
		text-transform: uppercase;
	}

	.boton {
		color: tomato;
		background-color: white;
		border-radius: 0px;
		font-weight: 100;
		cursor: pointer;
		border-width: thin;
		font-size: 20px;

	}

	#botones {
		display: flex;
		justify-content: center;
		align-items: center;
	}

	#botones button {

		font-size: 18px;
		margin-top: 10px;
		margin-bottom: 10px;
		padding: 10px 20px 10px 20px;

	}

	.mensaje {
		color: Tomato;
		font-family: Arial, Helvetica, Sans-serif;
		text-align: center;
		/*text-transform: uppercase;*/
	}

	.mensaje2 {
		color: #5DC45F;
		text-align: center;
	}

	/********** SELECT ************/
	#Select {
		width: 115px;
	}

	input[type="text"] {
		width: auto;
		word-wrap: break-word;
		padding: 0px;
		margin: 0px;
	}

	/************Tabla Adicionales********************/
	#Adicionales {
		width: 100%;
		border-collapse: collapse;
	}

	#Adicionales th,
	#Adicionales td {
		padding: 8px;
		text-align: center;
	}



	.sinborde {
		border: none;
		width: 100%;
		box-sizing: border-box;
	}

	#tabla-container {
		overflow-x: auto;
	}

	.modal-content {
		max-height: 700px;
		overflow-y: auto;
	}
</style>

{/literal}

{strip}

<div class="modal-dialog modal-lg" style="width: 55%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" id="cerrar" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">Consulta Web</h4>
		</div>
		{if $ERROR eq 1}
		<div id="tablaMov" class="" role="alert" style="margin-top: 60px; text-align: center; height: 200px">
			<img src="layouts/v7/icons_custom_modules/ConsultasWeb-red.png" style="width:40px;" title="Consultas Web"
				alt="layouts/v7/icons_custom_modules/ConsultasWeb-red.png">
			<h2 class="mensaje">Tiene atenciones activas.</h2>
		</div>
		<!--<div class="modal-body">
		  			<h2 class="subtitulo" >Consulta Web</h2>
		  			
		  				<div  id="tablaMov" class="alert alert-danger" role="alert" style = "margin-top: 30px;">

		  				<h2 class="mensaje" >Tiene Atenciones activas, debe trabajarlas primero</h2>
		  				</div>-->
		{elseif $ID eq ''}
		<div id="tablaMov" class="" role="alert" style="margin-top: 60px; text-align: center; height: 200px">
			<img src="layouts/v7/icons_custom_modules/ConsultasWeb-red.png" style="width:40px;" title="Consultas Web"
				alt="layouts/v7/icons_custom_modules/ConsultasWeb-red.png">
			<h2 class="mensaje">La consulta fue tomada por otro usuario.</h2>
		</div>
		{else}



		<div id="tablaMov" style="margin-top: 30px;">
			<div class="contenedor">
				<table id="tablaConsulta">
					<tbody>
						<tr>

							<th align="left" class="formato" style="text-align: left;"><label>De:</label></th>
							<td align="left" class="formato_texto"><span id="deemail">{$DEEMAIL}</span></td>
							<th class="formato" style="text-align: left;"><label>Persona: </label></th>
							<td align="center" class="formato_texto" style="text-align: left; font-weight:normal;">
								<label id="cuenta">{$CUENTA}</label>
							<th class="formato" style="text-align: left;"><label>Estado: </label></th>
							<td align="center" class="formato_texto" style="text-align: left;"><input type=""
									class="sinborde" id="estado" name="" value="{$ESTADO}"></td>
						</tr>
						<tr>
							<th align="left" class="formato" style="text-align: left;"><label>Tema:</label></th>
							<td align="left" class="formato_texto"><span id="tema_valor">{$TEMA}</span></td>
							<th class="formato" style="text-align: left;"><label>Nro Empresa: </label></th>
							<td align="center" class="formato_texto" style="text-align: left;"><input type=""
									class="sinborde" id="empresa" name="" value="{$EMPRESA}"></td>
							<th class="formato" style="text-align: left;"><label>Grupo Actual:</label></th>
							<td align="center" class="formato_texto" style="text-align: left;"><input type=""
									class="sinborde" id="grupo" name="" value="{$GRUPO}"></td>
						</tr>
						<tr>
							<th align="left" class="formato" style="text-align: left;"><label>Aplicativo:</label></th>
							<td align="left" class="formato_texto"><span id="tema_valor">{$ORIGEN}</span></td>
							<th class="formato" style="text-align: left;"><label>Nro Contribuyente: </label></th>
							<td align="center" class="formato_texto" style="text-align: left;"><input type=""
									class="sinborde" id="contribuyente" name="" value="{$CONTRIBUYENTE}"></td>
							<th class="formato" style="text-align: left;"><label>Fecha creacion:</label></th>
							<td align="center" class="formato_texto" style="text-align: left;"><input type=""
									class="sinborde" id="" name="" value="{$FECHA}"></td>
						</tr>
						<tr>
							<th align="left" class="formato" style="text-align: left;"><label>Para:</label></th>
							<td align="left" class="formato_texto"><span>{$PARA}</span></td>
							<th align="left" class="formato" style="text-align: left;"><label>Aportacion:</label></th>
							<td><span id="aportacion" class="formato_texto">{$APORTACION}</span></td>
						</tr>
						<tr>
							<th align="left" class="formato" style="text-align: left;"><label>Asunto:</label></th>
							<td align="left" class="formato_texto"><span>{$ASUNTO}</span></td>
						</tr>

						<!--<tr style="border: 1px solid LightGray;">
								<th align="left" class="formato" style="text-align: left;"><label >Adjuntos:</label></th>
								{foreach from = $ADJUNTOS item = a}
								<td align="left" class="formato_texto"><a href="index.php?module=Documents&action=DownloadFile&record={$a.id}&fileid={$a.attachmentsid}" download="{$a.nombre}" target="_blank" >{$a.titulo}</a></td>
								{/foreach}
							</tr>-->
						<tr style="border: 1px solid LightGray;">
							<th align="left" class="formato" style="text-align: left;"><label>Adjuntos:</label></th>
							{foreach from = $ADJUNTOS item = a}
							{assign var="DOCUMENT_RECORD_MODEL" value=Vtiger_Record_Model::getInstanceById($a.id)}
							<td align="left" class="formato_texto">
								<span class="actionImages">
									<a name="viewfile" href="javascript:void(0)"
										data-filelocationtype="{$DOCUMENT_RECORD_MODEL->get('filelocationtype')}"
										data-filename="{$DOCUMENT_RECORD_MODEL->get('filename')}"
										onclick="Vtiger_Header_Js.previewFile(event,{$a.id},'ConsultasWeb')">{$a.nombre}</a>
								</span>
							</td>
							{/foreach}
						</tr>

						<tr style="margin-top: 10px; padding-top: 20px;">
							<th align="left" class="formato" style="padding-top: 25px; text-align: left;">
								<label>Contenido:</label></th>
						</tr>

						<tr style="border: 1px solid LightGray;">
							<td colspan="4" class="contenido" style="margin: 10px;  white-space:normal;">{$CONTENIDO}
							</td>
						</tr>

						<tr>

							<td hidden><input type="" class="sinborde" id="cuentaid" name="" value="{$CUENTAID}"></td>

							<input id="cwid" name="prodId" type="hidden" value="{$ID}">
						</tr>




						<!-- Esto se genera depende los correos -->
						<tr>
							<th align="left" class="formato" style="padding-top: 25px; text-align: left;">
								<label>Relacionados:</label></th>
						</tr>
						<tr>
							<td align="left" class="formato_texto" hidden><span id="tema_id">{$TEMAID}</span></td>
						</tr>
				</table>
				<div id="tabla-container">
					<table id="Adicionales" class="table table-hover">
						<thead style="border: 1px solid LightGray;">
							<th class="formato"><label>Incluir</label></th>
							<th class="formato"><label>Origen</label></th>
							<th class="formato"><label>De Correo</label></th>
							<th class="formato"><label>Asunto</label></th>
							<th class="formato"><label>Estado</label></th>
							<th class="formato"><label>Tema</label></th>
							<th class="formato"><label>Grupo</label></th>

						</thead>

						{*if $CONSULTASWEB eq ""*}
						{foreach from = $CONSULTASWEB item = c}

						<tbody>
							<tr style="border: 1px solid LightGray;">
								<td align="center"><input type="checkbox" value="{$c.id}" name="cwadicc" value=""></td>
								<td align="center" id="{$c.id}">
									<input class="sinborde" id="show_message3" type="text" value="{$c.origen}">
								</td>
								<td align="center" id="{$c.id}">
									<input class="sinborde" id="show_message3" type="text" value="{$c.deemail}">
								</td>
								<td align="center" id="{$c.id}">
									<input class="sinborde" id="show_message3" type="text"
										value="Consulta sobre prestamos de jubilados">
								</td>
								<td align="center" id="{$c.id}">
									<input class="sinborde" id="show_message3" type="text" value="{$c.estado}">
								</td>
								<td align="center" id="{$c.id}">
									<input class="sinborde" id="show_message3" type="text" value="{$c.tema}">
								</td>
								<td align="center" id="{$c.id}">
									<input class="sinborde" id="show_message3" type="text" value="{$c.grupo}">
								</td>
							</tr>
						</tbody>
						{/foreach}
						{*/if*}

					</table>
				</div>
				<table style="margin-left: -5px;" class="table table-hover">
					<tr>
						<th align="left" class="formato" style="padding-top: 25px; text-align: left;"><label>Contenido
								del seleccionado:</label></th>
					</tr>
					<tr>
						<td>
							<span id="contenido" class="" style="text-align: center;
																			 white-space: -moz-pre-wrap; 
																			 white-space: -pre-wrap;
																			 white-space: -o-pre-wrap;
																			 white-space: pre-wrap;
																			 word-wrap: break-word;">

							</span>
						</td>
					</tr>

				</table>



				<!--Botones crear AtencionWeb -->

				<div id="botones">
					<div id="hijo">
						<select style="margin-left:5px; " class="form-control" id="Select" name="usuario">
							<option>Asignar a:</option>
							<option disabled="disabled">...............Usuarios...............</option>
						</select>
						<select style="margin-left:5px; display: none;" class="form-control" id="Select" name="grupos">
							<option>Asignar a:</option>
							<option disabled="disabled">...............Grupos ...............</option>
						</select>
						<!--<button  type="button" id="descartaraw" data-dismiss="modal" aria-hidden="true" class="btn btn-warning" style="margin-left:5px;"> Descartar </button>-->
						<button type="button" id="asignaraw" data-dismiss="modal" disabled="true" aria-hidden="true"
							class="btn btn-primary" style="margin-left:5px;"> Asignar </button>
						<button type="button" id="atencionweb" data-dismiss="modal" aria-hidden="true"
							class="btn btn-success" style="margin-left:5px;"> Atender </button>
						<button type="button" data-dismiss="modal" aria-hidden="true" id="cancelar"
							class="btn boton">Cancelar</button>
					</div>

				</div>

			</div>
			{/if}
		</div>
	</div>
</div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
{/strip}
{literal}

<script type="text/javascript">

	var seleccionid = '';
	localStorage.setItem('cwidmodal', $('#cwid').val());
	//Cargar usuarios  y grupos en el select 
	function getData(grupo) {
		ID = $('#cwid').val();
		//console.log("ideame "+ID);

		jQuery.ajax({

			data: {
				id: ID,
			},
			url: 'index.php?module=ConsultasWeb&action=getUsuarios',
			dataType: "json",
			success: function (data) {

				var response = [data];
				console.log("la informacion esta aca " + response);
				jQuery.each(data, function (key, registro) {
					//console.log("data center "+key+" "+registro);
					$("#Select").append('<option value=' + key + '>' + registro + '</option>');
				});
			},
			error: function (data) {
				console.log('error');
				//alert('error');
			}
		});
	};

	$(document).ready(function () {

		console.log("ready!");
		let error_popup = $('#error').val();
		let deemail = $('#deemail').text();
		console.log("cacho " + deemail);
		jQuery.ajax({
			url: 'index.php?module=HelpDesk&action=controlActivas',
			dataType: 'json',
			success: function (data) {
				console.log(data);
				llamada = data.result.llamada.callsid;
				atweb = data.result.atencionWeb;
				atpre = data.result.AtencionPresencial.atencionpresencialid;
				console.log(atweb + " " + atpre + " " + llamada);
				error = 0;
				if (llamada) { error = error + 1; }
				if (atweb) { error = error + 1; }
				if (atpre) { error = error + 1; }
				console.log("error " + error);
				if (error > 0) {
					if (deemail != '') {
						//alert("Debe finalizar o pausar alguna atencion una para continuar");
						app.helper.showAlertNotification({ 'message': 'Debe finalizar o pausar la llamada o atención para continuar' });
						$('#atencionweb').attr('disabled', true);
					}


				}
			}
		});


		getData();
		$("#Select").select2();
		//capturo los valores del select
		$("#Select").change(function () {

			seleccionid = $(this).children("option:selected").val();
			$('#asignaraw').removeAttr('disabled');
			$('#atencionweb').attr('disabled', true);
			console.log("Has seleccionado - " + seleccionid);
			if (seleccionid === 'Asignar a:') {
				console.log("Has seleccionado - " + seleccionid);
				$('#asignaraw').attr('disabled', true);
				$('#atencionweb').attr('disabled', false);
			}

		});

		//Cuando hago clic en tabla Adicionales carga contenido
		$('input[id^="show_message"]').click(function () {

			id = $(this).parent('td').attr('id');
			jQuery.ajax({
				async: false,
				data: {
					id: id,
					modo: 'getContenido'
				},
				url: 'index.php?module=AtencionesWeb&action=getAccionesConsulta',
				dataType: "json",
				success: function (data) {

					retorno = data;
					//console.log(retorno);

					$("#contenido").html(retorno.valueOf());
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				}
			});


		});
		//Cuando doy cancelar vuelvo a pendiente las consultas
		$('#cancelar').click(function () {
			console.log("cerrando el negocio");
			ID = $('#cwid').val();
			estado = $("#estado").val();
			localStorage.removeItem('cwidmodal');
			console.log(ID);
			jQuery.ajax({
				async: false,
				data: {
					id: ID,
					modo: 'cerrar',
					estado: estado
				},
				url: 'index.php?module=AtencionesWeb&action=getAccionesConsulta',
				dataType: "json",
				success: function (data) {
					console.log(data);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				}
			});
		});
		//Cuando doy cerrar vuelvo a pendiente las consultas
		$('#cerrar').click(function () {
			console.log("cerrando el negocio");
			ID = $('#cwid').val();
			estado = $("#estado").val();
			localStorage.removeItem('cwidmodal');
			console.log(ID);
			jQuery.ajax({
				async: false,
				data: {
					id: ID,
					estado: estado,
					modo: 'cerrar'
				},
				url: 'index.php?module=AtencionesWeb&action=getAccionesConsulta',
				dataType: "json",
				success: function (data) {
					console.log(data);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				}
			});
		});
		//Cuando transfiero
		$('#descartaraw').click(function () {
			console.log("Descartando el negocio");
			ID = $('#cwid').val();
			localStorage.removeItem('cwidmodal');
			console.log(ID);
			jQuery.ajax({
				async: false,
				data: {
					id: ID,
				},
				url: 'index.php?module=ConsultasWeb&action=Descartar',
				dataType: "json",
				success: function (data) {
					console.log(data);
					location.reload();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				}
			});

		});


		//Cuando doy en asignar- debe asignar a la persona o grupo a la consultaweb
		$('#asignaraw').click(function () {
			let cw_relacionada = [];
			ID = $('#cwid').val();
			persona = $("#cuentaid").val();
			tema = $("#tema_id").text();
			aportacion = $("#aportacion").text();
			empresa = $("#empresa").val();
			de = $("#deemail").text();
			asignado = seleccionid;
			console.log("ID" + asignado);
			$("input:checkbox:checked").each(function () {
				//alert($(this).val());
				cw_relacionada.push($(this).val());
			});
			console.log("rel: " + cw_relacionada);

			//Control de atencion activa al transferir
			jQuery.ajax({
				async: false,
				data: {
					id: ID,
					asignado: asignado,
					modo: 'chequear'
				},
				url: 'index.php?module=AtencionesWeb&action=getAccionesConsulta',
				dataType: "json",
				success: function (data) {
					if (data != 0) {
						console.log(data);
						Vtiger_Helper_Js.showPnotify({
							type: 'error', //error, info
							title: 'Este usuario tiene una atención web activa', //titulo
							// text: 'Se asigno con exito', //mensaje
							animation: 'show',
						});
						//volver el estado a pendiente
						ID = $('#cwid').val();
						estado = $("#estado").val();
						localStorage.removeItem('cwidmodal');
						console.log(ID);
						jQuery.ajax({
							async: false,
							data: {
								id: ID,
								estado: estado,
								modo: 'cerrar'
							},
							url: 'index.php?module=AtencionesWeb&action=getAccionesConsulta',
							dataType: "json",
							success: function (data) {
								console.log(data);
							},
							error: function (xhr, ajaxOptions, thrownError) {
								console.log(thrownError);
							}
						});
					}
					else {
						if (asignado === '') {
							Vtiger_Helper_Js.showPnotify({
								type: 'error', //error, info
								title: 'Error: Debe seleccionar un usuario o grupo', //titulo
								text: 'Debe seleccionar un usuario o grupo para asignar la atencion', //mensaje
								animation: 'show',
							});
							//
							jQuery.ajax({
								async: false,
								data: {
									id: ID,
									modo: 'cerrar'
								},
								url: 'index.php?module=AtencionesWeb&action=getAccionesConsulta',
								dataType: "json",
								success: function (data) {
									console.log(data);
									localStorage.removeItem('cwidmodal');
									window.location.href = "index.php?module=ConsultasWeb&view=List&app=SUPPORT";
								},
								error: function (xhr, ajaxOptions, thrownError) {
									console.log(thrownError);
								}
							});

						} else { //Si todo marcha bien asigno la consulta y sus relacionadas
							jQuery.ajax({
								async: false,
								data: {
									id: ID,
									asignado: asignado,
									relacionados: cw_relacionada,
									de: de,
									persona: persona,
									empresa: empresa,
									aportacion: aportacion,
									tema: tema,
									modo: 'asignar'
								},
								url: 'index.php?module=AtencionesWeb&action=getAccionesConsulta',
								dataType: "json",
								success: function (data) {
									console.log(data);
									Vtiger_Helper_Js.showPnotify({
										type: 'info', //error, info
										title: 'Guardado', //titulo
										text: 'Se asigno con exito', //mensaje
										animation: 'show',
									});
									localStorage.removeItem('cwidmodal');
									window.location.href = "index.php?module=ConsultasWeb&view=List&app=SUPPORT";
								},
								error: function (xhr, ajaxOptions, thrownError) {
									console.log(thrownError);
								}
							});
						}
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				}
			});


		});//


		//Cuando doy crear atencion web asociada al supervisor
		$('#atencionweb').click(function () {
			let cw_relacionada = [];
			console.log("estoy mostrando el id grupo" + seleccionid);
			$("input:checkbox:checked").each(function () {
				//alert($(this).val());
				cw_relacionada.push($(this).val());
			});
			console.log("relacionados " + cw_relacionada);
			ID = $('#cwid').val();
			persona = $("#cuentaid").val();
			tema = $("#tema_id").text();
			aportacion = $("#aportacion").text();
			empresa = $("#empresa").val();
			de = $("#deemail").text();
			//console.log("el id es "+aportacion + " Persona: "+ tema + " empresa "+ empresa);
			jQuery.ajax({
				async: false,
				data: {
					id: ID,
					de: de,
					persona: persona,
					empresa: empresa,
					aportacion: aportacion,
					tema: tema,
					modo: 'atencionweb',
					relacionados: cw_relacionada
				},
				url: 'index.php?module=AtencionesWeb&action=getAccionesConsulta',
				dataType: "json",
				success: function (data) {
					//console.log(data);	
					Vtiger_Helper_Js.showPnotify({
						type: 'info', //error, info
						title: 'Guardado', //titulo
						text: 'Atencion Web creada con exito', //mensaje
						animation: 'show',
					});
					localStorage.removeItem('cwidmodal');
					if (persona === '') {
						persona = 13;
						console.log(persona);
					}
					//console.log("este es el id papa "+data);		
					window.location.href = "index.php?module=Accounts&view=Detail&record=" + persona;
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				}
			});


		});


	});



</script>

{/literal}