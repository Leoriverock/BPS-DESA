<?php

set_time_limit(0); 

error_reporting(E_ERROR | E_PARSE);

$BASE_PATH 		= __DIR__;

$DEST_PATH 		= __DIR__;

$DEST_PATH 		= implode(DIRECTORY_SEPARATOR, explode(DIRECTORY_SEPARATOR, $DEST_PATH,-2) ); //Voy dos carpetas mas atras para llegar al crm


$crmName = array_pop(explode(DIRECTORY_SEPARATOR,$DEST_PATH));

$DB_BACKUP_PATH = $BASE_PATH.DIRECTORY_SEPARATOR."base.min.sql";

/**
 * 
 * Archivo de inicializacion de CRM
 * Crea config nose que pimba coso
 * **/

echo "-----------------------------------------------------------".PHP_EOL;
echo "-------------------- CRM Installer ------------------------".PHP_EOL;
echo "---------------------- LuderePro --------------------------".PHP_EOL;
echo "-----------------------------------------------------------".PHP_EOL;

$configParams = array(
	"root_directory"	=> $DEST_PATH."/", // Este lo tomamos
	"db_hostname"		=> "",
	"db_username"		=> "",
	"db_password"		=> "",
	"db_name"			=> "",
	"db_type"			=> "",
	"site_URL"			=> "",
	"admin_email"		=> "",
	"currency_name"		=> "",
	"vt_charset"		=> "",
	"default_language"	=> "",


);

$defaultDBName 	= strtolower($crmName);
$defaultHost 	= "http://localhost/".$crmName;
$defaultCurrency= 'USA, Dollars';



echo "Inicializando CRM ".$crmName."...".PHP_EOL;
echo "Creando archivo de instalacion: ".PHP_EOL;

$isdefault = false;
if(count($argv) > 1 && $argv[1] == "--default"){
    echo "Inicializando archivo de config con parametros por defecto...".PHP_EOL;
    $isdefault      = true;
    $configParams   = array(
        "root_directory"    => $DEST_PATH."/", // Este lo tomamos
        "db_hostname"       => "localhost",
        "db_username"       => "root",
        "db_password"       => "",
        "db_name"           => $defaultDBName,
        "db_type"           => "mysqli",
        "site_URL"          => $defaultHost,
        "admin_email"       => "contacto@luderepro.com",
        "currency_name"     => $defaultCurrency
    );
}
else{
    echo "Ingrese host para base de datos (localhost): ";
    $param = getInput();
    $configParams["db_hostname"] = $param == ""? "localhost" : $param;

    echo "Ingrese usuario para base de datos (root): ";
    $param = getInput();
    $configParams["db_username"] = $param == ""? "root" : $param;

    echo "Ingrese contrasena para base de datos (): ";
    $param = getInput();
    $configParams["db_password"] = $param;

    echo "Ingrese nombre de base de datos (".$defaultDBName."): ";
    $param = getInput();
    $configParams["db_name"] = $param == ""? $defaultDBName : $param;

    echo "Ingrese tipo de base de datos (mysqli): ";
    $param = getInput();
    $configParams["db_type"] = $param == ""? "mysqli" : $param;

    echo "Ingrese site_URL (".$defaultHost."): ";
    $param = getInput();
    $configParams["site_URL"] = $param == ""? $defaultHost : $param;

    echo "Ingrese admin_email (): ";
    $param = getInput();
    $configParams["admin_email"] = $param;

    echo "Ingrese currency_name (".$defaultCurrency."): ";
    $param = getInput();
    $configParams["currency_name"] = $param == ""? $defaultCurrency : $param;
}


// Copiamos el archivo de config.template.php
copy($DEST_PATH.DIRECTORY_SEPARATOR."config.template.php","config.template.php");

require_once($DEST_PATH.DIRECTORY_SEPARATOR."modules/Install/models/Utils.php");
require_once($DEST_PATH.DIRECTORY_SEPARATOR."modules/Install/models/ConfigFileUtils.php");


// Creando archivo de configuracion
$configFile = new Install_ConfigFileUtils_Model($configParams);
$configFile->createConfigFile();


// Verificamos que se haya creado el archivo de config
if(file_exists("config.inc.php")){
    echo "Archivo creado satisfactoriamente".PHP_EOL;
    if(copy("config.inc.php",$DEST_PATH.DIRECTORY_SEPARATOR."config.inc.php")){
        echo "Archivo copiado en carpeta base correctamente".PHP_EOL;
    }else{
        echo "ERROR! No se pudo copiar el archivo a carpeta base".PHP_EOL;
    }
}else{
    echo "ERROR! No se creo el archivo de configuracion".PHP_EOL;
}


echo "Cambiado a directorio: ".$DEST_PATH.PHP_EOL;
chdir($DEST_PATH);


echo "Desea cargar el archivo de base de datos? (si/no): ";
$param = getInput();
if($param == "si"){ 

    require_once('include/utils/utils.php');
    require_once('include/logging.php');
    require_once('include/database/PearDatabase.php');

    $db_type        = function_exists('mysqli_connect')?'mysqli':'mysql';
    $db_hostname    = $configParams["db_hostname"];
    $root_user      = $configParams["db_username"];
    $root_password  = $configParams["db_password"];
    $db_name        = $configParams["db_name"];
    // Creamos la pasta base de datos
    $dropdb_conn = NewADOConnection($db_type);
    if(@$dropdb_conn->Connect($db_hostname, $root_user, $root_password, $db_name)) {
        echo "Ya existe la base de datos".PHP_EOL;
        echo "Desea volver a cargarla? (si/no): ";
        $param = getInput();
        if($param == "si"){
            $query = "DROP DATABASE ".$db_name;
            $dropdb_conn->Execute($query);
            $dropdb_conn->Close();
        }
    }

    $createdb_conn = NewADOConnection($db_type);
    if(@$createdb_conn->Connect($db_hostname, $root_user, $root_password)) {
        echo "Creando base de datos: ".PHP_EOL;
        $query = "CREATE DATABASE ".$db_name;
        if(true) {
            if(stripos($db_type ,'mysql') === 0)
                $query .= " DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci";
            $db_utf8_support = true;
        }
        if($createdb_conn->Execute($query)) {
            $db_creation_failed = false;
            echo "Base de datos creada correctamente! ".PHP_EOL;
        }
        $createdb_conn->Close();
    }
    global $adb;

    $adb->resetSettings(
        $configParams['db_type'], 
        $configParams['db_hostname'], 
        $configParams['db_name'], 
        $configParams['db_username'], 
        $configParams['db_password']
    );
    
    echo "Cargando respaldo de base de datos desde ".$DB_BACKUP_PATH."...".PHP_EOL;     

    //$data = file_get_contents($DB_BACKUP_PATH);
    loadDB($DB_BACKUP_PATH,$adb);
    //$queries = explode(PHP_EOL,$backup);
    /*echo "Cantidad de queries a ejecutar: ".count($queries);
    foreach($queries as $q){
        if($q != ""){
            $adb->query($q);
        }
    } */
    $adb->query($backup);  
}


if(!file_exists("tabdata.php")){
    echo "Creando archivo: tabdata.php".PHP_EOL;
    touch("tabdata.php");
}

if(!file_exists("parent_tabdata.php")){
    echo "Creando archivo: parent_tabdata.php".PHP_EOL;
    touch("parent_tabdata.php");
}

if(!is_dir("user_privileges")){
    echo "Creando carpeta: user_privileges".PHP_EOL;
    mkdir("user_privileges");
}

require_once('include/utils/CommonUtils.php');
require_once('include/utils/UserInfoUtil.php');

echo "Creando archivo tabdata...".PHP_EOL;
create_tab_data_file();
echo "Creando archivo parent_tabdata...".PHP_EOL;
create_parenttab_data_file();

echo "Creando sharing rules...".PHP_EOL;
RecalculateSharingRules();

echo "Instalacion de CRM realizada correctamente!".PHP_EOL;
echo "OK".PHP_EOL;


function getInput(){
	$handle = fopen ("php://stdin","r");
	$input 	= trim(fgets($handle));
	fclose($handle);
	return trim($input);
}

/**
 * Funcion para cargar base de datos a partir de un archivo de backup
 * */
function loadDB($path,$adb){
    $data       = file_get_contents($path);


    // Obtenemos todas las querys a ejecutar
    $exploded   = explode(";".PHP_EOL,$data);

    // Obtenemos las tablas a insertar
    $tables     = explode("CREATE TABLE `",$data);
    $tablis     = array();

    foreach($tables as $tq){
        $tname = explode("`",$tq)[0];
        $query = trim(explode(";",$tq)[0]);
        if($tname != "")
            $tablis[$tname] = "CREATE TABLE `".$query.";";
    }

    echo "Cantidad de tablas: ".count($tables).PHP_EOL;

    $reRun = array();

    echo "Cantidad de queries a ejecutar: ".count($exploded).PHP_EOL;

    foreach($exploded as $q){
        $r = $adb->query($q);
        if(!$r){
            $reRun[] = $q;
        }
    }

    echo "Cantidad de queries a volver a ejecutar: ".count($reRun).PHP_EOL;
    foreach($reRun as $q){
        $r = $adb->query($q);
    }

    echo "verificamos las tablas: ".PHP_EOL;
    $tablasNoCreadas = array();
    foreach($tablis as $tablename => $q){
        if($adb->num_rows($adb->query("SHOW TABLES LIKE '".$tablename."'")) == 0){
            echo "Tabla no creada: ".$tablename;
            $tablasNoCreadas[$tablename] = array(
                "create" => $q
            );
            $insertPrefix = "insert  into `".$tablename."`";
            $insertSearch = explode($insertPrefix,$data);
            if(count($insertSearch) > 1){
                echo " - tiene tuplas";
                $tablasNoCreadas[$tablename]["insert"] = $insertPrefix.explode(";",$insertSearch[1])[0].";";
            }
            echo PHP_EOL;
        }
    }
    echo "Cantidad de tablas no creadas: ".count($tablasNoCreadas).PHP_EOL;

    echo "Cargamos tablas no creadas: ".PHP_EOL;
    foreach($tablasNoCreadas as $tablename => $d){
        $adb->query($d["create"]);
        if(isset($d["insert"])){
            $adb->query($d["insert"]);
        }
    }

    echo "Volvemos a verificar las tablas: ".PHP_EOL;
    $tablasNoCreadas = array();
    foreach($tablis as $tablename => $q){
        if($adb->num_rows($adb->query("SHOW TABLES LIKE '".$tablename."'")) == 0){
            echo "Tabla no creada: ".$tablename.PHP_EOL;
        }
    }
    if(count($tablasNoCreadas) == 0){
        echo "Todas las tablas fueron creadas correctamente".PHP_EOL;
    }
}


?> 