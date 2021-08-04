<?
$statuses = array(
	array(
		'label' => 'опубликовано',
		'value' => 'publish',
	),
	array(
		'label' => 'не опубликовано',
		'value' => 'draft',
	),
	array(
		'label' => 'на модерации',
		'value' => 'pending',
	),
	array(
		'label' => 'отклонено',
		'value' => 'rejected',
	)
);
?>

<div class="object-item">
	<div class="object-image">
		<?php
			$terms = get_the_terms(get_field('select_hotel')->ID, 'type' );
			if ( $terms ) {
				$term = array_shift( $terms );
			}
			if ( $term->term_id == 85 || $term->term_id == 86 || $term->term_id == 87 ) {
				$no_link = true;
			}else{
				$no_link = false;
			}
		?>
		<a href="<?php if(!$no_link) {echo get_permalink();} else {echo '#';}?>"><?=the_post_thumbnail(array(200, 200));?></a>
	</div>
	<div class="object-content">
		<div class="rating-wrap">
			<div class="rating rating-orange"><?=rating(get_field('guest_rating'), 'number');?></div>
			<div class="rating-text"><?=rating(get_field('guest_rating'), 'text');?></div>
			<div class="reviews"><?=num_word( get_comments_number(), array("отзыв","отзыва","отзывов") ); ?></div>
		</div>
		<a href="<?php  if(!$no_link) {echo get_permalink();} else {echo '#';}?>" class="object-title"><?=the_title();?></a>
		<div class="object-id">ID: <?=get_the_ID();?></div>

		<div class="df">
			<a href="<?=home_url('/objects/edit/');?>?post=<?=get_the_ID();?>&action=edit" class="edit-link">Редактировать</a>

			<? if( has_term(array(84, 85, 86, 87), 'type', $postid ) ) { ?>
				<a href="<?=home_url('/objects/edit/');?>?post=<?=get_the_ID();?>&action=edit#rooms" class="edit-link btn-room">Добавить номер</a>
			<? } ?>
		</div>

		<div class="status">
			<div class="status-title">Статус</div>
			<? foreach ($statuses as $status) { ?>
			<label class="custom-checkbox">
				<input type="checkbox" name="<?=$status['value'];?>" class="custom-input" 
				
					<?if (get_post_status( get_the_ID() ) == $status['value']) echo 'checked';?>

					<? if( get_post_status( get_the_ID() ) == 'pending' ) { ?>
						<?=$status['value'] == 'draft' ? 'checked' : ''; ?>
					<? } ?>

				disabled>
				<div class="check"></div>
				<span class="status-text"><?=$status['label'];?></span>
			</label>
			<? } ?>
		</div>
        <? if (get_post_status( get_the_ID() ) == 'trash') { ?>
            <div class="remove-link" data-id="<?=get_the_ID();?>" data-action="untrash_object">Восстановить</div>
        <? } else { ?>
            <div class="remove-link" data-id="<?=get_the_ID();?>" data-action="trash_object">Перенести в Архив</div>
        <? } ?>
		<!-- <div class="views">
			<div class="views-text">за сегодня <span>0</span></div>
			<div class="views-text">за все время <span>0</span></div>
		</div> -->
		<?php /*$a=1;*/?>
	</div>
</div>