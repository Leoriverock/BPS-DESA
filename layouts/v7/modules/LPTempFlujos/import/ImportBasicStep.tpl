{strip}
	<script src="{vresource_url('layouts/v7/modules/LPTempFlujos/resources/FlowImport.js')}"></script>
	<div class='fc-overlay-modal modal-content'>
		<div class="overlayHeader">
			{assign var=TITLE value="{'LBL_IMPORT'|@vtranslate:$MODULE} {$FOR_MODULE|@vtranslate:$FOR_MODULE}"}
			{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
		</div>
		<div class="importview-content">
			{assign var=LABELS value=[]}				
			{$LABELS["step1"] = 'SELECCIONAR JSON'}
			{$LABELS["step2"] = 'VISTA PREVIA'}
			{$LABELS["step3"] = 'IMPORTACION'}	
			<div class='modal-body' style="display: none;"  id ="importContainerJson">
				{include file="BreadCrumbs.tpl"|vtemplate_path:$MODULE BREADCRUMB_ID='navigation_links' ACTIVESTEP=2 BREADCRUMB_LABELS=$LABELS MODULE=$MODULE}			
				{include file='import/ImportPreview.tpl'|@vtemplate_path:'LPTempFlujos'}
			</div>
			<div class='modal-body' id ="importContainer">
				{include file="BreadCrumbs.tpl"|vtemplate_path:$MODULE BREADCRUMB_ID='navigation_links' ACTIVESTEP=1 BREADCRUMB_LABELS=$LABELS MODULE=$MODULE}			
				<div class ="importBlockContainer show" id = "uploadFileContainer">
					<table class = "table table-borderless" cellpadding = "30" >
						<tr id="file_type_container" style="height:50px">
							<td></td>	
							<td data-import-upload-size="{$IMPORT_UPLOAD_SIZE}" data-import-upload-size-mb="{$IMPORT_UPLOAD_SIZE_MB}">
								<div>
									<input type="hidden" id="type" name="type" value="csv" />
									<input type="hidden" name="is_scheduled" value="1" />
									<div class="fileUploadBtn btn btn-primary">
										<span><i class="fa fa-laptop"></i> {vtranslate('IMPORTAR JSON', $MODULE)}</span>
										<input type="file" name="import_file" id="import_file" onchange="Vtiger_FlowImport_Js.checkFileType(event)" data-file-formats="json" />
									</div>
									<div id="importFileDetails" class="padding10"></div>
								</div>
							</td>
						</tr>
					</table>
				</div>				
			</div>
			<div class='modal-body' style="display: none;"  id ="importContainerFinish">
				{include file="BreadCrumbs.tpl"|vtemplate_path:$MODULE BREADCRUMB_ID='navigation_links' ACTIVESTEP=3 BREADCRUMB_LABELS=$LABELS MODULE=$MODULE}			
				<div id="resultImportFinish">
				</div>	
			</div>
		</div>
		<div class='modal-overlay-footer border1px clearfix'>
			<div class="row clearfix">
				<div id="bottombarjson" style="display: none;" class='textAlignCenter col-lg-12 col-md-12 col-sm-12'>
					<button class="btn btn-success btn-lg" onclick="Vtiger_FlowImport_Js.importActionJSONOK();">{vtranslate('IMPORTAR', $MODULE)}</button>
					&nbsp;&nbsp;&nbsp;<a class='cancelLink' onclick="Vtiger_Import_Js.loadListRecords();" data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>	
				</div>
				<div id="bottombarnormal" class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
					<a class='cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
				</div>
				<div id="bottombarfinish" style="display: none;" class='textAlignCenter col-lg-12 col-md-12 col-sm-12'>
					<a class='cancelLink' onclick="Vtiger_Import_Js.loadListRecords();" data-dismiss="modal" href="#">{vtranslate('LBL_FINISH', $MODULE)}</a>	
				</div>
			</div>
		</div>
	</div>
{/strip}
{literal}
<style>
	.errorResult > h3 { color: #d70000; }
	.okResult > h3 { color: rgb(61, 215, 0); }
	.errorResult,.okResult {
		text-align: center;
	}
	.outoffscreen{
		position: absolute;
		top: 200%;
	}
	.flujo-cambio > th:first-child,
	.flujo-cambio > td:first-child {
		border-left: 1px solid #555!important;
	}
	.flujo-cambio > th:last-child,
	.flujo-cambio > td:last-child {
		border-right: 1px solid #555!important;
	}
	.flujo-cambio > th {
		background-color: rgb(153, 153, 153);
		color: rgb(60, 60, 60);
	}
	
	.flujo{
		background-color: #555;
		color: white;
		border: 1px solid #555!important;
		border-top: none;
		border-bottom: none;
	}
	
	.flujo-cambio:last-child {
		border-bottom: 1px solid #555!important;
	}
</style>
{/literal}