{strip}
<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="dashboardWidgetContent">
	<form class="form-horizontal" name="searchParams">
	  	<div class="form-group">
	    	<label for="accdocumentnumber" class="col-sm-12 fieldLabel">{vtranslate('accdocumentnumber', 'Accounts')}</label>
	    	<div class="col-sm-12">
	      		<input class="inputElement" id="accdocumentnumber" name="accdocumentnumber" autocomplete="off" type="text"/>
	    	</div>
	  	</div>
	  	<div class="form-group">
	    	<label for="acccountry" class="col-sm-12 fieldLabel">{vtranslate('acccountry', 'Accounts')}</label>
	    	<div class="col-sm-12">
	      		<select class="select2 referenceModulesList inputElement" id="acccountry" name="acccountry">
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
	    	<label for="accdocumenttype" class="col-sm-12 fieldLabel">{vtranslate('accdocumenttype', 'Accounts')}</label>
	    	<div class="col-sm-12">
	      		<select class="inputElement select2 select2-offscreen" id="accdocumenttype" data-fieldname="accdocumenttype" data-fieldtype="picklist" name="accdocumenttype" type="picklist">
                    {foreach key=id item=value from=$DOCUMENT_TYPES}
                    <option value="{$id}">
                        {$value}
                    </option>
                    {/foreach}
                </select>
	    	</div>
	  	</div>
	  	{*cuando el campo es Persona que llamó no tiene sentido desplegar la parte de contribuyente*}
	  	{if $SOURCE_FIELD neq 'contact_id'}
	  	<hr>
	  	<div class="form-group">
	    	<label for="acccontexternalnumber" class="col-sm-12 fieldLabel">{vtranslate('acccontexternalnumber', 'Accounts')}</label>
	    	<div class="col-sm-12">
	      		<input type="text" id="acccontexternalnumber" name="acccontexternalnumber" class="inputElement">
	    	</div>
	  	</div>
	  	<hr>
	  	<div class="form-group">
	    	<label for="accempexternalnumber" class="col-sm-12 fieldLabel">{vtranslate('accempexternalnumber', 'Accounts')}</label>
	    	<div class="col-sm-12">
	      		<input type="text" id="accempexternalnumber" name="accempexternalnumber" class="inputElement">
	    	</div>
	  	</div>
	  	<input type="hidden" for="error"id="error" value="">
		<input type="hidden" for="campos" id="campos" value="">
	  	{/if}
	  	<hr>
	  	<div class="form-group">
		    <div class="col-sm-offset-4 col-sm-4">
		     	<button type="button" id="validar" name="search" class="btn btn-success btn-block">Buscar</button>
		    </div>
		</div>
	</form>
</div>
<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME}
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