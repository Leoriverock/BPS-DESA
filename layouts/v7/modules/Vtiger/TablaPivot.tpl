{literal}
<!-- Estilos LP -->
<style type="text/css">
	.lp-tabla-pivot {
        margin:     10px;
        background: #fff;
        max-width:  100%;
        overflow-x: auto;
    }
</style>

<!-- Scripts JS LP -->
<script type="text/javascript">
var params = {};
params["modulo"] = '{/literal}{$MODULO}{literal}';
params["filtro"] = '{/literal}{$FILTRO}{literal}';

var datosTabla  = null;

// Init
jQuery(document).ready(function ()
{
	console.log('Iniciando tabla pivot...');

	// Traer los datos y mostrarlos
	actualizar();
});

function actualizar ()
{
	// Ocultar gráfica. Mostrar preloading
	jQuery('#output').hide();
	jQuery('#dashChartLoader').show();

	jQuery.ajax({
		data: 		params,
		url: 		"index.php?module=Vtiger&view=TablaPivot&mode=Data",
		dataType: 	'json',

		success: function (data)
		{
			console.log(data);
            datosTabla = data.data;
			graficar(data);
		},

		error: function (xhr, ajaxOptions, thrownError)
		{
			console.error(thrownError);
		}
	});
}

/***
Función graficar
@param 	datos	:Object
		Se espera un objeto con la siguiente estructura:
		{
			"cols" : [col1, col2, ...],
			"data" : [data1, data2, ...]
		}
***/
function graficar (datos /*Object*/)
{
	var derivers= jQuery.pivotUtilities.derivers,
        tpl     = jQuery.pivotUtilities.aggregatorTemplates,
        cols 	= datos.cols,
        rows    = datos.rows,
        renders = jQuery.extend(
            // Tablas
            {
                "Tabla": jQuery.pivotUtilities.renderers["Table"],
                "Tabla con barras": jQuery.pivotUtilities.renderers["Table Barchart"],
                "Heatmap": jQuery.pivotUtilities.renderers["Heatmap"],
                "Heatmap por filas": jQuery.pivotUtilities.renderers["Row Heatmap"],
                "Heatmap por columnas": jQuery.pivotUtilities.renderers["Col Heatmap"]
            },
            // Gráficos
            {
                "Gráfico de Líneas": jQuery.pivotUtilities.c3_renderers["Line Chart"],
                "Gráfico de Barras": jQuery.pivotUtilities.c3_renderers["Bar Chart"],
                "Gráfico de Barras Apilado": jQuery.pivotUtilities.c3_renderers["Stacked Bar Chart"],
                "Gráfico de Área": jQuery.pivotUtilities.c3_renderers["Area Chart"],
                "Gráfico de Dispersión": jQuery.pivotUtilities.c3_renderers["Scatter Chart"]
            },
            // Treemap de D3
            jQuery.pivotUtilities.d3_renderers
        );
    
    // Ocultar GIF de preloading
    jQuery('#dashChartLoader').hide();

    // Ubicar gráfica
    jQuery('#output').remove();
    jQuery('#resultados').append('<div id="output" class="lp-tabla-pivot"></div>');

    // Si hay fechas, crear los derivers
    var fechas = {};
    console.log(datos);
    if (!!datos.fech && datos.fech.length > 0)
    {
        datos.fech.forEach(function (campo)
        {
            fechas[campo + "(Año-Mes)"] = derivers.dateFormat(campo, '%y-%m');
            fechas[campo + "(Año)"] = derivers.dateFormat(campo, '%y');
        })
    }

    var settings = {/literal}{$SETTSPIVOT}{literal};
    console.log(settings);
    if(settings[0].length == 0 && !!datos.rows){
        settings[0] = datos.rows;
    }
    if(settings[1].length == 0 && !!datos.cols){
        settings[1] = datos.cols;
    }
    if( datos.data === undefined ){
        datos.data = datosTabla;
    }
    console.log(settings,datos);
    $("#output").pivotUI(
        datos.data, 
        {
            rows:               settings[0],
            cols:               settings[1],
            renderers:          renders,
            derivedAttributes:  fechas
        },
        true,
        'es'
    );
}

function exportarExcel (nombre)
{
    var $tabla  = jQuery('.pvtRendererArea'),
        html    = jQuery('<div>')
            .append($tabla.eq(0).clone())
            .html();

    // Normalizar caracteres raros
    while (html.indexOf('á') != -1) html = html.replace('á', '&aacute;');
    while (html.indexOf('Á') != -1) html = html.replace('Á', '&Aacute;');
    while (html.indexOf('é') != -1) html = html.replace('é', '&eacute;');
    while (html.indexOf('É') != -1) html = html.replace('É', '&Eacute;');
    while (html.indexOf('í') != -1) html = html.replace('í', '&iacute;');
    while (html.indexOf('Í') != -1) html = html.replace('Í', '&Iacute;');
    while (html.indexOf('ó') != -1) html = html.replace('ó', '&oacute;');
    while (html.indexOf('Ó') != -1) html = html.replace('Ó', '&Oacute;');
    while (html.indexOf('ú') != -1) html = html.replace('ú', '&uacute;');
    while (html.indexOf('Ú') != -1) html = html.replace('Ú', '&Uacute;');
    while (html.indexOf('º') != -1) html = html.replace('º', '&ordm;');
    while (html.indexOf('ñ') != -1) html = html.replace('ñ', '&ntilde;');
    while (html.indexOf('Ñ') != -1) html = html.replace('Ñ', '&Ntilde;');

    // Armar el form para enviar
    jQuery("#datos_a_enviar").val(html);
    jQuery("#nombre_a_enviar").val(nombre);
    jQuery("#is_submited").val((new Date()).getTime());
    jQuery("#FormularioExportacion").submit();

    return false;
}
</script>
{/literal}

<div class="detailViewContainer">
    <hr>
    <div class="row row-fluid detailViewTitle">
        <div class="col-md-5 recordLabel font-x-x-large span pushDown" title="{vtranslate($MODULO)}">
            <span style='font-weight: bold;font-size: 16px;'>Tabla Pivot {vtranslate($MODULO)}</span>
        </div>
        <div class="col-md-3">
            <select id="selectFiltros">
                <option>Personalizado</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn pull-right" id="abrirModalFiltro">Guardar Filtro</button>
        </div>
    </div>
    <hr>
    <div class="row-fluid detailViewTitle">
        <!--span class="row-fluid">
            <span class="muted">
                M&oacute;dulo "{vtranslate($MODULO)}"{if empty($FILTRONAME)}, mostrando todos sus campos.{else} filtrado por "{$FILTRONAME}".{/if}
            </span>
        </span-->
    </div>
</div>

<!-- Gráfica -->
<div class="detailViewInfo row-fluid">
    <div class="row-fluid">
    	<div id="dashChartLoader" style="text-align:center;">
            <img src="layouts/vlayout/skins/softed/images/loading.gif" border="0" align="absmiddle">
        </div>

        <div id="resultados" class="details">
            <div id="output" style="margin: 10px;"></div>
        </div>
    </div>

    <!-- Botones -->
    <div class="row-fluid" style="margin-top: 10px;">
        <a class="btn addButton" href="#" style="padding: 4px 6px; float: left; font-weight: bold; margin-right: 5px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;" onclick="exportarExcel('{vtranslate($MODULO)}');">
            Exportar a Excel
        </a>
        <form action="download.php" method="post" target="_blank" id="FormularioExportacion">
            <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
            <input type="hidden" id="nombre_a_enviar" name="nombre_a_enviar" />
            <input type="hidden" id="is_submited" name="is_submited" />
        </form>
    </div>

</div>


{include file='modules/Analisis/modalGuardarFiltro.tpl'}