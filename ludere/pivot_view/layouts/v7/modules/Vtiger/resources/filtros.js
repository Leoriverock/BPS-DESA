var filtros = [];
window.onload = function(){

	var viewname = document.getElementsByName('cvid').length? document.getElementsByName('cvid')[0].value : "";
	var module 	 = _META.module;
	getFiltros();
	document.getElementById("abrirModalFiltro").addEventListener('click',function(){
		$("#modalFiltro").modal();
	})

	document.getElementById("guardarFiltro").addEventListener('click',function(){		
		if(!!filtros[document.getElementById('nombreFiltro').value]){
			Vtiger_Helper_Js.showConfirmationBox({message:"Ya existe un filtro con este nombre, desea sobreescribirlo"}).then(
				function(e) {
					guardarFiltro();
				},
				function(error, err) {
					
				}
			);
		}else{
			guardarFiltro();
		}
	})
	

	document.getElementById("selectFiltros").addEventListener("change",function(){
		var filtro = filtros[this.value];
		if(filtro){
			vaciarSelecciones();
			var params = {cols:filtro.columnas,rows:filtro.filas};
			for(let key in filtro.filtros){
				if( key == "fecha" ){
					document.getElementById("createdtime").value = filtro.filtros[key];
				}else{
					!!document.getElementById(key+"Select") &&  (document.getElementById(key+"Select").value = filtro.filtros[key] )
				}
			}
			//actualizar();
			graficar(params);
		}
	})

	function vaciarSelecciones(){
		let filters = document.getElementsByClassName("filterParam");
		for(let i = 0; i < filters.length; filters[i++].value = "0");
	}

	function guardarFiltro(){
		var datos = getInfo();
		var params = {
			module : 'Analisis',
			action : 'Filtros',
			mode: 'create',
			datos : datos
		}
		AppConnector.request(params).then(function(response){
			if(typeof response == 'string') 
				response = JSON.parse(response);
			
			if(response.success){
				Vtiger_Helper_Js.showMessage({text:"Filtro creado correctamente"});
				agregarFiltro(response.result);
			}else{
				Vtiger_Helper_Js.showPnotify("Error al guardar el filtro");
			}
		});
	}

	function getFiltros(){
		var tabla = document.getElementById('nombreTabla')? document.getElementById('nombreTabla').value : "";		
		filtros = [];
		var params = {
			module : 'Analisis',
			action : 'Filtros',
			mode : 'getdata',
			tabla : tabla,
			modulename : module,
			viewname : viewname
		}
		AppConnector.request(params).then(function(response){
			if(typeof response == 'string') 
				response = JSON.parse(response);
			console.log(response)
			if(response.success){

				for(var i = 0;i<response.result.length; agregarFiltro(response.result[i++]));
			}
		})
	}

	function agregarFiltro(filtro){
		var existe = filtros[filtro.nombre];
		var select = document.getElementById('selectFiltros');
		var option = document.createElement('option');
		option.value = option.textContent = filtro.nombre;				
		filtros[filtro.nombre] = filtro;
		if(!existe) select.appendChild(option);

	}

	//Funcion que retorna los parametros actuales de filtro
	function getInfo(){
		var info = {nombre:"",filas:[],columnas:[],filtros:{},tabla:"",module:module,viewname:viewname}; //Agregar aggregator (vals), aggregatorname, tabla
		var nombre = document.getElementById('nombreFiltro').value;
		
		var rows = $(".pvtRows .pvtAttr");
		for(var x=0 ; x<rows.length ; info.filas.push(	getNombre(rows[x++]	)	));
		var cols = $(".pvtCols .pvtAttr");
		for(var x=0 ; x<cols.length ; info.columnas.push(	getNombre(cols[x++])	));
		var tabla = document.getElementById('nombreTabla')? document.getElementById('nombreTabla').value : "";

		
		info.nombre = nombre;

		info.tabla = tabla + "-" + viewname;

		// 07/03
		let filters = document.getElementsByClassName('filterParam');
		let filter_name  = "";
		let filter_value = "";
		for(let i = 0; i < filters.length; i++){
			info.filtros[filters[i].dataset.target] = filters[i].value;
		}
		// 07/03		


		var aggregator = $(".pvtAggregator").val();
		var aggregatorVal = $(".pvtAttrDropdown").val();
		console.log(info);
		return info;
	}
	function getNombre(span){
		var ar = span.textContent.split(" ");
		ar.pop();
		return ar.join(" ");
	}
	
}

	//Que hacer cuando se seleciona el filtro
	function seleccionarFiltro(){
		var select = document.getElementById('selectFiltros').value;	
		if(select!=""){
			return !!filtros[select]? filtros[select] : "";
		}	
	}