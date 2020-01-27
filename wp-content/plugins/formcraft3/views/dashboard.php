<?php


defined( 'ABSPATH' ) or die( 'Cheating, huh?' );

global $fc_addons, $fc_templates, $fc_meta, $fc_forms_table, $fc_submissions_table, $fc_views_table, $wpdb;
$time = date('Y-m-d 00:00:00',time()+fc_offset());
$date_format = get_option('date_format');

$forms = $wpdb->get_results( "SELECT id, name FROM $fc_forms_table WHERE name<>''" );

$total_subs = $wpdb->get_var( "SELECT COUNT(*) FROM $fc_submissions_table" );
$total_forms = $wpdb->get_var( "SELECT COUNT(*) FROM $fc_forms_table WHERE name<>''" );
$total_payments = $wpdb->get_var( "SELECT SUM(payment) FROM $fc_views_table" );
$class_col = $total_payments==0 ? 'small-4' : 'small-3';

$total_subs = $total_subs==null?0:$total_subs;
$total_forms = $total_forms==null?0:$total_forms;

$templates = array();
foreach ($fc_templates as $key => $templatesGroup) {
	if ( empty($templatesGroup) ) { continue; }
	if ( !file_exists($templatesGroup) ) { continue; }
	$templatesTemp = scandir($templatesGroup);
	if ( !$templatesTemp ){continue;}
	$templates[$key] = array();
	foreach ($templatesTemp as $key2 => $value) {
		$temp1 = explode('.', $value);
		if ( isset($temp1[ count($temp1) - 1 ]) && $temp1[ count($temp1) - 1 ] == 'txt' )
		{
			$templates[$key][] = array('name'=>str_replace('.txt', '', $value),'path'=>str_replace(WP_PLUGIN_DIR,'',$templatesGroup).$value);
		}
	}
}

$f3_messages = array();
$f3_key_temp = get_site_option('f3_key');

if ( !empty($f3_key_temp) )
{
	if (!ctype_digit(get_site_option('f3_purchased'))){update_site_option('f3_purchased',strtotime(get_site_option('f3_purchased')));}
	if (!ctype_digit(get_site_option('f3_registered'))){update_site_option('f3_registered',strtotime(get_site_option('f3_registered')));}
	if (!ctype_digit(get_site_option('f3_expires'))){update_site_option('f3_expires',strtotime(get_site_option('f3_expires')));}
}

if ( !empty($f3_key_temp) && $fc_meta['f3_multi_site_addon'] == false )
{
	$difference_checked = (strtotime(current_time('mysql'))-strtotime(get_site_option('f3_registered')))/(60 * 60 * 24);
	/* Re-verify the license key every 7 days */
	if ( $difference_checked > 7 )
	{
		$key = $f3_key_temp;
		$email = get_site_option('f3_email');
		$args = array(
			'timeout'     => 15,
			'redirection' => 5,
			'sslverify'   => false
			);
		$response = wp_remote_get("http://formcraft-wp.com?type=verify_license&v=2&key=".$key."&email=".$email."&site=".rawurlencode(site_url()));
		if ( !is_wp_error($response) && isset($response['body']) )
		{
			$response = json_decode($response['body'], 1);
			if ( $response!=NULL && !empty($response) )
			{
				if ( isset($response['failed']) )
				{
					update_site_option( 'f3_verified', '' );
					$f3_messages[] = "<div>".$response['failed']."</div>";
				}
				else if ( isset($response['success']) )
				{
					update_site_option( 'f3_registered', $response['registered'] );
				}
			}
		}
	}
}

if(preg_match('/(?i)msie/',$_SERVER['HTTP_USER_AGENT'])) {
    $f3_messages[] = "<div>".__('We have known issues with Internet Exporer. Please use Chrome or FireFox browser.','formcraft')."</div>";
}

$f3_key_temp = get_site_option('f3_key');
if ( (empty($f3_key_temp) || get_site_option('f3_verified')!='yes') && $fc_meta['f3_multi_site_addon'] == false )
{
	$f3_messages[] = "<div id='trigger-key-tab'>".__('Please enter the purchase key to register the plugin','formcraft')."</div>";
}
else if ( $fc_meta['f3_multi_site_addon'] == false && get_site_option('f3_blog_id')!=get_current_blog_id() )
{
	$f3_messages[] = "<div id='trigger-key-tab'>".__('Please enter the purchase key to register the plugin','formcraft')."</div>";
}
if ( is_multisite() && $fc_meta['f3_multi_site_addon']==false && get_site_option('f3_blog_id')!=get_current_blog_id() )
{
	$f3_messages[] = "<div>".__('To run FormCraft on all sub-sites with one license, you need the <a target="_blank" href="http://formcraft-wp.com/addons/multi-site/">FormCraft Multi Site Add-On</a>','formcraft')."</div>";
}
$expired_license = false;
if ( isset($_GET['no_license_message']) ) {
	add_site_option('f3_no_license_error', true);
}
if ( get_site_option( 'f3_expires' ) != '' && get_site_option( 'f3_no_license_error' ) != true )
{
	$expires_time = get_site_option( 'f3_expires' );
	if ( ( ( $expires_time-strtotime('now') ) / ( 60 * 60 * 24 ) ) < 0 )
	{
		$expired_license = true;
		echo " <style> html body .formcraft-css #activation-tab .is-activated #valid-till { color: red; } </style> ";
		$f3_messages[] = "<div>".__('Your license seems to have expired. Click <a target="_blank" href="http://formcraft-wp.com/buy/?addons=346&key='.get_site_option('f3_key').'">here</a> to renew it. <a target="_blank" href="http://formcraft-wp.com/help/faq-plugin-license/">Read more</a>. <a href="admin.php?page=formcraft3_dashboard&no_license_message">Dismiss</a>.','formcraft')."</div>";
	}
}


if ( !empty($f3_messages) )
{
	$f3_messages = implode('', $f3_messages);
	echo "<div class='f3-dashboard-message'><div>".$f3_messages."</div></div>";
}

$f3_purchased_temp = get_site_option('f3_purchased');
$f3_expires_temp = get_site_option('f3_expires');
$f3_registered_temp = get_site_option('f3_registered');

$f3_purchased_temp = date(get_option('date_format'),$f3_purchased_temp);
$f3_expires_temp = date(get_option('date_format'),$f3_expires_temp);
$f3_registered_temp = date(get_option('date_format'),$f3_registered_temp);

/*
delete_site_option('f3_verified');
delete_site_option('f3_key');
delete_site_option('f3_purchased');
delete_site_option('f3_blog_id');
delete_site_option('f3_registered');
delete_site_option('f3_expires');
*/
$is_verified = get_site_option('f3_verified')!='yes' ? 'not-verified' : 'is-verified';
$is_verified = $fc_meta['f3_multi_site_addon'] == false && get_site_option('f3_blog_id')!=get_current_blog_id() ? 'not-verified' : $is_verified;

?>
<style>
	#toast-container
	{
		top: 10px;
	}
</style>
<script>
window.formsList = <?php echo json_encode($forms); ?>
</script>
<div class='formcraft-css'>
	<div class='row' style='position: relative'>
		<div class='medium-12 column fc-brand-header'>
			<h1>FormCraft</h1><span class='fc-version'><?php echo $fc_meta['version']; ?></span>
		</div>
	</div>
	<nav class='main-tabs' data-content='#main_tabs'>
		<a class='active' href='#dashboard-tab'><?php _e('Dashboard','formcraft'); ?> </a>
		<a data-toggle='fc_modal' data-target='#new_form_modal'><?php _e('New Form','formcraft'); ?> <i class='icon-plus'></i></a>
		<a href='#submissions-tab'><?php _e('Submissions','formcraft'); ?> <i class='icon-list-bullet'></i></a>
		<a href='#survey-tab'><?php _e('Insights','formcraft'); ?> <i class='icon-chart'></i></a>
		<a href='#license-tab' class='<?php echo $is_verified; ?>'><?php _e('License','formcraft'); ?> <i class='icon-key-outline'></i></a>
		<a data-toggle='fc_modal' data-target='#file_uploads'><?php _e('File Uploads','formcraft'); ?> <i class='icon-upload-cloud'></i></a>
	</nav>
	<div class='nav-content' id='main_tabs'>
		<div class='active' data-tab-id='dashboard-tab'>
			<div class='row' style='position: relative; z-index: 101'>
				<div id='form-cover'></div>
				<div id='analytics-cover'></div>
			</div>
		</div>
		<div data-tab-id='submissions-tab'>
			<div class='row'>
				<div id='submission-cover'></div>
				<div id='submission-view'></div>
			</div>
		</div>
		<div data-tab-id='survey-tab'>
			<div class='row'>
				<div id='survey-cover'>as</div>
				<div id='survey-view'></div>
			</div>
		</div>
		<div id='activation-tab' data-tab-id='license-tab' class='<?php echo $is_verified; ?>'>
			<div class='cover'>
				<i class='activation-key icon-key-outline'></i>
				<i class='activation-key icon-ok-circle'></i>
				<div class='is-activated'>
					<br>
					<?php _e('Your copy of FormCraft is registered','formcraft'); ?>
					<br><br>
					<div style='font-size: 14px'>
						<?php _e('Purchased On','formcraft'); ?>: <span id='purchased-on'><?php echo empty($f3_purchased_temp) ? '' : $f3_purchased_temp; ?></span>
						<br>
						<?php _e('Free Support and Updates Till','formcraft'); ?>: <span id='valid-till'><?php echo empty($f3_expires_temp) ? '' : $f3_expires_temp; ?></span>
						<br>
						<?php _e('Last Verified','formcraft'); ?>: <span id='verified-on'><?php echo empty($f3_registered_temp) ? '' : $f3_registered_temp; ?></span>
						<br>
						<?php
						if ( get_site_option('f3_verified')=='yes' )
						{
							?>
							<button id='refresh-license' style='display: block; margin: 10px auto; padding: 0 11px; height: 34px; line-height: 34px' class='button blue'>
								<span style='cursor: pointer; font-size: 11px; display: block'><?php _e('Refresh Info','formcraft'); ?></span>
								<i style='font-size: 18px; line-height: 32px' class='icon-spin5 animate-spin'></i>
							</button>
							<span id='show-license-form'>...</span>
							<?php
						}
						?>
					</div>
				</div>
				<div class='not-activated'>
					<?php
					if ( get_site_option('f3_verified')!='yes' )
					{
						?>
						<?php _e('You need to register your copy of FormCraft below to get plugin updates, and support.','formcraft'); ?>
						<?php
					}
					?>
					<form id='activate-license'>
						<input name='email' type='text' class='underline-input top' placeholder='Your Email' data-toggle='tooltip' data-trigger='focus' data-html='true' data-placement='right' title="<?php _e("Later on, you can create an account on <strong><a target='_blank' href='http://formcraft-wp.com/dashboard'>formcraft-wp.com</a></strong> with this email, and manage your license keys, add-ons, and get support.<br> Don't worry. We don't use this to send you any email.","formcraft"); ?>" value='<?php echo urldecode(get_site_option( 'f3_email' )); ?>'>
						<input name='key' type='text' class='underline-input bottom' placeholder='Your License Key' data-toggle='tooltip' data-trigger='focus' data-html='true' data-placement='right' title='<?php _e('Your license key would look something like: <br><strong>2zd567ac-ed32-6fg4-827d-23a89cb05d08</strong><br><strong><a target="_blank" href="http://ncrafts.net/blog/2014/05/where-to-find-the-purchase-code-of-items/">Where do I find my license key?</a></strong>','formcraft'); ?>' value='<?php echo get_site_option( 'f3_key' ); ?>'/>
						<div style='text-align: right; padding-top: 9px'>
							<div class='response'></div>
							<button type="submit" class="button blue activation-submit">
								<span><?php _e('Register','formcraft'); ?></span>
								<i style='line-height: 36px' class='icon-spin5 animate-spin'></i>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="fc_modal fc_fade" id="new_form_modal">
		<div class="fc_modal-dialog" style="width: 490px">
			<form class="fc_modal-content" id='new_form'>
				<button class='fc_close' type="button" class="close" data-dismiss="fc_modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<div class="fc_modal-body">
					<div class='new_type_cover'>
						<div class='row new_type hide-checkbox checkbox-cover' style='max-width: 430px'>
							<label class='small-3 column active'>
								<input checked type='radio' name='new_form_type' value='blank'/>
								<i class='icon-doc'></i>
								<?php _e('Blank Form','formcraft'); ?>
							</label>
							<label class='small-3 column'>
								<input type='radio' name='new_form_type' value='template'/>
								<i class='icon-doc-text'></i>
								<?php _e('Template','formcraft'); ?>
							</label>
							<label class='small-3 column'>
								<input type='radio' name='new_form_type' value='import'/>
								<i class='icon-doc'><i class='icon-down-circled2'></i></i>
								<?php _e('Import Form','formcraft'); ?>
							</label>
							<label class='small-3 column'>
								<input type='radio' name='new_form_type' value='duplicate'/>
								<i class='icon-docs'></i>
								<?php _e('Duplicate','formcraft'); ?>
							</label>
						</div>
					</div>
					<div id='select-template-cover'>
						<div>
							<div class='template-select-slider hide-checkbox checkbox-cover'>
								<?php
								foreach ($templates as $head => $group) {
									echo "<h3>$head</h3>";
									foreach ($group as $key => $value) {
										echo "<label>".$value['name']."<input type='radio' name='template-select-slider' value='".$value['path']."'/></label>";
									}
								}
								?>
							</div>
							<div id='template-showcase'>
								<div id='template-showcase-form' class='form-live'></div>
							</div>
						</div>
					</div>
					<div class='non-template-cover'>
						<div style='max-width: 410px; margin: 0 auto'>
							<div style='display: none'>
								<span id='import-which-form' style='width: 100%' class='button button-file'><span class='file-name'>(<?php _e('Upload Template File','formcraft'); ?>)</span><input data-url='<?php echo admin_url( 'admin-ajax.php' )."?action=formcraft3_import_file"; ?>' type="file" id='import_form_input' name='form_file'/>
								<i class='icon-up-circled2'></i>
								<i class='icon-spin5 animate-spin'></i>
							</span>
						</div>
						<div style='display: none'>
							<select id='duplicate-which-form' name='duplicate'>
								<option value=''>
									(<?php _e('Select Form To Duplicate','formcraft'); ?>)
								</option>
								<?php
								foreach ($forms as $key => $value) {
									echo "<option value='".$value->id."'>".$value->name."</option>";
								}
								?>
							</select>
						</div>
						<input type='text' name='form_name' placeholder='<?php _e('Form Name','formcraft'); ?>'>
						<div style='text-align: right'>
							<span class='response'></span>
							<button type="submit" class="button blue submit-btn">
								<span><?php _e('Create Form','formcraft'); ?></span>
								<i class='icon-spin5 animate-spin'></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="fc_modal fc_fade" id="file_uploads">
	<div class="fc_modal-dialog" style="width: 480px">
		<div id='files-cover'></div>
	</div>
</div>
</div>
