<script src="{vresource_url('layouts/v7/modules/LPTempFlujos/cytoscape/cytoscape.min.js')}"></script>
<script src="{vresource_url('layouts/v7/modules/LPTempFlujos/cytoscape/lodash.js')}"></script>
<script src="{vresource_url('layouts/v7/modules/LPTempFlujos/cytoscape/cytoscape-edgehandles.js')}"></script>
<script src="{vresource_url('layouts/v7/modules/LPTempFlujos/cytoscape/cytoscape-cxtmenu.js')}"></script>
<link href="https://unpkg.com/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<div id="cy" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></div>

<script type="text/javascript">
{if $CAMPOS_VALORES}
    var nodes = {Vtiger_Functions::jsonEncode($CAMPOS_VALORES)};
    var edges = {Vtiger_Functions::jsonEncode($edges)};
{/if}
</script>
{literal}
	<style>
    #cy { height: 500px; }
	</style>
	<script>
  
        var _eliminar = [];
        function cargar_grafo(){
            _eliminar = [];
            if(cy !== undefined) {
                cy.destroy();
            }
            // generar grafo
            var cy = window.cy = cytoscape({
                container: document.getElementById('cy'),
                layout: {
                    name: 'grid',
                    rows: 2,
                    cols: 2,
                },
                style: [
                    {
                    selector: 'node[name]',
                    style: {
                        'content': 'data(name)'
                    }
                    },

                    {
                    selector: 'edge',
                    style: {
                        'curve-style': 'bezier',
                        'color': 'white',
                        'label': 'data(etiqueta)',
                        'font-size': '10px',
                        'text-background-color': 'data(color)',
                        'text-background-opacity': '.8',
                        'text-background-shape': 'round-rectangle',
                        'text-background-padding': '4px',
                        'target-arrow-shape': 'triangle'
                    }
                    },
                    // some style for the extension
                    {
                    selector: '.eh-handle',
                    style: {
                        'background-color': 'red',
                        'width': 12,
                        'height': 12,
                        'shape': 'ellipse',
                        'overlay-opacity': 0,
                        'border-width': 12, // makes the handle easier to hit
                        'border-opacity': 0
                    }
                    },
                    {
                    selector: '.eh-hover',
                    style: {
                        'background-color': 'red'
                    }
                    },
                    {
                    selector: '.eh-source',
                    style: {
                        'border-width': 2,
                        'border-color': 'red'
                    }
                    },

                    {
                    selector: '.eh-target',
                    style: {
                        'border-width': 2,
                        'border-color': 'red'
                    }
                    },

                    {
                    selector: '.eh-preview, .eh-ghost-edge',
                    style: {
                        'background-color': 'red',
                        'line-color': 'red',
                        'target-arrow-color': 'red',
                        'source-arrow-color': 'red'
                    }
                    },
                    {
                    selector: '.eh-ghost-edge.eh-preview-active',
                    style: {
                        'opacity': 0
                    }
                    }
                ],
                elements: { nodes, edges, },
                wheelSensitivity: .05,

            });
            // permitir manipular aristas
            var eh = cy.edgehandles({ snap: true });
            cy.on('ehcomplete', (event, sourceNode, targetNode, addedEles) => {
                let { position } = event;
                editar(addedEles);
            });
            // permitir mostrar menu con el click derecho
            cy.cxtmenu({
                selector: 'edge',
                outsideMenuCancel: 10,
                menuRadius: function(ele){ return 70; },
                commands: [
                    {
                        content: '<span class="fa fa-trash fa-2x"></span>',
                        select: function(ele){
                            var server = ele.data('server');
                            if (server) 
                                _eliminar.push(ele.data('id'));
                            ele.remove();
                        }
                    },
                    {
                        content: '<span class="fa fa-edit fa-2x"></span>',
                        select: function(ele){
                            editar(ele);
                        }
                    },
                ]
            });
            function editar(ele) {
                var server = ele.data('server');
                var etiqueta = ele.data('etiqueta');
                if (server && !etiqueta) etiqueta = "nueva linea";
                var comentario = ele.data('comentario');
                var paracrm = ele.data('paracrm');
                var paraportal = ele.data('paraportal');

                var color = ele.data('color');
                app.helper.showProgress();
                app.request
                .get({'url': `index.php?module=LPTempFlujos&view=ModalEdicionArista`})
                .then(function (error, container) {
                    app.helper.hideProgress();
                    var cb = data => {          
                        var formulario = data.find('#FormEdicion');
                        formulario.find("[name='edit_etiqueta']").val(etiqueta);
                        formulario.find("[name='edit_comentario']").attr('checked', comentario == 1);
                        formulario.find("[name='edit_paracrm']").attr('checked', paracrm == 1);
                        formulario.find("[name='edit_paraportal']").attr('checked', paraportal == 1);
                        formulario.find("[name='edit_color']").val(color);
                        formulario.vtValidate({
                            submitHandler: function (form) {
                                var JForm = $(form);
                                var comentarionew = JForm.find("[name='edit_comentario']").attr('checked') == 'checked';
                                var paracrmnew = JForm.find("[name='edit_paracrm']").attr('checked') == 'checked';
                                var paraportalnew = JForm.find("[name='edit_paraportal']").attr('checked') == 'checked';
                                ele.data('etiqueta',JForm.find("[name='edit_etiqueta']").val());
                                ele.data('comentario', comentarionew ? 1 : 0);
                                ele.data('paracrm', paracrmnew ? 1 : 0);
                                ele.data('paraportal', paraportalnew ? 1 : 0);
                                ele.data('color',JForm.find("[name='edit_color']").val());
                                ele.data('editado', true);
                                app.helper.hideModal();                        
                                return false;
                            }
                        });
                    }         
                    app.helper.showModal(container, {cb});     
                });       
            }
        }
        function confirmacion_guardado() {
            app.helper.showConfirmationBox({message : 'Confirmar cambios en flujos'})
            .then(guardar_cambios_grafo);
        }
        function guardar_cambios_grafo() {
            var edges = cy.edges();
            var editar = [];
            var borrar = _eliminar;
            for(let i = 0; i<edges.length; i++){
                if(edges[i].data('editado')) {
                    if (edges[i].data('server')){
                        editar.push({
                            id: edges[i].data('id'),
                            tfc_color: edges[i].data('color'),
                            tfc_comentario: (edges[i].data('comentario') =='on' || edges[i].data('comentario') =='1' || edges[i].data('comentario') =='true') ? 1 : 0,
                            tfc_paracrm: (edges[i].data('paracrm') =='on' || edges[i].data('paracrm') =='1' || edges[i].data('paracrm') =='true') ? 1 : 0,
                            tfc_paraportal: (edges[i].data('paraportal') =='on' || edges[i].data('paraportal') =='1' || edges[i].data('paraportal') =='true') ? 1 : 0,
                            tfc_etiqueta: edges[i].data('etiqueta'),
                        });
                    } else {
                        editar.push({
                            tfc_color: edges[i].data('color'),
                            tfc_comentario: (edges[i].data('comentario') =='on' || edges[i].data('comentario') =='1' || edges[i].data('comentario') =='true') ? 1 : 0,
                            tfc_paracrm: (edges[i].data('paracrm') =='on' || edges[i].data('paracrm') =='1' || edges[i].data('paracrm') =='true') ? 1 : 0,
                            tfc_paraportal: (edges[i].data('paraportal') =='on' || edges[i].data('paraportal') =='1' || edges[i].data('paraportal') =='true') ? 1 : 0,
                            tfc_etiqueta: edges[i].data('etiqueta'),
                            tfc_origen: edges[i].data('source'),
                            tfc_destino: edges[i].data('target'),

                        });
                    }
                }
            }
            if (!editar.length && !borrar.length) return;
            app.helper.showProgress();
            var formData = new FormData(); 
            var postData = {
                module: 'LPTempFlujos',
                recordid: app.getRecordId(),
                action: 'LPAjax',
                mode: 'editar',
                editar,
                borrar
            };
            appendFormdata(formData, postData);
            app.request
            .post({ 
                url: 'index.php', 
                type: 'POST', 
                data: formData, 
                processData: false, 
                contentType: false 
            }).then(function(err, data) {
                app.helper.hideProgress();
                location.reload();
            });
        }
        // copiado para armar el formulario que quiero enviar
        function appendFormdata(FormData, data, name){
            name = name || '';
            if (typeof data === 'object'){
                $.each(data, function(index, value){
                    if (name == ''){
                        appendFormdata(FormData, value, index);
                    } else {
                        appendFormdata(FormData, value, name + '['+index+']');
                    }
                })
            } else {
                FormData.append(name, data);
            }
        }
    </script>
{/literal}

