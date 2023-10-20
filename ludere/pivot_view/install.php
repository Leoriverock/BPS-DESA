<?php


$FILENAME 		= str_replace(__DIR__.DIRECTORY_SEPARATOR,"", __FILE__);

$README 		= str_replace(__DIR__.DIRECTORY_SEPARATOR,"", "README.md");

$BASE_PATH 		= __DIR__;

$DEST_PATH 		= __DIR__;

$DEST_PATH 		= implode(DIRECTORY_SEPARATOR, explode(DIRECTORY_SEPARATOR, $DEST_PATH,-2) ); //Voy dos carpetas mas atras para llegar al crm

$PLUGIN_NAME 	= 'Pivot View';

$VERSION  	 	= '1.0';

$CREDITS 	 	= 'KMaidana';

$LAST_UPDATE 	= '2021/07/28'; 

$VTLIBFOLDER    = "vtlibs";


$_ = "-----------------------------------------------------------";
$len = strlen($_);  
$text = " PLUGIN ".$PLUGIN_NAME." ";
$dots = $len - strlen($text);
$__ = "";
$___ = "";
for($x = 0;$x<$dots/2;$x++,$__ .= "-");

if($dots % 2 != 0) 
	$___ = substr($__,1);
else
	$___ = $__;

echo "-----------------------------------------------------------".PHP_EOL;
echo $___.$text.$__.PHP_EOL;
echo "-----------------------------------------------------------".PHP_EOL;

echo "Script de instalacion de plugin ".$PLUGIN_NAME.PHP_EOL;
echo "Version: ".$VERSION.PHP_EOL;
if($CREDITS) 		echo "Creado por: ".$CREDITS.PHP_EOL;	
if($LAST_UPDATE) 	echo "Ultima actualizacion: ".$LAST_UPDATE.PHP_EOL;

$arguments = parseArgs($argv);


if(isset($arguments["help"]) && $arguments["help"] === true){
	print_help();
	exit();
}


if(isset($arguments["check"]) && $arguments["check"] === true){
	// Do module check
	// Primero que nada verificamos que esten todos los archivos ya instalados en el repositorio
	echo "Iniciando verificacion...".PHP_EOL;
	echo "Scaneando directorio: ".$BASE_PATH.PHP_EOL;
	$scaned_dir = array_slice(scandir($BASE_PATH), 2);
	//Elimino el propio archivo del escaneo
	$key = array_search($FILENAME, $scaned_dir);
	unset($scaned_dir[$key]);

	$ret = check_files($scaned_dir,"");
	if(count($ret)){
		echo PHP_EOL."ATENCION! ".PHP_EOL;
		echo "Faltan copiar archivos: ".PHP_EOL;
		foreach($ret as $r){
			echo $r.PHP_EOL;
		}
		echo "Sugerimos volver a ejecutar la instalacion".PHP_EOL;
		exit();
	}else{
		echo PHP_EOL."Todos los archivos estan correctamente copiados".PHP_EOL;
	}
	echo PHP_EOL;

	// Luego verificamos si los modulos han sido instalados
	// Esto lo hacemos recorriendo la carpeta modules y verificando por cada carpeta el modulo correspondiente
	echo "Verificando modulos...".PHP_EOL;
	chdir($DEST_PATH); // Nos pasamos al directorio base del CRM
	error_reporting(E_ALL);
	require_once('include/database/PearDatabase.php');
	$adb = PearDatabase::getInstance();

	$modulesDir = $BASE_PATH.DIRECTORY_SEPARATOR."modules";
	$scaned_dir = array_slice(scandir($modulesDir), 2);
	foreach($scaned_dir as $obj){
		$obj_path = $DEST_PATH.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$obj;
		if(is_dir($obj_path)){
			$filename = "modules".DIRECTORY_SEPARATOR.$obj.DIRECTORY_SEPARATOR.$obj.".php";
			echo "Modulo: ".$obj." - ";
			if(is_file($filename)){
				$table = "vtiger_".strtolower($obj);
				echo "Tabla: ".$table;
				$rs = $adb->query("SHOW TABLES LIKE '".$table."'");
				if(empty($rs) || $adb->num_rows($rs) === 0){
					echo " - Error: Tabla no existe! ".PHP_EOL;
				}else{	
					echo " - OK".PHP_EOL;
				}

			}else{
				"ERROR! No existe archivo del modulo".PHP_EOL;
			}
		}
	}
	// TODO: Realizar verificacion por posibles tablas creadas por fuera de los modulos (por ejemplo vistas)
	exit();
}


if(isset($arguments["install"]) && $arguments["install"] === true){
	// Realizamos todo lo que ya tenemos en el script

	echo "Iniciando instalacion...".PHP_EOL;

	echo "Scaneando directorio: ".$BASE_PATH.PHP_EOL;
	$scaned_dir = array_slice(scandir($BASE_PATH), 2);

	//Elimino el propio archivo del escaneo
	$key = array_search($FILENAME, $scaned_dir);
	unset($scaned_dir[$key]);

	copy_files($scaned_dir,'');

	// Agregamos la ejecuccion de vtlib si --vtlib=on o --vtlib=true
	//
	//
	if(!isset($arguments["novtlib"]) || $arguments["novtlib"] !== true){
		echo "Ejecutando Vtlibs...".PHP_EOL;
		echo $DEST_PATH.DIRECTORY_SEPARATOR."modules".PHP_EOL;
		$vtlibs = getVtlibList($BASE_PATH.DIRECTORY_SEPARATOR."modules",$DEST_PATH.DIRECTORY_SEPARATOR."modules");
		// Cambiamos al directorio base
		echo "Current directory: ".getcwd().PHP_EOL;
		$CD = getcwd();
		chdir($DEST_PATH);
		foreach($vtlibs as $vt){
			echo "Ejecutando vtlib: ".$vt.PHP_EOL;
			run_vtlib($vt);
		}
		echo "Current directory: ".getcwd().PHP_EOL;
		chdir($CD);


	}                                               
	// Aca printeamos todo los pasos "Manuales"
	echo "Fin de instalacion...".PHP_EOL;

	if(file_exists($README)){
		echo "Archivo de Read me: ".PHP_EOL.PHP_EOL;
		echo file_get_contents($README);
		echo PHP_EOL.PHP_EOL;
	}


	echo "Modulos instalados correctamente".PHP_EOL;
	exit();
}

echo "Faltan argumentos..".PHP_EOL;
echo "Intente con php install.php --help".PHP_EOL;
exit();



function getVtlibList($pluginModulesDir,$modulesDir){
	global $BASE_PATH,$DEST_PATH,$VTLIBFOLDER;	
	$scaned_dir = array_slice(scandir($pluginModulesDir), 2);
	$vtlibs = array();
	foreach($scaned_dir as $obj){
		echo $pluginModulesDir.DIRECTORY_SEPARATOR.$obj.PHP_EOL; 
		$obj_path = $modulesDir.DIRECTORY_SEPARATOR.$obj.DIRECTORY_SEPARATOR.$VTLIBFOLDER;
		if(is_dir($obj_path)){
			echo "Analizando modulo: ".$obj.PHP_EOL;
			$vtlib_scan = array_slice(scandir($obj_path), 2);
			foreach($vtlib_scan as $fn){
				$file = $obj_path.DIRECTORY_SEPARATOR.$fn;
				echo "Archivo: ".$file.PHP_EOL;
				if(is_file($file)){
					//echo "Ejecutando vtlib: ".$fn.PHP_EOL;
					$vtlibs[] = $file;//run_vtlib($file);
				}
			}
		}else{
			if(is_dir($modulesDir.DIRECTORY_SEPARATOR.$obj)){
				echo "Modulo ".$obj." sin vtlibs para ejecutar".PHP_EOL;
			}else{
				echo "Modulo ".$obj." no instalado".PHP_EOL;
			}
		}
	}
	return $vtlibs;
}

function run_vtlib($file){
	$_ = "php ".$file;
	echo "Running vtlib: ".$_.PHP_EOL;
	exec($_);
}

function copy_files($dir,$path){
	global $BASE_PATH,$DEST_PATH;
	foreach($dir as $obj){
		$obj_path = $BASE_PATH.DIRECTORY_SEPARATOR.($path? $path.DIRECTORY_SEPARATOR : '').$obj;
		if(is_file($obj_path)){
			$dest_path = $DEST_PATH.DIRECTORY_SEPARATOR.($path? $path.DIRECTORY_SEPARATOR : '').$obj;
			echo "Copiando archivo desde: ".$obj_path." a: ".$dest_path.PHP_EOL;
			//Si existe el archivo hacer un diff
			$result = copy($obj_path,$dest_path);
			echo ($result? "Archivo copiado satisfactoriamente" : "Error al copiar el archivo").PHP_EOL; 
		}
		if(is_dir($obj_path)){
			$dest_path = $DEST_PATH.DIRECTORY_SEPARATOR.($path? $path.DIRECTORY_SEPARATOR : '').$obj;

			if( !file_exists($dest_path) ){ //Checkeo que exista en el crm
				echo "Creando el directorio: ".$dest_path.PHP_EOL; 
				$result = mkdir($dest_path); 

			}
			 
			echo "Scaneando directorio: ".$obj_path.PHP_EOL;
			$scaned_dir = array_slice(scandir($obj_path), 2);
			copy_files($scaned_dir,($path? $path.DIRECTORY_SEPARATOR : '').$obj);
		}
	}

}


function check_files($dir,$path){
	global $BASE_PATH,$DEST_PATH;
	$ret = array();
	foreach($dir as $obj){
		$obj_path = $BASE_PATH.DIRECTORY_SEPARATOR.($path? $path.DIRECTORY_SEPARATOR : '').$obj;
		if(is_file($obj_path)){
			$dest_path = $DEST_PATH.DIRECTORY_SEPARATOR.($path? $path.DIRECTORY_SEPARATOR : '').$obj;
			echo "Verificando archivo: ".$dest_path;
			//Si existe el archivo hacer un diff
			if(!file_exists($dest_path)){
				echo " - Error:Archivo faltante!".PHP_EOL;
				$ret[] = $dest_path;
			}else{
				echo " - OK".PHP_EOL;
			}
		}
		if(is_dir($obj_path)){
			$dest_path = $DEST_PATH.DIRECTORY_SEPARATOR.($path? $path.DIRECTORY_SEPARATOR : '').$obj;
			echo "Verificando directorio: ".$dest_path;
			if( !file_exists($dest_path) ){ //Checkeo que exista en el crm
				echo PHP_EOL." - Error: Directorio no existe!".PHP_EOL;
				$ret [] = $dest_path;
			}else{ 			 
				echo " - OK".PHP_EOL."Scaneando directorio: ".$obj_path.PHP_EOL;
				$scaned_dir = array_slice(scandir($obj_path), 2);
				$ret = array_merge($ret,check_files($scaned_dir,($path? $path.DIRECTORY_SEPARATOR : '').$obj));
			}
		}
	}
	return $ret;
}

function parseArgs($arguments){
	$params = array(
		"help"			=> "",
		"check"			=> "",
		"install"		=> "",
		"run"			=> "",
		"novtlib"		=> ""
	);
	foreach ($arguments as $parameter) {
		if(strpos($parameter, "--") === 0){
			$parameter = substr($parameter, 2);
			$split 	= explode("=", $parameter);
			if(count($split) > 1){
				$key 	= $split[0];
				$value  = $split[1];
				if(isset($params[$key]) && is_bool($params[$key])){
					$value = $value == "true" || $value == 1;
				}
				$params[$key] = $value;
			}else{
				$key 	= $split[0];
				$params[$key] = true;
			}
		}
	}
	return $params;
}


function print_help(){
	echo "Usage: php install.php [OPTION]...".PHP_EOL;
	echo "Argumentos posibles:".PHP_EOL;
	echo "--help: Presenta la información de los posibles comandos a utilizar".PHP_EOL;
	echo "--check: Verifica que este instalado correctamente. Se chequean los archivos necesarios y las tablas e base de datos".PHP_EOL;
	echo "--install: Copia todos los archivos de los modulos".PHP_EOL;
	echo "--novtlib: Indica que no se ejecuten los vtlibs al momento de instalar".PHP_EOL;
}

?>
