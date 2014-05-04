<?php
add_option('simple_callback_phone_number', '');
add_option('simple_callback_email_notification', SIMPLE_CALLBACK_EMAIL_NOTIFICATION_NONE);
add_option('simple_callback_email_list', '');
add_option('simple_callback_email_subject', '');
add_option('simple_callback_email_text', '');
add_option('simple_callback_form_title', 'Callback');
add_option('simple_callback_form_width', '');
add_option('simple_callback_form_height', '');


// Принимаем данные
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (isset($_POST['simpleCallBackPhoneMumber']))
		update_option('simple_callback_phone_number', $_POST['simpleCallBackPhoneMumber']);	
	
	if (isset($_POST['sendEmailNotification']))
		update_option('simple_callback_email_notification', $_POST['sendEmailNotification']);

	if (isset($_POST['sendEmailList']))
		update_option('simple_callback_email_list', $_POST['sendEmailList']);

	if (isset($_POST['emailSubject']))
		update_option('simple_callback_email_subject', $_POST['emailSubject']);	

	if (isset($_POST['emailBody']))
		update_option('simple_callback_email_text', $_POST['emailBody']);

	if (isset($_POST['formTitle']))
		update_option('simple_callback_form_title', $_POST['formTitle']);
		
		
	if (isset($_POST['formWidth']))
		update_option('simple_callback_form_width', $_POST['formWidth']);
		
	if (isset($_POST['formHeight']))
		update_option('simple_callback_form_height', $_POST['formHeight']);		
		
}


?>
<!-- Стили -->
<style type="text/css">
#simple_callback fieldset {
	border:  1px solid gray;
	border-radius: 4px;
	padding:  10px;
	margin-right:  10px;
	margin-bottom:  10px;	
}
#simple_callback legend {
	font-size: 14pt;
	padding: 5px;
}	
#simple_callback fieldset div {
	margin-bottom:  10px;
	clear:  both;
}
#simple_callback fieldset div:not(:last-child) {
	border-bottom: 1px dotted gray;
}
#simple_callback fieldset div p, #simple_callback fieldset div li {
	margin-left:  260px;
}	
#simple_callback label {
	display:  block;
	float:  left;
	width:  250px;
	margin-right: 10px;
	padding-top: 4px;
	text-align: right;
	font-weight: bold;
}
#simple_callback input, #simple_callback textarea {
	width:  50%;			
}
#simple_callback textarea
{
	height: 10em;
}
#simple_callback input[type="checkbox"] {
	width:  20px;			
}
#divSendEmailNotification {
	border-bottom-style: none !important;
}	
#divFormSize input
{
	width: 40px;
}

#divFormSize #labelFormHeight
{
	display: inline !important;
	width: auto;
	float: none;
	margin-left: 4em;
	position: relative;
	top: -2px;
}

</style>
<script type="text/javascript">
	jQuery(function ($)
	{
		var sendEmailNotification = $('#sendEmailNotification');
		var divSendEmailList = $('#divSendEmailList');
		var divEmailProps = $('#divEmailProps');

		if (sendEmailNotification.val() != <?php echo SIMPLE_CALLBACK_EMAIL_NOTIFICATION_EMAIL_LIST ?>)
			divSendEmailList.hide();

		if (sendEmailNotification.val() == <?php echo SIMPLE_CALLBACK_EMAIL_NOTIFICATION_NONE ?>)
			divEmailProps.hide();

		sendEmailNotification.change(function()
		{
			if (sendEmailNotification.val() != <?php echo SIMPLE_CALLBACK_EMAIL_NOTIFICATION_EMAIL_LIST ?>)
				divSendEmailList.hide('fast');
			else
				divSendEmailList.show('fast');	

			if (sendEmailNotification.val() == <?php echo SIMPLE_CALLBACK_EMAIL_NOTIFICATION_NONE ?>)
				divEmailProps.hide('fast');
			else
				divEmailProps.show('fast');		
		})
	});
</script>
<!-- Страница свойств -->
<div id="simple_callback">
	<h2><?php 
		echo '<img src="' . plugins_url( 'img/callback-icon-32x32.png' , __FILE__ ) . '" > ';
		_e('Simple Callback', 'simple_callback');
	?></h2>
	<form method="post" action="#">
		<fieldset>
			<legend><?php _e('Callback Form Options', 'simple_callback')?></legend>
			<div id="divPhoneNumber">
				<label for="phoneNumber"><?php _e('Your Phone Number', 'simple_callback')?></label>
				<input id="phoneNumber" name="simpleCallBackPhoneMumber" type="text" value="<?php echo get_option('simple_callback_phone_number'); ?>" />
			</div>	
			<div id="divSendEmailNotification">
				<label for="sendEmailNotification"><?php _e('Send Email Notification', 'simple_callback')?></label>
				<?php
					// Функция показа опции выпадающиего списка
					function showSendEmailNotificationOption($value, $caption, $currentOptionValue=SIMPLE_CALLBACK_EMAIL_NOTIFICATION_NONE)
					{
						echo '<option value="', $value, '"';
						if ($value == $currentOptionValue)
							echo ' selected="selected"';
						echo '>', $caption, '</option>', PHP_EOL;
					}
				?>
				<select id="sendEmailNotification" name="sendEmailNotification">
					<?php
						$currentOptionValue = get_option('simple_callback_email_notification');
						showSendEmailNotificationOption(SIMPLE_CALLBACK_EMAIL_NOTIFICATION_NONE, __('None', 'simple_callback'), $currentOptionValue);
						showSendEmailNotificationOption(SIMPLE_CALLBACK_EMAIL_NOTIFICATION_ADMINS, __('To all administrators', 'simple_callback'), $currentOptionValue);
						showSendEmailNotificationOption(SIMPLE_CALLBACK_EMAIL_NOTIFICATION_EMAIL_LIST, __('To this e-mails list', 'simple_callback'), $currentOptionValue);
					?>
				</select>
			</div>		
			<div id="divSendEmailList">
				<label for="sendEmailList"><?php _e('Email List', 'simple_callback')?></label>
				<textarea id="sendEmailList" name="sendEmailList"><?php echo get_option('simple_callback_email_list'); ?></textarea>
			</div>
			<div id="divEmailProps">
				<label for="emailSubject"><?php _e('E-mail Subject', 'simple_callback')?></label>
				<input id="emailSubject" name="emailSubject" type="text" value="<?php echo get_option('simple_callback_email_subject'); ?>" />	
				<br/>
				<label for="emailBody"><?php _e('E-mail Text', 'simple_callback')?></label>
				<textarea id="emailBody" name="emailBody"><?php echo get_option('simple_callback_email_text'); ?></textarea>
				<p><?php _e('You can use <strong>[name]</strong> and <strong>[phone]</strong> shortcodes at E-mail subject and text.', 'simple_callback')?></p>
			</div>
			<div id="divFormTitle">
				<label for="formTitle"><?php _e('Dialog Title', 'simple_callback')?></label>
				<input id="formTitle" name="formTitle" type="text" value="<?php echo get_option('simple_callback_form_title'); ?>" />
				<p><?php _e('Specify your form title (jQuery UI Dialog)', 'simple_callback')?></p>
			</div>				
			<div id="divFormSize">
				<label for="formWidth"><?php _e('Width', 'simple_callback')?></label>
				<input id="formWidth" name="formWidth" type="text" size="4" value="<?php echo get_option('simple_callback_form_width'); ?>" /> px.
				<label for="formHeight" id="labelFormHeight"><?php _e('Height', 'simple_callback')?></label>
				<input id="formHeight" name="formHeight" type="text" size="4" value="<?php echo get_option('simple_callback_form_height'); ?>" /> px.
				<p><?php _e('Specify your form (jQuery UI Dialog) size. Leave fields empty for auto sizes', 'simple_callback')?></p>
			</div>			
			
			
		</fieldset>
		<div>
			<button class="button button-primary" type="submit"><?php _e('Update settings', 'simple_callback')?></button>
		</div>
	</form>
	<h3><?php _e('Plugin Usage', 'simple_callback');	?></h3>
	<p><?php _e('Use the <strong>[callback-phone] [callback-form]</strong> shortcode at any post or page.', 'simple_callback')?></p>
	<p><?php _e('Use the <strong>simpleCallbackForm()</strong> function at any template.', 'simple_callback')?></p>
</div>