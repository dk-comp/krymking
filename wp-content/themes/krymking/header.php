<?php
if( !is_front_page() ) {
   $static = "static";
}
global $current_user;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<!-- <meta name="viewport" content="width=1920"> -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
	<script src="//api-maps.yandex.ru/2.1/?apikey=d5b0c5c9-4525-425f-b488-0871669beee0&lang=ru_RU&csp=true" type="text/javascript"></script>
</head>

<body <?php body_class(); ?>>

<header class="<?=$static?>">
	<div class="wrapper">
		<?php if ( is_user_logged_in() ) {?>
			<div class="header-left">
				<div class="logo">
					<a href="/">Krymking.ru</a>
					<div class="logo-text">Посуточная аренда жилья <br> в Крыму</div>
				</div>
				<a href="/hotels/" class="btn"><span class="text-btn">Найти жильё</span> <i class="icon-search"></i></a>
			</div>
			
			<div class="header-right">
				<a href="<?=home_url('/profile/add/');?>" class="btn"><span class="text-btn">Сдать жильё</span> <i class="icon-house"></i></a>
				<div class="btn btn-help"><span class="text-btn">Помощь</span> <i class="icon-help"></i></div>
				<a href="/favorites/" class="btn btn-wishlist"><span class="text-btn">Избранное</span> <i class="icon-wishlist"></i></a>
				<a href="/messages/" class="btn btn-message"><span class="text-btn">Сообщения</span> <i class="icon-message"></i></a>
				<div class="user-name"><div class="avatar"><?=user_photo($current_user);?></div><span class="text-btn"><?=$current_user->display_name;?></span></div>
			</div>
		<?php } else {?>
			<div class="logo">
				<a href="/">Krymking.ru</a>
				<div class="logo-text">Посуточная аренда жилья <br> в Крыму</div>
			</div>
			<div class="btn btn-login">
				<span class="text-btn">Сдать жильё</span>
				<i class="icon-house"></i>
			</div>

			<div class="currency">
				<span class="btn btn-currency">rub</span>
				<div class="btn flag flag-russia"></div>
			</div>
			<div class="btn btn-register"><span class="text-btn">Зарегистрироваться</span> <i class="icon-register"></i></div>
			<div class="btn btn-login"><span class="text-btn">Войти</span> <i class="icon-login"></i></div>
			<div class="btn btn-help"><span class="text-btn">Помощь</span> <i class="icon-help"></i></div>
			<div class="phone">
				<a href="tel:<?=preg_replace('/[^0-9]/', '', get_field('phone', 'options'));?>" class="btn"><?=the_field('phone', 'options');?></a>
				<div class="phone-text">Подберём, бесплатно <br> забронируем</div>
				<div class="arrow"></div>
			</div>
		<?php } ?>

	</div>
</header>

<div class="popup popup-guests">
	<div class="popup-content">
		<div class="guestsClose"><span></span></div>
		<div class="popup-head">Количество гостей</div>
		<ul class="guests-list">
			<li class="guest-item adults">
				<div class="guest-name">Взрослые <span>возраст от 13 лет</span></div>
				<div class="quantity"><div class="btn-controls btn-minus">-</div><input type="text" name="quantity" value="<?=guests_adults();?>" class="value" readonly="readonly"><div class="btn-controls btn-plus">+</div></div>
			</li>
			<li class="guest-item children">
				<div class="guest-name">Дети <span>возраст от 2 до 13 лет</span></div>
				<div class="quantity"><div class="btn-controls btn-minus">-</div><input type="text" name="quantity" value="<?=guests_childrens();?>" class="value" readonly="readonly"><div class="btn-controls btn-plus">+</div></div>
			</li>
			<li class="guest-item babies">
				<div class="guest-name">Младенцы <span>возраст до 2 лет</span></div>
				<div class="quantity"><div class="btn-controls btn-minus">-</div><input type="text" name="quantity" value="<?=guests_babies();?>" class="value" readonly="readonly"><div class="btn-controls btn-plus">+</div></div>
			</li>
		</ul>
	</div>
</div>

<script>
	$('.guestsClose').click(function(){
		$('.popup-guests').css('display', 'none');
	});
</script>

<div class="popup popup-help">
	<div class="popup-content">
		<ul class="menu">
			<li><a href="<?=home_url('/gostyam/');?>">Гостям</a></li>
			<li><a href="<?=home_url('/vladelczam/');?>">Владельцам</a></li>
			<li><a href="<?=home_url('/support/');?>">Служба поддержки</a></li>
		</ul>
	</div>
</div>

<?php if ( !is_user_logged_in() ) { ?>
	
	<?php get_template_part('front/auth');?><?php get_template_part('front/register');?>

	<div class="popup popup-tel">
		<div class="popup-content">
			<ul class="menu">
				<li><a href="tel:<?=preg_replace('/[^0-9]/', '', get_field('phone2', 'options'));?>"><span class="phone1"><?=the_field('phone2', 'options');?></span></a></li>
				<li><a href="tel:<?=preg_replace('/[^0-9]/', '', get_field('phone', 'options'));?>"><span><?=the_field('phone', 'options');?></span></a></li>
			</ul>
		</div>
	</div>

<?php } else { ?>

	<div class="popup popup-lk">
		<div class="popup-content">
			<ul class="menu">
				<li><a href="<?=home_url('/profile/');?>">Мой личный кабинет</a></li>
				<li><a href="<?=home_url('/settings/');?>">Настройки</a></li>
				<li><a href="<?=wp_logout_url(home_url());?>">Выйти</a></li>
			</ul>
		</div>
	</div>

<?php } ?>
<?php global $_CATEGORIES?>

<?php if(!empty($_CATEGORIES)):?>
	<?php

	global $_TAXONOMIES;
	
	$_TAXONOMIES = get_terms( array(
       'taxonomy' => 'hotel',
       'hide_empty' => false,
       )
	);

    $_TYPES = get_terms([
        'taxonomy' => 'type',
        'hide_empty' => false,
    ]);

    global $_CATEGORIES_FULL;
	$_CATEGORIES_FULL = [];

    if(!empty($_TYPES)){

        foreach ($_TYPES as $item){

            if(isset($_CATEGORIES[$item->term_id])){

                $_CATEGORIES_FULL[$item->term_id] = $item;

                $_CATEGORIES_FULL[$item->term_id]->POSTS = [];

                $args = array(
                    'post_type' => 'hotels',
                    'type'=> $item->slug,
                    'numposts' => 0
                );

                $query = new WP_Query;

                $data = $query->query($args);

                if($data){

                    foreach ($data as $value){

                        $_CATEGORIES_FULL[$item->term_id]->POSTS[$value->ID] = $value;

                    }

                }

            }

        }

    }

	$_TAXONOMIES_POPULAR = [];

	global $_TAXONOMIES_POPULAR;
	
	foreach($_TAXONOMIES as $key => $term){

		$args = array(
				'post_type' => 'hotels',
				'hotel'=> $term->slug,
				'numposts' => 0
		);
		
		$query = new WP_Query;
		
		$Aposts = $query->query($args);

		$postsByIds = [];

		if($Aposts){

		    foreach ($Aposts as $item){

		        $postsByIds[$item->ID] = $item;

            }

        }

        $_TAXONOMIES[$key]->objectsCount = count($Aposts);

        $_TAXONOMIES[$key]->objectsCountParts = [];

		foreach ($_CATEGORIES_FULL as $id => $item){

		    if(!isset($_TAXONOMIES[$key]->objectsCountParts[$id])) $_TAXONOMIES[$key]->objectsCountParts[$id] = 0;

		    foreach ($item->POSTS as $k => $v){

		        if(isset($postsByIds[$k])) $_TAXONOMIES[$key]->objectsCountParts[$id]++;

            }

        }
		
		$_TAXONOMIES_POPULAR[$term->term_id] = $_TAXONOMIES[$key];
		
	}
	
	?>
	
	<?php if(!empty($_TAXONOMIES)):?>

		<?php foreach($_CATEGORIES as $catId => $catName):?>

			<div class="popup popup-objects popup-<?=$catId?>">
				<div class="popup-content">
					<div class="popup-row">

						<?php foreach($_TAXONOMIES as $category){
							
							if($category->parent == 0){
								 ?>
								<div class="popup-item">
									<h3><?=$category->name?></h3>
									<ul class="city-list">
										<?php if($category->term_id !== 9) {?>
											<li><a href="<?=get_category_link($category->term_id)?>?type=<?=$catId?>"><?=$category->name?>(<?=$category->objectsCountParts[$catId]?>)</a></li>
											<?php
										}?>
										<?php foreach($_TAXONOMIES as $subcategory){?><?php if($subcategory->parent == $category->term_id){?>
											<li><a href="<?=get_category_link($subcategory->term_id);?>?type=<?=$catId?>"><?=$subcategory->name;?>(<?=$subcategory->objectsCountParts[$catId]?>)</a></li>
										<?php } ?><?php } ?>
									</ul>
								</div>
								<?php
							}
						}?>
					</div>
				</div>
			</div>
		<?php endforeach;?>
		<?php //$a=1?>
	<?php endif;?>
<?php endif;?>
 
<div class="popup popup-currency">
	<div class="popup-content">
		<ul class="currency-list">
			<li class="active">rub - российский рубль</li>
			<li>usd - доллар США</li>
			<li>eur - евро</li>
			<li>uah - украинская гривна</li>
		</ul>
	</div>
</div>

<div class="popup popup-language">
	<div class="popup-content">
		<ul class="language-list">
			<li class="active"><div class="flag-russia"></div> <span>русский</span></li>
			<li><div class="flag-eng"></div> <span>английский</span></li>
		</ul>
	</div>
</div>


<div class="popup popup-forgot">
	<div class="popup-content">
		<div class="popup-title">Восстановление пароля <div class="popup-close"></div></div>
		<div class="forgot-content">
			<div class="forgot-title">Введите адрес электронной почты или телефон, <br> связанный с Вашей учётной записью</div>
			<div class="input-group">
				<label class="input-title">Введите свой e-mail или телефон</label>
				<input type="text" name="forgot" class="form-control">
			</div>
			<div class="btn btn-forgot">Восстановить пароль</div>
			<!-- <div class="forgot-text">Мы позвоним Вам на номер +7 (978) 123-45-67,<br> определите последние 5 цифр номера из входящего <br> вызова и используйте их как пароль</div>
			<div class="input-group">
				<label class="input-title">Введите пароль</label>
				<input type="password" name="password" class="form-control">
				<div class="btn-visibility"></div>
			</div>
			<div class="input-group">
				<input type="submit" value="Войти" class="btn btn-submit">
			</div> -->
			<div class="text-register">Нет учётной записи? <span>Зарегистрироваться</span></div>
		</div>
	</div>
</div>

<div class="modal-form" id="registration-message">
	<div class="modal-content">
		<div class="modal-title">Спасибо за регистрацию! На Ваш электронный адрес отправлен запрос о подтверждении Ваших действий. В своем электронном почтовом ящике откройте письмо от krymking.ru и подтвердите регистрацию. Всего наилучшего!</div>
	</div>
</div>

<div class="popup popup-phone">
	<div class="popup-content">
		 
	</div>
</div>


<div class="overlay"></div>
 