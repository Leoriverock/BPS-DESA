{strip}

	{assign var=tieneAcceso value=$CURRENT_USER_MODEL->tienePermiso()}
	{foreach item=DETAIL_VIEW_WIDGET from=$DETAILVIEW_LINKS['DETAILVIEWWIDGET']}
		{if ($DETAIL_VIEW_WIDGET->getLabel() eq 'Documents') }
			{assign var=DOCUMENT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'ModComments')}
			{assign var=COMMENTS_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_UPDATES')}
			{assign var=UPDATES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{/if}
	{/foreach}

	<div class="left-block col-lg-4">
		{* Module Summary View*}
		<div class="summaryView">
			<div class="summaryViewHeader">
				<h4 class="display-inline-block">{vtranslate('LBL_KEY_FIELDS', $MODULE_NAME)}</h4>
			</div>
			<div class="summaryViewFields">
				{$MODULE_SUMMARY}
			</div>
			{*solo si es persona, es decir, solo tiene seteado el accpersid*}
			{if $RECORD->get('accpersid')}
			<div class="text-center">
				<hr/>
				<a type="button" title="Ir al Escritorio Ciudadano" target="_blank" class="btn btn-primary" href="{$LINK_ESCRITORIO_CIUDADANO}" style="margin-right: 10px";>Escritorio Ciudadano</a>	
				{* if $RECORD->get('accdocumenttype') eq 'Documento' && $ACCOUNTRY eq 'uruguay' *}
					<a type="button" title="Ir al Portadocumentos" target="_blank" class="btn btn-primary" href="{$LINK_PORTADOCUMENTOS}">Portadocumentos</a>	
				{*/if*}
			</div>
			{/if}
			
			
			
			
		</div>
		{* Module Summary View Ends Here*}


		{* Summary View Documents Widget*}
		{if $DOCUMENT_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_documents" data-url="{$DOCUMENT_WIDGET_MODEL->getUrl()}" data-name="{$DOCUMENT_WIDGET_MODEL->getLabel()}">
					<div class="widget_header clearfix">
						<input type="hidden" name="relatedModule" value="{$DOCUMENT_WIDGET_MODEL->get('linkName')}" />
						<span class="toggleButton pull-left"><i class="fa fa-angle-down"></i>&nbsp;&nbsp;</span>
						<h4 class="display-inline-block pull-left">{vtranslate($DOCUMENT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>

						{if $DOCUMENT_WIDGET_MODEL->get('action')}
							{assign var=PARENT_ID value=$RECORD->getId()}
							<div class="pull-right">
								<div class="dropdown">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<span class="fa fa-plus" title="{vtranslate('LBL_NEW_DOCUMENT', $MODULE_NAME)}"></span>&nbsp;{vtranslate('LBL_NEW_DOCUMENT', 'Documents')}&nbsp; <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li class="dropdown-header"><i class="fa fa-upload"></i> {vtranslate('LBL_FILE_UPLOAD', 'Documents')}</li>
										<li id="VtigerAction">
											<a href="javascript:Documents_Index_Js.uploadTo('Vtiger',{$PARENT_ID},'{$MODULE_NAME}')">
												<img style="  margin-top: -3px;margin-right: 4%;" title="Vtiger" alt="Vtiger" src="layouts/v7/skins//images/Vtiger.png">
												{vtranslate('LBL_TO_SERVICE', 'Documents', {vtranslate('LBL_VTIGER', 'Documents')})}
											</a>
										</li>
										<li role="separator" class="divider"></li>
										<li class="dropdown-header"><i class="fa fa-link"></i> {vtranslate('LBL_LINK_EXTERNAL_DOCUMENT', 'Documents')}</li>
										<li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E',{$PARENT_ID},'{$MODULE_NAME}')">&nbsp;<i class="fa fa-external-link"></i>&nbsp;&nbsp; {vtranslate('LBL_FROM_SERVICE', 'Documents', {vtranslate('LBL_FILE_URL', 'Documents')})}</a></li>
										<li role="separator" class="divider"></li>
										<li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W',{$PARENT_ID},'{$MODULE_NAME}')"><i class="fa fa-file-text"></i> {vtranslate('LBL_CREATE_NEW', 'Documents', {vtranslate('SINGLE_Documents', 'Documents')})}</a></li>
									</ul>
								</div>
							</div>
						{/if}
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Documents Widget Ends Here*}
	</div>

	<div class="middle-block col-lg-8">
		<div class="summaryWidgetContainer">
			<h4>Últimos 10 {vtranslate('HelpDesk', 'HelpDesk')}
				<a type="button" 
					module="HelpDesk" 
					class="btn btn-default"
					style="float: right"
					href="index.php?module=HelpDesk&view=Edit&sourceModule=Accounts&parent_id={$RECORD->getId()}&relationOperation=true&sourceRecord={$RECORD->getId()}"
				>
					<i class="fa fa-plus"></i>&nbsp;&nbsp;Añadir {vtranslate('SINGLE_HelpDesk', 'HelpDesk')}
				</a>
			</h4>
			<table class = "widgetTicketsTable table table-stripped">
				<thead class = "widgetTicketsTr">
					<th>Tema</th>
					<th>Título</th>
					<th>Canal</th>
					<th>Estado</th>
					<th>Fecha y Hora de creación</th>
					<th class = "text-right">Ver</th>
				</thead>
				<tbody>
					{if $LASTS_TICKETS|@count eq 0}
						<td colspan = "5" class = "widgetTicketsNoLines">No hay {vtranslate('HelpDesk', 'HelpDesk')|lower} asociados</td>
					{/if}
					{if $LASTS_TICKETS|@count gt 0}
						{foreach key=UID item=TICKET from=$LASTS_TICKETS}
							<tr>
								<td>{$TICKET['topicname']}</td>
								<td>{$TICKET['title']}</td>
								<td>{$TICKET['ticketcanal']}</td>
								<td>{$TICKET['status']}</td>
								<td>{$TICKET['createdtime']|date_format:"%d/%m/%Y %H:%M:%S"}</td>
								<td class = "text-right">
									<a href = "{$TICKET['url']}">
										<i class = "fa fa-arrow-right"></i>
									</a>
								</td>
							</tr>
						{/foreach}
					{/if}
				</tbody>
			</table>
		</div>
		<!--
		<div class="summaryWidgetContainer">
			<h4>Últimas 10 {vtranslate('AtencionesWeb', 'AtencionesWeb')}</h4>
			<table class = "widgetAtencionesWebTable table table-stripped">
				<thead class = "widgetAtencionesWebTr">
					<th>Tema</th>
					<th>Numero</th>
					{* 
					<th>Fecha/Hora comienzo</th>
					<th>Fecha/Hora fin</th>
					 *}
					<th>Categoria</th>
					<th>Estado</th>
					<th>Fecha y Hora de creación</th>
					<th class = "text-right">Ver</th>
				</thead>
				<tbody>
					{if $LASTS_ATENCIONESWEB|@count eq 0}
						<td colspan = "5" class = "widgetAtencionesWebNoLines">No hay {vtranslate('AtencionesWeb', 'AtencionesWeb')|lower} asociadas</td>
					{/if}
					{if $LASTS_ATENCIONESWEB|@count gt 0}
						{foreach key=UID item=ATENCIONWEB from=$LASTS_ATENCIONESWEB}
							<tr>
								<td>{$ATENCIONWEB['aw_tema']}</td>
								<td>{$ATENCIONWEB['aw_numero']}</td>
								{* 
								<td>{$ATENCIONWEB['aw_fechacomienzo']|date_format:"%d/%m/%Y %H:%M:%S"}</td>
								<td>{$ATENCIONWEB['aw_fechafin']|date_format:"%d/%m/%Y %H:%M:%S"}</td>
								*}
								<td>{$ATENCIONWEB['aw_categoria']}</td>
								<td>{$ATENCIONWEB['aw_estado']}</td>
								<td>{$ATENCIONWEB['createdtime']|date_format:"%d/%m/%Y %H:%M:%S"}</td>
								<td class = "text-right">
									<a href = "{$ATENCIONWEB['url']}">
										<i class = "fa fa-arrow-right"></i>
									</a>
								</td>
							</tr>
						{/foreach}
					{/if}
				</tbody>
			</table>
		</div>
		-->
		{* Summary View Related Activities Widget*}
		<div id="relatedActivities">
			{$RELATED_ACTIVITIES}
		</div>
		{* Summary View Related Activities Widget Ends Here*}

		{* Summary View Comments Widget*}
		{if $COMMENTS_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_comments" data-url="{$COMMENTS_WIDGET_MODEL->getUrl()}" data-name="{$COMMENTS_WIDGET_MODEL->getLabel()}">
					{*<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$COMMENTS_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block">{vtranslate($COMMENTS_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>*}
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Comments Widget Ends Here*}
	</div>
{/strip}
{literal}
<style type="text/css">
	.btn-primary {
    	width: 150px; /* Ajusta el ancho según tus necesidades */
  	}
</style>
{/literal}