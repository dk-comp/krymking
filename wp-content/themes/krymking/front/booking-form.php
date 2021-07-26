
<?php 	$terms = get_the_terms(get_field('select_hotel')->ID, 'type' );?>
<?php 	$term = array_shift( $terms )?>


<div  <?php if($term->term_id != 83 && $term->term_id != 89 && $term->term_id != 90 && $term->term_id != 91) echo "style='display: none'"?>>
<div class="button-booking">Забронировать номер</div>
<div class="booking-title">Бронирование</div>
<div class="form">
    <div class="booking-price">
	    
        <div class="price"><?=the_price($hotel_id);?> RUB / ночь</div>
        <div class="rating"><?=rating(get_field('guest_rating'), 'number');?> <span>(<?php comments_number( '0', '1', '%' ); ?>)</span></div>
    </div>
    
    <form action="/booking/" method="post" class="sidebar-form-content">
	    
        <input type="hidden" name="post_id" value="<?php if($postid){echo $postid;}else{ echo $post_id;}?>">
        <input type="hidden" name="hotel_id" value="<?=$hotel_id?>">
        <input type="hidden" name="room_id" value="<?=$hotel_id?>">
        
        <input type="hidden" name="price" value="<?=the_price($hotel_id)?>">

        <input type="hidden" name="adults" value="<?php if(!$_SESSION['adults']){echo 1;}else{ echo guests_adults();}?>">
        <input type="hidden" name="children" value="<?=guests_childrens()?>">
        <input type="hidden" name="babies" value="<?=guests_babies()?>">

        <div class="flexbox">
	        
            <div class="form-field form-in">
                <input type="text" name="check_in" value="<?php if($_SESSION['check_in']){
                	echo $_SESSION['check_in'];
                }else{
                	echo date('d.m.Y');
                };?>" placeholder="Заезд" autocomplete="off" required class="datepicker text-field">
            </div>
            <div class="form-field form-out">
                <input type="text" name="check_out" value="<?php if($_SESSION['check_out']){
	                echo $_SESSION['check_out'];
                }else{
	                echo date('d.m.Y', strtotime("+1 day"));
                }?>" placeholder="Выезд" autocomplete="off" required class="datepicker text-field">
            </div>
	        
        </div>
        <div class="form-field">
            <input type="text" name="counts_guests" value="<?=guests();?>" placeholder="Количество гостей" autocomplete="off" required class="text-field">
        </div>
        <div class="form-field">

            <? if ( is_page_template('single-hotel.php') || is_page_template('single-room.php')  ) { ?>

                <a href="#available-rooms" class="btn btn-submit btn-select">Выбрать номер</a>


                    <? if ( is_user_logged_in() ) { ?>

                        <input type="submit" value="Забронировать" class="submit-room">

                    <? } else { ?>

                        <button class="btn btn-submit submit-room btn-register"></button>

                    <? } ?>

                <? } else { ?>

                    <? if ( is_user_logged_in() ) { ?>

                        <input id="single" type="submit" value="Забронировать" class="btn btn-submit">

                    <? } else { ?>

                        <button class="btn btn-submit btn-register">Забронировать</button>

                    <? } ?>

                <? } ?>

            </div>
            
        </form>
        <div class="ajax-calc">
            <?=calc_amount(days($_SESSION['check_in'], $_SESSION['check_out']), the_price($hotel_id));?>
        </div>
    </div>
<?php /*endif; */?>
</div>
