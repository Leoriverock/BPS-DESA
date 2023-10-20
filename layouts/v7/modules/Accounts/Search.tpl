{strip}
<style type="text/css">
	.modal-body {
		min-height: 40vh;
	}
	#popupContents hr {
		border: 1px solid black;
		margin: 3vh 0;
	}
	#popupPageContainer {
		overflow: auto;
	}
</style>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
    	{assign var=MODULO value=vtranslate($MODULE,$MODULE)}
    	{assign var=TITLE value="Buscar `$MODULO`"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
        <div class="modal-body">
            <div id="popupPageContainer" class="contentsDiv col-sm-12" >
                <input type="hidden" id="parentModule" value="{$SOURCE_MODULE}"/>
                <input type="hidden" id="module" value="{$MODULE}"/>
                <input type="hidden" id="sourceRecord" value="{$SOURCE_RECORD}"/>
                <input type="hidden" id="sourceField" value="{$SOURCE_FIELD}"/>
                <input type="hidden" id="selectedIds" name="selectedIds">
                <div id="popupContents" class="col-sm-8 col-sm-offset-2">
                	<form class="form-horizontal" name="searchParams">
					  	<div class="form-group">
					    	<label for="accdocumentnumber" class="col-sm-6 fieldLabel">{vtranslate('accdocumentnumber', 'Accounts')}</label>
					    	<div class="col-sm-6">
					      		<input class="inputElement" tabindex="1" id="accdocumentnumber" name="accdocumentnumber" autocomplete="off" type="text"/>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="acccountry" class="col-sm-6 fieldLabel">{vtranslate('acccountry', 'Accounts')}</label>
					    	<div class="col-sm-6">
					      		<select class="select2 referenceModulesList inputElement" id="acccountry" name="acccountry" tabindex="1">
					      			{*por defecto seleccionado 🇺🇾*}
				                    {foreach key=id item=value from=$COUNTRIES}
				                    <option {if $id eq 1}selected{/if} value="{$id}">
				                        {$value}
				                    </option>
				                    {/foreach}
				                </select>
					    	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="accdocumenttype" class="col-sm-6 fieldLabel">{vtranslate('accdocumenttype', 'Accounts')}</label>
					    	<div class="col-sm-6">
					      		<select class="inputElement select2 select2-offscreen" id="accdocumenttype" data-fieldname="accdocumenttype" tabindex="1" data-fieldtype="picklist" name="accdocumenttype" type="picklist">
				                    {foreach key=id item=value from=$DOCUMENT_TYPES}
				                    <option value="{$id}">
				                        {$value}
				                    </option>
				                    {/foreach}
				                </select>
					    	</div>
					  	</div>
					  	{*var_dump($SOURCE_MODULE)*}
					  	{if $SOURCE_MODULE != 'AtencionesWeb' && $SOURCE_MODULE != 'AtencionPresencial'}
					  	{*cuando el campo es Persona que llamó no tiene sentido desplegar la parte de contribuyente*}
					  	{if $SOURCE_FIELD neq 'contact_id' && $SOURCE_FIELD neq 'callaccount' }
					  	<hr>
					  	<div class="form-group">
					    	<label for="acccontexternalnumber" class="col-sm-6 fieldLabel">{vtranslate('acccontexternalnumber', 'Accounts')}</label>
					    	<div class="col-sm-6">
					      		<input type="text" id="acccontexternalnumber" tabindex="1" name="acccontexternalnumber" class="inputElement">
					    	</div>
					  	</div>
					  	<hr>
					  	<div class="form-group">
					    	<label for="accempexternalnumber" class="col-sm-6 fieldLabel">{vtranslate('accempexternalnumber', 'Accounts')}</label>
					    	<div class="col-sm-6">
					      		<input type="text" id="accempexternalnumber" tabindex="1" name="accempexternalnumber" class="inputElement">
					    	</div>
					  	</div>
					  	
					  	<hr>{/if}{/if}
					  	{*var_dump($SOURCE_MODULE)*}
					  	{if $SOURCE_MODULE == 'AtencionesWeb' or $SOURCE_MODULE == 'Calls' or $SOURCE_MODULE == 'AtencionPresencial'}
					  	<div class="col-sm-6">
					      		<input type="hidden" id="acccontexternalnumber" name="acccontexternalnumber" class="inputElement" >
					    	</div>
					    <div class="col-sm-6">
					      		<input type="hidden" id="accempexternalnumber" name="accempexternalnumber" class="inputElement" >
					    	</div>
					  	{/if}
					  	<input type="hidden" id="error" value="">
					  	<input type="hidden" id="campos" value="">
					  	<div class="form-group">
						    <div class="col-sm-offset-4 col-sm-4">
						     	<button type="button" tabindex="1" id="validar" name ="search" class="btn btn-success btn-block">Buscar</button>
						    </div>
						</div>
                	</form>
                </div>
            </div>
        </div>
    </div>
</div>
{/strip}
{literal}
	<script type="text/javascript">
		
		$("#validar").click(function(){
			var error = 0;
			var campos  = [];
			if (
				$("#accdocumentnumber") && $("#accdocumentnumber").length &&
				$("#accdocumentnumber").val().length > 0
			) { 
				error = error + 1; 
				campos.push('Numero de Documento');
			}
			if (
				$("#acccontexternalnumber") && $("#acccontexternalnumber").length &&
				$("#acccontexternalnumber").val().length > 0
			){ 
				error = error + 1; 
				campos.push('Número de contribuyente/RUT');
			}
			if (
				$("#accempexternalnumber") && $("#accempexternalnumber").length &&
				$("#accempexternalnumber").val().length > 0
			){ 
				error = error + 1;
				campos.push('Número de empresa');
			}
			$("#error").val(error);
			$("#campos").val(campos);
		});
		
	</script>

{/literal}