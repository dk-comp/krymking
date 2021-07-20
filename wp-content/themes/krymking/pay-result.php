<?php
// Пароль #1 (для тестовых платежей)
$mrh_pass1 = 'zV395RuabF6HbqbmWRJ3';
 
// Чтение параметров
/*
 * для  робокассы было
$inv_id  = intval(@$_POST['InvId']);
$out_sum = @$_POST['OutSum'];
$crc     = strtoupper(@$_POST['SignatureValue']);
*/
$inv_id  = intval(@$_GET['OrderId']);
$out_sum = @$_POST['Amount']/100;



//mount

if (!empty($inv_id)) {

    // $my_crc = strtoupper(md5("$out_sum:$inv_id:$mrh_pass1"));

    // if ($my_crc == $crc) {
    // Письмо гостю предоплата
    $customer = get_userdata(get_field('customer', $inv_id));

    $headers1 = 'Content-type: text/html; charset=utf-8' . "\r\n" . 'From: Krymking <info@krymking.ru>';
    $subject1 = 'Благодарим за бронирование';

    $message1 = 'Здравствуйте, ' . $customer->first_name . ' ' . $customer->last_name . '! ';
    $message1 .= 'Благодарим за бронирование №' . $inv_id . ' на Крымкинг.ру! ';
    $message1 .= 'После подтверждения оплаты на Ваш электронный адрес будет отправлен Ваучер на заселение с контактной информацией о Владельце и забронированном жилье. ';
    $message1 .= 'Откройте электронное письмо от Krymking.ru с Ваучером и сохраните или распечатайте его.';
    $message1 .= '<br>';
    $message1 .= '<br>';
    $message1 .= 'С уважением, <br> Команда Krymking.ru';

    if(!wp_mail($customer->user_email, $subject1, $message1, $headers1))
        mail($customer->user_email, $subject1, $message1, $headers1);

    // Обновляем статус на оплачен
    update_field('booking_status', '2', $inv_id);
    update_field('prepayment', $out_sum, $inv_id);

    // Письмо с успешной оплатой
    $object = get_post(get_field('apartment', $inv_id));

    $post = get_post($inv_id);
    $author = get_userdata($post->post_author);
    $user = get_userdata(get_field('customer', $inv_id));

    $headers = 'Content-type: text/html; charset=utf-8' . "\r\n" . 'From: Krymking <info@krymking.ru>';
    $subject = 'Благодарим за бронирование №' . $inv_id . '';

    $message = 'Уважаемый, ' . $user->first_name . ' ' . $user->last_name . '! ';
    $message .= 'Поздравляем с успешным бронированием № ' . $inv_id . ' объекта жилья <a href="' . get_permalink($object) . '">' . $object->post_title . '</a>, ';
    $message .= 'при помощи Krymking.ru. ';
    $message .= 'Во вложении находится Ваучер на заселение по данному объекту. ';
    $message .= 'Пожалуйста, сохраните его или распечатайте и предъявите при заезде Владельцу жилья. ';
    $message .= '<br>';
    $message .= '<br>';
    $message .= 'С уважением, <br> Команда Krymking.ru';

    $check_in = get_field('check_in', $inv_id);
    $check_out = get_field('check_out', $inv_id);
    $days = days($check_in, $check_out);
    $price = the_price($object->ID);
    $total = price_total($price, $days);

    $fileContentArr = [
        'Номер бронирования' => $inv_id,
        'Название объекта' => $object->post_title,
        'Адрес объекта жилья' => hotel_address($object->ID) . ', ул. ' . get_field('street', $object->ID) . ', ' . get_field('house', $object->ID),
        'ФИО владельца' => $author->display_name,
        'Телефон владельца' => get_field('phone', 'user_' . $author->ID),
        'Дата и время заезда' => $check_in,
        'Дата и время выезда' => $check_out,
        'Общая длительность проживания' => $days,
        'Итого за ' . num_word($days, array("сутки", "суток", "суток")) => $total . ' (' . $price . ' * ' . $days . ')',
        'Предоплата' => $out_sum,
        'Оплата при заселении' => $price * $days - $out_sum
    ];

    include_once(get_template_directory() . '/lib/PHPExcel.php');

    $phpExel = new PHPExcel();

    $phpExel->setActiveSheetIndex(0);

    $activeSheet = $phpExel->getActiveSheet();

    $activeSheet->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);

    $activeSheet->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

    $activeSheet->getPageMargins()->setTop(0.5);
    $activeSheet->getPageMargins()->setRight(0.75);
    $activeSheet->getPageMargins()->setBottom(0.5);
    $activeSheet->getPageMargins()->setLeft(0.75);

    $activeSheet->setTitle('Ваучер на заселение');

    $activeSheet->getHeaderFooter()->setOddFooter('&L&B' . $activeSheet->getTitle() . '&RСтраница &P из &N');

    $phpExel->getDefaultStyle()->getFont()->setName('Arial');

    $phpExel->getDefaultStyle()->getFont()->setSize(10);

    $activeSheet->getColumnDimension('A')->setWidth(50);
    $activeSheet->getColumnDimension('B')->setWidth(50);

    $activeSheet->getRowDimension('1')->setRowHeight(60);

    $activeSheet->mergeCells('A1:B1');

    $activeSheet->setCellValue('A1', 'Ваучер на заселение');
    $activeSheet->getStyle('A1')->getAlignment()->setWrapText(true);

    $style_header = array(
        'font' => array(
            'italic' => true,
            'name' => 'Arial',
            'size' => 14,

        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),

    );

    $style_parameters = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => true
        ),
    );

    $activeSheet->getStyle('A1:B1')->applyFromArray($style_header);

    $row_start = 2;
    $current_row = $row_start;

    foreach ($fileContentArr as $key => $item) {

        $current_row++;

        $activeSheet->setCellValue('A' . $current_row, $key);
        $activeSheet->setCellValue('B' . $current_row, $item);

//        $activeSheet->getStyle('A' . $current_row)->getAlignment()->setWrapText(true);
//        $activeSheet->getStyle('B' . $current_row)->getAlignment()->setWrapText(true);
//
//        $activeSheet->getStyle('A' . $current_row)->applyFromArray($style_parameters);
//        $activeSheet->getStyle('B' . $current_row)->applyFromArray($style_parameters);

    }

    $activeSheet->getStyle('A'.$row_start.':B'.$current_row)->applyFromArray($style_parameters);

//    $current_row += 2;
//
//    $activeSheet->mergeCells('A' . $current_row . ':B' . $current_row);
//
//    $activeSheet->setCellValue('A' . $current_row, 'С уваженим команда krymking.ru');
//
//    $activeSheet->getStyle('A' . $current_row . ':B' . $current_row)->applyFromArray([
//        'alignment' => array(
//            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
//            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
//            'wrap' => true
//        ),
//    ]);

    //$activeSheet->removeRow(++$current_row);

    $fileType = [
        'pdf' => [
            'c-t' => 'application/pdf',
            'ext' => 'pdf',
            'render' => 'PDF'
        ],
    ];

    $rendererName = \PHPExcel_Settings::PDF_RENDERER_TCPDF;
    $rendererLibraryPath = get_template_directory() . '/lib/PHPExcel/tcpdf';

    if (!PHPExcel_Settings::setPdfRenderer(
        $rendererName,
        $rendererLibraryPath
    )) {
        die(
            'NOTICE: Please set the ' . $rendererName . ' and ' . $rendererLibraryPath . ' values' .
            '<br />' .
            'at the top of this script as appropriate for your directory structure'
        );
    }

    $objectWriter = PHPExcel_IOFactory::createWriter($phpExel, $fileType['pdf']['render']);

    $fileName = microtime();

    $file = get_template_directory() . '/lib/temp_files/' . $fileName . '.' . $fileType['pdf']['ext'];

    $objectWriter->save(get_template_directory() . '/lib/temp_files/' . $fileName . '.' . $fileType['pdf']['ext']);

    if(!wp_mail($user->user_email, $subject, $message, $headers, $file)){

        mail($user->user_email, $subject, $message, $headers);

        header('Content-Type:' . $fileType['pdf']['c-t']);

        header("Accept-Ranges: bytes");

        header("Content-Length: ".filesize($file));

        header('Content-Disposition:attachment;filename="' . $file . '"');

        readfile($file);

        //$objectWriter->save('php://output');

    }

    @unlink($file);

}


get_header();
/* Template Name: Результат оплаты */
?>
<div class="headLine"></div>
<div class="wrapper">
	<?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
	<h1 class="page-title"><?=the_title()?></h1>

<? if ( !empty($inv_id) ) {

		echo '<div class="success-text">Благодарим за бронирование на Крымкинг.ру! После подтверждения оплаты на Ваш электронный адрес будет отправлен Ваучер на заселение с контактной информацией о Владельце и забронированном жилье.  Откройте электронное письмо от Krymking.ru с Ваучером и сохраните или распечатайте его.</div>';
		
		// exit();	
	// } else {
	// 	echo '<div class="success-text">Произошла ошибка</div>';
	// }
} else { 
	echo '<div class="success-text">Произошла ошибка</div>';
} ?>
 
</div>

<?php
get_footer();