<?php

class LudereProLoader {
    /**
     * usando runkit_method_copy
     * funcion que permite agregar o reemplazxar una funcion desde una clas e a otra, por defecto desde esta clase
     */
    static function LPReplaceMethod($className, $methodName, $sourceClass="LudereProLoader") {
		if (extension_loaded('runkit') || extension_loaded('runkit7')){
			if (method_exists($className, $methodName)) 
				runkit_method_remove($className, $methodName);
			runkit_method_copy($className, $methodName, $sourceClass);
		}
    }

    static function LPReplaceAllFunctions($className) {
        $lpClassName = "LuderePro$className";
        $class = new ReflectionClass($lpClassName);
        $methods = $class->getMethods();
        foreach($methods as $mt) {
			// solo extender los metodos que estan en la clase, no los heredados, de eso se encarga la herencia misma
            if ($mt->class <> $lpClassName) continue; 
            $methodName =  $mt->getName();
            LudereProLoader::LPReplaceMethod($className,$methodName,$lpClassName);
        }
        // antes iba lo del comentario #0001, ahora se cambio porlo de arriba que es mas simple y funciona mejor   
    }
    // Vtiger_Loader
	static function autoLoad($className) {
		$parts = explode('_', $className);
		$noOfParts = count($parts);
		if($noOfParts > 2) {
			$filePath = 'modules.';
			$lpfilePath = 'modules.';
			// LP files
			$lpfiles = substr($parts[0], 0, strlen("LuderePro")) === "LuderePro";
			if($lpfiles) $parts[0]=substr($parts[0], strlen("LuderePro"), strlen($parts[0]));
			// Append modules and sub modules names to the path
			for($i=0; $i<($noOfParts-2); ++$i) {
				$filePath .= $parts[$i]. '.';
				$lpfilePath .= $parts[$i]. '.';
			}
			if ($lpfiles) $fileName = "LP_".$parts[$noOfParts-2];
			else {
				$fileName = $parts[$noOfParts-2];
				$lpfileComponentName = strtolower($parts[$noOfParts-1]).'s';
				$lpfilePath .= $lpfileComponentName. '.LP_' .$fileName;
			}
			// LP files
			$fileComponentName = strtolower($parts[$noOfParts-1]).'s';
			$filePath .= $fileComponentName. '.' .$fileName;
            $result = Vtiger_Loader::includeOnce($filePath);
            // Tratar de importar la clase de Ludere 
            if (!$lpfiles && !!$lpfilePath && !empty($lpfilePath)) {
                $file = Vtiger_Loader::resolveNameToPath($lpfilePath);
                if (file_exists($file)) {
                    $existeLP = Vtiger_Loader::includeOnce($lpfilePath);
                    if ($existeLP) LudereProLoader::LPReplaceAllFunctions($className);
                }
            }
            return $result;
		}
		return false;
	}
    // CRMEntity
	static function getInstance($module) {
		$modName = $module;
		if ($module == 'Calendar' || $module == 'Events') {
			$module = 'Calendar';
			$modName = 'Activity';
		}
		// File access security check
		if (!class_exists($modName)) {
			checkFileAccessForInclusion("modules/$module/$modName.php");
			require_once("modules/$module/$modName.php");
		}        
		// LP Files
		// tratar de incluir el archivo del modulo customizado si lo hay
		if (file_exists("modules/$module/LP_$modName.php")) {
            $lpModName = "LuderePro$modName";
			if (!class_exists($lpModName)) {
                require_once("modules/$module/LP_$modName.php");
                LudereProLoader::LPReplaceAllFunctions($modName);
            }
		}
		// el que $focus tenga otra clase va a incidir en otros lados por ejemplo el metodo getModuleName en VTEntityData.inc
		// LP Files		
		$focus = new $modName();
		$focus->moduleName = $module;
		$focus->column_fields = new TrackableObject();
		$focus->column_fields = getColumnFields($module);
		if (method_exists($focus, 'initialize')) $focus->initialize();
		return $focus;
	}
    // Vtiger_Controller
	static function LPCheckIfExistJS($completeFilePath, $jsFileName, $fileExtension="js") {
		$old=$jsFileName;
		$filesName = explode('.',$jsFileName);
		$simpleFilename = $filesName[sizeof($filesName) - 1];
		$luderejsFileName = "LP_$simpleFilename"; 
		if( ( $pos = strrpos( $completeFilePath , $simpleFilename ) ) !== false ) {
			$search_length  = strlen( $simpleFilename );
			$completeFilePath   = substr_replace( $completeFilePath , $luderejsFileName , $pos , $search_length );
		}

		if( ( $pos = strrpos( $jsFileName , $simpleFilename ) ) !== false ) {
			$search_length  = strlen( $simpleFilename );
			$jsFileName   = substr_replace( $jsFileName , $luderejsFileName , $pos , $search_length );
		}
		if(file_exists($completeFilePath)) {
			if (strpos($jsFileName, '~') === 0) {
				$filePath = ltrim(ltrim($jsFileName, '~'), '/');
				// if ~~ (reference is outside vtiger6 folder)
				if (substr_count($jsFileName, "~") == 2) {
					$filePath = "../" . $filePath;
				}
			} else {
				$filePath = str_replace('.','/', $jsFileName) . '.'.$fileExtension;
			}
			return array(
				"filename" => $jsFileName,
				"filepath" => $filePath,
			);
		} else {
			$fallBackFilePath = Vtiger_Loader::resolveNameToPath(Vtiger_JavaScript::getBaseJavaScriptPath().'/'.$jsFileName, 'js');
			if(file_exists($fallBackFilePath)) {
				$filePath = str_replace('.','/', $jsFileName) . '.js';
				return array(
					"filename" => $jsFileName,
					"filepath" => $filePath,
				);
			}
		}
		return null;
	}
    static function checkAndConvertJsScripts($jsFileNames) {
		$fileExtension = 'js';
		$jsScriptInstances = array();
		if($jsFileNames) {
			foreach($jsFileNames as $jsFileName) {
				// TODO Handle absolute inclusions (~/...) like in checkAndConvertCssStyles
				$jsScript = new Vtiger_JsScript_Model();
				// external javascript source file handling
				if(strpos($jsFileName, 'http://') === 0 || strpos($jsFileName, 'https://') === 0) {
					$jsScriptInstances[$jsFileName] = $jsScript->set('src', $jsFileName);
					continue;
				}
				$completeFilePath = Vtiger_Loader::resolveNameToPath($jsFileName, $fileExtension);
				if(file_exists($completeFilePath)) {
					if (strpos($jsFileName, '~') === 0) {
						$filePath = ltrim(ltrim($jsFileName, '~'), '/');
						// if ~~ (reference is outside vtiger6 folder)
						if (substr_count($jsFileName, "~") == 2) {
							$filePath = "../" . $filePath;
						}
					} else {
						$filePath = str_replace('.','/', $jsFileName) . '.'.$fileExtension;
					}
					$jsScriptInstances[$jsFileName] = $jsScript->set('src', $filePath);
					// agregado 
					$ludereFile = LudereProLoader::LPCheckIfExistJS($completeFilePath,$jsFileName);
					if (!!$ludereFile) {
						$luderejsScript = new Vtiger_JsScript_Model();
						$jsScriptInstances[$ludereFile['filename']] = $luderejsScript->set('src', Vtiger_JavaScript::getFilePath($ludereFile['filepath']));
					}
					// agregado 
				} else {
					$fallBackFilePath = Vtiger_Loader::resolveNameToPath(Vtiger_JavaScript::getBaseJavaScriptPath().'/'.$jsFileName, 'js');
					if(file_exists($fallBackFilePath)) {
						$filePath = str_replace('.','/', $jsFileName) . '.js';
						$jsScriptInstances[$jsFileName] = $jsScript->set('src', Vtiger_JavaScript::getFilePath($filePath));
						// agregado 
						$ludereFile = LudereProLoader::LPCheckIfExistJS($completeFilePath,$jsFileName);
						if (!!$ludereFile) {
							$luderejsScript = new Vtiger_JsScript_Model();
							$jsScriptInstances[$ludereFile['filename']] = $luderejsScript->set('src', Vtiger_JavaScript::getFilePath($ludereFile['filepath']));
						}
						// agregado 
					}
				}
			}
		}
		return $jsScriptInstances;
	}
    // Vtiger_Viewer
	public function getTemplatePath($templateName, $moduleName='') {
		// BUSCAR EL TEMPLATE QEU ARRANCA CON LP_
		$lpfile= $this->LPGetTemplatePath($templateName, $moduleName);
		if (!!$lpfile) return $lpfile;
		$moduleName = str_replace(':', '/', $moduleName);
		$completeFilePath = $this->getTemplateDir(0). DIRECTORY_SEPARATOR . "modules/$moduleName/$templateName";
		if(!empty($moduleName) && file_exists($completeFilePath)) {
			return "modules/$moduleName/$templateName";
		} else {
			// Fall back lookup on actual module, in case where parent module doesn't contain actual module within in (directory structure)
			if(strpos($moduleName, '/') > 0) {
				$moduleHierarchyParts = explode('/', $moduleName);
				$actualModuleName = $moduleHierarchyParts[count($moduleHierarchyParts)-1];
				$baseModuleName = $moduleHierarchyParts[0];
				$fallBackOrder = array (
					"$actualModuleName",
					"$baseModuleName/Vtiger"
				);

				foreach($fallBackOrder as $fallBackModuleName) {
					$intermediateFallBackFileName = 'modules/'. $fallBackModuleName .'/'.$templateName;
					// BUSCAR EL TEMPLATE QEU ARRANCA CON LP_
					$lpfile= $this->LPGetTemplatePath($templateName, $fallBackModuleName);
					if (!!$lpfile) return $lpfile;
					$intermediateFallBackFilePath = $this->getTemplateDir(0). DIRECTORY_SEPARATOR . $intermediateFallBackFileName;
					if(file_exists($intermediateFallBackFilePath)) {
						return $intermediateFallBackFileName;
					}
				}
			}
			// BUSCAR EL TEMPLATE QEU ARRANCA CON LP_
			$lpfile= $this->LPGetTemplatePath($templateName, "Vtiger");
			if (!!$lpfile) return $lpfile;
			return "modules/Vtiger/$templateName";
		}
	}
	public function LPGetTemplatePath($templateName, $moduleName='') {
		$moduleName = str_replace(':', '/', $moduleName);
		$completeFilePath = $this->getTemplateDir(0). DIRECTORY_SEPARATOR . "modules/$moduleName/LP_$templateName";
		if(!empty($moduleName) && file_exists($completeFilePath)) {
			return "modules/$moduleName/LP_$templateName";
		}
		return null;
	}

	// LanguageHandler
	/**
	 * buscar en losa rchivos customs tambien  las traducciones
	 */
	public static function getModuleStringsFromFile($language, $module='Vtiger'){
		$module = str_replace(':', '.', $module);
		if(empty(self::$languageContainer[$language][$module])){
			$qualifiedName = 'languages.'.$language.'.'.$module;
			$file = Vtiger_Loader::resolveNameToPath($qualifiedName);
			$languageStrings = $jsLanguageStrings = array();
			if(file_exists($file)){
				require $file;
				self::$languageContainer[$language][$module]['languageStrings'] = $languageStrings;
				self::$languageContainer[$language][$module]['jsLanguageStrings'] = $jsLanguageStrings;
			}
			// luego se buscan las traducciones del archivo LP_<module>, si existe
			$LPqualifiedName = 'languages.'.$language.'.LP_'.$module;
			$LPfile = Vtiger_Loader::resolveNameToPath($LPqualifiedName);
			if(file_exists($LPfile)){
				require $LPfile;
				foreach($languageStrings as $_key => $_t)
					self::$languageContainer[$language][$module]['languageStrings'][$_key] = $_t;
				foreach($jsLanguageStrings as $_key => $_t)
					self::$languageContainer[$language][$module]['jsLanguageStrings'][$_key] = $_t;
			}
		}
		$return = array();
		if(isset(self::$languageContainer[$language][$module])){
			$return = self::$languageContainer[$language][$module];
		}
		return $return;
	}
}