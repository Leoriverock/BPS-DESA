{if $OPCIONES} 
<div class="col-sm-6 pull-right" id="MainContainerLPTempFlujos">
    {if $TF_CAMPO_MOD}
        <script type="text/javascript">
            var _TF_CAMPO_MOD = "{$TF_CAMPO_MOD}";
        </script>   
    {/if}
    <div class="row clearfix">
        <div class="col-sm-12 padding0px pull-right">
        {if $OPCIONES['flujos'] and $OPCIONES['flujos']|count > 0}   
            {assign var=MODULE_INSTANCE value=Vtiger_Module_Model::getInstance($OPCIONES['source_module'])} 
            {assign var=FIELD_INSTANCE value=Vtiger_Field_Model::getInstance($OPCIONES['tf_campo_mod'], $MODULE_INSTANCE)} 
            {if $FIELD_INSTANCE->getPermissions() eq 1}
                {*Este if verifica que el perfil del usuario activo tenga permisos para ver el campo_mod*}
                {foreach from=$OPCIONES['flujos'] item=FLUJO}
                    {if $FIELD_INSTANCE->getProfileReadWritePermission() eq 1} 
                        {*Este if verifica que el perfil del usuario activo tenga permisos para modificar el campo_mod*}
                        <div class="btn-group pull-right">
                            <button data-id-flujo="{$FLUJO['id']}" data-comentario="{$FLUJO['tfc_comentario']}" 
                                class="btn btn-success LpTempFlujosActionBtn" 
                                style="
                                    background-color: {$FLUJO['tfc_color']}; 
                                    background-image:none;
                                    border: 1px solid gainsboro;
                                    border-radius: 4px;
                                    "
                                >
                                {vtranslate($FLUJO['tfc_etiqueta'], $OPCIONES['source_module'])}
                            </button>
                        </div>
                    {/if}
                {/foreach}
            {/if}
        {/if}
        </div>
    </div>
</div>
{/if}