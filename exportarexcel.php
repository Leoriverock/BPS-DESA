<?php
require_once 'PHPExcel/Classes/PHPExcel.php';

// Crear un nuevo objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Configurar propiedades del documento
$objPHPExcel->getProperties()->setCreator("Tu nombre")
                             ->setLastModifiedBy("Tu nombre")
                             ->setTitle("Título del documento")
                             ->setSubject("Asunto del documento")
                             ->setDescription("Descripción del documento")
                             ->setKeywords("excel php phpexcel")
                             ->setCategory("Categoria del documento");

// Agregar datos a la hoja de cálculo
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Nombre')
            ->setCellValue('B1', 'Apellido')
            ->setCellValue('A2', 'Juan')
            ->setCellValue('B2', 'Pérez');

// Establecer el nombre del archivo
$filename = 'datos.xls';

// Configurar encabezados HTTP para descargar el archivo como Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Crear objeto Writer para guardar el archivo Excel
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

// Limpiar buffer de salida
while (ob_get_level()) {
    ob_end_clean();
}

// Enviar archivo Excel al navegador
$objWriter->save('php://output');
exit();
?>
