<?php
// POST параметры
define('SIMPLE_CALLBACK_POST_NAME', 'simpleCallbackName');
define('SIMPLE_CALLBACK_POST_PHONE', 'simpleCallbackPhone');


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
			


		}

		if (!empty($errorMessage))
			$errorMessage = '<p class="error-message">' . $errorMessage . '</p>' . PHP_EOL;

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