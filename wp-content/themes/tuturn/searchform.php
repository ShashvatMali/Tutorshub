<?php
/**
 *
 * Theme Search form
 *
 * @package   Tuturn
 * @author    Amentotech
 * @link      https://themeforest.net/user/amentotech/portfolio
 * @version 1.0
 * @since 1.0
 */
?>
<form class="tu-formtheme tu-formsearch" method="get" role="search" action="<?php echo esc_url(home_url('/')); ?>">
	<fieldset>
		<div class="form-group">
			<input type="search" name="s" value="<?php echo get_search_query(); ?>" class="form-control" placeholder="<?php esc_attr_e('Search with keyword', 'tuturn') ?>">
			<button type="submit" class="tu-searchgbtn"><i class="fa fa-search"></i><span><?php esc_html_e('Search now', 'tuturn') ?></span></button>
		</div>
	</fieldset>
</form>


