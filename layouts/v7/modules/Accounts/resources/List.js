Vtiger_List_Js("Accounts_List_Js", {}, {
	registerAddUserBtnClick :function() {
		jQuery('#Accounts_listView_basicAction_LBL_ADD_RECORD').removeAttr('onclick');
		jQuery('#Accounts_listView_basicAction_LBL_ADD_RECORD').on('click', function(e){
			e.preventDefault();
			let url = 'index.php?module=Accounts&view=Edit&app=INVENTORY';
			jQuery('.searchRow input.inputElement').each((i, e) => {
				if (jQuery(e).val()) {
					url += `&${jQuery(e).attr('name')}=${encodeURI(jQuery(e).val())}`;
				}
			});
			window.location = url;
		});
	},
	registerEvents: function() {
		this._super();
		this.registerAddUserBtnClick();
	}
});