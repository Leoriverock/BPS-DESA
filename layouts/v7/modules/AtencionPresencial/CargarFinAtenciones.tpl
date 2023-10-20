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
		.contenedor{
			margin-right: 25px;
			margin-left: 25px;
  			height: 300px;
  			margin-top: 20px;

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
		
		#tipotramite{
			width: 100%;
			background-color: transparent;
			font-size: 14px;
		}
		#tipoconsulta{
			background-color: transparent;
			width: 100%;
			font-size: 14px;
		}
		#tipotramite option{
			background: white !important;
			padding: 5px;
		}
		#tipoconsulta option{
			padding: 5px;
			background: white !important;
			
		}
		
		input[type="text"]
		{
		  	width: auto;
  			word-wrap: break-word;
  			padding: 0px;
  			margin: 0px;
		}


	</style>
{/literal}

{strip}
		{include file="PicklistColorMap.tpl"|vtemplate_path:$MODULE LISTVIEW_HEADERS=$RELATED_HEADERS}
		<div class="modal-dialog modal-lg" style="width: 30%;height: 250px;">
			<div class="modal-content" style="height: 250px">
			    <div class="modal-header">
				    <button type="button" class="close" id="cerrar" data-dismiss="modal" aria-hidden="true">&times;</button>
				    <h4 class="modal-title"> Finalizar atención</h4>

			    </div>
			    
		  		<div class="modal-body" style="height: 200px">
		  			
		  			<div  id="tablaMov" >
						<div class="contenedor" > 
						
							<table align="left" style="width: 100%">
							
								<tr >	
									<td>
										<label style="font-size:16px; margin-right: 5px;">Tr&aacute;mite: </label>
									</td>
									<td>
										 
										<select id ="tipotramite" name ='tipotramite'>
											<option value="0" selected>Eliga opción:</option>
											{foreach from = $TIPOTRAMITE item = t}

																	
												<option value="{$t.id}" {if $TRAMITE eq $t.nombre} selected {/if}>{$t.nombre}</option>
											
											
											{/foreach}
											</select>
									</td>								
								</tr>
								<tr>
			                    	<th colspan="1" ></th>
			                    </tr>
								<tr>
									<td>
										<label style="font-size:16px; margin-right: 15px;">Resultado: </label>
									</td>
									<td>
										
										<select id ="tipoconsulta" name ='tipoconsulta'>
											<option value="0" selected>Eliga opción:</option>
											{foreach from = $TIP0CONSULTA item = t}
												<option value="{$t.tconsulta}">{$t.tconsulta}</option>
											{/foreach}
											</select>
									</td>								
								</tr>
								<tr>
									<td>
										<input type="hidden" id="atencionid" name="" value="{$ID}">
									</td>
								</tr>
								<tr>
									<td colspan="3" style="padding-right: 10%; padding-top:55px; margin-top: 5px;">
										<button class="btn btn-success" style=" font-size:16px;margin-left: 45%" onclick="cargarModalFin()" name="fin_atencion">Finalizar</button> 
									</td>
								</tr>
							</table>
							
							

							
						</div>
					</div>
					</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
{/strip}
{literal}
<script type="text/javascript">
		$( document ).ready(function() {
			jQuery('[name="fin_atencion"]').attr('disabled', true); 
			//capturo el tipo de consulta seleccionado
			$("#tipoconsulta").change(function(){

            			seleccionid= $(this).children("option:selected").val();
            			
      					console.log("Has seleccionado - " + seleccionid);
      					tipoconsulta = seleccionid.slice(0,2);
      						console.log("Has seleccionado nro - " + tipoconsulta);
      					if(seleccionid === '0'){
      						console.log("Has seleccionado - " + seleccionid);
      						
      						jQuery('[name="fin_atencion"]').attr('disabled', true); 
      						
      					}else{
      						jQuery('[name="fin_atencion"]').attr('disabled', false); 
      					}

        		});
			//capturo el tipo de tramite seleccionado
			$tipotramite = '';
			tipotramite= $("#tipotramite").val();

			console.log("M "+tipotramite);
			$("#tipotramite").change(function(){

            			tipotramite= $(this).children("option:selected").val();
            			
      					console.log("Has seleccionado - " + tipotramite);
      					
      					

        	});


	        var id = jQuery('#atencionid').val();
	       	
	         jQuery('[name="fin_atencion"]').click(function(e){
	                        console.log("entra a fin_atencion ");
	                        var params = {};
	                            params['module'] = 'AtencionPresencial';
	                            params['action'] = 'getAcciones';
	                            params['tipotramite'] = tipotramite;
	                            params['modo'] = 'finalizarNumero';
	                            params['id'] = id;
								params['tipoconsulta'] = tipoconsulta;
								params['seleccionid'] = seleccionid;
	                            console.log(params);
	                            var progressIndicatorElement = jQuery.progressIndicator();
								AppConnector.request(params).then(function(data){
                                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                    console.log(data.result);
                                    app.helper.showSuccessNotification({title:'', message:'Se finalizó con exito'});
                                    //location.reload();
                                    // Redirigir a la página de lista de Atención Presencial en Vtiger
                                    
									window.location.href = 'index.php?module=AtencionPresencial&view=List&app=SUPPORT';

                                    /*if (data && data.result && data.result.success) {
                                        
                                        
                                       
                                        
                                    }else{
                                        if(data && data.result && data.result.error){
                                            console.log("entro aqui");
                                            app.helper.showErrorNotification({message:data.result.error});
                                        }else{
                                            app.helper.showErrorNotification({message:'Hubo problemas al finalizar la atención'});
                                        }
                                    }*/
                                },function(error){
                                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                    app.helper.showErrorNotification({message:error.message})
                                    //console.log("entro aqui222");
                                    console.log(error);
                                }); 
	                            
	        });
	         
    });
</script>
{/literal}