<?php

define('URL_WS_PERSONAS', 'http://rperwst.bps.net:8501/RegistrosCorporativos/Persona/WSPersona');
define('URL_WS_CONTRIBUYENTES', 'http://proxyserv.bps.net/UtilitariosWsRouter/WsRegUtilitariosService');
define('URL_WS_BUSQUEDA_EMPRESAS', 'http://proxyserv.bps.net/Registros/Empresas/V003/WsRegEmpresasService');
define('URL_WS_EMPRESAS', 'http://proxyservt.bps.net/RegEmpWsRouter/services/WsRegECA');
define('URL_ESCRITORIO_CIUDADANO', 'http://esctrabintt:8080/EscritorioTrabajador/EscritorioTrabajadorInterno.xhtml');
//para habilitar/deshabilitar los logs de los ws
define('LOGS_WS_ENABLED', true);
define('EMAIL_FROM', 'vtigert@sca.bps.net');
define('EMAIL_TEMPLATE_ID', 17);
define("LISTVIEW_CONSULTASWEB_ROLES", serialize( array('H8') ) ); 	//ROLES QUE NO VEN LA LISTVIEW DE CONTACTS
define("LISTVIEW_ATENCIONESWEB_ROLES", serialize( array('H7','H8') ) ); 
define("CORREO_SALIDA_RESPUESTA",'vtigertest@bps.gub.uy');
define("NAME_CORREO_SALIDA_RESPUESTA",'vtigertest');
define("EJECUTIVOS_MAILS",'h7');