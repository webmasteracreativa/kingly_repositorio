<?php

add_filter( 'vc_grid_item_shortcodes', 'formcraft3_vc_shortcode' );
function formcraft3_vc_shortcode( $shortcodes ) {
 global $vc_gitem_add_link_param;
 global $vc_add_css_animation;
 global $fc_forms_table;
 global $wpdb;
 $forms = $wpdb->get_results( "SELECT id, name FROM $fc_forms_table", ARRAY_A );
 $forms2 = array('0' => 'Select Form');
 foreach ($forms as $key => $value) {
   $forms2[$value['name']] = $value['id'];
 }
 $shortcodes['vc_gitem_formcraft'] = array(
  'name' => 'FormCraft',
  'base' => 'vc_gitem_formcraft',
  'category' => 'Elements',
  'description' => 'Insert a FormCraft form',
  'params' => array(
    array(
      'type' => 'dropdown',
      'heading' => 'Form to insert',
      'param_name' => 'fc_id',
      'value' => $forms2
      ),
    array(
      'type' => 'dropdown',
      'heading' => 'Select insert method type',
      'param_name' => 'fc_type',
      'std' => 'inline',
      'value' => array('Inline' => 'inline', 'Popup' => 'popup', 'Slide' => 'slide')
      ),            
    array(
      'type' => 'dropdown',
      'heading' => 'Form alignment',
      'param_name' => 'fc_alignment',
      'std' => 'left',
      'value' => array('Left' => 'left', 'Center' => 'center', 'Right' => 'right'),
      'dependency' => array(
          'element' => 'fc_type',
          'value' => array('inline')
        )      
      ), 
    array(
      'type' => 'dropdown',
      'heading' => 'Form Placement',
      'param_name' => 'fc_placement_popup',
      'std' => 'left',
      'value' => array('Left' => 'left', 'Inline' => 'inline', 'Right' => 'right'),
      'dependency' => array(
          'element' => 'fc_type',
          'value' => array('popup')
        )       
      ), 
    array(
      'type' => 'dropdown',
      'heading' => 'Form Placement',
      'param_name' => 'fc_placement_slide',
      'std' => 'left',
      'value' => array('Left' => 'left', 'Right' => 'right', 'Bottom Right' => 'bottom-right'),
      'dependency' => array(
          'element' => 'fc_type',
          'value' => array('slide')
        ) 
      ), 
    array(
      'type' => 'textfield',
      'heading' => 'Button Text',
      'param_name' => 'button_image',
      'std' => 'Click Me',
      'dependency' => array(
          'element' => 'fc_type',
          'value' => array('popup','slide')
        )       
      ), 
    array(
      'type' => 'textfield',
      'heading' => 'Button Color',
      'param_name' => 'button_color',
      'std' => '#4488ee',
      'dependency' => array(
          'element' => 'fc_type',
          'value' => array('popup','slide')
        )       
      ),
    array(
      'type' => 'textfield',
      'heading' => 'Button Font Color',
      'param_name' => 'button_font_color',
      'std' => '#fff',
      'dependency' => array(
          'element' => 'fc_type',
          'value' => array('popup','slide')
        )       
      ),
    ),
  'post_type' => Vc_Grid_Item_Editor::postType(),
  );
 return $shortcodes;
}

add_shortcode( 'vc_gitem_formcraft', 'vc_gitem_formcraft_render' );
function vc_gitem_formcraft_render( $atts ) {
  $atts['fc_type'] = empty($atts['fc_type']) ? 'inline' : $atts['fc_type'];
  $atts['button_image'] = empty($atts['button_image']) ? 'Click Me' : $atts['button_image'];
  $atts['button_color'] = empty($atts['button_color']) ? '#4488ee' : $atts['button_color'];
  $atts['button_font_color'] = empty($atts['button_font_color']) ? '#fff' : $atts['button_font_color'];
  $atts['fc_alignment'] = empty($atts['fc_alignment']) ? 'left' : $atts['fc_alignment'];
  $atts['fc_placement_slide'] = empty($atts['fc_placement_slide']) ? 'left' : $atts['fc_placement_slide'];
  $atts['fc_placement_popup'] = empty($atts['fc_placement_popup']) ? 'left' : $atts['fc_placement_popup'];
  if ( $atts['fc_type'] == 'inline' ) {
    return "[fc align='".$atts['fc_alignment']."' id='".$atts['fc_id']."'][/fc]";
  } else if ( $atts['fc_type'] == 'slide' ) {
    return "[fc id='".$atts['fc_id']."' type='slide' button_color='".$atts['button_color']."' font_color='".$atts['button_font_color']."' placement='".$atts['fc_placement_slide']."']".$atts['button_image']."[/fc]";
  } else if ( $atts['fc_type'] == 'popup' ) {
    return "[fc id='".$atts['fc_id']."' type='popup' button_color='".$atts['button_color']."' font_color='".$atts['button_font_color']."' placement='".$atts['fc_placement_popup']."']".$atts['button_image']."[/fc]";
  }
}
?>