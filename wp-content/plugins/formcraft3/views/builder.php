<?php
defined( 'ABSPATH' ) or die( 'Cheating, huh?' );
global $fc_meta, $wpdb, $fc_forms_table,$fc_addons,$fc_addons_directory, $fc_triggers;
$form_id = intval($_GET['id']);
$form_name = stripslashes($wpdb->get_var( "SELECT name FROM $fc_forms_table WHERE id = '$form_id'" ));
$counter = $wpdb->get_var( "SELECT counter FROM $fc_forms_table WHERE id = '$form_id'" );
if (is_rtl())
{
	?>
	<script>
		window.isRTL = true;
	</script>
	<?php
}
else
{
	?>
	<script>
		window.isRTL = false;
	</script>
	<?php
}

$backgrounds = array();
$base = plugins_url( '../assets/images/backgrounds/', __FILE__ );
$backgrounds[] = array('Birds','url('.$base.'birds.jpg)','url('.$base.'thumb-birds.jpg)');
$backgrounds[] = array('Speck','url('.$base.'brillant.png)','url('.$base.'thumb-brillant.png)');
$backgrounds[] = array('Fibre','url('.$base.'fibre.png)','url('.$base.'thumb-fibre.png)');
$backgrounds[] = array('Noise','url('.$base.'grey-noise.png)','url('.$base.'thumb-grey-noise.png)');
$backgrounds[] = array('Lined','url('.$base.'lined.png)','url('.$base.'thumb-lined.png)');
$backgrounds[] = array('Linen','url('.$base.'white-linen.png)','url('.$base.'thumb-white-linen.png)');
$backgrounds[] = array('Coarse','url('.$base.'coarse.png)','url('.$base.'thumb-coarse.png)');
$backgrounds[] = array('Jeans','url('.$base.'jeans.png)','url('.$base.'jeans.png)');

?>
<input type='hidden' id='form_id' value='<?php echo $form_id; ?>'>
<div ng-app='FormCraft' style=''>
	<div id='formcraft-builder-cover' class='formcraft-css form-loading' ng-controller='FormController' ng-init='AngularInit()'>
		<div class='main-loader formcraft-loader'></div>
		<div id='notification-panel'>
		</div>
		<div id='add-fields-panel' class='fields-list-right fields-list-sortable show-{{Builder.Config.showAddField}}'>
			<button class='toggleAddField' ng-click='Builder.Config.showAddField = !Builder.Config.showAddField'>
				Add Field
				<i class='down-arrow formcraft-icon'>keyboard_arrow_down</i>
				<i class='up-arrow formcraft-icon'>keyboard_arrow_up</i>
			</button>
			<button ng-click='addFormElement("heading")'><?php _e('Heading','formcraft') ?></button>
			<button ng-click='addFormElement("oneLineText")'><?php _e('One Line Input','formcraft') ?></button>
			<button ng-click='addFormElement("email")'><?php _e('Email Input','formcraft') ?></button>
			<button ng-click='addFormElement("textarea")'><?php _e('Textarea','formcraft') ?></button>
			<button ng-click='addFormElement("checkbox")'><?php _e('Checkbox','formcraft') ?></button>
			<button ng-click='addFormElement("dropdown")'><?php _e('Dropdown','formcraft') ?></button>
			<button ng-click='addFormElement("datepicker")'><?php _e('Datepicker','formcraft') ?></button>
			<button ng-click='addFormElement("customText")'><?php _e('Custom Text','formcraft') ?></button>
			<button ng-click='addFormElement("submit")'><?php _e('Submit','formcraft') ?></button>
			<?php
			do_action('formcraft_after_fields');
			?>
			<div class='hover-on-fields'>
				<button><i class='formcraft-icon'>keyboard_arrow_left</i><?php _e('More Fields','formcraft') ?></button>
				<ul class='fields-list fields-list-sortable'>
					<li><button ng-click='addFormElement("password")'><?php _e('Password','formcraft') ?></button></li>
					<li><button ng-click='addFormElement("fileupload")'><?php _e('File Upload','formcraft') ?></button></li>
					<li><button ng-click='addFormElement("slider")'><?php _e('Slider','formcraft') ?></button></li>
					<li><button ng-click='addFormElement("timepicker")'><?php _e('Timepicker','formcraft') ?></button></li>
					<li><button ng-click='addFormElement("address")'><?php _e('Address','formcraft') ?></button></li>
					<li ng-repeat='field in addField.others'><button ng-click='addFormElement(field.name)'>{{field.name}}</button></li>
				</ul>
			</div>
			<div class='hover-on-fields' style='z-index: 100'>
				<button><i class='formcraft-icon'>keyboard_arrow_left</i><?php _e('Survey','formcraft') ?></button>
				<ul class='fields-list fields-list-sortable'>
					<li><button ng-click='addFormElement("thumb")'><?php _e('Thumb Rating','formcraft') ?></button></li>
					<li><button ng-click='addFormElement("star")'><?php _e('Star Rating','formcraft') ?></button></li>
					<li><button ng-click='addFormElement("matrix")'><?php _e('Choice Matrix','formcraft') ?></button></li>
				</ul>
			</div>
			<div class='hover-on-fields fields-nos-{{addField.payments.length}}'>
				<button><i class='formcraft-icon'>keyboard_arrow_left</i>Payments</button>
				<ul class='fields-list fields-list-sortable'>
					<li ng-repeat='field in addField.payments'><button ng-click='addFormElement(field.name)'>{{field.name}}</button></li>
				</ul>
			</div>
		</div>

		<div class='option-box  state-{{Builder.Config.showLogic}}' id='form_logic_box'>
			<div id='logic_tabs' class='nav-content-slide'>
				<div class='active'>
					<div id='add-logic-heads'>
						<div style='margin-left: 3.5%; width: 45%'><?php _e('Conditions','formcraft'); ?></div>
						<div style='margin-left: 7%; width: 39%'><?php _e('Actions','formcraft'); ?></div>
					</div>
					<div class='add-logic-area' ng-repeat='logic in Builder.Config.Logic'>
						<div class='logic-text' style='width: 3.5%'>
							if
						</div>
						<div style='width: 45%' class='group actions-nos-{{Builder.Config.Logic[$index][0].length}}'>
							<div ng-repeat='action in Builder.Config.Logic[$index][0]' class='group-row show-{{Builder.Config.Logic[$parent.$index][0].length}}'>
								<div style='width: 30%'>
									<select id='select_fix_{{$parent.$index}}_{{$index}}' ng-model='action[0]'>
										<option value=''>(<?php _e('field','formcraft'); ?>)</option>
										<optgroup ng-repeat='page in Builder.FormElements' label='{{Builder.Config.page_names[$index]}}'>
											<option ng-repeat='element in page' value='{{element.elementDefaults.identifier}}'>{{element.elementDefaults.main_label}}</option>
										</optgroup>
									</select>
								</div>
								<div style='width: 33%'>
									<select ng-model='action[1]'>
										<option value=''>(<?php _e('trigger','formcraft'); ?>)</option>
										<option value='equal_to'><?php _e('is equal to','formcraft'); ?></option>
										<option value='not_equal_to'><?php _e('is not equal to','formcraft'); ?></option>
										<option value='contains'><?php _e('contains','formcraft'); ?></option>
										<option value='contains_not'><?php _e('doest not contain','formcraft'); ?></option>
										<option value='greater_than'><?php _e('is greater than','formcraft'); ?></option>
										<option value='less_than'><?php _e('is less than','formcraft'); ?></option>
									</select>
								</div>
								<div style='width: 30%'>
									<input type='text' ng-model='action[2]' placeholder='...'>
								</div>
								<div ng-click='removeLogicAction($parent.$index, $index)' class='remove-action' style='width: 7%; border-right-width: 0px'>
									×
								</div>
								<div class='and-or'>
									<select ng-model='Builder.Config.Logic[$parent.$index][2]'>
										<option value='and'>And</option>
										<option value='or'>Or</option>
									</select>
								</div>
							</div>
							<span ng-click='addLogicAction($index)' class='add-group'><?php _e('add condition row','formcraft'); ?></span>
						</div>
						<div class='logic-text' style='width: 6%''>
							then
						</div>
						<div style='width: 40%' class='group'>
							<div ng-repeat='result in Builder.Config.Logic[$index][1]' class='group-row'>
								<div style='width: 100%' class='sign-and'>
									&
								</div>								
								<div style='width: 43%' class='set-value-{{result[0]}}'>
									<select ng-model='result[0]'>
										<option value=''><?php _e('(action)','formcraft'); ?></option>
										<option value='show_fields'><?php _e('show fields','formcraft'); ?></option>
										<option value='hide_fields'><?php _e('hide fields','formcraft'); ?></option>
										<option value='email_to'><?php _e('send email to','formcraft'); ?></option>
										<option value='redirect_to'><?php _e('redirect to','formcraft'); ?></option>
										<option value='trigger_integration'><?php _e('trigger integration','formcraft'); ?></option>
										<option value='set_value'><?php _e('set value of','formcraft'); ?></option>
									</select>
									<select class='set-value-field' id='cons_select_fix_{{$parent.$index}}_{{$index}}' ng-model='result[4]'>
										<option value=''>(<?php _e('field','formcraft'); ?>)</option>
										<optgroup ng-repeat='page in Builder.FormElements' label='{{Builder.Config.page_names[$index]}}'>
											<option ng-repeat='element in page' value='{{element.elementDefaults.identifier}}'>{{element.elementDefaults.main_label}}</option>
										</optgroup>
									</select>
								</div>
								<div class='result-type-{{result[0]}}' style='width: 49%; border-left: 1px solid #d4d4d4'>
									<input type='text' class='select-fields-logic' select-fields ng-model='result[1]' placeholder='(add fields)'/>
									<input type='text' class='type-in-logic' ng-model='result[2]' placeholder='...'>
									<?php
									if ( isset($fc_triggers) && count($fc_triggers)>0 )
									{
										echo "<select class='trigger-intergration-select' ng-model='result[3]'>";
										echo "<option value=''>".__('(select)','formcraft')."</option>";
										foreach ($fc_triggers as $key => $value) {
											echo "<option value='$value'>$value</option>";
										}
										echo "</select>";
									}
									?>
								</div>
								<div ng-click='removeLogicResult($parent.$index, $index)' class='remove-action' style='width: 8%; border-right: 0px'>
									×
								</div>
							</div>
							<div ng-click='addLogicResult($index)' class='add-group'><?php _e('add action row','formcraft'); ?></div>
						</div>
						<div class='remove-logic' ng-click='removeLogic($index)'>×</div>
					</div>
					<div id='add-logic-cover'>
						<button class='add-logic-button' ng-click='addLogic()'><?php _e('Add New Logic','formcraft'); ?></button>
						<a style='margin: 10px auto;font-size: 12px' class='trigger-help' data-post-id='9'><?php _e('how to use Conditional Logic', 'formcraft'); ?></a>
					</div>
				</div>
			</div>
		</div>

		<div class='option-box  state-{{Builder.Config.showAddons}}' id='form_addon_box'>
			<nav class='nav-tabs-slide' data-content='#addon_tabs'>
				<span style='width: 50%' class='active'><?php _e('Installed','formcraft'); ?></span>
				<span style='width: 50%'><?php _e('Add New','formcraft'); ?></span>
			</nav>
			<div id='addon_tabs' class='nav-content-slide'>
				<div class='active'>
					<?php
					if ( is_array($fc_addons) && count($fc_addons)>0 )
					{
						foreach ($fc_addons as $key => $addon) {
							$extra_class = isset($_GET['f3_activated']) && $_GET['f3_activated']==$addon['plugin_id'] ? 'fc_highlight' : '';
							?>
							<div class='addon addon-id-<?php echo $addon['plugin_id'].' '; echo $extra_class; ?>' <?php if ( !empty($addon['controller']) ) { ?> ng-controller='<?php echo $addon['controller']; ?>' <?php } ?> data-name='<?php echo $addon['title']; ?>'>
								<div class='addon-head ac-toggle' ng-click='Init()'>
									<div class='addon-logo-cover'>
										<img class='addon-logo' src='<?php echo $addon['logo']; ?>' alt='<?php echo $addon['title']; ?>'/>
									</div>
									<span class='addon-title'><?php echo $addon['title']; ?></span>
									<span class='toggle-angle'><i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i><i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i></span>
								</div>
								<div class='addon-content ac-inner'>
									<div>
										<?php
										$addon['content_fn']();
										?>
									</div>
								</div>
							</div>
							<?php
						}
					}
					else
					{
						?>
						<div class='no-addons'><?php _e('No Installed Addons','formcraft') ?></div>
						<?php
					}
					?>
				</div>
				<div class='new-addons'>
				</div>
			</div>
		</div>

		<div class='option-box state-{{Builder.Config.showOptions}}' id='form_options_box'>
			<nav class='nav-tabs-slide' data-content='#options_tabs'>
				<span style='width: 18%' class='active'><?php _e('General','formcraft'); ?></span>
				<span style='width: 17%'><?php _e('Email','formcraft'); ?></span>
				<span style='width: 17%'><?php _e('Embed','formcraft'); ?></span>
				<span style='width: 26%'><?php _e('Custom Text','formcraft'); ?></span>
				<span style='width: 22%'><?php _e('Advanced','formcraft'); ?></span>
			</nav>
			<div id='options_tabs' class='nav-content-slide'>
				<div class='active'>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.form_disable'>
						<h3><?php _e('Disable form','formcraft'); ?></h3>
						<div class='option-description' ng-slide-toggle='Builder.Config.form_disable'><?php _e('Show this message when disabled: ','formcraft'); ?>
							<textarea style='width: 100%' ng-model='Builder.Config.form_disable_message'>
							</textarea>
						</div>
					</label>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.disable_form_link'>
						<h3><?php _e('Disable form page','formcraft'); ?></h3>
						<div class='option-description'><?php _e('You can share this link directly to allow people to fill the form','formcraft'); ?>
							<textarea onclick='select()' style='width: 100%' rows='1' class='copy-code' readonly><?php echo get_site_url().'/form-view/'.$form_id; ?></textarea>
						</div>
					</label>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.disable_multiple'>
						<h3><?php _e('Disable multiple submissions from same device','formcraft'); ?></h3>
						<div class='option-description' ng-slide-toggle='Builder.Config.disable_multiple'><?php _e('Show this message when disabled: ','formcraft'); ?>
							<textarea style='width: 100%' ng-model='Builder.Config.disable_multiple_message'>
							</textarea>
						</div>
					</label>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.disable_after'>
						<h3><?php _e('Disable form when it reaches','formcraft'); ?><input type='text' ng-model='Builder.Config.disable_after_nos' style='width: 60px'/><?php _e('submissions','formcraft'); ?></h3>
						<div class='option-description'><?php _e('Current submission counter: ') ?><?php echo $counter; ?></div>
					</label>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.save_progress'>
						<h3><?php _e('Enable auto-save form progress','formcraft'); ?></h3>
						<div class='option-description'><?php _e('Your users\' data is automatically saved as they type. If the form is not submitted, they can come back to the form later on, and will be able to continue from where they left. The data is stored for 60 days.','formcraft'); ?></div>
					</label>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.Post_data'>
						<h3><?php _e('Send data to custom URL','formcraft'); ?>&nbsp;<a style='display: inline-block; padding: 0' class='trigger-help' data-post-id='538'>(<?php _e('read more', 'formcraft'); ?>)</a></h3>
						<div class='option-description'>
							<?php _e('When the form has been successfully submitted, we will also send the form data to a URL of your choice:','formcraft'); ?>
							<input type='text' placeholder='http://example.com/my_app.php' style='width: 100%' ng-model='Builder.Config.webhook'>
							<label>
								<input style='margin-right: 2px' type='radio' ng-model='Builder.Config.webhook_method' value='POST'>
								POST
							</label>
								<label style='margin-left: 8px'>
									<input style='margin-right: 2px' type='radio' ng-model='Builder.Config.webhook_method' value='POSTJSON'>
								POST (JSON)
							</label>
								<label style='margin-left: 8px'>
									<input style='margin-right: 2px' type='radio' ng-model='Builder.Config.webhook_method' value='GET'>
								GET
							</label>
						</div>
					</label>
				</div>
				<div class='email-setup-cover'>
					<div class='ac-toggle'>
						<?php _e('Email Setup','formcraft'); ?>
						<i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i>
						<i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i>
					</div>
					<div class='ac-inner'>
						<div style='width: 100%'>
							<div class='help-link'>
								<a class='trigger-help' data-post-id='33'><?php _e('what do I fill in here?', 'formcraft'); ?></a>
							</div>
							<div ng-slide-toggle='Builder.Config.notifications.showTip' style='font-size: 12px; padding: 7px; border: 1px solid #ddd; background: white; border-radius: 2px; width: 100%; margin-bottom: 5px'>
								If you are using Google Apps, you may need to enable the option ‘Enforce Less Secure Apps’. You can read about it <a target='_blank' href='https://support.google.com/a/answer/6260879?hl=en'>here</a>.
							</div>
							<div class='button-checkbox-group type-text email-method-select'>
								<label>
									<input update-label value='php' type='radio' ng-model='Builder.Config.notifications._method'/>
									<?php _e('WP Mail','formcraft'); ?>
								</label>
								<label>
									<input update-label value='smtp' type='radio' ng-model='Builder.Config.notifications._method'/>
									<?php _e('SMTP Method','formcraft'); ?>
								</label>
								<i data-placement='top' data-html='true' class='formcraft-icon tooltip-icon float-right' data-toggle='tooltip' title='<strong>WP Mail:</strong> Uses default WordPress mail settings. If you have configured a third-party email plugin, check this option.<br/><strong>SMTP Method:</strong> Use custom SMTP settings to send emails. This method requires you to have SMTP config from your web host.'>info_outline</i>
							</div>
							<div class='email-method-list'>
								<div ng-class='["email-php", "method-"+Builder.Config.notifications._method]'>
									<div class='input-group'>
										<label style='width: 100%'>
											<input type='text' placeholder='Sender Name' ng-model='Builder.Config.notifications.general_sender_name'/>
										</label>
										<label style='width: 100%'>
											<input style='border-bottom-width: 0px' type='text' placeholder='Sender Email' ng-model='Builder.Config.notifications.general_sender_email'/>
										</label>
									</div>
								</div>
								<div ng-class='["email-smtp", "method-"+Builder.Config.notifications._method]'>
									<div class='input-group'>
										<label style='width: 100%'>
											<input type='text' placeholder='Sender Name' ng-model='Builder.Config.notifications.general_sender_name'/>
										</label>
										<label style='width: 100%'>
											<input type='text' placeholder='Sender Email' ng-model='Builder.Config.notifications.general_sender_email'/>
										</label>
										<label style='width: 100%'>
											<input type='text' placeholder='Username' ng-model='Builder.Config.notifications.smtp_sender_username'/>
										</label>
										<label style='width: 100%'>
											<input type='password' placeholder='Password' ng-model='Builder.Config.notifications.smtp_sender_password'/>
										</label>
										<label style='width: 100%'>
											<input type='text' placeholder='SMTP Host' ng-model='Builder.Config.notifications.smtp_sender_host'/>
										</label>
										<div>
											<label style='width: 50%'>
												<input style='border-bottom-width: 0px' type='text' placeholder='Port' ng-model='Builder.Config.notifications.smtp_sender_port'/>
											</label>
											<label style='width: 50%'>
												<select style='border-left-width: 1px; border-bottom-width: 0px' ng-model='Builder.Config.notifications.smtp_sender_security'>
													<option value=''>Security Type</option>
													<option value='none'>None</option>
													<option value='ssl'>SSL</option>
													<option value='tls'>TLS</option>
												</select>
											</label>
										</div>
									</div>
								</div>
								<br>
								<form ng-submit='testEmail()'>
									<span><?php _e('Send Test Email To:','formcraft'); ?></span>
									<i data-placement='top' data-html='true' class='formcraft-icon tooltip-icon float-right' data-toggle='tooltip' title='<?php _e('You can send a test email to check if the email settings are working properly.<br>Add an email here, and press Enter','formcraft'); ?>'>info_outline</i>
									<input placeholder='dan@example.com' type='text' ng-model='Builder.TestEmails'/>
									<span class='float-right'>press Enter to send</span>
									<div class='email-test-result' compile='TestEmailResult'></div>
								</form>
							</div>
						</div>
					</div>
					<div class='ac-toggle'><?php _e('Email Notifications','formcraft'); ?><i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i><i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i></div>
					<div class='ac-inner'>
						<div>
							<label>
								<?php _e('Send Email(s) To','formcraft'); ?>
								<i data-placement='top' data-html='true' class='formcraft-icon tooltip-icon float-right' data-toggle='tooltip' title='<?php _e('When the form is submitted, an email will be sent to these addresses.<br>You can add multiple emails, separated by a comma.','formcraft'); ?>'>info_outline</i>								
								<input type="text" placeholder="dan@example.com, joe@example.com" ng-model='Builder.Config.notifications.recipients' style='width: 100%; padding-right: 35px'>
							</label>
						</div>
						<div style='margin-top: 1.5em'>
							<label>
								<?php _e('Email Subject','formcraft'); ?>
								<i data-placement='top' data-html='true' class='formcraft-icon tooltip-icon float-right' data-toggle='tooltip' title='<?php _e('You can add form values to subject, using the labels of fields, example: <br><strong>[Your Name]</strong>','formcraft'); ?>'>info_outline</i>							
								<input type="text" placeholder="New Form Entry - [Form Name]" ng-model='Builder.Config.notifications.email_subject' style='width: 100%; padding-right: 35px'>
							</label>
						</div>
						<div style='margin-top: 1.5em'>
							<div>
								<?php _e('Email Body','formcraft'); ?>
								<text-angular class='textangular' ng-model="Builder.Config.notifications.email_body"></text-angular>
							</div>
							<a class='trigger-help' data-post-id='186'>
								<?php _e('read more about customising email content', 'formcraft'); ?>
							</a>
							<label style='margin-top: 1em; display: block'>
								<input type='checkbox' ng-model='Builder.Config.notifications.attach_images'>
								<?php _e('Attach file uploads to emails','formcraft'); ?>
							</label>
							<label style='margin-top: 1em; display: block'>
								<input type='checkbox' ng-model='Builder.Config.notifications.form_layout'>
								<?php _e('Use form\'s multi-column layout in email body','formcraft'); ?>
							</label>
						</div>
					</div>
					<div class='ac-toggle'><?php _e('Email Autoresponders','formcraft'); ?><i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i><i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i></div>
					<div class='ac-inner'>
						<div class='option-info'>
							Make sure to edit the email field in your form and check the option "Send Autoresponder" for fields where we should send autoresponder emails.
						</div>
						<div style='margin-top: 1.5em; width: 47%; display: inline-block; margin-right: 5%'>
							<label>
								<?php _e('Sender Name','formcraft'); ?>
								<input type='text' ng-model="Builder.Config.autoresponder.email_sender_name" placeholder='<?php _e('John Smith','formcraft') ?>'/>
							</label>
						</div>
						<div style='margin-top: 1.5em; width: 47%; display: inline-block;'>
							<label>
								<?php _e('Sender Email','formcraft'); ?>
								<input type='text' ng-model="Builder.Config.autoresponder.email_sender_email" placeholder='<?php _e('john@gmail.com','formcraft') ?>'/>
							</label>
						</div>
						<div style='margin-top: 1.5em'>
							<label>
								<?php _e('Email Subject','formcraft'); ?>
								<input type='text' ng-model="Builder.Config.autoresponder.email_subject" placeholder='<?php _e('Thank you for your submission','formcraft') ?>'/>
							</label>
						</div>
						<div style='margin-top: 1.5em'>
							<label>
								<?php _e('Email Content','formcraft'); ?>
							</label>
						</div>
						<text-angular class='textangular' ng-model="Builder.Config.autoresponder.email_body"></text-angular>
						<a class='trigger-help' data-post-id='186'><?php _e('read more about customising email content', 'formcraft'); ?></a>
					</div>
				</div>
				<div>
					<div class='ac-toggle'><?php _e('Dedicated Form Page','formcraft'); ?><i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i><i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i></div>
					<div class='ac-inner'>
						<span><?php _e('You can share this link directly to allow people to fill the form','formcraft'); ?>
							<textarea onclick='select()' style='width: 100%; margin: 5px 0' rows='1' class='copy-code' readonly><?php echo get_site_url().'/form-view/'.$form_id; ?></textarea>
						</span>
						<label style='margin-top: 6px'>
							<input update-label type='checkbox' value='true' ng-model='Builder.Config.disable_form_link'>
							<?php _e('Disable This Page','formcraft'); ?>
						</label>
					</div>

					<div class='ac-toggle'><?php _e('Shortcode','formcraft'); ?><i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i><i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i></div>
					<div class='ac-inner'>
						<span><?php _e('Add this shortcode to your page / post','formcraft'); ?>.
							<textarea onclick='select()' style='width: 100%' rows='1' class='copy-code' readonly>[fc id='<?php echo $_GET['id']; ?>'][/fc]</textarea>
							<a style='padding: 0; text-align: left' data-post-id='179' class='trigger-help'><?php _e('Read more','formcraft'); ?></a>							
						</span>
					</div>

					<div class='ac-toggle'><?php _e('Post / Page Editor','formcraft'); ?><i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i><i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i></div>
					<div class='ac-inner' style='text-align: center'>
						<img style='margin: -15px -15px 5px -15px;width:108%' class='bg-image' src='<?php echo plugins_url( '../assets/images/add-form.png', __FILE__ ); ?>'/>
						<a style='padding: 0; text-align: left' data-post-id='66' class='trigger-help'><?php _e('Read more','formcraft'); ?></a>
					</div>

					<div class='ac-toggle'><?php _e('PHP Function','formcraft'); ?><i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i><i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i></div>
					<div class='ac-inner' style='text-align: center'>
						<textarea onclick='select()' style='font-family: monospace, Arial;width: 100%;white-space:pre-line' rows='5' class='copy-code' readonly>
							&#x3C;?php
							  if (function_exists(&#x22;add_formcraft_form&#x22;)) {
							    add_formcraft_form(&#x22;[fc id=&#x27;<?php echo $_GET['id']; ?>&#x27;][/fc]&#x22;);
							  }
							?&#x3E;
						</textarea>
						<a style='padding: 0; text-align: left' data-post-id='84' class='trigger-help'><?php _e('Read more','formcraft'); ?></a>
					</div>

					<div class='ac-toggle'><?php _e('Widget','formcraft'); ?><i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i><i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i></div>
					<div class='ac-inner' style='text-align: center'>
						<span><?php _e('Go to Appearance → Widgets, and add a FormCraft widget','formcraft'); ?>.</span>
					</div>

				</div>
				<div class='custom-text-cover'>
					<textarea id='success-message' autosize ng-model='Builder.Config.Messages.success' placeholder='<?php _e('Form success message (you can use HTML here)','formcraft'); ?>'></textarea>
					<input type='text' ng-model='Builder.Config.Messages.failed' placeholder='<?php _e('Form failed message','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.is_required' placeholder='<?php _e('Required field','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.is_invalid' placeholder='<?php _e('Invalid value','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.min_char' placeholder='<?php _e('Min [x] characters required','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.max_char' placeholder='<?php _e('Max [x] characters allowed','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.min_files' placeholder='<?php _e('Min [x] file(s) required','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.max_files' placeholder='<?php _e('Max [x] file(s) allowed','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.max_file_size' placeholder='<?php _e('Files bigger than [x] MB not allowed','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.allow_email' placeholder='<?php _e('Invalid Email Address','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.allow_alphabets' placeholder='<?php _e('Only alphabets allowed','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.allow_numbers' placeholder='<?php _e('Only numbers allowed','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.allow_alphanumeric' placeholder='<?php _e('Only alphabets and numbers allowed','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.allow_url' placeholder='<?php _e('Invalid URL','formcraft'); ?>'/>
					<input type='text' ng-model='Builder.Config.Messages.allow_regexp' placeholder='<?php _e('Invalid Expression','formcraft'); ?>'/>
				</div>
				<div>
					<label class='single-option'>
						<h3>
							<?php _e('Form Name','formcraft'); ?>: <input style='width: 225px; text-align: left' type='text' value='<?php echo $form_name ?>' ng-model='Builder.Config.form_name'/>
						</h3>
					</label>
					<label class='single-option'>
						<h3>
							<?php _e('Redirect on Submit','formcraft'); ?>: <input style='width: 300px; text-align: left' placeholder='http://example.com/thank-you' type='text' value='<?php echo $form_name ?>' ng-model='Builder.Config.redirect_main'/>
							<i data-placement='top' data-html='true' class='formcraft-icon' data-toggle='tooltip' title='<?php _e('Your redirect URL can contain values from your form. If your form has a field with the label <strong>Your Name</strong>, your redirect URL can use:<br><strong>http://example.com/?name=[Your Name]</strong>','formcraft'); ?>'>info_outline</i>
						</h3>
					</label>
					<label class='single-option'>
						<h3><?php _e('Redirect','formcraft'); ?> <input type='text' ng-model='Builder.Config.Redirect_delay_seconds' style='width: 60px'/> <?php _e('seconds after form submit','formcraft'); ?></h3>
					</label>
					<label class='single-option'>
						<h3><?php _e('Thousand separator: ','formcraft'); ?>
							<select ng-model='Builder.Config.thousand_separator'  style='width: 120px'>
								<option value=''><?php _e('None','formcraft'); ?></option>
								<option value=' '>space ( )</option>
								<option value=','>comma (,)</option>
								<option value='.'>period (.)</option>
							</select>
						</h3>
					</label>
					<label class='single-option'>
						<h3><?php _e('Decimal separator: ','formcraft'); ?>
							<select ng-model='Builder.Config.decimal_separator' style='width: 120px'>
								<option value='.'>period (.)</option>
								<option value=','>comma (,)</option>
							</select>
						</h3>
					</label>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.dont_submit_hidden'>
						<h3><?php _e('Don\'t submit values for hidden fields','formcraft'); ?></h3>
					</label>					
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.spin_effect'>
						<h3><?php _e('Enable spin effect on math numbers','formcraft'); ?></h3>
					</label>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.disable_enter'>
						<h3><?php _e('Disable form submit on Enter','formcraft'); ?></h3>
					</label>
					<label class='single-option has-checkbox'>
						<input update-label type='checkbox' value='true' ng-model='Builder.Config.disable_store'>
						<h3><?php _e('Delete entries','formcraft'); ?>
							<input type='text' ng-model='Builder.Config.disable_store_days' style='width: 60px'/>
							<?php _e('days later','formcraft'); ?>
						</h3>
						<div class='option-description'>
							<?php _e('You can set this to 0 to disable storing form entries in the database altogether. You would be relying on emails then. Please note that this option applies to all entries, including those collected before this option was enabled, so this would delete past entries.','formcraft'); ?>
						</div>
					</label>
					<div class='single-option'>
						<h3>
							<?php _e('Custom JavaScript','formcraft'); ?>
						</h3>
						<div class='option-description'>
							Add any JavaScript code in here, and it will be executed on page load. You don't have to use &lt;script&gt; tags. Make sure this is valid JavaScript!
						</div>
						<div class='option-description'>
							<textarea  placeholder='console.log("It Works!");' autosize ng-model='Builder.Config.CustomJS' rows='7' style='text-align: left; width: 100%; min-height: 145px; font-family: monospace, Arial;'>
							</textarea>
						</div>
					</div>
					<div class='single-option'>
						<a target='_blank' href='<?php echo get_site_url(); ?>?formcraft3_export_form=<?php echo $form_id; ?>' class='formcraft-button'><?php _e('Export Form File','formcraft'); ?></a>
						<br/>
						<div class='option-description'>
							<?php _e('You can import this form template on any other WordPress site with the plugin installed.','formcraft'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id='form_styling_box' class='option-box  state-{{Builder.Config.showStyling}}'>
			<nav class='nav-tabs-slide' data-content='#styling_tabs'>
				<span style='width: 25%' class='active'><?php _e('General','formcraft'); ?></span>
				<span style='width: 40%'><?php _e('Color Scheme','formcraft'); ?></span>
				<span style='width: 35%'><?php _e('Background','formcraft'); ?></span>
			</nav>
			<div id='styling_tabs' class='nav-content-slide'>
				<div class='active'>
					<div class='ac-toggle'>
						<?php _e('General Styling','formcraft'); ?>
						<i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i>
						<i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i>						
					</div>
					<div class='ac-inner'>
						<label class='single-option has-checkbox'>
							<input update-label type='checkbox' ng-model='Builder.form_frame' ng-true-value='"hidden"' ng-false-value='"visible"'>
							<h3><?php _e('Remove Form Frame','formcraft'); ?></h3>
						</label>
						<label class='single-option has-checkbox'>
							<input update-label type='checkbox' ng-model='Builder.form_asterisk'>
							<h3>
								<?php _e('Hide','formcraft'); ?> <span style='color: red; font-weight: bold'>*</span>
							</h3>
						</label>
						<label class='single-option has-checkbox'>
							<input update-label type='checkbox' ng-model='Builder.hide_icons'>
							<h3><?php _e('Hide Field Icons','formcraft'); ?></h3>
						</label>
					</div>
					<div class='ac-toggle'>
						<?php _e('Font Styling','formcraft'); ?>
						<i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i>
						<i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i>						
					</div>
					<div class='ac-inner'>
						<div class='single-option'>
							<select class='standalone' ng-model='Builder.Config.font_family' style='width: 52%; display: inline-block; vertical-align: top; margin-top: 3px; margin-right: 3%;'>
								<option value="inherit">Default Font</option>
								<optgroup label='<?php _e('General Fonts','formcraft'); ?>'>
									<option value="Helvetica, Arial, sans-serif">Helvetica / Arial</option>
									<option value="'Trebuchet MS', Helvetica, Arial, sans-serif">Trebuchet MS</option>
									<option value="'Courier New', Courier, monospace">Courier New</option>
									<option value="'Georgia', sans-serif">Georgia</option>
									<option value="'Times New Roman', sans-serif">Times New Roman</option>
								</optgroup>
								<optgroup label='<?php _e('Google Fonts','formcraft'); ?>'>
									<option value="Source Sans Pro">Source Sans Pro</option>
									<option value="Ubuntu">Ubuntu</option>
									<option value="Merriweather">Merriweather</option>
									<option value="Roboto">Roboto</option>
									<option value="Raleway">Raleway</option>
									<option value="Lato">Lato</option>
									<option value="Oswald">Oswald</option>
									<option value="Lora">Lora</option>
									<option value="Bitter">Bitter</option>
									<option value="Cabin">Cabin</option>
									<option value="Playfair Display">Playfair Display</option>
									<option value="Courgette">Courgette</option>
								</optgroup>
							</select>
							<div class='button-checkbox-group type-text' style='width: 42%'>
								<label ng-click='Builder.font_size = Builder.font_size + 5'>Font Size +</label>
								<label ng-click='Builder.font_size = Builder.font_size - 5'>Font Size -</label>
							</div>
						</div>
					</div>
					<div class='ac-toggle'>
						<?php _e('Field Styling','formcraft'); ?>
						<i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i>
						<i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i>						
					</div>
					<div class='ac-inner'>
						<div class='single-option'>
							<h3>Field Layout</h3>
							<div class='button-checkbox-group field-layout'>
								<label>
									<span class='change-background' style='left: 11%; width: 17.5%; bottom: 21px; height: 13px; background: #bbb'></span>
									<span class='change-background' style='left: 7%; width: 21.5%; bottom: 9px; height: 9px; background: #ccc'></span>
									<span class='change-border' style='background: white; border: 2px solid #ccc; height: 21px; left: 31%; bottom: 9px; right: 7%'></span>
									<input type='radio' fc-placeholder-update name='fs_label' update-label value='inline' ng-model='Builder.label_style'>
								</label>
								<label>
									<span class='change-background' style='z-index: 1; left: 11%; width: 30px; bottom: 14px; height: 15px; background: #bbb'></span>
									<span class='change-border' style='background: white; border: 2px solid #ccc; height: 21px; width: 86%; left: 7%; bottom: 9px'></span>
									<input type='radio' fc-placeholder-update name='fs_label' update-label value='placeholder' ng-model='Builder.label_style'>
								</label>
								<label>
									<span class='change-background' style='left: 8px; width: 20px; bottom: 49px; height: 13px; background: #bbb'></span>
									<span class='change-background' style='left: 8px; width: 35px; bottom: 37px; height: 9px; background: #ccc'></span>
									<span class='change-border' style='background: white; border: 2px solid #ccc; height: 21px; width: 86%; left: 8px; bottom: 9px'></span>
									<input type='radio' fc-placeholder-update name='fs_label' update-label value='block' ng-model='Builder.label_style'>
								</label>
								<label>
									<span class='change-background' style='z-index: 1; left: 11%; width: 30px; bottom: 14px; height: 13px; background: #bbb'></span>
									<span class='change-border' style='border-bottom: 2px solid #ccc; height: 21px; width: 86%; left: 7%; bottom: 9px; border-radius: 0'></span>
									<input type='radio' fc-placeholder-update name='fs_label' update-label value='floating' ng-model='Builder.label_style'>
								</label>
							</div>
						</div>
						<div class='single-option'>
							<h3>Field Alignment</h3>
							<div class='button-checkbox-group field-alignment'>
								<label>
									<span class='change-background' style='left: 8px; width: 20px; bottom: 49px; height: 13px; background: #bbb'></span>
									<span class='change-background' style='left: 8px; width: 35px; bottom: 37px; height: 9px; background: #ccc'></span>
									<span class='change-border' style='background: white; border: 2px solid #ccc; height: 21px; width: 86%; left: 8px; bottom: 9px'></span>
									<input type='radio' fc-placeholder-update name='fs_label' update-label value='left' ng-model='Builder.form_internal_alignment'>
								</label>
								<label>
									<span class='change-background' style='left: 50%; margin-left: -10px; width: 20px; bottom: 49px; height: 13px; background: #bbb'></span>
									<span class='change-background' style='left: 50%; margin-left: -17px; width: 35px; bottom: 37px; height: 9px; background: #ccc'></span>
									<span class='change-border' style='background: white; border: 2px solid #ccc; height: 21px; width: 86%; left: 8px; bottom: 9px'></span>
									<input type='radio' fc-placeholder-update name='fs_label' update-label value='center' ng-model='Builder.form_internal_alignment'>
								</label>
								<label>
									<span class='change-background' style='right: 8px; width: 20px; bottom: 49px; height: 13px; background: #bbb'></span>
									<span class='change-background' style='right: 8px; width: 35px; bottom: 37px; height: 9px; background: #ccc'></span>
									<span class='change-border' style='background: white; border: 2px solid #ccc; height: 21px; width: 86%; right: 8px; bottom: 9px'></span>
									<input type='radio' fc-placeholder-update name='fs_label' update-label value='right' ng-model='Builder.form_internal_alignment'>
								</label>
							</div>							
						</div>				
					</div>
					<div class='ac-toggle'>
						<?php _e('Your Logo','formcraft'); ?>
						<i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i>
						<i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i>						
					</div>
					<div class='ac-inner'>
						<div class='single-option'>
							<input style='font-family: monospace, Arial' type='text' placeholder='<?php _e('Image URL','formcraft'); ?>' ng-model='Builder.Config.form_logo_url'/>
						</div>
					</div>
					<div class='ac-toggle'>
						<?php _e('Custom CSS','formcraft'); ?>
						<i class='formcraft-icon icon-type-down'>keyboard_arrow_down</i>
						<i class='formcraft-icon icon-type-up'>keyboard_arrow_up</i>						
					</div>
					<div class='ac-inner'>
						<div class='single-option'>
							<textarea autosize id='custom-css-textarea' ng-model='Builder.Config.Custom_CSS' rows='7' style='width: 100%; min-height: 145px; margin-top: 5px; font-family: monospace, Arial'>
							</textarea>
							<a style='padding-bottom: 0' class='trigger-help' data-post-id='253'><?php _e('Guide to using Custom CSS', 'formcraft'); ?></a>
						</div>
					</div>
				</div>
				<div>
					<div class='option-head'>
						<?php _e('Choose A Color Scheme','formcraft'); ?>
					</div>
					<div class='single-option'>
						<div class='color-schemes colors'>
							<label style='background: #4488ee; border-color: <?php echo fc_adjustBrightness('#4488ee', -30); ?>'>
								<input type='radio' update-label value='#4488ee' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #a9a9a9; border-color: <?php echo fc_adjustBrightness('#a9a9a9', -30); ?>'>
								<input type='radio' update-label value='#a9a9a9' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #e9967a; border-color: <?php echo fc_adjustBrightness('#e9967a', -30); ?>'>
								<input type='radio' update-label value='#e9967a' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #3cb371; border-color: <?php echo fc_adjustBrightness('#3cb371', -30); ?>'>
								<input type='radio' update-label value='#3cb371' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #8FBC8F; border-color: <?php echo fc_adjustBrightness('#8FBC8F', -30); ?>'>
								<input type='radio' update-label value='#8FBC8F' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #F08080; border-color: <?php echo fc_adjustBrightness('#F08080', -30); ?>'>
								<input type='radio' update-label value='#F08080' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #778899; border-color: <?php echo fc_adjustBrightness('#778899', -30); ?>'>
								<input type='radio' update-label value='#778899' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #FF6347; border-color: <?php echo fc_adjustBrightness('#FF6347', -30); ?>'>
								<input type='radio' update-label value='#FF6347' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #4682B4; border-color: <?php echo fc_adjustBrightness('#4682B4', -30); ?>'>
								<input type='radio' update-label value='#4682B4' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #5F9EA0; border-color: <?php echo fc_adjustBrightness('#5F9EA0', -30); ?>'>
								<input type='radio' update-label value='#5F9EA0' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #deb887; border-color: <?php echo fc_adjustBrightness('#deb887', -30); ?>'>
								<input type='radio' update-label value='#deb887' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #ff69b4; border-color: <?php echo fc_adjustBrightness('#ff69b4', -30); ?>'>
								<input type='radio' update-label value='#ff69b4' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #cd5c5c; border-color: <?php echo fc_adjustBrightness('#cd5c5c', -30); ?>'>
								<input type='radio' update-label value='#cd5c5c' ng-model='Color_scheme' name='radio_cs'>
							</label>
							<label style='background: #637bb3; border-color: <?php echo fc_adjustBrightness('#637bb3', -30); ?>'>
								<input type='radio' update-label value='#637bb3' ng-model='Color_scheme' name='radio_cs'>
							</label>
						</div>
					</div>
					<div class='custom-color'>
						<div class='option-head'>
							<?php _e('Or Build A Custom One','formcraft'); ?>
						</div>
						<div class='single-option has-checkbox' style='border-top-width: 1px'>
							<input type="text" value="#fff" angular-color class="color-picker" ng-model='Builder.Config.color_scheme_button'>							
							<h3>
								<?php _e('Base Color','formcraft'); ?>
							</h3>
						</div>
						<div class='single-option has-checkbox'>
							<input type="text" value="#fff" angular-color class="color-picker" ng-model='Builder.Config.color_scheme_font'>							
							<h3>
								<?php _e('Button Font Color','formcraft'); ?>
							</h3>
						</div>
						<div class='single-option has-checkbox'>
							<h3>
								<?php _e('Pagination Button Color','formcraft'); ?>
							</h3>
							<input type="text" value="#fff" angular-color class="color-picker" ng-model='Builder.Config.color_scheme_step'>
						</div>
						<div class='single-option has-checkbox'>
							<h3>
								<?php _e('Field Background Color','formcraft'); ?>
							</h3>
							<input type="text" value="#fff" angular-color class="color-picker" ng-model='Builder.Config.color_field_background'>
						</div>
						<div class='single-option has-checkbox'>
							<h3>
								<?php _e('General Font Color','formcraft'); ?>
							</h3>
							<input type="text" value="#fff" angular-color class="color-picker" ng-model='Builder.Config.font_color'>
						</div>
						<div class='single-option has-checkbox'>
							<h3>
								<?php _e('Field Font Color','formcraft'); ?>
							</h3>
							<input type="text" value="#777" angular-color class="color-picker" ng-model='Builder.Config.field_font_color'>
						</div>
					</div>
				</div>
				<div>
					<div class='option-head'>
						<?php _e('Choose A Form Background','formcraft'); ?>
					</div>
					<label class='single-option has-checkbox' style='border-top-width: 1px'>
						<input type='radio' name='radio_bs_type' value='none' ng-model='Builder.Config.form_background_type'/>
						<h3>Transparent / None</h3>
					</label>
					<label class='single-option has-checkbox'>
						<input type='radio' name='radio_bs_type' value='white' ng-model='Builder.Config.form_background_type'/>
						<h3>White</h3>
					</label>
					<label class='single-option has-checkbox'>
						<input type='radio' name='radio_bs_type' value='image' ng-model='Builder.Config.form_background_type'/>
						<h3>Preset Image</h3>
						<div ng-slide-toggle='Builder.Config.form_background_type == "image"' class='color-schemes image-schemes hide-checkbox'>
							<?php
							foreach ($backgrounds as $key => $value) {
								?>
								<label ng-click='clearCustom()' title='<?php echo $value[0]; ?>' style='background: <?php echo $value[2]; ?>'>
									<input type='radio' name='radio_bs' update-label value='<?php echo $value[1]; ?>' ng-model='Builder.form_background'>
								</label>
								<?php
							}
							?>
						</div>
					</label>
					<label class='single-option has-checkbox'>
						<input type='radio' name='radio_bs_type' value='custom' ng-model='Builder.Config.form_background_type'/>
						<h3>Custom Image</h3>
						<div ng-slide-toggle='Builder.Config.form_background_type == "custom"'>
							<input type='text' placeholder='Image URL' ng-model='Builder.form_background_custom_image'>
						</div>
					</label>
				</div>
			</div>
		</div>

		<div id='main-options-panel'>

			<a href='admin.php?page=formcraft-dashboard'>
				<i class='formcraft-icon'>keyboard_arrow_left</i>Dashboard
			</a>

			<button id='form_options_button' ng-click='Builder.Config.showOptions = !Builder.Config.showOptions; Builder.Config.showStyling = false; Builder.Config.showAddons = false; Builder.Config.showLogic = false' class='active-{{Builder.Config.showOptions}}'>
				<i class='formcraft-icon'>settings</i><?php _e('Settings','formcraft') ?>
			</button>

			<button id='form_styling_button' ng-click='Builder.Config.showStyling = !Builder.Config.showStyling; Builder.Config.showAddons = false; Builder.Config.showOptions = false; Builder.Config.showLogic = false' class='active-{{Builder.Config.showStyling}}'>
				<i class='formcraft-icon'>format_size</i><?php _e('Styling','formcraft') ?>
			</button>

			<button id='form_addons_button' ng-click='Builder.Config.showAddons = !Builder.Config.showAddons; Builder.Config.showStyling = false; Builder.Config.showOptions = false; Builder.Config.showLogic = false' class='active-{{Builder.Config.showAddons}}'>
				<i class='formcraft-icon'>library_add</i><?php _e('AddOns','formcraft') ?>
			</button>

			<button id='form_logic_button' ng-click='Builder.Config.showLogic = !Builder.Config.showLogic; Builder.Config.showStyling = false; Builder.Config.showOptions = false; Builder.Config.showAddons = false' class='active-{{Builder.Config.showLogic}}'>
				<i class='formcraft-icon'>shuffle</i><?php _e('Logic','formcraft') ?>
			</button>

			<button id='form_save_button' ng-click='saveForm()'>
				<div class='formcraft-loader'></div>
				<i class='formcraft-icon'>file_upload</i>
				<span class='save-text'><?php _e('Save','formcraft') ?></span>
				<span class='saving-text'><?php _e('Saving','formcraft') ?></span>
			</button>

			<button style='display: none' id='plugin-save' ng-click='saveForm("pluginInstalled")'></button>

			<button type='submit' ng-click='saveForm("preview")' id='form_preview_button'>
				<i class='formcraft-icon'>open_in_new</i><?php _e('Preview','formcraft') ?>
			</button>

			<button data-target='#help_modal' data-toggle='fc_modal' type='submit' id='help_button'>
				<i class='formcraft-icon'>help_outline</i><?php _e('Help','formcraft') ?>
			</button>

		</div>

		<style>
		{{Builder.Config.Custom_CSS}}
	</style>
	<div class='form-cover-builder'>
		<span class='fc-spinner fc-spinner-form small'><div class='bounce1'></div><div class='bounce2'></div><div class='bounce3'></div></span>
		<div id='form-cover-html' class='nos-{{Builder.FormElements.length}}{{Builder.FormElements[0].length}}' style='width: {{Builder.form_width}}'>
			<div class='fc-pagination-cover fc-pagination-{{Builder.FormElements.length}}'>
				<div class='fc-pagination' style='width: 100%'>
					<div class='pagination-trigger' data-index='{{$index}}' ng-repeat='page in Builder.FormElements'>
						<span class='page-number'><span>{{$index+1}}</span></span>
						<span class='page-name'>{{Builder.Config.page_names[$index]}}</span>
						<!--RFH--><input type='text' ng-model='Builder.Config.page_names[$index]'><!--RTH-->
					</div>
				</div>
			</div>
			<!--RFH-->
			<div class='no-fields' ng-click='Builder.Config.show_fields = true'><?php _e('(No Fields)','formcraft'); ?></div>
			<div style='width: {{Builder.form_width}}' id='form-width-cover'><span><?php _e('Width','formcraft') ?></span><input ng-model='Builder.form_width' type='text'/>
			</div>
			<!--RTH-->
			<style scoped='scoped'>
			@media (max-width : 480px) {
				.fc_modal-dialog-<?php echo $form_id; ?> .fc-pagination-cover .fc-pagination
				{
					background-color: {{Builder.form_background}} !important;
				}
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .submit-cover .submit-button,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .fileupload-cover .button-file,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .field-cover .button,
			#ui-datepicker-div.fc-datepicker .ui-datepicker-header,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .stripe-amount-show
			{
				background: {{Builder.Config.color_scheme_button}};
				color: {{Builder.Config.color_scheme_font}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .slider-cover .ui-slider-range
			{
				background: {{Builder.Config.color_scheme_button}};
			}
			#ui-datepicker-div.fc-datepicker .ui-datepicker-header,
			.formcraft-css .fc-form .field-cover>div.full hr
			{
				border-color: {{Builder.Config.color_scheme_button_dark}};
			}
			#ui-datepicker-div.fc-datepicker .ui-datepicker-prev:hover,
			#ui-datepicker-div.fc-datepicker .ui-datepicker-next:hover,
			#ui-datepicker-div.fc-datepicker select.ui-datepicker-month:hover,
			#ui-datepicker-div.fc-datepicker select.ui-datepicker-year:hover
			{
				background-color: {{Builder.Config.color_scheme_button_dark}};
			}
			.formcraft-css .fc-pagination>div.active .page-number,
			.formcraft-css .form-cover-builder .fc-pagination>div:first-child .page-number
			{
				background-color: {{Builder.Config.color_scheme_step}};
				color: {{Builder.Config.color_scheme_font}};
			}
			#ui-datepicker-div.fc-datepicker table.ui-datepicker-calendar th,
			#ui-datepicker-div.fc-datepicker table.ui-datepicker-calendar td.ui-datepicker-today a,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .star-cover label,
			html .formcraft-css .fc-form.label-floating .form-element .field-cover.has-focus>span,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .customText-cover a,
			.formcraft-css .prev-next>div span:hover
			{
				color: {{Builder.Config.color_scheme_button}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .customText-cover a:hover
			{
				color: {{Builder.Config.color_scheme_button_dark}};
			}
			html .formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>.label-floating .form-element .field-cover > span
			{
				color: {{Builder.Config.font_color}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>.label-floating .form-element .field-cover input[type="text"],
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>.label-floating .form-element .field-cover input[type="email"],
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>.label-floating .form-element .field-cover input[type="password"],
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>.label-floating .form-element .field-cover input[type="tel"],
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>.label-floating .form-element .field-cover textarea,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>.label-floating .form-element .field-cover select,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>.label-floating .form-element .field-cover .time-fields-cover
			{
				border-bottom-color: {{Builder.Config.font_color}};
				color: {{Builder.Config.field_font_color}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .field-cover input[type="text"],
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .field-cover input[type="password"],
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .field-cover input[type="email"],
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .field-cover input[type="tel"],
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .field-cover select,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .field-cover textarea
			{
				background-color: {{Builder.Config.color_field_background}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .star-cover label .star
			{
				text-shadow: 0px 1px 0px {{Builder.Config.color_scheme_button_dark}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .slider-cover .ui-slider-range
			{
				box-shadow: 0px 1px 1px {{Builder.Config.color_scheme_button_dark}} inset;
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .fileupload-cover .button-file,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .stripe-amount-show
			{
				border-color: {{Builder.Config.color_scheme_button_dark}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .stripe-cover.field-cover div.stripe-amount-show::before
			{
				border-top-color: {{Builder.Config.color_scheme_button_dark}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .stripe-cover.field-cover div.stripe-amount-show::after
			{
				border-right-color: {{Builder.Config.color_scheme_button}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .stripe-amount-show,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .submit-cover .submit-button .text,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .field-cover .button
			{
				text-shadow: 1px 0px 3px {{Builder.Config.color_scheme_button_dark}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .form-element-html input[type="password"]:focus,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .form-element-html input[type="email"]:focus,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .form-element-html input[type="tel"]:focus,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .form-element-html input[type="text"]:focus,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .form-element-html textarea:focus,
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .form-element-html select:focus
			{
				border-color: {{Builder.Config.color_scheme_button}};
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?> .form-element .form-element-html .field-cover .is-read-only:focus {
				border-color: #ccc;
			}
			.formcraft-css .fc-form.fc-form-<?php echo $form_id; ?>
			{
				font-family: {{Builder.Config.font_family}};
			}
			@media (max-width : 480px) {
				html .dedicated-page,
				html .dedicated-page .formcraft-css .fc-pagination > div.active
				{
					background: {{Builder.form_background}};
				}
			}
		</style>
		<div class='form-cover'>
			<form data-thousand='{{Builder.Config.thousand_separator}}' data-decimal='{{Builder.Config.decimal_separator}}' data-delay='{{Builder.Config.Redirect_delay_seconds}}' data-id='<?php echo $form_id; ?>' class='fc-form fc-form-<?php echo $form_id; ?> label-{{Builder.label_style}} align-{{Builder.form_align}} fc-temp-class frame-{{Builder.form_frame}} spin-{{Builder.spin_effect}} save-form-{{Builder.Config.save_progress}} dont-submit-hidden-{{Builder.Config.dont_submit_hidden}} remove-asterisk-{{Builder.form_asterisk}} icons-hide-{{Builder.hide_icons}} field-alignment-{{Builder.form_internal_alignment}} disable-enter-{{Builder.Config.disable_enter}}' style='width: {{Builder.form_width}}; color: {{Builder.Config.font_color}}; font-size: {{Builder.font_size}}%; background: {{Builder.form_background}}'>
				<div class='form-page form-page-{{$index}}' ng-repeat='page in Builder.FormElements' data-index='{{$index}}'>
					<!--RFH-->
					<div class='delete-page' ng-click='removeFormPage($index)' title='<?php _e('Delete Page','formcraft'); ?>'>
						<i class='formcraft-icon'>delete</i>
					</div>
					<!--RTH-->
					<div ng-init='builderInit()' ui-sortable="sortableOptions[$index]" ng-model='page' class='form-page-content'>
						<div ng-class-odd="'odd'" data-identifier='{{element.elementDefaults.identifier}}' ng-class='["form-element", "form-element-"+element.elementDefaults.identifier, "options-"+element.showOptions, "index-"+element.showOptions, "form-element-"+$index, "default-"+element.elementDefaults.hidden_default, "form-element-type-"+element.type, "is-required-"+element.elementDefaults.required]' ng-class-even="'even'" ng-repeat='element in page track by element.identifier' data-index='{{$index}}' style='width: {{element.elementDefaults.field_width}}'>
							<div ng-click='toggleOptions($parent.$index, $index)' watch-show-options='{{element.showOptions}}' class='form-element-html' compile='element.element'>
							</div>
							<!--RFH-->
							<div class='form-options animate-{{element.showOptionsAnimate}} state-{{element.showOptions}}'>
								<div class='options-head'>
									<div title='Field ID' class='field-id'>{{element.elementDefaults.identifier}}</div>
									<i title='Delete field' ng-click='removeFormElement($parent.$index, $index)' class='delete formcraft-icon'>add_circle</i>
									<i title='Duplicate field' ng-click='duplicateFormElement($parent.$index, $index)' class='duplicate formcraft-icon'>add_circle</i>
									<i title='Minimize options' ng-click='toggleOptions($parent.$index, $index)' class='minimize formcraft-icon'>remove_circle</i>									
								</div>
								<div class='options-main' compile='element.elementOptions'></div>
							</div>
							<!--RTH-->
						</div>
					</div>
				</div>
			</form>
			<div class='prev-next prev-next-{{Builder.FormElements.length}}' style='width: {{Builder.form_width}}; color: {{Builder.Config.font_color}}; font-size: {{Builder.font_size}}%; background: {{Builder.form_background}}'>
				<div><input type='text' ng-model='Builder.prevText'/><span class='inactive page-prev'><i class='icon-angle-left'></i>{{Builder.prevText}}</span></div>
				<div><input type='text' ng-model='Builder.nextText'/><span class='page-next'>{{Builder.nextText}}<i class='icon-angle-right'></i></span></div>
			</div>
		</div>
	</div>
</div>
<div class="fc_modal fc_fade" id="general_modal">
	<div class="fc_modal-dialog">
		<div class="fc_modal-content">
			<div class="fc_modal-header">
				<button class='fc_close' type="button" class="close" data-dismiss="fc_modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="fc_modal-title"><?php _e('Form Options','formcraft'); ?></h4>
			</div>
		</div>
	</div>
</div>
<div class="fc_modal fc_fade" id="help_modal">
	<div class="fc_modal-dialog" style="width: 800px">
		<div class="fc_modal-content">
			<div class="fc_modal-body">
				<div id='help-menu'>
					<form id='help-search'>
						<input type='text' placeholder='<?php _e('Search','formcraft'); ?>'>
					</form>
					<ul>
					</ul>
				</div>
				<div id='help-content'>
					<div class='formcraft-loader'></div>
					<div id='help-content-content'></div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

</div>
<?php

?>
