<?
global $wpdb;
global $current_user;

include 'TinkoffMerchantAPI.php';

//spl_autoload('TinkoffMerchantAPI');
$api = new TinkoffMerchantAPI(
    '1618915637197DEMO',  //Ваш Terminal_Key
    'ptlarl9mfnwe9kh9'   //Ваш Secret_Key
);


$enabledTaxation = true;
//$amount = 1000 * 100;


function balanceAmount($isShipping, $items, $amount)
{
    $itemsWithoutShipping = $items;

    if ($isShipping) {
        $shipping = array_pop($itemsWithoutShipping);
    }

    $sum = 0;

    foreach ($itemsWithoutShipping as $item) {
        $sum += $item['Amount'];
    }

    if (isset($shipping)) {
        $sum += $shipping['Amount'];
    }

    if ($sum != $amount) {
        $sumAmountNew = 0;
        $difference = $amount - $sum;
        $amountNews = [];

        foreach ($itemsWithoutShipping as $key => $item) {
            $itemsAmountNew = $item['Amount'] + floor($difference * $item['Amount'] / $sum);
            $amountNews[$key] = $itemsAmountNew;
            $sumAmountNew += $itemsAmountNew;
        }

        if (isset($shipping)) {
            $sumAmountNew += $shipping['Amount'];
        }

        if ($sumAmountNew != $amount) {
            $max_key = array_keys($amountNews, max($amountNews))[0];    // ключ макс значения
            $amountNews[$max_key] = max($amountNews) + ($amount - $sumAmountNew);
        }

        foreach ($amountNews as $key => $item) {
            $items[$key]['Amount'] = $amountNews[$key];
        }
    }

    return $items;
}






if($_GET['booking-id']) {
	$booking_id = get_field('apartment', $_GET['booking-id']);
	$check_in   = get_field('check_in', $_GET['booking-id']);
	$check_out  = get_field('check_out', $_GET['booking-id']);
	$post_id = $_GET['booking-id'];
} else {
	$booking_id = $_POST['hotel_id'];
	$check_in   = $_POST['check_in'];
	$check_out  = $_POST['check_out'];
}

$url   = get_template_directory_uri();
$days  = days($check_in, $check_out);
$price = the_price($booking_id);
$total = price_total($price, $days);
 
if (!empty($_POST['send'])) {

	if(empty($_GET['booking-id'])) {

        $start1 = microtime(true);




		if(get_field('fast_booking',  $_POST['post_id']) == 'Включить') {
			$status = 'confirmed';
		} else {
			$status = 'request';
		}

		$post_id = wp_insert_post(  wp_slash( array(
			'post_status'   => $status,
			'post_type'     => 'orders',
			'post_title'    => 'Бронирование №'. SecondLastPostId(),
			'post_content'  => '',
			'post_author'   => get_post($_POST['post_id'])->post_author,
			'ping_status'   => get_option('default_ping_status'),
			'meta_input'    => [ 'meta_key'=>'meta_value' ],
		) ) );

		if( is_wp_error($post_id) ){

			$result['error'] = 'success';
			$result['message'] = $post_id->get_error_message();
		} else {

		    $result['status'] = 'success';
			$result['message'] = 'Бронирование № '.SecondLastPostId();

			update_field('check_in', $_POST['check_in'], $post_id);
			update_field('check_out', $_POST['check_out'], $post_id);
			update_field('guests', $_POST['guests'], $post_id);
			update_field('comment', $_POST['comment'], $post_id);
			update_field('apartment', $_POST['post_id'], $post_id);
			update_field('time_arrival', $_POST['time_arrival'], $post_id);

			$user = get_user_by('email', $_POST['user_email']);
			$user_id = $user->ID;

			update_field('customer', $user_id, $post_id);
			update_field('booking_status', '1', $post_id);

			update_field('user_name', $_POST['user_name'], $post_id);
			update_field('user_lastname', $_POST['user_lastname'], $post_id);
			update_field('user_email', $_POST['user_email'], $post_id);
			update_field('user_phone', $_POST['phone'], $post_id);
			update_field('main_guest', $_POST['main_guest'], $post_id);

		}

		$post = get_post($_POST['post_id']);
		$author = get_userdata($post->post_author);

		// Письмо владельцу
		if ( get_field('fast_booking', $post->ID ) == 'Включить') {

			// Мгновенное бронирование
			$headers = 'Content-type: text/html; charset=utf-8'."\r\n".'From: Krymking <info@krymking.ru>';
			$subject = 'Бронирования объекта на сайте Krymking.ru';

            $message = <<<HERE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
 <head> 
  <meta charset="UTF-8"> 
  <meta content="width=device-width, initial-scale=1" name="viewport"> 
  <meta name="x-apple-disable-message-reformatting"> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
  <meta content="telephone=no" name="format-detection"> 
  <title>Крымкинг - забронируй лето!</title> 
  <!--[if (mso 16)]>
    <style type="text/css">
    a {text-decoration: none;}
    </style>
    <![endif]--> 
  <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--> 
  <!--[if gte mso 9]>
<xml>
    <o:OfficeDocumentSettings>
    <o:AllowPNG></o:AllowPNG>
    <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
</xml>
<![endif]--> 
  <style type="text/css">
#outlook a {
	padding:0;
}
.ExternalClass {
	width:100%;
}
.ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
	line-height:100%;
}
.es-button {
	mso-style-priority:100!important;
	text-decoration:none!important;
}
a[x-apple-data-detectors] {
	color:inherit!important;
	text-decoration:none!important;
	font-size:inherit!important;
	font-family:inherit!important;
	font-weight:inherit!important;
	line-height:inherit!important;
}
.es-desk-hidden {
	display:none;
	float:left;
	overflow:hidden;
	width:0;
	max-height:0;
	line-height:0;
	mso-hide:all;
}
[data-ogsb] .es-button {
	border-width:0!important;
	padding:10px 20px 10px 20px!important;
}
.es-button-border:hover a.es-button,
.es-button-border:hover button.es-button {
	background:#e5e5e5!important;
	border-color:#e5e5e5!important;
	color:#000000!important;
}
.es-button-border:hover {
	border-color:#42d159 #42d159 #42d159 #42d159!important;
	background:#e5e5e5!important;
}
td .es-button-border:hover a.es-button-1 {
	background:#2f94cf!important;
	border-color:#2f94cf!important;
	color:#ffffff!important;
}
td .es-button-border-2:hover {
	background:#2f94cf!important;
	border-color:#e5e5e5 #e5e5e5 #e5e5e5 #e5e5e5!important;
}
[data-ogsb] .es-button.es-button-3 {
	padding:5px 20px!important;
}
@media only screen and (max-width:600px) {h1 { font-size:26px!important; text-align:center } h2 { font-size:26px!important; text-align:center } h3 { font-size:13px!important; text-align:center } .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a { font-size:26px!important } .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a { font-size:26px!important } .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a { font-size:13px!important } .es-menu td a { font-size:16px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:16px!important } .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a { font-size:16px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:16px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button, button.es-button { font-size:14px!important; display:inline-block!important } .es-btn-fw { border-width:10px 0px!important; text-align:center!important } .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0!important } .es-m-p0r { padding-right:0!important } .es-m-p0l { padding-left:0!important } .es-m-p0t { padding-top:0!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden { width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } tr.es-desk-hidden { display:table-row!important } table.es-desk-hidden { display:table!important } td.es-desk-menu-hidden { display:table-cell!important } .es-menu td { width:1%!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } .es-m-p5 { padding:5px!important } .es-m-p5t { padding-top:5px!important } .es-m-p5b { padding-bottom:5px!important } .es-m-p5r { padding-right:5px!important } .es-m-p5l { padding-left:5px!important } .es-m-p10 { padding:10px!important } .es-m-p10t { padding-top:10px!important } .es-m-p10b { padding-bottom:10px!important } .es-m-p10r { padding-right:10px!important } .es-m-p10l { padding-left:10px!important } .es-m-p15 { padding:15px!important } .es-m-p15t { padding-top:15px!important } .es-m-p15b { padding-bottom:15px!important } .es-m-p15r { padding-right:15px!important } .es-m-p15l { padding-left:15px!important } .es-m-p20 { padding:20px!important } .es-m-p20t { padding-top:20px!important } .es-m-p20r { padding-right:20px!important } .es-m-p20l { padding-left:20px!important } .es-m-p25 { padding:25px!important } .es-m-p25t { padding-top:25px!important } .es-m-p25b { padding-bottom:25px!important } .es-m-p25r { padding-right:25px!important } .es-m-p25l { padding-left:25px!important } .es-m-p30 { padding:30px!important } .es-m-p30t { padding-top:30px!important } .es-m-p30b { padding-bottom:30px!important } .es-m-p30r { padding-right:30px!important } .es-m-p30l { padding-left:30px!important } .es-m-p35 { padding:35px!important } .es-m-p35t { padding-top:35px!important } .es-m-p35b { padding-bottom:35px!important } .es-m-p35r { padding-right:35px!important } .es-m-p35l { padding-left:35px!important } .es-m-p40 { padding:40px!important } .es-m-p40t { padding-top:40px!important } .es-m-p40b { padding-bottom:40px!important } .es-m-p40r { padding-right:40px!important } .es-m-p40l { padding-left:40px!important } }
</style> 
 </head> 
 <body style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0"> 
  <span style="display:none !important;font-size:0px;line-height:0;color:#FFFFFF;visibility:hidden;opacity:0;height:0;width:0;mso-hide:all">Посуточная аренда жилья в Крыму</span> 
  <div class="es-wrapper-color" style="background-color:#F6F6F6"> 
   <!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="#f6f6f6"></v:fill>
			</v:background>
		<![endif]--> 
   <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top"> 
     <tr style="border-collapse:collapse"> 
      <td valign="top" style="padding:0;Margin:0"> 
       <table cellpadding="0" cellspacing="0" class="es-header es-mobile-hidden" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" bgcolor="#347BA6" style="padding:0;Margin:0;background-color:#347BA6"> 
           <table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td align="left" bgcolor="#347BA6" style="padding:0;Margin:0;padding-bottom:10px;padding-left:20px;padding-right:20px;background-color:#347BA6"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="10" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" bgcolor="#347BA6" style="padding:0;Margin:0;background-color:#347BA6"> 
           <table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;border-top:1px solid #CFE2F3;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" bgcolor="#347BA6" style="Margin:0;padding-top:10px;padding-bottom:15px;padding-left:40px;padding-right:40px;background-color:#347BA6"> 
               <!--[if mso]><table style="width:578px" cellpadding="0"
                            cellspacing="0"><tr><td style="width:419px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td class="es-m-p0r es-m-p20b" valign="top" align="center" style="padding:0;Margin:0;width:419px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="left" class="es-m-txt-c" style="padding:0;Margin:0;padding-top:10px"><h1 class="letter" style="Margin:0;line-height:31px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:26px;font-style:normal;font-weight:normal;color:#FFFFFF;letter-spacing:2px"><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#FFFFFF;font-size:26px;font-family:arial, 'helvetica neue', helvetica, sans-serif" href="http://krymking.ru/">Krymking.ru</a></strong></h1></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:20px"></td><td style="width:139px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:139px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-top:5px"><span class="es-button-border-2 es-button-border" style="border-style:solid;border-color:#FFFFFF;background:#2678A8;border-width:2px;display:inline-block;border-radius:30px;width:auto"><a href="http://krymking.ru/" class="es-button es-button-1" target="_blank" style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:16px;border-style:solid;border-color:#2678A8;border-width:5px 20px;display:inline-block;background:#2678A8;border-radius:30px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-weight:normal;font-style:normal;line-height:19px;width:auto;text-align:center"> 
                         <!--[if !mso]><!-- --><img src="https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/81801622632434671.png" alt="icon" width="24" style="display:inline-block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;vertical-align:middle;margin-right:10px" align="absmiddle"> 
                         <!--<![endif]-->Войти</a></span></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td></tr></table><![endif]--></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px" cellspacing="0" cellpadding="0" align="center"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" background="https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/46571622636492111.jpg" style="Margin:0;padding-bottom:10px;padding-top:40px;padding-left:40px;padding-right:40px;background-image:url(https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/46571622636492111.jpg);background-repeat:no-repeat;background-position:left top"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:578px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="40" style="padding:0;Margin:0"></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><h1 style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:30px;font-style:normal;font-weight:normal;color:#FFFFFF"><strong>Крымкинг - забронируй лето!</strong></h1></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#FFFFFF;font-size:16px">Посуточная аренда жилья в Крыму</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="40" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-left:1px solid #9FC5E8;border-right:1px solid #9FC5E8;border-top:1px solid #9FC5E8;border-bottom:1px solid #9FC5E8;background-color:#FFFFFF;border-radius:8px" role="presentation"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-bottom:5px;padding-left:10px;padding-right:10px;padding-top:30px"><h1 style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:30px;font-style:normal;font-weight:normal;color:#333333"><strong>Здравствуйте, $author->first_name $author->last_name!</strong></h1></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-top:15px;padding-left:15px;padding-right:15px;padding-bottom:40px">
                        <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">
HERE;
            $message .= 'Поздравляем, Ваш объект жилья id-номер '.$post->ID.' забронирован Гостем. ';
            $message .= 'Для получения детальной информации перейдите в <a href="'.home_url("/profile/").'">Личный кабинет</a>. ';
            $message .= 'Напоминаем, что Вы можете обмениваться сообщениями со своими гостями на сайте Krymking.ru.';
            $message .='<br>';
            $message .='<br>';
            $message .='С уважением, <br> Команда Krymking.ru';
            $message .= <<<HEREDOC
</p>
                      </td> 
                     </tr> 
                   </table></td> 
                 </tr> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:578px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-bottom:10px;padding-left:20px;padding-right:20px;padding-top:30px;font-size:0"> 
                       <table border="0" width="60%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                         <tr style="border-collapse:collapse"> 
                          <td style="padding:0;Margin:0;border-bottom:1px solid #CCCCCC;background:none;height:1px;width:100%;margin:0px"></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:30px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">С уважением,<br>Команда Krymking.ru</p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#CCCCCC;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px;border-bottom:1px solid #CFE2F3" bgcolor="#cccccc"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" style="Margin:0;padding-bottom:20px;padding-top:25px;padding-left:40px;padding-right:40px"> 
               <!--[if mso]><table style="width:578px" cellpadding="0" cellspacing="0"><tr><td style="width:205px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:185px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Мы в социальных сетях</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;font-size:0"> 
                       <table cellpadding="0" cellspacing="0" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                         <tr style="border-collapse:collapse"> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://www.facebook.com/Krymkingru-106685441598072" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Facebook" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/facebook-rounded-colored.png" alt="Fb" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://www.instagram.com/krymking.ru/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Instagram" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/instagram-rounded-colored.png" alt="Inst" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://vk.com/krymking" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Vkontakte" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/vk-rounded-colored.png" alt="VK" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://ok.ru/krymking.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Odnoklassniki" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/odnoklassniki-rounded-colored.png" alt="Ok" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0"><a target="_blank" href="https://t.me/krymkingru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Telegram" src="https://ifcex.stripocdn.email/content/assets/img/messenger-icons/rounded-colored/telegram-rounded-colored.png" alt="Telegram" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:225px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" class="es-m-p20b" style="padding:0;Margin:0;width:225px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Звоните нам</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px"><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:16px" href="tel:+73652777180">+7 3652 777-180</a></strong></p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:20px"></td><td style="width:128px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:128px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Пишите нам</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px"><strong><a target="_blank" href="mailto:info@krymking.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:16px">info@krymking.ru</a></strong></p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td></tr></table><![endif]--></td> 
             </tr> 
             <tr style="border-collapse:collapse"> 
              <td align="left" style="Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;padding-bottom:35px"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:618px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><h3 style="Margin:0;line-height:19px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:16px;font-style:normal;font-weight:normal;color:#333333"><u><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#178DE0;font-size:16px" href="http://krymking.ru/">Krymking.ru</a></strong>&nbsp;</u>Посуточная аренда жилья в Крыму!</h3></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><h3 style="Margin:0;line-height:16px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:13px;font-style:normal;font-weight:normal;color:#333333">© 2021 ООО Крымкинг. Все права защищены</h3></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-footer es-mobile-hidden" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td align="left" style="padding:20px;Margin:0"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="10" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table></td> 
     </tr> 
   </table> 
  </div>  
 </body>
</html>
HEREDOC;

	
		} else {

			// Без мгновенного бронирования
			$headers = 'Content-type: text/html; charset=utf-8'."\r\n".'From: Krymking <info@krymking.ru>';
			$subject = 'Бронирования объекта на сайте Krymking.ru';

            $message = <<<HERE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
 <head> 
  <meta charset="UTF-8"> 
  <meta content="width=device-width, initial-scale=1" name="viewport"> 
  <meta name="x-apple-disable-message-reformatting"> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
  <meta content="telephone=no" name="format-detection"> 
  <title>Крымкинг - забронируй лето!</title> 
  <!--[if (mso 16)]>
    <style type="text/css">
    a {text-decoration: none;}
    </style>
    <![endif]--> 
  <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--> 
  <!--[if gte mso 9]>
<xml>
    <o:OfficeDocumentSettings>
    <o:AllowPNG></o:AllowPNG>
    <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
</xml>
<![endif]--> 
  <style type="text/css">
#outlook a {
	padding:0;
}
.ExternalClass {
	width:100%;
}
.ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
	line-height:100%;
}
.es-button {
	mso-style-priority:100!important;
	text-decoration:none!important;
}
a[x-apple-data-detectors] {
	color:inherit!important;
	text-decoration:none!important;
	font-size:inherit!important;
	font-family:inherit!important;
	font-weight:inherit!important;
	line-height:inherit!important;
}
.es-desk-hidden {
	display:none;
	float:left;
	overflow:hidden;
	width:0;
	max-height:0;
	line-height:0;
	mso-hide:all;
}
[data-ogsb] .es-button {
	border-width:0!important;
	padding:10px 20px 10px 20px!important;
}
.es-button-border:hover a.es-button,
.es-button-border:hover button.es-button {
	background:#e5e5e5!important;
	border-color:#e5e5e5!important;
	color:#000000!important;
}
.es-button-border:hover {
	border-color:#42d159 #42d159 #42d159 #42d159!important;
	background:#e5e5e5!important;
}
td .es-button-border:hover a.es-button-1 {
	background:#2f94cf!important;
	border-color:#2f94cf!important;
	color:#ffffff!important;
}
td .es-button-border-2:hover {
	background:#2f94cf!important;
	border-color:#e5e5e5 #e5e5e5 #e5e5e5 #e5e5e5!important;
}
[data-ogsb] .es-button.es-button-3 {
	padding:5px 20px!important;
}
@media only screen and (max-width:600px) {h1 { font-size:26px!important; text-align:center } h2 { font-size:26px!important; text-align:center } h3 { font-size:13px!important; text-align:center } .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a { font-size:26px!important } .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a { font-size:26px!important } .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a { font-size:13px!important } .es-menu td a { font-size:16px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:16px!important } .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a { font-size:16px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:16px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button, button.es-button { font-size:14px!important; display:inline-block!important } .es-btn-fw { border-width:10px 0px!important; text-align:center!important } .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0!important } .es-m-p0r { padding-right:0!important } .es-m-p0l { padding-left:0!important } .es-m-p0t { padding-top:0!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden { width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } tr.es-desk-hidden { display:table-row!important } table.es-desk-hidden { display:table!important } td.es-desk-menu-hidden { display:table-cell!important } .es-menu td { width:1%!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } .es-m-p5 { padding:5px!important } .es-m-p5t { padding-top:5px!important } .es-m-p5b { padding-bottom:5px!important } .es-m-p5r { padding-right:5px!important } .es-m-p5l { padding-left:5px!important } .es-m-p10 { padding:10px!important } .es-m-p10t { padding-top:10px!important } .es-m-p10b { padding-bottom:10px!important } .es-m-p10r { padding-right:10px!important } .es-m-p10l { padding-left:10px!important } .es-m-p15 { padding:15px!important } .es-m-p15t { padding-top:15px!important } .es-m-p15b { padding-bottom:15px!important } .es-m-p15r { padding-right:15px!important } .es-m-p15l { padding-left:15px!important } .es-m-p20 { padding:20px!important } .es-m-p20t { padding-top:20px!important } .es-m-p20r { padding-right:20px!important } .es-m-p20l { padding-left:20px!important } .es-m-p25 { padding:25px!important } .es-m-p25t { padding-top:25px!important } .es-m-p25b { padding-bottom:25px!important } .es-m-p25r { padding-right:25px!important } .es-m-p25l { padding-left:25px!important } .es-m-p30 { padding:30px!important } .es-m-p30t { padding-top:30px!important } .es-m-p30b { padding-bottom:30px!important } .es-m-p30r { padding-right:30px!important } .es-m-p30l { padding-left:30px!important } .es-m-p35 { padding:35px!important } .es-m-p35t { padding-top:35px!important } .es-m-p35b { padding-bottom:35px!important } .es-m-p35r { padding-right:35px!important } .es-m-p35l { padding-left:35px!important } .es-m-p40 { padding:40px!important } .es-m-p40t { padding-top:40px!important } .es-m-p40b { padding-bottom:40px!important } .es-m-p40r { padding-right:40px!important } .es-m-p40l { padding-left:40px!important } }
</style> 
 </head> 
 <body style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0"> 
  <span style="display:none !important;font-size:0px;line-height:0;color:#FFFFFF;visibility:hidden;opacity:0;height:0;width:0;mso-hide:all">Посуточная аренда жилья в Крыму</span> 
  <div class="es-wrapper-color" style="background-color:#F6F6F6"> 
   <!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="#f6f6f6"></v:fill>
			</v:background>
		<![endif]--> 
   <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top"> 
     <tr style="border-collapse:collapse"> 
      <td valign="top" style="padding:0;Margin:0"> 
       <table cellpadding="0" cellspacing="0" class="es-header es-mobile-hidden" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" bgcolor="#347BA6" style="padding:0;Margin:0;background-color:#347BA6"> 
           <table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td align="left" bgcolor="#347BA6" style="padding:0;Margin:0;padding-bottom:10px;padding-left:20px;padding-right:20px;background-color:#347BA6"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="10" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" bgcolor="#347BA6" style="padding:0;Margin:0;background-color:#347BA6"> 
           <table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;border-top:1px solid #CFE2F3;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" bgcolor="#347BA6" style="Margin:0;padding-top:10px;padding-bottom:15px;padding-left:40px;padding-right:40px;background-color:#347BA6"> 
               <!--[if mso]><table style="width:578px" cellpadding="0"
                            cellspacing="0"><tr><td style="width:419px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td class="es-m-p0r es-m-p20b" valign="top" align="center" style="padding:0;Margin:0;width:419px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="left" class="es-m-txt-c" style="padding:0;Margin:0;padding-top:10px"><h1 class="letter" style="Margin:0;line-height:31px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:26px;font-style:normal;font-weight:normal;color:#FFFFFF;letter-spacing:2px"><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#FFFFFF;font-size:26px;font-family:arial, 'helvetica neue', helvetica, sans-serif" href="http://krymking.ru/">Krymking.ru</a></strong></h1></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:20px"></td><td style="width:139px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:139px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-top:5px"><span class="es-button-border-2 es-button-border" style="border-style:solid;border-color:#FFFFFF;background:#2678A8;border-width:2px;display:inline-block;border-radius:30px;width:auto"><a href="http://krymking.ru/" class="es-button es-button-1" target="_blank" style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:16px;border-style:solid;border-color:#2678A8;border-width:5px 20px;display:inline-block;background:#2678A8;border-radius:30px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-weight:normal;font-style:normal;line-height:19px;width:auto;text-align:center"> 
                         <!--[if !mso]><!-- --><img src="https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/81801622632434671.png" alt="icon" width="24" style="display:inline-block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;vertical-align:middle;margin-right:10px" align="absmiddle"> 
                         <!--<![endif]-->Войти</a></span></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td></tr></table><![endif]--></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px" cellspacing="0" cellpadding="0" align="center"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" background="https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/46571622636492111.jpg" style="Margin:0;padding-bottom:10px;padding-top:40px;padding-left:40px;padding-right:40px;background-image:url(https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/46571622636492111.jpg);background-repeat:no-repeat;background-position:left top"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:578px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="40" style="padding:0;Margin:0"></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><h1 style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:30px;font-style:normal;font-weight:normal;color:#FFFFFF"><strong>Крымкинг - забронируй лето!</strong></h1></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#FFFFFF;font-size:16px">Посуточная аренда жилья в Крыму</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="40" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-left:1px solid #9FC5E8;border-right:1px solid #9FC5E8;border-top:1px solid #9FC5E8;border-bottom:1px solid #9FC5E8;background-color:#FFFFFF;border-radius:8px" role="presentation"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-bottom:5px;padding-left:10px;padding-right:10px;padding-top:30px"><h1 style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:30px;font-style:normal;font-weight:normal;color:#333333"><strong>Здравствуйте, $author->first_name $author->last_name!</strong></h1></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-top:15px;padding-left:15px;padding-right:15px;padding-bottom:40px">
                        <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">
HERE;
            $message .= 'По Вашему объекту жилья id-номер '.$post->ID.' пришел запрос на бронирование. ';
            $message .= 'Для получения детальной информации перейдите в <a href="'.home_url("/profile/orders/").'">Личный кабинет</a>. ';
            $message .='<br>';
            $message .='<br>';
            $message .='С уважением, <br> Команда Krymking.ru';
            $message .= <<<HEREDOC
</p>
                      </td> 
                     </tr> 
                   </table></td> 
                 </tr> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:578px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-bottom:10px;padding-left:20px;padding-right:20px;padding-top:30px;font-size:0"> 
                       <table border="0" width="60%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                         <tr style="border-collapse:collapse"> 
                          <td style="padding:0;Margin:0;border-bottom:1px solid #CCCCCC;background:none;height:1px;width:100%;margin:0px"></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:30px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">С уважением,<br>Команда Krymking.ru</p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#CCCCCC;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px;border-bottom:1px solid #CFE2F3" bgcolor="#cccccc"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" style="Margin:0;padding-bottom:20px;padding-top:25px;padding-left:40px;padding-right:40px"> 
               <!--[if mso]><table style="width:578px" cellpadding="0" cellspacing="0"><tr><td style="width:205px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:185px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Мы в социальных сетях</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;font-size:0"> 
                       <table cellpadding="0" cellspacing="0" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                         <tr style="border-collapse:collapse"> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://www.facebook.com/Krymkingru-106685441598072" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Facebook" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/facebook-rounded-colored.png" alt="Fb" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://www.instagram.com/krymking.ru/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Instagram" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/instagram-rounded-colored.png" alt="Inst" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://vk.com/krymking" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Vkontakte" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/vk-rounded-colored.png" alt="VK" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://ok.ru/krymking.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Odnoklassniki" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/odnoklassniki-rounded-colored.png" alt="Ok" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0"><a target="_blank" href="https://t.me/krymkingru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Telegram" src="https://ifcex.stripocdn.email/content/assets/img/messenger-icons/rounded-colored/telegram-rounded-colored.png" alt="Telegram" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:225px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" class="es-m-p20b" style="padding:0;Margin:0;width:225px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Звоните нам</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px"><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:16px" href="tel:+73652777180">+7 3652 777-180</a></strong></p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:20px"></td><td style="width:128px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:128px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Пишите нам</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px"><strong><a target="_blank" href="mailto:info@krymking.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:16px">info@krymking.ru</a></strong></p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td></tr></table><![endif]--></td> 
             </tr> 
             <tr style="border-collapse:collapse"> 
              <td align="left" style="Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;padding-bottom:35px"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:618px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><h3 style="Margin:0;line-height:19px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:16px;font-style:normal;font-weight:normal;color:#333333"><u><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#178DE0;font-size:16px" href="http://krymking.ru/">Krymking.ru</a></strong>&nbsp;</u>Посуточная аренда жилья в Крыму!</h3></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><h3 style="Margin:0;line-height:16px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:13px;font-style:normal;font-weight:normal;color:#333333">© 2021 ООО Крымкинг. Все права защищены</h3></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-footer es-mobile-hidden" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td align="left" style="padding:20px;Margin:0"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="10" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table></td> 
     </tr> 
   </table> 
  </div>  
 </body>
</html>
HEREDOC;

		}



		//wp_mail($author->user_email, $subject, $message, $headers);
        mail($author->user_email, $subject, $message, $headers);

	}


	if($_POST['send'] == 'payment') {

        $days = days($_POST['check_in'], $_POST['check_out']);
        $price = the_price($_POST['post_id']);

        $prepay = calc_percent(price_total($price, $days));//стоимость с днями
        $prepay_kope = $prepay * 100;//стоимость в копейках

        $date_start = wp_date( 'j F Y', strtotime($_POST['check_in']) );
        $date_end = wp_date( 'j F Y', strtotime($_POST['check_out']) );

        $message = 'Оплата бронирования на сайте Krymking.ru: бронь №'.$post_id.', ';
        $message .= ''.address($_POST['post_id']).', ';
        $message .= 'заезд '.$date_start.' г., выезд '.$date_end.' г. Сумма к оплате '.$prepay.' рублей.';

        //https://www.tinkoff.ru/kassa/develop/api/payments/init-description/ - апи на тинькове
	    $arr['TerminalKey'] = "TinkoffBankTest";
        $arr['Amount'] = $prepay_kope;
        $arr['OrderId'] = $post_id;
        $arr['Description'] = $message;
        //$arr['DATA']['Phone'] = '+71234567890';
        $arr['DATA']['Email'] = $_POST['user_email'];

        $arr['Receipt']['Email'] = $_POST['user_email'];//емейл клиента
        //$arr['Receipt']['Phone'] = "+79031234567";
        $arr['Receipt']['EmailCompany'] = "contact@krymking.ru";//емейл фирмы
        $arr['Receipt']['Taxation'] = "usn_income_outcome";//система налог ображения
        $arr['Receipt']['Items']['Name'] = $_POST['post_id'];//Должно быть название товара
        $arr['Receipt']['Items']['Price'] = $prepay_kope;//цена за еденицу товара в копейках
        $arr['Receipt']['Items']['Quantity'] = '1.00';//Количество или вес товара
        $arr['Receipt']['Items']['Amount'] = $prepay_kope;//стоимость товра в копейках
        $arr['Receipt']['Items']['PaymentMethod'] = 'advance';//оплата аванса
        $arr['Receipt']['Items']['PaymentObject'] = 'service';//передаю за что деньги в данном случае услуга
        $arr['Receipt']['Items']['Tax'] = 'none';//передаю данны о ндс


        $link = 'https://securepay.tinkoff.ru/v2/Init';






        $email = 'test@test.com';
        $emailCompany = 'testCompany@test.com';
        $phone = '89179990000';


        $receiptItem = [[
            'Name'          => 'product1',
            'Price'         => $prepay_kope,
            'Quantity'      => 1,
            'Amount'        => $prepay_kope,
            'PaymentMethod' => 'advance',
            'PaymentObject' => 'service',
            'Tax'           => 'none'
        ]];

        $isShipping = false;

        if (!empty($isShipping[2]['Name'] === 'shipping')) {
            $isShipping = true;
        }

        $enabledTaxation = true;

        $receipt = [
            'EmailCompany' => "contact@krymking.ru",
            'Email'        => $_POST['user_email'],
            'Taxation'     => "usn_income_outcome",
            'Items'        => balanceAmount($isShipping, $receiptItem, $prepay_kope),
        ];


        $params = [
            'OrderId' => $post_id,
            'Amount'  => $prepay_kope,
            'DATA'    => [
                'Email'           => $_POST['user_email'],
                'Connection_type' => 'example'
            ],
        ];

        if ($enabledTaxation) {
            $params['Receipt'] = $receipt;
        }

        $api->init($params);



        header('Location:' . $api->paymentUrl);




/*
		$mrh_pass1 = "zV395RuabF6HbqbmWRJ3";

		$days = days($_POST['check_in'], $_POST['check_out']);
		$price = the_price($_POST['post_id']);
		$prepay = calc_percent(price_total($price, $days));

		$date_start = wp_date( 'j F Y', strtotime($_POST['check_in']) );
		$date_end = wp_date( 'j F Y', strtotime($_POST['check_out']) );

		$message = 'Оплата бронирования на сайте Krymking.ru: бронь №'.$post_id.', ';
		$message .= ''.address($_POST['post_id']).', ';
		$message .= 'заезд '.$date_start.' г., выезд '.$date_end.' г. Сумма к оплате '.$prepay.' рублей.';

		$params = array(
			'MerchantLogin' => 'krymking.ru', // Идентификатор магазина
			'InvId'         => $post_id, // ID заказа
			'Description'   => $message, // Описание заказа (мах 100 символов)
			'OutSum'        => $prepay, // Сумма заказа
			'Culture'       => 'ru',
			'Email'			=> $_POST['user_email'],
			'Encoding'      => 'utf-8',
			'IsTest'        => 1, // Тестовый режим
		);

		// Формирование подписи
		$params['SignatureValue'] = md5("{$params['MerchantLogin']}:{$params['OutSum']}:{$params['InvId']}:{$mrh_pass1}");

		// Перенаправляем пользователя на страницу оплаты
		header('Location: https://auth.robokassa.ru/Merchant/Index.aspx?' . urldecode(http_build_query($params)));
*/
	} elseif ($_POST['send'] == 'request') {
		// Перенаправляем пользователя на страницу запроса

		// Письмо гостю
		$user = get_userdata(get_field('customer', $post_id));
		$post2 = get_field('apartment', $post_id);
	
		$headers2 = 'Content-type: text/html; charset=utf-8'."\r\n".'From: Krymking <info@krymking.ru>';
		$subject2 = 'Запрос на бронирование объекта';
	
		/*$message2 = 'Здравствуйте, '.$user->first_name.' '.$user->last_name.'! ';
		$message2 .= 'Вы запросили бронирование №'.$post_id.' объекта жилья <a href="'.get_permalink($post2).'">'.get_permalink($post2).'</a>. ';
		$message2 .= 'Информация отправлена Владельцу. Вы будете уведомлены о его ответе при помощи электронной почты. ';
		$message2 .= 'При подтверждении дат бронирования Владельцем Вам останется только внести предоплату на сайте, получить ваучер и планировать свое путешествие.';
		$message2 .='<br>';
		$message2 .='<br>';
		$message2 .='С уважением, <br> Команда Krymking.ru';*/

        $message2 = <<<HERE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
 <head> 
  <meta charset="UTF-8"> 
  <meta content="width=device-width, initial-scale=1" name="viewport"> 
  <meta name="x-apple-disable-message-reformatting"> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
  <meta content="telephone=no" name="format-detection"> 
  <title>Крымкинг - забронируй лето!</title> 
  <!--[if (mso 16)]>
    <style type="text/css">
    a {text-decoration: none;}
    </style>
    <![endif]--> 
  <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--> 
  <!--[if gte mso 9]>
<xml>
    <o:OfficeDocumentSettings>
    <o:AllowPNG></o:AllowPNG>
    <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
</xml>
<![endif]--> 
  <style type="text/css">
#outlook a {
	padding:0;
}
.ExternalClass {
	width:100%;
}
.ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
	line-height:100%;
}
.es-button {
	mso-style-priority:100!important;
	text-decoration:none!important;
}
a[x-apple-data-detectors] {
	color:inherit!important;
	text-decoration:none!important;
	font-size:inherit!important;
	font-family:inherit!important;
	font-weight:inherit!important;
	line-height:inherit!important;
}
.es-desk-hidden {
	display:none;
	float:left;
	overflow:hidden;
	width:0;
	max-height:0;
	line-height:0;
	mso-hide:all;
}
[data-ogsb] .es-button {
	border-width:0!important;
	padding:10px 20px 10px 20px!important;
}
.es-button-border:hover a.es-button,
.es-button-border:hover button.es-button {
	background:#e5e5e5!important;
	border-color:#e5e5e5!important;
	color:#000000!important;
}
.es-button-border:hover {
	border-color:#42d159 #42d159 #42d159 #42d159!important;
	background:#e5e5e5!important;
}
td .es-button-border:hover a.es-button-1 {
	background:#2f94cf!important;
	border-color:#2f94cf!important;
	color:#ffffff!important;
}
td .es-button-border-2:hover {
	background:#2f94cf!important;
	border-color:#e5e5e5 #e5e5e5 #e5e5e5 #e5e5e5!important;
}
[data-ogsb] .es-button.es-button-3 {
	padding:5px 20px!important;
}
@media only screen and (max-width:600px) {h1 { font-size:26px!important; text-align:center } h2 { font-size:26px!important; text-align:center } h3 { font-size:13px!important; text-align:center } .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a { font-size:26px!important } .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a { font-size:26px!important } .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a { font-size:13px!important } .es-menu td a { font-size:16px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:16px!important } .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a { font-size:16px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:16px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button, button.es-button { font-size:14px!important; display:inline-block!important } .es-btn-fw { border-width:10px 0px!important; text-align:center!important } .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0!important } .es-m-p0r { padding-right:0!important } .es-m-p0l { padding-left:0!important } .es-m-p0t { padding-top:0!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden { width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } tr.es-desk-hidden { display:table-row!important } table.es-desk-hidden { display:table!important } td.es-desk-menu-hidden { display:table-cell!important } .es-menu td { width:1%!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } .es-m-p5 { padding:5px!important } .es-m-p5t { padding-top:5px!important } .es-m-p5b { padding-bottom:5px!important } .es-m-p5r { padding-right:5px!important } .es-m-p5l { padding-left:5px!important } .es-m-p10 { padding:10px!important } .es-m-p10t { padding-top:10px!important } .es-m-p10b { padding-bottom:10px!important } .es-m-p10r { padding-right:10px!important } .es-m-p10l { padding-left:10px!important } .es-m-p15 { padding:15px!important } .es-m-p15t { padding-top:15px!important } .es-m-p15b { padding-bottom:15px!important } .es-m-p15r { padding-right:15px!important } .es-m-p15l { padding-left:15px!important } .es-m-p20 { padding:20px!important } .es-m-p20t { padding-top:20px!important } .es-m-p20r { padding-right:20px!important } .es-m-p20l { padding-left:20px!important } .es-m-p25 { padding:25px!important } .es-m-p25t { padding-top:25px!important } .es-m-p25b { padding-bottom:25px!important } .es-m-p25r { padding-right:25px!important } .es-m-p25l { padding-left:25px!important } .es-m-p30 { padding:30px!important } .es-m-p30t { padding-top:30px!important } .es-m-p30b { padding-bottom:30px!important } .es-m-p30r { padding-right:30px!important } .es-m-p30l { padding-left:30px!important } .es-m-p35 { padding:35px!important } .es-m-p35t { padding-top:35px!important } .es-m-p35b { padding-bottom:35px!important } .es-m-p35r { padding-right:35px!important } .es-m-p35l { padding-left:35px!important } .es-m-p40 { padding:40px!important } .es-m-p40t { padding-top:40px!important } .es-m-p40b { padding-bottom:40px!important } .es-m-p40r { padding-right:40px!important } .es-m-p40l { padding-left:40px!important } }
</style> 
 </head> 
 <body style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0"> 
  <span style="display:none !important;font-size:0px;line-height:0;color:#FFFFFF;visibility:hidden;opacity:0;height:0;width:0;mso-hide:all">Посуточная аренда жилья в Крыму</span> 
  <div class="es-wrapper-color" style="background-color:#F6F6F6"> 
   <!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="#f6f6f6"></v:fill>
			</v:background>
		<![endif]--> 
   <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top"> 
     <tr style="border-collapse:collapse"> 
      <td valign="top" style="padding:0;Margin:0"> 
       <table cellpadding="0" cellspacing="0" class="es-header es-mobile-hidden" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" bgcolor="#347BA6" style="padding:0;Margin:0;background-color:#347BA6"> 
           <table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td align="left" bgcolor="#347BA6" style="padding:0;Margin:0;padding-bottom:10px;padding-left:20px;padding-right:20px;background-color:#347BA6"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="10" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" bgcolor="#347BA6" style="padding:0;Margin:0;background-color:#347BA6"> 
           <table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;border-top:1px solid #CFE2F3;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" bgcolor="#347BA6" style="Margin:0;padding-top:10px;padding-bottom:15px;padding-left:40px;padding-right:40px;background-color:#347BA6"> 
               <!--[if mso]><table style="width:578px" cellpadding="0"
                            cellspacing="0"><tr><td style="width:419px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td class="es-m-p0r es-m-p20b" valign="top" align="center" style="padding:0;Margin:0;width:419px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="left" class="es-m-txt-c" style="padding:0;Margin:0;padding-top:10px"><h1 class="letter" style="Margin:0;line-height:31px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:26px;font-style:normal;font-weight:normal;color:#FFFFFF;letter-spacing:2px"><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#FFFFFF;font-size:26px;font-family:arial, 'helvetica neue', helvetica, sans-serif" href="http://krymking.ru/">Krymking.ru</a></strong></h1></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:20px"></td><td style="width:139px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:139px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-top:5px"><span class="es-button-border-2 es-button-border" style="border-style:solid;border-color:#FFFFFF;background:#2678A8;border-width:2px;display:inline-block;border-radius:30px;width:auto"><a href="http://krymking.ru/" class="es-button es-button-1" target="_blank" style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:16px;border-style:solid;border-color:#2678A8;border-width:5px 20px;display:inline-block;background:#2678A8;border-radius:30px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-weight:normal;font-style:normal;line-height:19px;width:auto;text-align:center"> 
                         <!--[if !mso]><!-- --><img src="https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/81801622632434671.png" alt="icon" width="24" style="display:inline-block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;vertical-align:middle;margin-right:10px" align="absmiddle"> 
                         <!--<![endif]-->Войти</a></span></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td></tr></table><![endif]--></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px" cellspacing="0" cellpadding="0" align="center"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" background="https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/46571622636492111.jpg" style="Margin:0;padding-bottom:10px;padding-top:40px;padding-left:40px;padding-right:40px;background-image:url(https://ifcex.stripocdn.email/content/guids/CABINET_08c625216b63db66fc33c5214d363eef/images/46571622636492111.jpg);background-repeat:no-repeat;background-position:left top"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:578px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="40" style="padding:0;Margin:0"></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><h1 style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:30px;font-style:normal;font-weight:normal;color:#FFFFFF"><strong>Крымкинг - забронируй лето!</strong></h1></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#FFFFFF;font-size:16px">Посуточная аренда жилья в Крыму</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="40" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-left:1px solid #9FC5E8;border-right:1px solid #9FC5E8;border-top:1px solid #9FC5E8;border-bottom:1px solid #9FC5E8;background-color:#FFFFFF;border-radius:8px" role="presentation"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-bottom:5px;padding-left:10px;padding-right:10px;padding-top:30px"><h1 style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:30px;font-style:normal;font-weight:normal;color:#333333"><strong>Здравствуйте, $user->first_name $user->last_name!</strong></h1></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-top:15px;padding-left:15px;padding-right:15px;padding-bottom:40px">
                        <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">
HERE;
        $message2 .= 'Вы запросили бронирование №'.$post_id.' объекта жилья <a href="'.get_permalink($post2).'">'.get_permalink($post2).'</a>. ';
        $message2 .= 'Информация отправлена Владельцу. Вы будете уведомлены о его ответе при помощи электронной почты. ';
        $message2 .= 'При подтверждении дат бронирования Владельцем Вам останется только внести предоплату на сайте, получить ваучер и планировать свое путешествие.';
        $message2 .='<br>';
        $message2 .='<br>';
        $message2 .='С уважением, <br> Команда Krymking.ru';
        $message .= <<<HEREDOC
</p>
                      </td> 
                     </tr> 
                   </table></td> 
                 </tr> 
                 <tr style="border-collapse:collapse"> 
                  <td align="center" style="padding:0;Margin:0;width:578px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="Margin:0;padding-bottom:10px;padding-left:20px;padding-right:20px;padding-top:30px;font-size:0"> 
                       <table border="0" width="60%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                         <tr style="border-collapse:collapse"> 
                          <td style="padding:0;Margin:0;border-bottom:1px solid #CCCCCC;background:none;height:1px;width:100%;margin:0px"></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:30px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">С уважением,<br>Команда Krymking.ru</p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#CCCCCC;border-right:1px solid #CFE2F3;border-left:1px solid #CFE2F3;width:660px;border-bottom:1px solid #CFE2F3" bgcolor="#cccccc"> 
             <tr style="border-collapse:collapse"> 
              <td class="es-m-p20r es-m-p20l" align="left" style="Margin:0;padding-bottom:20px;padding-top:25px;padding-left:40px;padding-right:40px"> 
               <!--[if mso]><table style="width:578px" cellpadding="0" cellspacing="0"><tr><td style="width:205px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:185px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Мы в социальных сетях</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;font-size:0"> 
                       <table cellpadding="0" cellspacing="0" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                         <tr style="border-collapse:collapse"> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://www.facebook.com/Krymkingru-106685441598072" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Facebook" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/facebook-rounded-colored.png" alt="Fb" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://www.instagram.com/krymking.ru/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Instagram" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/instagram-rounded-colored.png" alt="Inst" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://vk.com/krymking" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Vkontakte" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/vk-rounded-colored.png" alt="VK" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://ok.ru/krymking.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Odnoklassniki" src="https://ifcex.stripocdn.email/content/assets/img/social-icons/rounded-colored/odnoklassniki-rounded-colored.png" alt="Ok" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                          <td align="center" valign="top" style="padding:0;Margin:0"><a target="_blank" href="https://t.me/krymkingru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img title="Telegram" src="https://ifcex.stripocdn.email/content/assets/img/messenger-icons/rounded-colored/telegram-rounded-colored.png" alt="Telegram" width="24" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td> 
                         </tr> 
                       </table></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:225px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" class="es-m-p20b" style="padding:0;Margin:0;width:225px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Звоните нам</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px"><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:16px" href="tel:+73652777180">+7 3652 777-180</a></strong></p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td style="width:20px"></td><td style="width:128px" valign="top"><![endif]--> 
               <table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:128px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">Пишите нам</p></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px"><strong><a target="_blank" href="mailto:info@krymking.ru" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:16px">info@krymking.ru</a></strong></p></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table> 
               <!--[if mso]></td></tr></table><![endif]--></td> 
             </tr> 
             <tr style="border-collapse:collapse"> 
              <td align="left" style="Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;padding-bottom:35px"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:618px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:10px"><h3 style="Margin:0;line-height:19px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:16px;font-style:normal;font-weight:normal;color:#333333"><u><strong><a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#178DE0;font-size:16px" href="http://krymking.ru/">Krymking.ru</a></strong>&nbsp;</u>Посуточная аренда жилья в Крыму!</h3></td> 
                     </tr> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" style="padding:0;Margin:0"><h3 style="Margin:0;line-height:16px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:13px;font-style:normal;font-weight:normal;color:#333333">© 2021 ООО Крымкинг. Все права защищены</h3></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table> 
       <table cellpadding="0" cellspacing="0" class="es-footer es-mobile-hidden" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"> 
         <tr style="border-collapse:collapse"> 
          <td align="center" style="padding:0;Margin:0"> 
           <table class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:660px"> 
             <tr style="border-collapse:collapse"> 
              <td align="left" style="padding:20px;Margin:0"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                 <tr style="border-collapse:collapse"> 
                  <td align="left" style="padding:0;Margin:0;width:620px"> 
                   <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"> 
                     <tr style="border-collapse:collapse"> 
                      <td align="center" height="10" style="padding:0;Margin:0"></td> 
                     </tr> 
                   </table></td> 
                 </tr> 
               </table></td> 
             </tr> 
           </table></td> 
         </tr> 
       </table></td> 
     </tr> 
   </table> 
  </div>  
 </body>
</html>
HEREDOC;
	

		global $current_user;
		$email_to = $current_user->user_email;
		wp_mail($user->user_email, $subject2, $message2, $headers2);	


		wp_redirect( home_url('/request/?order-id='.$post_id.'') ); 
	}



}

get_header();
/* Template Name: Бронирование */
?>
<div class="headLine"></div>

<div class="booking">
	<div class="wrapper">
 
	<? if (!is_user_logged_in() ) { ?>
		<div class="booking-auth-title">
			<span class="link-auth">Войдите в свою учетную запись</span>, чтобы продолжить бронирование.
		</div>
	<? } else { ?>

		<h1 class="page-title">Завершение бронирования и оплата</h1>
		<div class="booking-number">Бронь № 
			<?if ($_GET['booking-id']) {?>
				<?=$_GET['booking-id'];?>
			<? } else { ?>
				<?=SecondLastPostId();?>
			<? } ?>
		</div>
		<div class="page-wrap">
			<div class="booking-hotel">
				<div class="booking-title"><a href="<?=get_permalink($booking_id);?>"><?=get_the_title($booking_id);?></a></div>
				<div class="booking-thumbnail">
					<div class="booking-gallery">
					<?foreach (get_field('gallery', $booking_id) as $image) {?>
						<a href="<?=$image['url'];?>" data-fancybox="gallery">
							<img src="<?=$image['sizes']['large'];?>">
						</a>
					<?}?>
					</div>
					<div class="counts-slides"><span class="current">1</span> / <span><?=count(get_field('gallery', $booking_id));?></span></div>
				</div>
				<div class="booking-type">
					<div class="hotel-name"><a href="<?=get_permalink($booking_id);?>"><?=rooms_type(get_field('rooms_count', $booking_id));?></a></div>
					<div class="hotel-address"><?=hotel_address($booking_id);?>, ул. <?=get_field('street', $booking_id);?>, <?=get_field('house', $booking_id);?></div>
				</div>
				<div class="flexbox">
					<div class="booking-date">
						<div class="dates-title">Заезд</div>
						<div class="date"><?=getWeekday($check_in);?>, <?=$check_in;?>, с 12:00</div>
					</div>
					<div class="booking-date">
						<div class="dates-title">Выезд</div>
						<div class="date"><?=getWeekday($check_out);?>, <?=$check_out;?>, с 12:00</div>
					</div>
				</div>
				<div class="booking-guests flexbox">Общая длительность проживания <span><?=num_word($days, array("сутки","суток","суток") );?></span></div>
				<div class="booking-guests flexbox">Количество гостей <span><?=guests();?></span></div>
				<ul class="booking-details">
					<li><strong>Итого за <?=$days;?> суток:</strong> <strong><?=price_total($price, $days);?> RUB</strong></li>
					<li class="payment-calc"><span><?=$price;?> RUB * <?=$days;?> суток</span> <span><?=price_total($price, $days);?> RUB</span></li>
				</ul>
				<div class="btn-calc">Скрыть расчёт</div>
		 		<ul class="booking-details">
					<li><span>Предоплата</span> <strong><?=calc_percent(price_total($price, $days));?> RUB</strong></li>
					<li><span>Оплата при заселении</span> <strong><?=price_total($price, $days)-calc_percent(price_total($price, $days));?> RUB</strong></li>
				</ul>
				<div class="booking-commissions">Без комиссий и сборов</div>
<!-- 				<div class="btn btn-close">Отменить бронирование</div> -->
			</div>


            <form method="POST" enctype="multipart/form-data" class="bookingForm">

				<input type="hidden" name="post_id" value="<?=$booking_id;?>">
				<input type="hidden" name="check_in" value="<?=$check_in;?>">
				<input type="hidden" name="check_out" value="<?=$check_out;?>">
				<input type="hidden" name="guests" value="<?=guests();?>">

				<?if (!is_user_logged_in() ) {?>
					<div class="booking-title">Введите свои данные</div>
				<? } else { ?>
					<div class="booking-title">Ваши данные</div>
				<? } ?>
				<?if (!is_user_logged_in() ) {?>
				<div class="booking-auth"><span class="link-auth">Войдите в свою учетную запись</span>, чтобы использовать сохранённые данные, или <br> <span class="link-registr">зарегистрируйтесь</span>, чтобы управлять бронированием</div>
				<div class="booking-info">Если у Вас нет учётной записи и Вы не хотите зарегистрироваться <br> сейчас, просто заполните обязательные поля для бронирования</div>
				<?}?>
				<div class="flexbox">
					<div class="input-group">
						<label class="input-title">Имя<span class="required">*</span> <span class="required-text">(обязательно к  заполнению)</span></label>
						<input type="text" name="user_name" value="<?=$current_user->user_firstname;?>" class="form-control" required>
					</div>
					<div class="input-group">
						<label class="input-title">Фамилия<span class="required">*</span> <span class="required-text">(обязательно к  заполнению)</span></label>
						<input type="text" name="user_lastname" value="<?=$current_user->user_lastname;?>" class="form-control" required>
					</div>
				</div>
				<div class="input-group">
					<label class="input-title">Электронная почта<span class="required">*</span> <span class="required-text">(обязательно к  заполнению)</span></label>
					<input type="email" name="user_email" class="form-control" value="<?=$current_user->user_email;?>" placeholder="Убедитесь, что вводите без опечаток" required>
				</div>
				<div class="input-group">
					<label class="input-title">Подтвердите адрес электронной почты<span class="required">*</span> <span class="required-text">(обязательно к  заполнению)</span></label>
					<input type="email" name="confirm_email" value="<?=$current_user->user_email;?>" class="form-control" required>
					<span class="text-hint">На этот адрес будет отправлено подтверждение бронирования</span>
				</div>
				<div class="input-group">
					<label class="input-title">Ваш номер телефона <span class="required-text">(необязательно к заполнению)</span></label>
					<select name="country" class="form-control">
						<option value="ru" selected>Россия</option>
						<option value="ua">Украина</option>
						<option value="kz">Казахстан</option>
						<option value="by">Беларусь</option>
						<option value="tj">Таджикистан</option>
						<option value="uz">Узбекистан</option>
						<option value="am">Армения</option>
						<option value="az">Азербайджан</option>
						<option value="kg">Кыргызстан</option>
						<option value="md">Молдова</option>
						<option value="tm">Туркменистан</option>
					</select>
					<input type="tel" name="phone" value="<?=the_field('phone', 'user_' .get_current_user_id());?>" class="form-control" autocomplete="off" required>
				</div>

				<div class="booking-info title-commnet">Ответьте на несколько простых вопросов, которые помогут <br> Вам получить лучший сервис</div>

				<div class="input-group flex-radio">
					<label class="input-title">Кто основной гость?</label>
					<label class="custom-radio form-control">
						<input type="radio" name="main_guest" value="Я" checked="">
						<div class="label-name">Я</div>
					</label>
					<label class="custom-radio form-control">
						<input type="radio" name="main_guest" value="Другой человек">
						<div class="label-name">Другой человек</div>
					</label>
				</div>

				<div class="main-guest">
					<div class="input-group">
						<label class="input-title">Имя<span class="required">*</span></label>
						<input type="text" name="other_name" class="form-control">
					</div>
					<div class="input-group">
						<label class="input-title">Фамилия<span class="required">*</span></label>
						<input type="text" name="other_lastname" class="form-control">
					</div>
					<div class="input-group">
						<label class="input-title">Электронная почта</label>
						<input type="email" name="other_email" class="form-control" placeholder="Убедитесь, что вводите без опечаток">
					</div>
					<p class="text-hint">Мы отправляем на электронную почту только информацию о бронировании</p>
				</div>
				
				<div class="input-group">
					<label class="input-title">Расскажите владельцу о себе, какая цель поездки, с кем Вы путешествуете?</label>
					<textarea name="comment" class="form-control"></textarea>
				</div>
				<div class="booking-info">Сообщите Владельцу предполагаемое время Заезда</div>

				<div class="input-title title-main">Ваш номер будет готов в дату заезда с <?=get_field('check_in_time', $booking_id)['label'];?></div>

				<div class="input-group flexbox align-items">
					<label class="input-title">Время прибытия</label>
					<input type="datetime-local" name="time_arrival" class="form-control">
				</div>
				<div class="input-title title-main">Правила проживания</div>
				<div class="booking-attributes">
					<ul>
					<? foreach (acf_get_fields(348) as $param) { ?>
						<?if (get_field_object($param['key'], $booking_id)['type'] == 'select') {?>
							<li><strong><?=get_field_object($param['key'], $booking_id)['label'];?>:</strong> <?=get_field_object($param['key'], $booking_id)['value']['value'];?></li>
						<? } else { ?>
							<li><strong><?=get_field_object($param['key'], $booking_id)['label'];?>:</strong> <?=get_field_object($param['key'], $booking_id)['value'];?></li>
						<? } ?>
					<? } ?>
					</ul>
				</div>

				<div class="text-agreement">
					<label class="custom-checkbox"><input type="checkbox" name="agreement" class="custom-input" checked=""><div class="check"></div></label>
					<span class="text">Нажимая на кнопку 
						
				<? if(get_field('fast_booking', $booking_id) == 'Включить' || get_post_status( $_GET['booking-id'] ) == 'confirmed') { ?>
					«Внести предоплату»
				<? } else { ?>
					«Запросить бронирование»
				<? } ?>
					
					, Вы соглашаетесь с <span>Правилами проживания</span> и <a href="/gostyam/pravila-otmeny-bronirovaniya/" target="_blank">Правилами отмены бронирования</a></span>
				</div>
				<div class="input-group">
				
				<? if(get_field('fast_booking', $booking_id) == 'Включить' || get_post_status( $_GET['booking-id'] ) == 'confirmed') { ?>
					<button type="submit" class="btn btn-submit">Внести предоплату</button>
					<input type="hidden" name="send" value="payment">
				<? } else { ?>
					<button type="submit" class="btn btn-submit">Запросить бронирование</button>
					<input type="hidden" name="send" value="request">
				<? } ?>
				</div>
			</form>
		</div>
		<? } ?>
	</div>
</div>


<?php
get_footer();