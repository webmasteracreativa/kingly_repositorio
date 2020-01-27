<?php defined('ABSPATH') or die("KEEP CALM AND CARRY ON");?>

<div class="swift-message swift-purchase-key-warning">
	<i class="fas fa-exclamation-circle"></i>
	<span class="swift-message-text"><?php esc_html_e('This feature is available only in premium version.', 'swift-performance');?></span>
	<a href="<?php echo Swift_Performance_Lite::upgrade_link();?>" target="_blank" class="swift-btn swift-btn-gray"><?php esc_html_e('Upgrade NOW!', 'swift-performance')?></a>
</div>