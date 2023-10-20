{strip}
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" id="FormEnvio" >
                <input type="hidden" name="module" value="ModComments" />
                <input type="hidden" name="action" value="Save" />
                {assign var=HEADER_TITLE value={vtranslate('Agregar Comentario', $MODULE)}}
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}                
                <div class="modal-body">
                    <div class="container-fluid">                 
                        <div class="row commentTextArea">
                            <textarea class="col-lg-12" name="commentcontent" id="commentcontent" style="width: 100%;"
                                rows="4" placeholder="{vtranslate('Comentario', $MODULE)}..."
                                data-rule-required="true"></textarea>
                        </div>
                    </div> 
                </div>
                <div class="modal-footer ">
                    <center>
                        <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SEND', $MODULE)}</strong></button>
                        <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </center>
                </div>
            </form>
        </div>
    </div>
{/strip}