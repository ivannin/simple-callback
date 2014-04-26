<?php
// Шорткод
add_shortcode('callback-phone', 'getSimpleCallbackPhone');
// Функция показывает телефон и, при необходимости обрабатывает форму
function getSimpleCallbackPhone()
{
	// Номер телефона
	$phoneNumber = get_option('simple_callback_phone_number');
	
	// оставим в телефоне только цифры
	$phoneOnlyDigits = preg_replace('/[^\d\+]/', '', $phoneNumber);
	
	// Вывод ссылки
	$output = '<a class="simpleCallbackPhone" href="tel:' . $phoneOnlyDigits . '">' . $phoneNumber . '</a>' . PHP_EOL;
	
	// Вывод скрипта только один раз, если шорткод испольщовался несколько раз.
	static $htmlBlockEnabled;
	if (! $htmlBlockEnabled)
	{
		$form = getSimpleCallbackForm();
		$output .= <<<CALLBACK_PHONE_BLOCK
<script></script>
<div id="simpleCallbackForm" style="display:none">
$form
</div>
CALLBACK_PHONE_BLOCK;
		$htmlBlockEnabled = true;
	}
	return $output;
}

// Функция показывает номер телефона
function simpleCallbackPhone()
{
	echo getSimpleCallbackPhone();
}


// Шорткод
add_shortcode('callback-form', 'getSimpleCallbackForm');
// Функция обрабатывает и возвращает код формы
function getSimpleCallbackForm()
{
	// Вывод данных;
	$output = '';
	$errorMessage = '';
	
	// Значения полей
	$nameValue = ''; 
	$phoneValue = '';
	
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['formId']) && $_POST['formId'] == 'simple-callback-form')
	{
		$nameValue = (isset($_POST[SIMPLE_CALLBACK_POST_NAME])) ? $_POST[SIMPLE_CALLBACK_POST_NAME] : '';
		$phoneValue = (isset($_POST[SIMPLE_CALLBACK_POST_PHONE])) ? $_POST[SIMPLE_CALLBACK_POST_PHONE] : '';

		// Фильтрация и проверка на корректность
		$nameValue = mb_convert_case(stripcslashes(strip_tags(trim($nameValue))), MB_CASE_TITLE, 'UTF-8');
		$phoneValue = stripcslashes(strip_tags(trim($phoneValue)));
		// Проверка телефона
		if (!preg_match('/^[ \+\d\(\)-]{5,20}$/', $phoneValue))
			$phoneValue = '';

		// Проверка на заполненность полей
		if (empty($nameValue))
			$errorMessage .= __( 'Please, enter your name <br />', 'simple_callback' );
		if (empty($phoneValue))
			$errorMessage .= __( 'Please, enter correct phone number <br />', 'simple_callback' );

		// Если ошибок нет, обрабатываем форму
		if (empty($errorMessage))
		{
			
			// Создаем запись
			$newRecord = array(
				'post_type'		=> SIMPLE_CALLBACK_TYPE,
				'post_title'	=> $nameValue,
				// 'post_content'	=> '',
				'post_status'	=> 'publish',
			);
			// Добавляем запись
			$postId = wp_insert_post($newRecord);
			// Указываем телефон
			setSimpleCallbackField($postId, __('Phone', 'simple_callback'), $phoneValue);
			// Устанавливаем статус записи
			wp_set_object_terms($postId, __('New', 'simple_callback'), SIMPLE_CALLBACK_TAXONOMY);


			// Отправляем почту
			$notification = get_option('simple_callback_email_notification');
			if ($notification != SIMPLE_CALLBACK_EMAIL_NOTIFICATION_NONE)
			{
				// E-mail пользователей
				$emails = array();
				switch ($notification)
				{
					case SIMPLE_CALLBACK_EMAIL_NOTIFICATION_ADMINS:
						$admins = get_users('role=administrator');
						foreach ($admins as $admin)
							$emails[] = $admin->user_email;
						break;

					case SIMPLE_CALLBACK_EMAIL_NOTIFICATION_EMAIL_LIST:
						$emails = preg_split('/[\s]/', get_option('simple_callback_email_list'));
						// http://stackoverflow.com/questions/3654295/remove-empty-array-elements
						$emails = array_filter($emails, 'strlen');
						break;
				}
				
				// Подготовка письма
				$subject = get_option('simple_callback_email_subject');
				$body = get_option('simple_callback_email_text');

				// Замены
				$shortcodes = array(
					'[name]' => $nameValue,
					'[phone]' => $phoneValue,
				);
				$subject = str_replace(array_keys($shortcodes), array_values($shortcodes), $subject);
				$body = str_replace(array_keys($shortcodes), array_values($shortcodes), $body);

				// Отправляем письмо
				if (count($emails) >0 )
					wp_mail($emails, $subject, $body);
			}

			// Вывводим сообщение
			$output = '<p class="message">' . __('Thank you! We shall call you later.', 'simple_callback') . '</p>';

		}

		if (!empty($errorMessage))
			$errorMessage = '<p class="error message">' . $errorMessage . '</p>' . PHP_EOL;

	}

	// Отображение формы	
	$output = (empty($output)) ? $errorMessage . simpleCallbackGetHTML($nameValue, $phoneValue) : $output;

	return $output;
}

// Функция показывает форму
function simpleCallbackForm()
{
	echo getSimpleCallbackForm();
}

// Функция формирует HTML код формы
function simpleCallbackGetHTML($nameValue='', $phoneValue='')
{
	// Переводные строки
	$labelName = __( 'Your Name', 'simple_callback' );
	$labelPhone = __( 'Your Phone', 'simple_callback' );
	$placeholderName = __( 'Enter your name', 'simple_callback' );
	$placeholderPhone = __( 'Enter your phone', 'simple_callback' );
	$buttonSumbit = __( 'Submit', 'simple_callback' );

	// Вычисляемые поля
	$action = $_SERVER['REQUEST_URI'];
	$txtName = SIMPLE_CALLBACK_POST_NAME;
	$txtPhone = SIMPLE_CALLBACK_POST_PHONE;

	$html = <<<EOT
<form class="simple-callback-form" action="{$action}" method="post">
	<input type="hidden" name="formId" value="simple-callback-form" />
	<div>
		<label for="txtSimpleCallbackName">{$labelName}</label>
		<input id="txtSimpleCallbackName" name="{$txtName}" type="text" value="{$nameValue}" placeholder="{$placeholderName}" />
	</div>
	<div>
		<label for="txtSimpleCallbackPhone">{$labelPhone}</label>
		<input id="txtSimpleCallbackPhone" name="{$txtPhone}" type="tel" value="{$phoneValue}" placeholder="{$placeholderPhone}" />
	</div>
	<div>
		<button type="submit">{$buttonSumbit}</button>
	</div>
</form>
EOT;

	return $html;
}

?>