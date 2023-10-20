{strip}
    <div class="modal-dialog" id="ModalEdit">
        <form class="modal-content FormEdicion" id="FormEdicion"  method="post" action="index.php" >                
            <div class="modal-header">
                <div class="clearfix">
                    <div class="pull-right " >
                        <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true" class='fa fa-close'></span>
                        </button>
                    </div>
                    <h4 class="pull-left" id="ModalHeader">Edicion</h4>
                </div>
            </div>  
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="fieldLabel">
                                    <label class="pull-right detailViewButtoncontainer"><font color="red">*</font> {vtranslate('Etiqueta',$MODULE)}</label>
                                </td>
                                <td class="fieldValue">
                                    <input name="edit_etiqueta" required id="edit_etiqueta" class="inputElement width75per" value="" type="text">
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldLabel">
                                    <label class="pull-right detailViewButtoncontainer">{vtranslate('Color',$MODULE)}</label>
                                </td>
                                <td class="fieldValue">
                                    <input name="edit_color" id="edit_color" class="inputElement width75per" value="" type="color">
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldLabel">
                                    <label class="pull-right detailViewButtoncontainer">{vtranslate('Requiere comentario',$MODULE)}</label>
                                </td>
                                <td class="fieldValue">
                                    <input name="edit_comentario" id="edit_comentario" class="inputElement width75per" type="checkbox">
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldLabel">
                                    <label class="pull-right detailViewButtoncontainer">{vtranslate('Para CRM',$MODULE)}</label>
                                </td>
                                <td class="fieldValue">
                                    <input name="edit_paracrm" id="edit_paracrm" class="atleastone inputElement width75per" type="checkbox">
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldLabel">
                                    <label class="pull-right detailViewButtoncontainer">{vtranslate('Para Portal',$MODULE)}</label>
                                </td>
                                <td class="fieldValue">
                                    <input name="edit_paraportal" id="edit_paraportal" class="atleastone inputElement width75per" type="checkbox">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
        </form>
    </div>
{/strip}
{literal}    
<script type="text/javascript">

(function() {
    const form = document.querySelector('#FormEdicion');
    const checkboxes = form.querySelectorAll("[type='checkbox'].atleastone");
    const checkboxLength = checkboxes.length;
    const firstCheckbox = checkboxLength > 0 ? checkboxes[0] : null;
    function init() {
        if (firstCheckbox) {
            for (let i = 0; i < checkboxLength; i++) checkboxes[i].addEventListener('change', checkValidity);
            checkValidity();
        }
    }
    function isChecked() {
        for (let i = 0; i < checkboxLength; i++) if (checkboxes[i].checked) return true;
        return false;
    }
    function checkValidity() {
        const errorMessage = !isChecked() ? 'Seleccione al menos una opcion' : '';
        firstCheckbox.setCustomValidity(errorMessage);
        firstCheckbox.required = !isChecked();
        console.log("www",errorMessage, firstCheckbox);
    }
    init();
})();

</script>
{/literal}