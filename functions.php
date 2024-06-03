<?php
/**
 * Twenty Twenty-Two functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Two
 * @since Twenty Twenty-Two 1.0
 */


if ( ! function_exists( 'twentytwentytwo_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );
	}

endif;

add_action( 'after_setup_theme', 'twentytwentytwo_support' );

if ( ! function_exists( 'twentytwentytwo_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'twentytwentytwo-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'twentytwentytwo-style' );
	}

endif;

function coauthor_metabox() {
    add_meta_box(
        'coauthor_metabox_id',
        'Coauthor', 
        'coauthor_metabox_callback',    
        'post',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'coauthor_metabox');

function coauthor_metabox_callback($post) {
    wp_nonce_field('save_metabox_coauthor', 'coauthor_metabox_nonce');
    
    $coauthor_id = get_post_meta($post->ID, 'coauthor', true);
    
    $authors = get_users(array('who' => 'authors'));

    // HTML para o campo de seleção
    echo '<label for="coauthor">Select Co-author: </label>';
    echo '<select id="coauthor" name="coauthor">';
    echo '<option value="">None</option>';
    foreach ($authors as $author) {
        $selected = ($author->ID == $coauthor_id) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($author->ID) . '" ' . $selected . '>' . esc_html($author->display_name) . '</option>';
    }
    echo '</select>';
}

function save_metabox_coauthor($post_id) {
    if (!isset($_POST['coauthor_metabox_nonce']) || !wp_verify_nonce($_POST['coauthor_metabox_nonce'], 'save_metabox_coauthor')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['coauthor'])) {
        $coauthor = sanitize_text_field($_POST['coauthor']);
        update_post_meta($post_id, 'coauthor', $coauthor);
    }
}

function coauthor_shortcode() {
    if (is_singular('post')) {
        global $post;
        $coauthor_id = get_post_meta($post->ID, 'coauthor', true);
        if (!empty($coauthor_id)) {
            $coauthor_info = get_userdata($coauthor_id);
            return '<div class="wp-block-post-author has-small-font-size"><p class="wp-block-post-author__name">Coauthor: ' . esc_html($coauthor_info->display_name) . '</p></div>';
        }
    }
    return '';
}



add_shortcode('coauthor', 'coauthor_shortcode');

add_action('save_post', 'save_metabox_coauthor');
add_action( 'wp_enqueue_scripts', 'twentytwentytwo_styles' );

add_action('rest_api_init', 'register_custom_routes');

function register_custom_routes() {
    register_rest_route('nscreen/v1', '/custom_route_message/',
     array(
        'methods' => 'GET',
        'callback' => 'custom_route_message',
        'permission_callback' => '__return_true',
    ));
}

function custom_route_message() {
    $response = array(
        'message' => 'Custom Route Message',
    );

    return rest_ensure_response($response);
}

function custom_menu() {
    add_menu_page(
        'Teste nScreen',
        'Teste nScreen',
        'manage_options',
        'teste_nscreen',
        'custom_menu_main_page',
        '',
        6
    );

    add_submenu_page(
        'teste_nscreen',
        'Teste 1',
        'Teste 1',
        'manage_options',
        'teste_nscreen_1',
        'test_screen1'
    );

    add_submenu_page(
        'teste_nscreen',
        'Teste 2',
        'Teste 2',
        'manage_options',
        'teste_nscreen_2',
        'test_screen2'
    );

	remove_submenu_page('teste_nscreen', 'teste_nscreen');
}

add_action('admin_menu', 'custom_menu');

function custom_menu_main_page() {
    echo '<div class="wrap"><h1>Teste nScreen</h1><p>Bem-vindo ao Teste nScreen.</p></div>';
}

function test_screen1() {
    echo '<div class="wrap"><h1>Teste 1</h1><p>YAY page Teste 1.</p></div>';
}

function test_screen2() {
    echo '<div class="wrap"><h1>Teste 2</h1><p>YAY page Teste 2.</p></div>';
}


require get_template_directory() . '/inc/block-patterns.php';
