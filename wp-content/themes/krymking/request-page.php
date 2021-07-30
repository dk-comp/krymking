<?php
get_header();
/* Template Name: Запрос */
$url = get_template_directory_uri();
?>
<div class="headLine"></div>

<div class="wrapper">
	<?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
 	<h1 class="page-title"><?=the_title();?></h1>
    <div class="success-text">Вы запросили бронирование №<?=$_GET['order-id'];?>. Информация отправлена Владельцу. Вы будете уведомлены о его ответе при помощи электронной почты. При подтверждении дат бронирования Владельцем Вам останется только внести предоплату на сайте, получить ваучер и планировать свое путешествие. <a href="<?=home_url("/profile/travel/");?>" class="btn">Мои бронирования</a></div>
</div>

<?php
/*


$post_id = wp_insert_post(  wp_slash( array(
	'post_status'   => $status,
	'post_type'     => 'orders',
	'post_title'    => 'Бронирование №'. SecondLastPostId(),
	'post_content'  => '',
	'post_author'   => get_post($_POST['post_id'])->post_author,
	'ping_status'   => get_option('default_ping_status'),
	'meta_input'    => [ 'meta_key'=>'meta_value' ],
) ) );


// Письмо гостю
$user = get_userdata(get_field('customer', $post_id));
$post2 = get_field('apartment', $post_id);

$headers2 = 'Content-type: text/html; charset=utf-8'."\r\n".'From: Krymking <info@krymking.ru>';
$subject2 = 'Запрос на бронирование объекта';


$messageContent = 'Вы запросили бронирование №'.$post_id.' объекта жилья <a href="'.get_permalink($post2).'">'.get_permalink($post2).'</a>. ';
$messageContent .= 'Информация отправлена Владельцу. Вы будете уведомлены о его ответе при помощи электронной почты. ';
$messageContent .= 'При подтверждении дат бронирования Владельцем Вам останется только внести предоплату на сайте, получить ваучер и планировать свое путешествие.';
$messageContent .='<br>';
$messageContent .='<br>';
$messageContent .='С уважением, <br> Команда Krymking.ru';


global $current_user;
$email_to = $current_user->user_email;

$message3 = include $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/'. get_template() .'/back/mail_template.php';

$message3 = str_replace('<<MAILCONTENT>>', $messageContent, $message3);
$message3 = str_replace('<<FIRSTNAME>>', $user->first_name, $message3);
$message = str_replace('<<LASTNAME>>', $user->last_name, $message3);



if(!wp_mail($email_to, $subject2, $message, $headers2)){

	mail($email_to, $subject2, $message, $headers2);

}*/

get_footer();