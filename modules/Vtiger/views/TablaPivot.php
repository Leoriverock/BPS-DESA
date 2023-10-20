<?php
// error_reporting(E_ERROR | E_WARNING | E_PARSE);
// ini_set("display_errors", 1);

class Vtiger_TablaPivot_View extends Vtiger_List_View
{
	public function requiresPermission(Vtiger_Request $request){
		if($request->has("module") && $request->get("module") == "Vtiger")
			$request->set("module","Analisis");
		$permissions[] = array('module_parameter' => 'module', 'action' => 'TablaPivot');
		return $permissions;
	}

	public function preProcess (Vtiger_Request $request, $display = true)
	{
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $request->getModule());

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('CURRENT_USER', $currentUserModel);
		$viewer->assign('TABLA_PIVOT_VIEW',true);

		parent::preProcess($request, false);

		if ($display)
		{
			$this->preProcessDisplay($request);
		}
	}

	public function getHeaderScripts (Vtiger_Request $request)
	{
		$headerScriptInstances = parent::getHeaderScripts($request);

		$jsFileNames = array(
			"~/libraries/pivottable/ext/jquery-ui-1.9.2.custom.min.js",
			"~/libraries/pivottable/dist/d3.min.js",
			"~/libraries/pivottable/dist/c3.min.js",
			"~/libraries/pivottable/dist/pivot.min.js",
			"~/libraries/pivottable/dist/c3_renderers.min.js",
			"~/libraries/pivottable/dist/d3_renderers.min.js",
			"~/libraries/pivottable/dist/pivot.es.min.js",
			'~/libraries/jquery/funciones.js',
			'modules.Vtiger.resources.filtros'	// JS con interacciones para filtros personalizados
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getHeaderCss (Vtiger_Request $request)
	{
		$headerCssInstances = parent::getHeaderCss($request);

		$cssFileNames = array(
			'~/libraries/fullcalendar/fullcalendar.css',
			'~/libraries/fullcalendar/fullcalendar-bootstrap.css',
			'~/libraries/pivottable/dist/pivot.min.css',
			'~/libraries/pivottable/dist/c3.min.css',
		);

		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);

		return $headerCssInstances;
	}

	public function process (Vtiger_Request $request)
	{
		// Modo
		$mode = strtolower($request->getMode());

		if ($mode == 'data')
		{
			return $this->getData($request);
		}

		// Asignar el usuario
		$viewer 			= $this->getViewer($request);
		$currentUserModel 	= Users_Record_Model::getCurrentUserModel();

		// Datos necesarios para la pivot
		$modulo = $request->getModule();
		
		$filtro = $request->get('viewname');
		if(!empty($viewName)) {
			$this->viewName = $viewName;
		}

		$finame = $request->get('finame');

		// Settings de la pivot
		$settings = $request->get('settings');
		$settings = empty($settings) ? array(array(), array()) : $settings;

		// Asignar variables al template
		$viewer->assign('CURRENT_USER', $currentUserModel);
		$viewer->assign('MODULO', $modulo);
		$viewer->assign('FILTRO', $filtro);
		$viewer->assign('FILTRONAME', $finame);
		$viewer->assign('SETTSPIVOT', json_encode($settings));

		// Abrir el template
		$viewer->view('TablaPivotView.tpl', $request->getModule());
	}

	public function getData (Vtiger_Request $request)
	{
		// Globales
		global $adb, $log;
		$log->debug("En la ricaaa");

		$resultado = array();

		$modulo = $request->get('modulo');
		$filtro = $request->get('filtro');
		$cview 	= !empty($filtro);

		$currentUserModel 	= Users_Record_Model::getCurrentUserModel();
		$moduleInstance 	= Vtiger_Module::getInstance($modulo);

		$querygen = new QueryGenerator($modulo, $currentUserModel);

		if ($cview)
		{
			$querygen->initForCustomViewById($filtro);
			$fields = $querygen->getCustomViewFields();
		} else
		{
			$fields = array_keys($querygen->getModuleFields());
			$querygen->setFields($fields);
		}

		// Ejecutar sentencia SQL
		$sql = $querygen->getQuery();
		$log->debug("Query a ejecutar:: ".$sql);
		$res = $adb->query($sql);
		$qty = $adb->num_rows($res);

		if ($qty > 0)
		{
			// Lista de campos a ignorar
			// Se agregan todos los campos obligatorios de Vtiger
			$blacklist 	= array('id', 'CreatedTime', 'ModifiedTime');

			// Variables auxiliares
			$nombres 	= array();
			$col_bd 	= array();
			$refs 		= array();
			$picklists 	= array();
			$fechas 	= array();

			// Resultado vacío
			$resultado 	= array(
				'cols' => null,
				'data' => array(),
				'fech' => array(),
			);

			// Obtener fields de referencia
			$refinfo = $querygen->getReferenceFieldInfoList();
			$reflist = $querygen->getReferenceFieldList();

			// Obtener datos de los fields de la vista
			foreach ($fields as $field)
			{
				$campo = Vtiger_Functions::getModuleFieldInfo($modulo, $field);
				$nombres[$field] = vtranslate($campo['fieldlabel'], $modulo);
				$col_bd[$field] =  $campo['columnname'];

				// Si es tipo fecha, indicarlo en fecha
				if (in_array($campo['uitype'], array('5', '6', '23', '70')))
				{
					$fechas[] = $nombres[$field];
				}

				// Si es del tipo picklist, ponerlo para traducir
				if (in_array($campo['uitype'], array('15', '16', '33')))
				{
					$picklists[] = $campo['fieldname'];
				}

				if (in_array($field, $reflist) && !isset($refs[$field])) $refs[$field] = array();
			}

			$resultado['cols'] = $nombres;
			$resultado['fech'] = $fechas;

			while ($fila = $adb->fetch_array($res))
			{
				$linea = array();

				foreach ($fields as $field)
				{
					if (!in_array($field, $blacklist))
					{
						$nombre = $nombres[$field];
						$linea[$nombre] = $fila[$col_bd[$field]];

						// Si es de un picklist, traducir el contenido
						if (in_array($field, $picklists))
						{
							$linea[$nombre] = vtranslate($linea[$nombre], $modulo);
						}

						// Si es un campo 'assigned_user_id', buscar el nombre del usuario
						if ($field == 'assigned_user_id') $linea[$nombre] = $this->getUsuario($linea[$nombre]);

						// Si es una fecha, procesarla
						if (in_array($nombre, $fechas))
						{
							$tokens = explode(' ', $linea[$nombre]);
							$linea[$nombre] = $tokens[0];
						}

						// Si es un campo relacionado, agregar id a la lista
						if (isset($refs[$field])) $refs[$field][$fila[$col_bd[$field]]] = $fila[$col_bd[$field]];
					}
				}

				$resultado['data'][] = $linea;
			}

			// Procesamiento de labels de campos relacionados
			$refsfinal = array();

			foreach ($refs as $campo => $campos)
			{
				$modulosCampos = $refinfo[$campo];
				$listaNombres = null;

				foreach ($modulosCampos as $moduloCampo)
				{
					if ($moduloCampo == 'Users')
					{
						$listaNombres = getOwnerNameList($campos);
					} else
					{
						$listaNombres = getEntityName($moduloCampo, $campos);
					}
				}

				$refsfinal[$campo] = $listaNombres;
			}

			// Si la lista refsfinal no está vacía, cambiar los ids contenidos en los campos relevantes por los labels obtenidos
			if (!empty($refsfinal))
			{
				foreach ($refsfinal as $campo => $valores)
				{
					$cantidad = count($resultado['data']);

					for ($i = 0; $i < $cantidad; $i++)
					{
						$nombre = $nombres[$campo];
						$resultado['data'][$i][$nombre] = $valores[$resultado['data'][$i][$nombre]];
					}
				}
			}
		}
		$log->debug("out la ricaaa");
		echo json_encode($resultado);
	}

	public function getUsuario ($id)
	{
		// Globales
		global $adb;

		$res = $adb->pquery("SELECT CONCAT(first_name, ' ', last_name) AS nombre FROM vtiger_users WHERE id = ?", array($id));

		return trim($adb->query_result($res, 0, 'nombre'));
	}
}
