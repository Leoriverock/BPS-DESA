<?php

define('URL_WS_PERSONAS', 'http://rperwst.bps.net:8501/RegistrosCorporativos/Persona/WSPersona');

//define('URL_WS_PERSONAS', 'http://192.168.217.40:8501/RegistrosCorporativos/Persona/WSPersona');
define('URL_WS_PERSONAS', 'http://rperwst.bps.net:8501/RegistrosCorporativos/Persona/WSPersona');


define('URL_WS_CONTRIBUYENTES', 'http://proxyserv.bps.net/UtilitariosWsRouter/WsRegUtilitariosService');
define('URL_WS_BUSQUEDA_EMPRESAS', 'http://proxyservt.bps.net/Registros/Empresas/V003/WsRegEmpresasService');
define('URL_WS_EMPRESAS', 'http://proxyservt.bps.net/RegEmpWsRouter/services/WsRegECA');
define('URL_ESCRITORIO_CIUDADANO', 'http://esctrabintt:8080/EscritorioTrabajador/EscritorioTrabajadorInterno.xhtml');
//para habilitar/deshabilitar los logs de los ws
define('LOGS_WS_ENABLED', true);
define('EMAIL_FROM', 'vtigert@sca.bps.net');
define('EMAIL_TEMPLATE_ID', 17);
define("LISTVIEW_CONSULTASWEB_ROLES", serialize( array('H7') ) ); 	//ROLES QUE NO VEN LA LISTVIEW DE CONTACTS
define("LISTVIEW_ATENCIONESWEB_ROLES", serialize( array('H6','H7') ) ); 
define("CORREO_SALIDA_RESPUESTA",'vtigertest@bps.gub.uy');
define("NAME_CORREO_SALIDA_RESPUESTA",'vtigertest');
define("EJECUTIVOS_MAILS",'h7');
define('FONTAWESOME_URL', 'libraries/fontawesome');

define('URL_PORTADOCUMENTOS', 'http://intellikonnet:81/INET/Catalog');