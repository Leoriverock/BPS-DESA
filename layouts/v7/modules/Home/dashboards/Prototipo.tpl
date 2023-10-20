<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="dashboardWidgetContent">
	<div class="panel panel-default col-sm-12">
        <div class="panel-heading" style="padding-bottom: unset;">
            <h3 class="panel-title">Llamada</h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" name="llamada">
                <div class="form-group">
                    <label for="input0" class="col-sm-4 control-label">Tipo de documento</label>
                    <div class="col-sm-4">
                        <select id="input0" name="calldocumenttype" class="inputElement select2">
                            <option value="DO">Documento</option>
                            <option value="PA">Pasaporte</option>
                            <option value="FR">Fronterizo</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input1" class="col-sm-4 control-label">País</label>
                    <div class="col-sm-4">
                        <select id="input1" name="callcountry" class="inputElement select2">
                            {foreach key=UID item=COUNTRY from=$COUNTRIES}
                                <option value="{$UID}">{$COUNTRY}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input2" class="col-sm-4 control-label">Número de documento</label>
                    <div class="col-sm-4">
                        <input type="text" class="inputElement" name="calldocumentnumber" id="input2" placeholder="Número de documento">
                    </div>
                </div>
                <div class="form-group">
                    <label for="input3" class="col-sm-4 control-label">Id interacción</label>
                    <div class="col-sm-4">
                        <input type="text" class="inputElement" name="callid" id="input3" placeholder="Id interacción">
                    </div>
                </div>
                <div class="form-group">
                    <label for="input4" class="col-sm-4 control-label">Número que llamó</label>
                    <div class="col-sm-4">
                        <input type="text" class="inputElement" name="callphonenumber" id="input4" placeholder="Número que llamó">
                    </div>
                </div>
                <div class="form-group">
                    <label for="input5" class="col-sm-4 control-label">Fecha llamada</label>
                    <div class="col-sm-4">
                        <div class="input-group inputElement" style="margin-bottom: 3px">
                            <input id="input5" type="text" class="dateField inputElement" name="callstartdate" data-date-format="yyyy-mm-dd"/>
                            <span class="input-group-addon"><i class="fa fa-calendar "></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input6" class="col-sm-4 control-label">Hora llamada</label>
                    <div class="col-sm-4">
                        <div class="input-group inputElement time">
                            <!--input id="input6" type="text" data-format="12" class="timepicker-default inputElement" name="callstarttime"/-->
                            <input id="input6" type="text" name="callstarttime" class="inputElement"/>
                            <span class="input-group-addon" style="width: 30px;">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input7" class="col-sm-4 control-label">WG</label>
                    <div class="col-sm-4">
                        <input id="input7" type="text" class="inputElement" name="callwg" id="input4" placeholder="WG">
                    </div>
                </div>
                <div class="form-group">
                    <label for="input8" class="col-sm-4 control-label">PIN</label>
                    <div class="col-sm-4">
                        <input id="input8" type="text" class="inputElement" name="callpin" id="input5" placeholder="PIN">
                    </div>
                </div>
                <div class="form-group">
                    <label for="input9" class="col-sm-4 control-label">Múltiple</label>
                    <div class="col-sm-4">
                        <input id="input9" type="text" class="inputElement" name="callmultiple" id="input6" placeholder="Múltiple">
                    </div>
                </div>
                <div class="form-group">
                    <label for="input10" class="col-sm-4 control-label">WorkGroup</label>
                    <div class="col-sm-4">
                        <input type="text" class="inputElement" name="workgroup" id="input10" placeholder="WorkGroup">
                    </div>
                </div>
                <div class="form-group">
                    <label for="input11" class="col-sm-4 control-label">Usuario asignado</label>
                    <div class="col-sm-4">
                        <select id="input11" name="calluser" class="inputElement select2">
                            {foreach key=UID item=USUARIO from=$USUARIOS}
                                <option value="{$USUARIO}">{$USUARIO}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="btn-group col-sm-offset-2">
                        <button type="button" name="iniciarLlamada" class="btn btn-success">
                            <span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> Iniciar llamada
                        </button>
                        <button type="button" name="finalizarLlamada" class="btn btn-danger">
                            <span class="glyphicon glyphicon-phone-alt" aria-hidden="true"></span> Finalizar llamada
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME}
    </div>
</div>