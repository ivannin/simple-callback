<?php
// Hook into the 'init' action
add_action( 'init', 'registerSimpleCallbackType', 0 );	

// Register Custom Post Type
function registerSimpleCallbackType() {

	$labels = array(
		'name'                => __( 'Callback Requests', 'simple_callback' ),
		'singular_name'       => __( 'Callback Request', 'simple_callback' ),
		'menu_name'           => __( 'Callback', 'simple_callback' ),
		'parent_item_colon'   => __( 'Parent Requests:', 'simple_callback' ),
		'all_items'           => __( 'All Requests', 'simple_callback' ),
		'view_item'           => __( 'View Request', 'simple_callback' ),
		'add_new_item'        => __( 'Add New Request', 'simple_callback' ),
		'add_new'             => __( 'New Request', 'simple_callback' ),
		'edit_item'           => __( 'Edit Request', 'simple_callback' ),
		'update_item'         => __( 'Update Request', 'simple_callback' ),
		'search_items'        => __( 'Search requests', 'simple_callback' ),
		'not_found'           => __( 'No requests found', 'simple_callback' ),
		'not_found_in_trash'  => __( 'No requests found in Trash', 'simple_callback' ),
	);
	$args = array(
		'label'               => __( 'simple_callback', 'simple_callback' ),
		'description'         => __( 'Callback requests', 'simple_callback' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'custom-fields', ),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => plugins_url( 'img/callback-icon-16x16.png' , __FILE__ ),
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => false,
		'capability_type'     => 'page',
	);
	register_post_type(SIMPLE_CALLBACK_TYPE, $args );

	// Register Custom Taxonomy
	$labels = array(
		'name'                       => __( 'Statuses', 'simple_callback' ),
		'singular_name'              => __( 'Status', 'simple_callback' ),
		'menu_name'                  => __( 'Status', 'simple_callback' ),
		'all_items'                  => __( 'All Statuses', 'simple_callback' ),
		'parent_item'                => __( 'Parent Status', 'simple_callback' ),
		'parent_item_colon'          => __( 'Parent Status:', 'simple_callback' ),
		'new_item_name'              => __( 'New Status Name', 'simple_callback' ),
		'add_new_item'               => __( 'Add New Status', 'simple_callback' ),
		'edit_item'                  => __( 'Edit Status', 'simple_callback' ),
		'update_item'                => __( 'Update Status', 'simple_callback' ),
		'separate_items_with_commas' => __( 'Separate statuses with commas', 'simple_callback' ),
		'search_items'               => __( 'Search statuses', 'simple_callback' ),
		'add_or_remove_items'        => __( 'Add or remove statuses', 'simple_callback' ),
		'choose_from_most_used'      => __( 'Choose from the most used statuses', 'simple_callback' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'rewrite'                    => false,
	);
	register_taxonomy(SIMPLE_CALLBACK_TAXONOMY, SIMPLE_CALLBACK_TYPE, $args );


	// Статусы по умолчанию
	$defaultStatuses = array(
		'New'			=> array('title' => __('New', 'simple_callback'), 'description' => __('New Callback Request', 'simple_callback')),
		'Later'			=> array('title' => __('Later', 'simple_callback'), 'description' => __('Dial later', 'simple_callback')),
		'Completed'		=> array('title' => __('Completed', 'simple_callback'), 'description' => __('Request Completed', 'simple_callback')),
	);
	foreach ($defaultStatuses as $slug => $status)
	{
		if (!term_exists($status['title'], SIMPLE_CALLBACK_TAXONOMY)) 
		{
			wp_insert_term(
				$status['title'],				// the term 
				SIMPLE_CALLBACK_TAXONOMY,		// the taxonomy
				array(
					'description'	=> $status['description'],
					'slug'			=> $slug,
					'parent'		=> 0
				));
		}
	}
}

// Иконка на странице аминистрирования
add_action('admin_head', 'simpleCallbackAdminCSS');
function simpleCallbackAdminCSS() 
{ ?>
<style type="text/css">
	.icon32-posts-simple_callback {
		background: url('/wp-content/plugins/simple-callback/img/callback-icon-32x32.png') no-repeat !important;
	}
</style>
<?php
}

// Возвращает значение произвольных полей
function getSimpleCallbackField($postId, $customField) 
{
	$values = get_post_meta($postId, $customField);
	if (count($values) == 0) 
		return '';
	else
		return trim($values[0]);		
}

// Пишет значение произвольных полей
function setSimpleCallbackField($postId, $customField, $value='') 
{

	add_post_meta($postId, $customField, $value, true) 
		or update_post_meta($postId, $customField, $value);
    return true;		
}


// Дополнительные колонки в таблице обратных звонков
define('SIMPLE_CALLBACK_COLUMN_PHONE', 'colCallbackPhone');

add_filter('manage_simple_callback_posts_columns', 'getCallbackColumnsHead');  
add_action('manage_simple_callback_posts_custom_column', 'showCallbackColumnsContent', 10, 2); 

// Названия колонок в таблице обратных звонков  
function getCallbackColumnsHead($defaults) 
{
	// Добавляем новые колонки и переименовываем существующие 
	$defaults['title'] = __('Customer', 'simple_callback');
    //$defaults[SIMPLE_CALLBACK_COLUMN_PHONE] = __('Phone', 'simple_callback');
	$count = 0;
	$result = array();
	foreach ($defaults as $key => $val)
	{
		if ($count == 2)
			$result[SIMPLE_CALLBACK_COLUMN_PHONE] = __('Phone', 'simple_callback');
		$result[$key] = $val;
		$count++;
	}
    return $result;  
}  
  
// Вывод данных в таблице доставки  
function showCallbackColumnsContent($column_name, $postId) 
{  
    switch ($column_name)
	{
		case SIMPLE_CALLBACK_COLUMN_PHONE:
			echo getSimpleCallbackField($postId, __('Phone', 'simple_callback'));
			break;

	}
}


?>