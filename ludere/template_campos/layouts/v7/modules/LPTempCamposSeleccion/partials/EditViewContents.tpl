{* Todos los posibles campos de los posibles modulos configurables que tienen campos UITypes 15, 16 o 33 *}
<input type="hidden" name="posibles_ts_campo" value='{Vtiger_Functions::jsonEncode($POSIBLES_TS_CAMPO)}' />

{* Todos los posibles valores de los posibles modulos configurables que tienen campos UITypes 15, 16 o 33 *}
<input type="hidden" name="posibles_ts_valor" value='{Vtiger_Functions::jsonEncode($POSIBLES_TS_VALOR)}' />

{* Todos los valores actuales elegidos para el campo ts_valor en cuestion *}
<input type="hidden" name="actuales_ts_valor" value='{$ACTUALES_TS_VALOR}' />

{* Incluir la vista Edit estandar de vtiger para no repetir codigo *}
{include file="partials/EditViewContents.tpl"|vtemplate_path:Vtiger}