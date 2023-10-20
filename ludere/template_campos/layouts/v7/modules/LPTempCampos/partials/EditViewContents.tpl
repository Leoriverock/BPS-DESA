{* Todos los posibles campos de los posibles modulos configurables que tienen campos UITypes 15, 16 o 33 *}
<input type="hidden" name="posibles_tc_campo" value='{Vtiger_Functions::jsonEncode($POSIBLES_TC_CAMPO)}' />

{* Incluir la vista Edit estandar de vtiger para no repetir codigo *}
{include file="partials/EditViewContents.tpl"|vtemplate_path:Vtiger}

{* Plantear al cliente si queire que se puedan editar los detalles en esta vista
y si acepta entonces estimar cuanto tiempo mas demoraria hacerlo e implementar *}