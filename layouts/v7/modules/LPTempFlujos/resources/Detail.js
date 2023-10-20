Vtiger_Detail_Js("LPTempFlujos_Detail_Js",{
},{
	registerBasicEvents: function(container){
		this._super(container);
        app.event.on("post.relatedListLoad.click",() => {
			if (typeof cargar_grafo == 'function' && !!document.getElementById('cy'))
				cargar_grafo();
		});
		if (typeof cargar_grafo == 'function' && !!document.getElementById('cy'))
			cargar_grafo();
	},
});
