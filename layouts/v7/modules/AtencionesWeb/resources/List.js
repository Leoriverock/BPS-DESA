/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("AtencionesWeb_List_Js", {
	cargarModal : function() {
				var progressIndicatorElement = jQuery.progressIndicator();
		 		var params = {};
		        params['module'] = 'AtencionesWeb';
		        params['view'] = 'CargarModalConsultas';
		        AppConnector.request(params).then(
		            function(data) {
		            	
		                var callBackFunction = function(data) {
		                }
		                app.showModalWindow(data,function(data){
		                    if(typeof callBackFunction == 'function'){
		                        callBackFunction(data);
		                        console.log("Entrando");
		                    }
		                }, false);
		                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
		            },
		            function(error) {
		                console.log("error");
		                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
		            	}
		        	);
					

				},
			},{
				registerRowDoubleClickEvent: function () {
					console.log("LLego a registerRowDoubleClickEvent ");
					var thisInstance = this;
					var listViewContentDiv = this.getListViewContainer();
			
					// Double click event - ajax edit
					listViewContentDiv.on('dblclick', '.listViewEntries', function (e) {
						if (listViewContentDiv.find('#isExcelEditSupported').val() == 'no') {
							return;
						}
			
						var currentTrElement = jQuery(e.currentTarget);
						// added to unset the time out set for <a> tags 
						var rows = currentTrElement.find('a');
						rows.each(function (i, elem) {
							if (jQuery(elem).data('timer')) {
								clearTimeout(jQuery(elem).data('timer'));
								jQuery(elem).data('timer', null);
							}
							;
						});
						var editedLength = jQuery('.listViewEntries.edited').length;
						if (editedLength === 0) {
							var currentTrElement = jQuery(e.currentTarget);
							var target = jQuery(e.target, jQuery(e.currentTarget));
							if (target.closest('td').is('td:first-child'))
								return;
							if (target.closest('tr').hasClass('edited'))
								return;
							//thisInstance.registerInlineEdit(currentTrElement);
						}
					});
				},

				registerEvents: function () {
					var thisInstance = this;
					this._super();
					this.registerRowDoubleClickEvent();
					//recordSelectTrackerObj.registerEvents();
				},
})