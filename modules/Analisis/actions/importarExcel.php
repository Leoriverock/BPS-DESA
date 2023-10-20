<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Analisis_importarExcel_Action extends Vtiger_Action_Controller {

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    public function process(Vtiger_Request $request) {
        global $log, $adb;
        $fechaini = $request->get("fechaini");
        $fechafin = $request->get("fechafin");

        //$comentarios = 0;
        $comentarios = array();
        $sql = "SELECT COUNT(*) AS nrocomentarios , DATE_FORMAT(c.createdtime, '%Y-%m-%d') fecha FROM vtiger_crmentity c WHERE c.setype = 'ModComments' AND DATE_FORMAT(c.createdtime, '%Y-%m-%d') BETWEEN ? AND ? GROUP BY fecha";
        $rs = $adb->pquery($sql, array($fechaini, $fechafin));
        foreach ($rs as $fila) {
            //$comentarios = $fila['nrocomentarios'];
            $date = DateTime::createFromFormat('Y-m-d', $fila['fecha']);
            $comentarios[] = array('fecha' => $date->format('d/m/Y'), 'comentarios' => $fila['nrocomentarios']);
        }

        //$contactos = 0;
        $contactos = array();
        $sql = "SELECT COUNT(*) AS nrocontactos, DATE_FORMAT(c.createdtime, '%Y-%m-%d') fecha  FROM vtiger_crmentity c WHERE c.setype = 'Contacts' AND DATE_FORMAT(c.createdtime, '%Y-%m-%d') BETWEEN ? AND ? GROUP BY fecha";
        $rs = $adb->pquery($sql, array($fechaini, $fechafin));
        foreach ($rs as $fila) {
            //$contactos = $fila['nrocontactos'];
            $date = DateTime::createFromFormat('Y-m-d', $fila['fecha']);
            $contactos[] = array('fecha' => $date->format('d/m/Y'), 'contactos' => $fila['nrocontactos']);
        }

        $tareas = array();
        $sql = "SELECT COUNT(*) AS cantidad, DATE_FORMAT(c.createdtime, '%Y-%m-%d') fecha FROM vtiger_crmentity c WHERE c.setype in ('Calendar','Events') AND DATE_FORMAT(c.createdtime, '%Y-%m-%d') BETWEEN ? AND ? GROUP BY fecha";
        $rs = $adb->pquery($sql, array($fechaini, $fechafin));
        foreach ($rs as $fila) {
            $date = DateTime::createFromFormat('Y-m-d', $fila['fecha']);
            $tareas[] = array('fecha' => $date->format('d/m/Y'), 'tareas' => $fila['cantidad']);
        }

        $relaciones = array();
        $sql = "SELECT COUNT(*) AS cantidad, DATE_FORMAT(c.createdtime, '%Y-%m-%d') fecha FROM vtiger_crmentity c WHERE c.setype = 'Relationship' AND DATE_FORMAT(c.createdtime, '%Y-%m-%d') BETWEEN ? AND ? GROUP BY fecha";
        $rs = $adb->pquery($sql, array($fechaini, $fechafin));
        foreach ($rs as $fila) {
            $date = DateTime::createFromFormat('Y-m-d', $fila['fecha']);
            $relaciones[] = array('fecha' => $date->format('d/m/Y'), 'cantidad' => $fila['cantidad']);
        }

        $modificaciones = array();
        $sql = "SELECT COUNT(*) AS cantidad, DATE_FORMAT(m.changedon, '%Y-%m-%d') fecha FROM vtiger_modtracker_basic m WHERE DATE_FORMAT(m.changedon, '%Y-%m-%d') BETWEEN ? AND ? GROUP BY fecha";
        $rs = $adb->pquery($sql, array($fechaini, $fechafin));
        foreach ($rs as $fila) {
            $date = DateTime::createFromFormat('Y-m-d', $fila['fecha']);
            $modificaciones[] = array('fecha' => $date->format('d/m/Y'), 'modificaciones' => $fila['cantidad']);
        }

        $cantidadusuariosdia = array();
        $sql = "SELECT fecha, COUNT(*) users FROM (SELECT DISTINCT DATE_FORMAT(lh.login_time, '%Y-%m-%d') fecha, lh.user_name user FROM vtiger_loginhistory lh) tabla WHERE fecha BETWEEN ? AND ? GROUP BY fecha ORDER BY fecha";
        $rs = $adb->pquery($sql, array($fechaini, $fechafin));
        foreach ($rs as $fila) {
            $date = DateTime::createFromFormat('Y-m-d', $fila['fecha']);
            $cantidadusuariosdia[] = array('fecha' => $date->format('d/m/Y'), 'usuarios' => $fila['users']);
        }

        $usuariosnunca = array();
        $sql = "SELECT u.user_name user, 1 AS ultima, CONCAT(u.first_name, CONCAT(' ', u.last_name)) nombre FROM vtiger_users u WHERE u.user_name NOT IN (SELECT user_name FROM vtiger_loginhistory lh WHERE DATE_FORMAT(lh.login_time, '%Y-%m-%d') BETWEEN ? AND ?) AND u.user_name IN (SELECT DISTINCT user_name FROM vtiger_loginhistory lh) UNION SELECT u.user_name user, null AS ultima, CONCAT(u.first_name, CONCAT(' ', u.last_name)) nombre FROM vtiger_users u WHERE u.user_name NOT IN (SELECT user_name FROM vtiger_loginhistory)";
        $rs = $adb->pquery($sql, array($fechaini, $fechafin));
        foreach ($rs as $fila) {
            $alguna = $fila['ultima'] == 1 ? true : false;
            $usuariosnunca[] = array('usuario' => $fila['user'], 'nombre' => trim($fila['nombre']), 'alguna' => $alguna);
        }

        $datos['CANTIDADUSUARIOSDIA'] = $cantidadusuariosdia;
        $datos['COMENTARIOS'] = $comentarios;
        $datos['CONTACTOS'] = $contactos;
        $datos['TAREAS'] = $tareas;
        $datos['RELACIONES'] = $relaciones;
        $datos['MODIFICACIONES'] = $modificaciones;
        $datos['USUARIOSNOLOGUEADOS'] = $usuariosnunca;
        $this->exportarExcel($datos, $fechaini, $fechafin);
        
    }

    public function exportarExcel($datos, $fechaini, $fechafin){
        //ini_set('display_errors','on'); version_compare(PHP_VERSION, '5.5.0') <= 0 ? error_reporting(E_WARNING & ~E_NOTICE & ~E_DEPRECATED) : error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);   // DEBUGGING
        global $log, $current_user;
        require_once 'libraries/PHPExcel/PHPExcel.php';

        $date = DateTime::createFromFormat('Y-m-d', $fechaini);
        $inicio = $date->format('d/m/Y');
        $date = DateTime::createFromFormat('Y-m-d', $fechafin);
        $fin = $date->format('d/m/Y');

        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("SISCOM")
        ->setLastModifiedBy("SISCOM")
        ->setTitle("Analisis de CRM BSE ($inicio - $fin)")
        ->setSubject("Analisis")
        ->setDescription("Analisis de BSE en rango de fechas $inicio - $fin")
        ->setKeywords("Analisis, BSE")
        ->setCategory("Analisis");

        $titulos = array( 
            'CANTIDADUSUARIOSDIA' => 'Cantidad de usuarios distintos conectados por día',
            'COMENTARIOS' => 'Cantidad de comentarios creados por día',
            'CONTACTOS' => 'Cantidad de contactos creados por día',
            'TAREAS' => 'Cantidad de tareas y eventos creados por día',
            'RELACIONES' => 'Cantidad de relaciones creadas por día',
            'MODIFICACIONES' => 'Cantidad de modificaciones creadas por día',
            'USUARIOSNOLOGUEADOS' => 'Usuarios no logueados en el rango de fechas',
        );

        $estiloTituloReporte = array(
            'font' => array(
                'name'      => 'Verdana',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size' =>16,
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF220835')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE                    
                )
            ), 
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation'   => 0,
                'wrap'          => TRUE
            )
        );

        $estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill'  => array(
                'type'      => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation'   => 90,
                'startcolor' => array(
                    'rgb' => 'c47cf2'
                ),
                'endcolor'   => array(
                    'argb' => 'FF431a5d'
                )
            ),
            'borders' => array(
                'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'          => TRUE
            ));

        $estiloInfo = array(
            array(
                'font' => array(
                    'name'      => 'Arial',               
                    'color'     => array(
                        'rgb' => '000000'
                    )
                ),
                'fill'  => array(
                    'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                    'color'     => array('argb' => 'FFd9b7f4')
                ),
                'borders' => array(
                    'left'     => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN ,
                        'color' => array(
                            'rgb' => '3a2a47'
                        )
                    )             
                )
            )
        );

        $contador = 0;
        foreach ($datos as $hoja => $valores) {
            $objPHPExcel->setActiveSheetIndex($contador)
                ->mergeCells('A1:B1');
            $objPHPExcel->setActiveSheetIndex($contador)
                ->setCellValue('A1',  $titulos[$hoja]);
            $objPHPExcel->setActiveSheetIndex($contador)->getColumnDimension('A')->setWidth(25);
            $objPHPExcel->setActiveSheetIndex($contador)->getColumnDimension('B')->setWidth(20);
            switch ($hoja) {
                case 'CANTIDADUSUARIOSDIA':
                    $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue('A2',  'Fecha')
                        ->setCellValue('B2',  'Cantidad de usuarios');
                    $fila=3;
                    foreach ($valores as $row) {
                        $columnaA = 'A'.$fila;
                        $columnaB = 'B'.$fila;
                        $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue($columnaA, $row['fecha'])
                        ->setCellValue($columnaB, $row['usuarios']);
                        $fila++;
                    }  
                    break;
                case 'COMENTARIOS':
                    $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue('A2',  'Fecha')
                        ->setCellValue('B2',  'Comentarios');
                    $fila=3;
                    foreach ($valores as $row) {
                        $columnaA = 'A'.$fila;
                        $columnaB = 'B'.$fila;
                        $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue($columnaA, $row['fecha'])
                        ->setCellValue($columnaB, $row['comentarios']);
                        $fila++;
                    }  
                    break;
                case 'CONTACTOS':
                    $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue('A2',  'Fecha')
                        ->setCellValue('B2',  'Contactos');
                    $fila=3;
                    foreach ($valores as $row) {
                        $columnaA = 'A'.$fila;
                        $columnaB = 'B'.$fila;
                        $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue($columnaA, $row['fecha'])
                        ->setCellValue($columnaB, $row['contactos']);
                        $fila++;
                    }  
                    break;
                case 'TAREAS':
                    $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue('A2',  'Fecha')
                        ->setCellValue('B2',  'Tareas y eventos');
                    $fila=3;
                    foreach ($valores as $row) {
                        $columnaA = 'A'.$fila;
                        $columnaB = 'B'.$fila;
                        $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue($columnaA,  $row['fecha'])
                        ->setCellValue($columnaB,$row['tareas']);
                        $fila++;
                    }  
                    break;
                case 'RELACIONES':
                    $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue('A2',  'Fecha')
                        ->setCellValue('B2',  'Relaciones');
                    $fila=3;
                    foreach ($valores as $row) {
                        $columnaA = 'A'.$fila;
                        $columnaB = 'B'.$fila;
                        $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue($columnaA, $row['fecha'])
                        ->setCellValue($columnaB, $row['cantidad']);
                        $fila++;
                    }  
                    break;
                case 'MODIFICACIONES':
                    $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue('A2',  'Fecha')
                        ->setCellValue('B2',  'Modificaciones');
                    $fila=3;
                    foreach ($valores as $row) {
                        $columnaA = 'A'.$fila;
                        $columnaB = 'B'.$fila;
                        $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue($columnaA, $row['fecha'])
                        ->setCellValue($columnaB, $row['modificaciones']);
                        $fila++;
                    }  
                    break;
                case 'USUARIOSNOLOGUEADOS':
                    $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue('A2',  'Usuario')
                        ->setCellValue('B2',  '¿Se ha logueado alguna vez?');
                    $fila=3;
                    foreach ($valores as $row) {
                        $columnaA = 'A'.$fila;
                        $columnaB = 'B'.$fila;
                        $alguna = $row['alguna'] ? 'Sí' : 'No';
                        $user = $row['nombre']. " (".$row['usuario'].")";
                        $objPHPExcel->setActiveSheetIndex($contador)
                        ->setCellValue($columnaA, $user)
                        ->setCellValue($columnaB, $alguna);
                        $fila++;
                    }  
                    break;
            }

            $objPHPExcel->setActiveSheetIndex($contador)->setTitle($hoja);
            $contador++;
            if ($contador != count(array_keys($datos))) {
               $objPHPExcel->createSheet();
            }
        }

        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');


        $rootDirectory = vglobal('root_directory');
        $nombreTemporal = "storage/Reporte/Analisis".$current_user->id.".xlsx";
        if (!file_exists($rootDirectory.'storage/Reporte/')) {
            mkdir($rootDirectory.'storage/Reporte/');
        }

        $objWriter->save($rootDirectory.$nombreTemporal);
        //echo json_encode(array('success' => true, 'directorio' => $nombreTemporal));
        echo json_encode(array('success' => true, 'directorio' => $rootDirectory.$nombreTemporal));
        return;
    }
}

?>
