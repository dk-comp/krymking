﻿<?
global $postid;

// Основные характеристики
$main_attr[] = get_field_object('field_5fd719b126a08', $postid); // Количество принимаемых гостей
$main_attr[] = get_field_object('field_5fd71ec9f88af', $postid); // Площадь
$main_attr[] = get_field_object('field_6001a30f081a5', $postid); // Этаж
$main_attr[] = get_field_object('field_5fd719f332ddb', $postid); // Количество комнат
$main_attr[] = get_field_object('field_6001a7c1076ab', $postid); // Одноместных кроватей
$main_attr[] = get_field_object('field_6001a8017ca4b', $postid); // Двухместных кроватей
$main_attr[] = get_field_object('field_5fd71e630cd1f', $postid); // Всего спальных мест
$main_attr[] = get_field_object('field_60019e3645d2e', $postid); // Количество санузлов всего
$main_attr[] = get_field_object('field_5fd7273e11a21', $postid); // Количество ванных комнат
$main_attr[] = get_field_object('field_6064531334137', $postid); // Количество туалетов
$main_attr[] = get_field_object('field_5fd71f7ce412a', $postid); // Расстояние до моря

// Ценообразование
$pricing[] = get_field_object('field_6004abfa51380', $postid); // Валюта
$pricing[] = get_field_object('field_6004af999969f', $postid); // Минимальный срок бронирования
$pricing[] = get_field_object('field_5fd7269bfbee0', $postid); // Цена за сутки
$pricing[] = get_field_object('field_6004b20a63cb8', $postid); // Цены с учетом скидок

$location[] = get_field_object('field_600714f654d78', $postid); // Выберите тип улицы
$location[] = get_field_object('field_5fe2e279d947c', $postid); // Улица
$location[] = get_field_object('field_5fe2e299d947d', $postid); // Дом
$location[] = get_field_object('field_600715e070e8b', $postid); // Квартира
$location[] = get_field_object('field_5fe2e2ebd947e', $postid); // Корпус

$disabilities[] = get_field_object('field_600919d84971f', $postid);

//foreach (acf_get_fields(310) as $field) {
//	$general[] = get_field_object($field['key'], $postid);
// }
// foreach (acf_get_fields(311) as $field) { Закоментил 24052020
//	$bathrooms[] = get_field_object($field['key'], $postid);
// }
$general[] = get_field_object('field_60057524eebd4', $postid); // Парковка
$general[] = get_field_object('field_6005774f41bef', $postid); // Интернет
$general[] = get_field_object('field_6008af39ef781', $postid); // Кухня(перенесено из кухни)
$general[] = get_field_object('field_600577ac4928d', $postid); // Телевизор
$general[] = get_field_object('field_602eb1bce5d78', $postid); // Кондиционер
$general[] = get_field_object('field_60ab8ec14b87e', $postid); // Кондиционер количество
$general[] = get_field_object('field_60057b98865b5', $postid); // Горячая вода
$general[] = get_field_object('field_60057e268a6b6', $postid); // Отопление
$general[] = get_field_object('field_60057e562e1bf', $postid); // Балкон\лоджия
$general[] = get_field_object('field_60057e7c2e1c0', $postid); // Терраса
$general[] = get_field_object('field_60057ee7e2ef0', $postid); // Видовые характеристики
$general[] = get_field_object('field_60057f45e2ef1', $postid); // Сейф
$general[] = get_field_object('field_600581180da66', $postid); // Домофон
//$general[] = get_field_object('field_60792c963e8bd', $postid); // Лифт
$general[] = get_field_object('field_6005812c0da67', $postid); // Пожарная сигнализация
$general[] = get_field_object('field_600581490da68', $postid); // Система анти потоп
$general[] = get_field_object('field_6005816d0da69', $postid); // Постельное белье
$general[] = get_field_object('field_602eb23e2c2b3', $postid); // Ремонт
$general[] = get_field_object('field_602eb45c27c38', $postid); // Охранная система
$general[] = get_field_object('field_602eb4cae6289', $postid); // Уборка
$general[] = get_field_object('field_6005818e0da6a', $postid); // Отчетные документы

// $bathrooms[] = get_field_object('field_6005842ba1203', $postid); // Количество санузлов
// $bathrooms[] = get_field_object('field_6064531334137', $postid); // Количество туалетов
$bathrooms[] = get_field_object('field_602eb59a422c0', $postid); // Ванная комната
$bathrooms[] = get_field_object('field_600586fd05d50', $postid); // Ванна
$bathrooms[] = get_field_object('field_600587ab94792', $postid); // Душевая кабина
$bathrooms[] = get_field_object('field_600587c1504ef', $postid); // Биде
$bathrooms[] = get_field_object('field_600587f6fc36d', $postid); // Гигиенический душ
$bathrooms[] = get_field_object('field_600588127403a', $postid); // Джакузи
$bathrooms[] = get_field_object('field_6005883805012', $postid); // Сауна\/баня
$bathrooms[] = get_field_object('field_6005884d05013', $postid); // Водонагреватель электрический
$bathrooms[] = get_field_object('field_600588b37407d', $postid); // Водонагреватель газовый
$bathrooms[] = get_field_object('field_600588e17407e', $postid); // Стиральная машина
$bathrooms[] = get_field_object('field_600588f97407f', $postid); // Сушильная машина
$bathrooms[] = get_field_object('field_6005ad2151be6', $postid); // Сушка для белья
$bathrooms[] = get_field_object('field_6005ad4551be7', $postid); // Фен
$bathrooms[] = get_field_object('field_6005ad5a51be8', $postid); // Полотенца
$bathrooms[] = get_field_object('field_6005ad6f51be9', $postid); // Халат
$bathrooms[] = get_field_object('field_6005ad8651bea', $postid); // Тапочки
$bathrooms[] = get_field_object('field_6005ada851beb', $postid); // Туалетные принадлежности

// foreach (acf_get_fields(371) as $field) {
//	$kitchen[] = get_field_object($field['key'], $postid);
// }
// $kitchen[] = get_field_object('field_6008af39ef781', $postid); Кухня
$kitchen[] = get_field_object('field_6008af7aef782', $postid);
$kitchen[] = get_field_object('field_6008af9def783', $postid);
$kitchen[] = get_field_object('field_6008afb8ef784', $postid);
$kitchen[] = get_field_object('field_6008aff6ef785', $postid);
$kitchen[] = get_field_object('field_6008b009ef786', $postid);
$kitchen[] = get_field_object('field_6008b02bef787', $postid);
$kitchen[] = get_field_object('field_6008b045ef788', $postid);
$kitchen[] = get_field_object('field_6008b058ef789', $postid);
$kitchen[] = get_field_object('field_6008b069ef78a', $postid);
$kitchen[] = get_field_object('field_6008b07def78b', $postid);
$kitchen[] = get_field_object('field_6008b097ef78c', $postid);
$kitchen[] = get_field_object('field_6008b0a4ef78d', $postid);

// foreach (acf_get_fields(332) as $field) {
//	$rooms[] = get_field_object($field['key'], $postid);
// }
$rooms[] = get_field_object('field_600693ee44d7e', $postid);
// $rooms[] = get_field_object('field_6006946744d80', $postid);
// $rooms[] = get_field_object('field_6006947e44d81', $postid);
$rooms[] = get_field_object('field_6006949b44d82', $postid);
$rooms[] = get_field_object('field_600694bd44d83', $postid);
$rooms[] = get_field_object('field_600694d244d84', $postid);
$rooms[] = get_field_object('field_6006951344d85', $postid);
$rooms[] = get_field_object('field_6006953444d86', $postid);
$rooms[] = get_field_object('field_6006954a44d87', $postid);
$rooms[] = get_field_object('field_6006956744d88', $postid);
$rooms[] = get_field_object('field_6006957744d89', $postid);
$rooms[] = get_field_object('field_6006958f44d8a', $postid);
$rooms[] = get_field_object('field_6006959c44d8b', $postid);


foreach (acf_get_fields(385) as $field) {
	$child[] = get_field_object($field['key'], $postid);
}
foreach (acf_get_fields(390) as $field) {
	$territory[] = get_field_object($field['key'], $postid);
}
foreach (acf_get_fields(410) as $field) {
	$infrastr[] = get_field_object($field['key'], $postid);
}
foreach (acf_get_fields(412) as $field) {
	$services[] = get_field_object($field['key'], $postid);
}
foreach (acf_get_fields(348) as $field) {
	$pravila[] = get_field_object($field['key'], $postid);
}
foreach (acf_get_fields(357) as $field) {
	$prices[] = get_field_object($field['key'], $postid);
}

// Регион и город
if( term_children($postid) == 0 ) {
	$terms = get_the_terms( $post_id, 'hotel' );
	if( $terms ){
		$term = array_shift( $terms );
	
		$region = $term->term_id;
		$city = $term->term_id;
	}
} else {
	$city = term_children($postid);
	$region = wp_get_term_taxonomy_parent_id( $city, 'hotel' );
}
 
if ($postid) {
	$title = get_the_title($postid);
} else {
	$title = '';
}
?>

<?include_once( get_template_directory() . '/front/profile-location.php' );?>

<div class="form-section" id="options">
	<div class="heading">Основные характеристики</div>
	<div class="form-group section-gray">
		<?=main_fields($main_attr);?>
	</div>

	<div class="heading show-more">Общие удобства </div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($general);?>
	</div>

	<div class="heading">Санузлы</div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($bathrooms);?>
	</div>

	<div class="heading">Кухня</div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($kitchen);?>
	</div>

	<div class="heading">Комнаты</div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($rooms);?>
	</div>

	<div class="heading">Детям</div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($child);?>
	</div>

	<div class="heading">На территории</div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($territory);?>
	</div>

	<div class="heading">Доступная среда</div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($disabilities);?>
	</div>

	<div class="heading">Инфраструктура</div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($infrastr);?>
	</div>

	<div class="heading">Услуги</div>
	<div class="form-group section-gray hide-param">
		<?=main_fields($services);?>
	</div>
</div>

<div class="form-section" id="desc">
	<div class="heading">Описание и название</div>
	<div class="form-group section-gray">
		<div class="input-group full">
			<input type="text" name="apartment_name" placeholder="Введите название объекта, отражающее уникальность жилья" value="<?=$title;?>" maxlength="59" required class="form-control">
			<div class="count">59</div>
		</div>
		<div class="create-title">Расскажите гостям о своем жилье! Чем оно на Ваш взгляд примечательно? Какие его особенности? Чем интересен Ваш район? Какие услуги Вы предоставляете дополнительно?</div>
		<div class="input-group textarea-group full">
			<div class="textarea-field">
			<textarea name="description" placeholder="Описание объекта жилья, предоставленное его Владельцем" maxlength="500" rows="8" required class="form-control textarea"><?=get_post($postid)->post_content;?></textarea>
			<div class="count">500</div>
			</div>
		</div>
	</div>
</div>

<div class="form-section" id="rules">
	<div class="heading">Правила проживания</div>
	<div class="form-group section-gray">
		<?=main_fields($pravila);?>
	</div>
</div>

<div class="form-section" id="pricing">
	<div class="heading">Ценообразование</div>
	<div class="form-group section-gray">
		<?=main_fields($prices);?>
	</div>
	<div class="heading">Цены по датам</div>
	
	<div class="section-gray calendar-wrap">
		<div class="calendar-left">
			<div class="load-prices">

				<?=pricePeriod($postid);?>

			</div>
		</div>
		<div class="calendar-right">
			<div class="input-group group-vertical">
				<div class="input-title">С</div>
				<input type="text" name="date_from" value="<?=date("d.m.Y");?>" class="calendar-season form-control">
			</div>
			<div class="input-group group-vertical">
				<div class="input-title">До</div>
				<input type="text" name="date_end" value="<?=date("d.m.Y");?>" class="calendar-season form-control">
			</div>
			<div class="input-group group-vertical">
				<div class="input-title">Цена за сутки</div>
				<input type="number" name="price_day" placeholder="<?=the_field('price', $postid);?>" value="<?=the_field('price', $postid);?>" class="form-control">
			</div>
			<div class="btn btn-save">Сохранить</div>
		</div>
	</div>
</div>

<div class="form-section" id="calendar">
	<div class="heading">Календарь и свободные даты</div>
	<div class="section-gray section-border">
		<div class="create-title">Настройте свободные даты в Календаре</div>
		<div class="create-info">Гости могут бронировать свободные даты до 12 месяцев вперед</div>
		<div class="marker-info">
			<div class="marker"><span class="circle circle-busy"></span> занято</div>
			<div class="marker"><span class="circle circle-free"></span> свободно</div>
		</div>
		<div class="create-text">Чтобы не получать заявки о бронировании на занятые даты, перечеркните в календаре дни, когда заселение невозможно</div>
		<div class="calendar-flex">
			<div class="calendar-left">
				<div class="calendar-header"></div>

				<div class="ajax ajax-date"><?=dates_free($postid);?></div>
				<textarea name="multipleDate"></textarea>
			</div>
			<!-- <div class="calendar-right">
				<div class="calendar-item calendar-text">Свяжите календарь своего объекта для синхронизации с календарями этого объекта на других сайтах</div>
				<div class="calendar-item calendar-title">Связать календари</div>
				<div class="calendar-item calendar-text">Чтобы бронирование с Krymking попадали на другие сайты, вставьте у них ссылку на этот календарь объекта</div>
				<div class="calendar-item flexbox">
					<div class="input-group">
						<input type="text" name="calendar_link " placeholder="Ссылка Krymking" class="form-control">
					</div>
					<div class="btn">Копировать</div>
				</div>
				<div class="calendar-item calendar-text">Чтобы бронирование с других сайтов попадали сюда, вставьте ссылку c их календарем у нас здесь</div>
				<div class="calendar-item flexbox">
					<div class="input-group">
						<input type="text" name="calendar_site " placeholder="Ссылка других" class="form-control">
					</div>
					<div class="btn add-calendar">Добавить еще сайт</div>
				</div>
			</div> -->
		</div>
	</div>
</div>



<div class="form-section" id="lightning">
	<div class="heading">Подключение функции «Мгновенное бронирование» по Вашему объекту</div>
	<div class="section-gray">
		<div class="heading-info">Подключенная функция «Мгновенное бронирование» дает удобную возможность Гостю забронировать жилье на определенные даты без согласования в переписке с Владельцем. Владелец получает быстрое бронирование своего объекта на свободные даты согласно календарю объекта, увеличивает число броней и повышает свой рейтинг на Крымкинг.ру.</div>
		
		<? if (get_field('fast_booking', $postid) == 'Включить') {?>
			<label class="custom-checkbox instant-booking">
				<input type="checkbox" name="fast_booking" value="off" class="custom-input" checked="checked">
				<div class="check"></div>
				<span class="label-name">Отключить Мгновенное бронирование</span>
			</label>
		<?}else{?>
			<label class="custom-checkbox instant-booking">
				<input type="checkbox" name="fast_booking" value="on" class="custom-input">
				<div class="check"></div>
				<span class="label-name">Подключить Мгновенное бронирование</span>
			</label>
		<?}?>

		<ul class="lightning-info">
			<li>Если Вы, как Владелец, не уверены в соблюдении главного правила Мгновенного бронирования: своевременная актуализация календаря бронирования объекта (Вам нужно ответственно отмечать свободные и занятые даты), Крымкинг.ру рекомендует Вам не подключать эту функцию. В таком случае, Гостю придется отправить Вам Запрос на бронирование, а Владельцу в переписке с Гостем, проверив календарь, дополнительно подтвердить актуальность запрашиваемых дат по объекту.</li>
			<li>В случае, если объект с подключенной функцией Мгновенное бронирование окажется занят на необходимые даты и это выяснится до заезда Гостя, Владелец будет оштрафован. Если объект окажется занят непосредственно при заселении Гостя, Владелец и его объекты будут удалены с Крымкинг.ру</li>
		</ul>
	</div>
	<?if(get_field('fast_booking', $postid) == 'Включить'){?>
		<div class="instant-text green">Внимание! У Вас ПОДКЛЮЧЕНО Мгновенное бронирование!</div>
	<?}else{?>
		<div class="instant-text">Внимание! У Вас НЕ ПОДКЛЮЧЕНО Мгновенное бронирование!</div>
	<?}?>
</div>

<div class="form-section" id="photo">
	<div class="heading">Фото и видео</div>
	<div class="form-group section-gray">
		<div class="create-title">Добавьте лучшие фотографии своего объекта и ваше объявление станет идеальным</div>
 
			<fieldset class="form-group">
			    <div class="fieldset-title">Нажмите для добавления фото</div>
			    <input type="file" id="sub-image" name="files[]" style="display: none;" class="form-control" multiple>
			</fieldset>
			<div class="preview-images-zone">
				<?foreach (get_field_object('field_5fe06e7c53d4b', $postid)['value'] as $image) { ?>
				<div class="preview-image preview-show-<?=$image['ID'];?>">
					<div class="image-cancel" data-no="<?=$image['ID'];?>">x</div>
					<div class="image-zone">
						<img id="pro-img-<?=$image['ID'];?>" src="<?=$image['url'];?>">
						<input name="gallery[]" value="<?=$image['ID'];?>" type="hidden">
					</div>
					<div class="tools-edit-image">
						<a href="javascript:void(0)" data-no="<?=$image['ID'];?>" class="btn-edit-image">Редактировать</a>
					</div>
				</div>
				<? } ?>
			</div>
 
		<div class="create-note"><span>Не более 25 фотографий.</span> Размер файла <span>– не более 10 МБ</span>. Требования к фотографиям: <span>не должно быть адресов, телефонов, логотипов, других отметок.</span> Форматы фото: <span>jpg., gif., png.</span></div>
		<div class="input-group full">
			<label class="input-title">Видео с YouTube</label>
			<input type="text" name="video" placeholder="Например: https://www.youtube.com/watch?v=qPeVoi6OmR1" value="<?=the_field('video', $postid);?>" class="form-control">
		</div>
	</div>
</div>

<div class="form-section " id="verification">
	<div class="heading">Верификация Владельца</div>
	<div class="section-gray">
		<div class="text-agreement"><label class="custom-checkbox"><input type="checkbox" name="agreement" class="custom-input" checked=""><div class="check"></div></label> <span class="text">Нажимая на кнопку Подтвердить, Я подтверждаю свое право на размещение данного объекта в краткосрочную аренду, соглашаюсь с <a href="#">Правилами размещения объекта</a> и принимаю условия <a href="#">Договора оферты Владельцам</a></div>
		
		
		
		<? if(get_field('verification', $postid)) { ?>
			<input type="hidden" name="verification" value="confirm">
			<div class="btn btn-confirm active">Подтвердить</div>

			<div class="text-confirm" style="display: block;">Вы согласились с <a href="#">Правилами размещения объекта</a>, приняли условия <a href="#">Договора оферты Владельцам</a> и подтвердили свои права на размещение</div>
		<? } else { ?>
			<input type="hidden" name="verification" value="">
			<div class="btn btn-confirm">Подтвердить</div>

			<div class="text-confirm">Вы согласились с <a href="#">Правилами размещения объекта</a>, приняли условия <a href="#">Договора оферты Владельцам</a> и подтвердили свои права на размещение</div>
		<? } ?>
	</div>
</div>

<a id="link" href="/profile/add/" class="btn new-add hidden">Добавить еще объект</a>

<div class="navigation">
	<div class="btn btn-prev">Назад</div>
	<div class="btn btn-next">Далее</div>
</div>

 
<script>
$(document).ready(function() {
    $('.fieldset-title').click(function() {
    	$('#sub-image').click();
    });
    	document.getElementById('sub-image').addEventListener('change', readImage, false);
    
    	$( ".preview-images-zone" ).sortable();
    
		$(document).on('click', '.image-cancel', function() {
		    let no = $(this).data('no');
		    $(".preview-image.preview-show-"+no).remove();
		});
 
		var num = 4;
		function readImage() {
		    if (window.File && window.FileList && window.FileReader) {
		        var files = event.target.files; //FileList object
		        var output = $(".preview-images-zone");
		
		        for (let i = 0; i < files.length; i++) {
		            var file = files[i];
		            if (!file.type.match('image')) continue;
		            
		            var picReader = new FileReader();
		            
		            picReader.addEventListener('load', function (event) {
		                var picFile = event.target;
		                var html =  '<div class="preview-image preview-show-' + num + '">' +
		                            '<div class="image-cancel" data-no="' + num + '">x</div>' +
		                            '<div class="image-zone"><img id="pro-img-' + num + '" src="' + picFile.result + '"></div>' +
		                            '<div class="tools-edit-image"><a href="javascript:void(0)" data-no="' + num + '" class="btn-edit-image">Редактировать</a></div>' +
		                            '</div>';
		
		                output.append(html);
		                num = num + 1;
		            });
		
		            picReader.readAsDataURL(file);
		        }
		        // $("#sub-image").val('');
		    } else {
		        console.log('Browser not support');
		    }
		}
 });

</script>