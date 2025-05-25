<?php
if (!defined('ABSPATH')) {
	exit;
}

$this->add_render_attribute('countdown_container', [
	'data-eventdate' => strtotime($settings['due_date']),
	'class' => ['thegem-te-countdown']
]);
?>
<div <?php echo $this->get_render_attribute_string('countdown_container') ?>>
	<div class="countdown-wrapper countdown-info">
		<?php if ('yes' === $settings['content_labels_show_days']): ?>
			<div class="countdown-item count-1">
				<div class="wrap">
					<?php if ($show_link): ?>
						<a <?php echo $this->get_render_attribute_string($countdown_wrap_link) ?>></a><?php endif ?>
					<span class="item-count countdown-days title-h5 <?php echo esc_attr($settings['numbers_style_weight'] == 'light' ? 'light' : ''); ?>"></span><span
							class="item-title"><?php echo $settings['content_labels_days_label'] ?></span></div>
			</div>
		<?php endif ?>
		<?php if ('yes' === $settings['content_labels_show_hours']): ?>
			<div class="countdown-item count-2">
				<div class="wrap">
					<?php if ($show_link): ?>
						<a <?php echo $this->get_render_attribute_string($countdown_wrap_link) ?>></a><?php endif ?>
					<span class="item-count countdown-hours title-h5 <?php echo esc_attr($settings['numbers_style_weight'] == 'light' ? 'light' : ''); ?>"></span><span
							class="item-title"><?php echo $settings['content_labels_hours_label'] ?></span></div>
			</div>
		<?php endif ?>
		<?php if ('yes' === $settings['content_labels_show_minutes']): ?>
			<div class="countdown-item count-3">
				<div class="wrap">
					<?php if ($show_link): ?>
						<a <?php echo $this->get_render_attribute_string($countdown_wrap_link) ?>></a><?php endif ?>
					<span class="item-count countdown-minutes title-h5 <?php echo esc_attr($settings['numbers_style_weight'] == 'light' ? 'light' : ''); ?>"></span><span
							class="item-title"><?php echo $settings['content_labels_minutes_label'] ?></span>
				</div>
			</div>
		<?php endif ?>
		<?php if ('yes' === $settings['content_labels_show_seconds']): ?>
			<div class="countdown-item count-4">
				<div class="wrap">
					<?php if ($show_link): ?>
						<a <?php echo $this->get_render_attribute_string($countdown_wrap_link) ?>></a><?php endif ?>
					<span class="item-count countdown-seconds title-h5 <?php echo esc_attr($settings['numbers_style_weight'] == 'light' ? 'light' : ''); ?>"></span><span
							class="item-title"><?php echo $settings['content_labels_seconds_label'] ?></span>
				</div>
			</div>
		<?php endif ?>
	</div>
</div>
