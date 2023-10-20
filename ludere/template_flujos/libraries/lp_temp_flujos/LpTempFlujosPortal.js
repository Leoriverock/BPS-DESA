/**
*	Script para integrar LPTempFlujos de CRM con Portal clientes
*	Author: KM
*	Date: 2021-07-05
*/
LPTempFlujosPortal = {

	module  : null,
	record  : null, 

	OPCIONES : null,
	TF_CAMPO_MOD : null,

	container : null,

	loadControls : function (module,recordId){
		LPTempFlujosPortal.module = module;
		LPTempFlujosPortal.record = recordId;
		LPTempFlujosPortal.fetchControls(module,recordId).then(result => {
			if(result){			
            	LPTempFlujosPortal.OPCIONES 	= result.opciones;
            	LPTempFlujosPortal.TF_CAMPO_MOD = result.tf_campo_mod;
            	LPTempFlujosPortal.container 	= document.getElementById("LPTempFlujosContainer");
            	if(LPTempFlujosPortal.OPCIONES){
            		LPTempFlujosPortal.OPCIONES['flujos'].forEach(flujo => 
            			LPTempFlujosPortal.addFlujo(flujo)
            		);
            	}
			}
		})
	},


	fetchControls : function (){
		return new Promise(function(resolve, reject) {
			fetch('index.php?module=' + LPTempFlujosPortal.module + '&record=' + LPTempFlujosPortal.record + '&api=LPFetchFlujos').then( _ =>
	            _.json()
	        ).then(response =>{
	            console.log(response)
	            if(response.success){
	            	resolve(response.result);
	            }else{
	            	resolve(false);
	            }
	        })
		    
		});
	},

	addFlujo : function(flujo){
		let div = document.createElement("div");
		div.classList.add("btn-group","pull-right");
		let btn = document.createElement("button");
		btn.dataset["idFlujo"] = flujo.id;
		btn.dataset["comentario"] = flujo.tfc_comentario;
		btn.classList.add("btn","btn-success","LPTempFlujosActionBtn");
		btn.style.backgroundColor = flujo.tfc_color;
		btn.style.border = "1px solid gainsboro";
		btn.style.borderRadius = "4px";
		btn.textContent = flujo.tfc_etiqueta;
		LPTempFlujosPortal.addBtnListener(btn);

		div.appendChild(btn);
		LPTempFlujosPortal.container.appendChild(div);
	},


	// Acciones de Flujos:
	showProgress : function(){
		if(document.getElementById("LPTempFlujosPortalLoader")){
			document.getElementById("LPTempFlujosPortalLoader").style.display = "";
			document.getElementById("LPTempFlujosPortalLoader").firstElementChild.style.display = "";
			return;
		}
		let divContainer 	= document.createElement("div");
		let div 			= document.createElement("div");
		divContainer.id 	= "LPTempFlujosPortalLoader";
		div.style.textAlign = "center";
		div.style.position 	= "fixed";
		div.style.top 		= "50%";
		div.style.left 		= "40%";
		let img 			= document.createElement("img");
		img.src 			= "../layouts/v7/skins/images/loading.gif";
		div.appendChild(img);
		divContainer.appendChild(div);
		document.body.appendChild(divContainer);
	},

	hideProgress : function(){
		document.getElementById("LPTempFlujosPortalLoader").style.display = "none";
		document.getElementById("LPTempFlujosPortalLoader").firstElementChild.style.display = "none";
	},

	addBtnListener : function(btn){
		btn.addEventListener("click",function(evt){
			evt.preventDefault();
			// Si no necesita comentario
			if(btn.dataset["comentario"] == 0){
				// Aplicamos el filtro
				LPTempFlujosPortal.aplicarFlujo(btn.dataset["idFlujo"]);
			}else{
				// Caso contrario 
				LPTempFlujosPortal.modalComment().then(response =>{
					if(response){
						LPTempFlujosPortal.aplicarFlujo(btn.dataset["idFlujo"]);
					}
				})
			}
		})
	},


	aplicarFlujo: function(flujo) {
        // app.helper.showProgress();
        // 
        LPTempFlujosPortal.showProgress();
        let params = {
        	module: 'LPTempFlujos',
        	api : "LPEjecutarFlujo",
            //action: 'LPAjax',
            //mode: 'ejecutarFlujo',
            source_module : LPTempFlujosPortal.module,
            recordid:  LPTempFlujosPortal.record,
            flujo : flujo
        };
        params_string = [];
        for(let key in params){
        	params_string.push(key+"="+params[key])
        }
        params_string  = params_string.join("&");
        fetch("index.php?"+params_string).then( _ => _.json()).then(function(response) {            
            if(response.success == false){
            	LPTempFlujosPortal.hideProgress();
            }
            location.reload();

        });
    },

    modalComment : function(){
    	// A lo bandido
    	let tpl = '<div class="modal-dialog">' +	
        			'<div class="modal-content">'+
        				'<div class="modal-header"><div class="clearfix"><div class="pull-right "><button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button></div><h4 class="pull-left">Agregar Comentario</h4></div></div>'+
            			'<form class="form-horizontal" id="FormEnvio" >' +
                			'<input type="hidden" name="module" value="ModComments" />' +
				                '<input type="hidden" name="action" value="Save" />' +
				                '<div class="modal-body">' +
				                    '<div class="container-fluid">                 ' +
				                        '<div class="row commentTextArea">' +
				                            '<textarea class="col-lg-12" name="commentcontent" id="commentcontent" style="width: 100%;"' +
				                                'rows="4" placeholder="Comentario..."' +
				                                'data-rule-required="true"></textarea>' +
				                        '</div>' +
				                    '</div> ' +
				                '</div>' +
				                '<div class="modal-footer ">' +
				                    '<center>' +
				                        '<button id="saveButton" class="btn btn-success" type="submit" name="saveButton" style="margin-right:10px"><strong>Guardar</strong></button>' +
				                        '<a href="#" id="modalCancel" class="cancelLink" type="reset" data-dismiss="modal">Cancelar</a>' +
				                    '</center>' +
				                '</div>' +
				            '</form>' +
				        '</div>' +
				    '</div>';
		let modalContainer = document.createElement("div");
		modalContainer.id = "LPTempFlujosPortalModal";
		modalContainer.style.position	= "absolute";
		modalContainer.style.left	= "0";
		modalContainer.style.top	= "10%";
		modalContainer.style.right	= "0";
		modalContainer.style.bottom	= "0";
		modalContainer.innerHTML = tpl;
		document.body.appendChild(modalContainer);	

		// Retornamos una promesa
		return new Promise(function(resolve,reject){
			// Listener para botones del modal
			document.getElementById("saveButton").addEventListener("click",function(evt){
				evt.preventDefault();
				evt.stopPropagation();
				// Lo vamos a hacer por el momento utilizando lo ya existente en el schema				
				let comment = document.getElementById("commentcontent").value;
				// Obtenemos el formulario de comentario en la vista
				let commentForm = document.getElementsByName("commentForm");
				if(commentForm.length){
					// Colocamos el texto en textarea
					commentForm[0].querySelector("textarea").value = comment;
					commentForm[0].querySelector("textarea").dispatchEvent(new Event("change"));
					// Forzamos el click
					commentForm[0].querySelector("button").click();
					// Ocultamos el modal
					LPTempFlujosPortal.hideModal();
					resolve(true);
				}else{
					resolve(false);
				}

			});
			document.getElementById("modalCancel").addEventListener("click",function(evt){
				evt.preventDefault();
				evt.stopPropagation();
				LPTempFlujosPortal.hideModal();
				resolve(false);
			})
		})
	},

	hideModal : function(){
		document.getElementById("LPTempFlujosPortalModal").remove();
	}

}