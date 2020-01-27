<?php defined('ABSPATH') or die("KEEP CALM AND CARRY ON");?>

<div id="swift-performance-wrapper">
      <div class="swift-performance-header">
            <img class="swift-performance-logo" src="<?php echo SWIFT_PERFORMANCE_URI;?>images/logo.png">
            <div class="swift-performance-slogan">
                  <?php esc_html_e('Speed up WordPress', 'swift-performance');?>
                  <small><?php esc_html_e('is not rocket science anymore', 'swift-performance');?></small>
            </div>
      </div>
      <div id="swift-performance-wrapper-inner">
      <ul class="swift-menu">
            <?php foreach(Swift_Performance_Lite::get_menu() as $element):?>
                  <li class="<?php echo((isset($_GET['subpage']) && $_GET['subpage'] == $element['slug']) || (!isset($_GET['subpage']) && $element['slug'] == 'dashboard') ? 'active ' : '');?>swift-menu-<?php echo $element['slug'];?>">
                        <a href="<?php echo esc_url(add_query_arg('subpage', $element['slug'], menu_page_url(SWIFT_PERFORMANCE_SLUG, false)));?>">
                              <?php if(isset($element['icon'])):?>
                                    <i class="<?php echo esc_attr($element['icon']);?>"></i>
                              <?php endif;?>
                              <?php echo esc_html($element['name']);?>
                        </a>
                  </li>
            <?php endforeach;?>
      </ul>