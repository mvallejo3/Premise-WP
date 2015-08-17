<?php 
/**
 * 
 */

/**
 * Premise tab
 *
 * @see  http://www.onextrapixel.com/2013/07/31/creating-content-tabs-with-pure-css/
 *
 * @param  string $part        Tab part: 'header', 'footer', 'first'
 * @param  intege $number_tabs Number of tabs
 * @param  string $title       Tab title
 * @param  string $icon        font awesome icon or theme logo if 'first'
 *
 * @return outputs the Premise tab HTML
 */
function premise_tab( $part, $number_tabs = 1, $title = '', $icon = '' ) {

	static $tab_number = 1;

	switch ( $part ) {

		case 'first':
			if ( $tab_number == 1 ) : ?>

			<ul class="premise-tabs clear">

			<?php endif; ?>
				<li style="width: <?php echo 100 / $number_tabs; ?>%">
					<label class="first">
						<img src="<?php echo $icon; ?>" alt="Logo" />
						<br />
						<span><?php echo $title; ?></span>
					</label>
				</li>
			<?php
			break;
		
		case 'header':

			$checked = '';

			if ( $tab_number == 1 )
				$checked = 'checked';
			?>
				<li style="width: <?php echo 100 / $number_tabs; ?>%">
					<input type="radio" <?php echo $checked; ?> name="premise-tabs" id="premise-tab<?php echo $tab_number; ?>">
					<label for="premise-tab<?php echo $tab_number; ?>">
						<i class="fa <?php echo $icon; ?>"></i>
						<?php echo $title; ?>
					</label>

					<div id="premise-tab-content<?php echo $tab_number; ?>" class="premise-tab-content">
			<?php

			$tab_number ++;

			break;
		
		case 'footer':
			?>
					</div><!-- /premise-tab-content -->
				</li>
			<?php if ( ($tab_number + 1) == $number_tabs ) : ?>

			</ul>

			<?php endif;
			break;

		default:

			break;
	}
}

?>