Vtiger_Widget_Js('Vtiger_Prototipo_Widget_Js',{},{
	postLoadWidget : function() {
		this._super();
		this.registrarEventos();
	},
	registrarEventos: function() {
		const self = this;
		jQuery('[name=iniciarLlamada]').on('click', function(e){
			app.helper.showProgress();
			const formData = {
				module: 'Calls',
				action: 'IniciarLlamada'
			};
			const formArr = jQuery('form[name=llamada]', self.getContainer()).serializeArray();
			formArr.forEach(campo => formData[campo.name] = campo.value);
			app.request.get({
				data: formData
			}).then(function(e, res){
				app.helper.hideProgress();
				if (!e) {
					if (res.ok) {
						app.helper.showSuccessNotification({message: 'Llamada en proceso'});
						jQuery('form[name=llamada]', self.getContainer()).reset();
					} else {
						app.helper.showErrorNotification({message: res.error});
					}
				} else {
					app.helper.showErrorNotification({message: 'Error al iniciar llamada'});
				}
			});
		});
		jQuery('[name=finalizarLlamada]').on('click', function(e){
			app.helper.showProgress();
			const formData = {
				module: 'Calls',
				action: 'FinalizarLlamada'
			};
			const formArr = jQuery('form[name=llamada]', self.getContainer()).serializeArray();
			formArr.forEach(campo => formData[campo.name] = campo.value);
			app.request.get({
				data: formData
			}).then(function(e, res){
				app.helper.hideProgress();
				if (!e) {
					if (res.ok) {
						app.helper.showSuccessNotification({message: 'Llamada finalizada'});
					} else {
						app.helper.showErrorNotification({message: res.error});
					}
				} else {
					app.helper.showErrorNotification({message: 'Error al finalizar llamada'});
				}
			});
		});
	},
    refreshWidget: function() {
		
    },
	postRefreshWidget : function() {
		this._super();
	},
});

Vtiger_Widget_Js('Vtiger_SearchUser_Widget_Js',{},{
	postLoadWidget : function() {
		this._super();
		this.registrarEventos();
	},
	registrarEventos: function() {
		jQuery('body').on('click', '[name=search]', function(e){
			const params = {}
            const inputs = jQuery('[name=searchParams]').serializeArray();
            const error = jQuery('#error').val();
            const mensaje = jQuery('#campos').val();
            inputs.forEach(input => params[input.name] = input.value);
			if( params['accdocumentnumber'] == '' && params['accempexternalnumber'] == '' &&
				params['acccontexternalnumber'] == '' ){
					app.helper.showErrorNotification({
                        message: 'No se ha indicando documento del usuario'
                    });
					e.preventDefault();
					return;
				}
			console.log(error);
            params.module = 'Accounts';
            params.action = 'Search';
            if(error < 2){
	            app.request.get({
	                data: params
	            }).then(function(e, res) {
	                app.helper.hideProgress();
	                console.log("eee "+e);
	                if (!e) {

	                	if (res && res.record) {
							window.location.href = `index.php?module=Accounts&view=Detail&record=${res.record}&app=SUPPORT`;
	                	} else {
	                		app.helper.showErrorNotification({
		                        message: 'No se encontraron registros para la búsqueda realizada'
		                    });
	                	}
	                } else {
	                    app.helper.showErrorNotification({
	                        message: 'Error al realizar búsqueda'
	                    });
	                }
	            });
        	}//fin de if(error < 2)
        	else{
                app.helper.showErrorNotification({
                                message: "Debe buscar por un solo campo, intente quitar uno: " + mensaje
                            });
            }
		});
	},
    refreshWidget: function() {
		
    },
	postRefreshWidget : function() {
		this._super();
	},
});