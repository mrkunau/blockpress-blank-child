<?php
// Theme Settings
require_once 'inc/settings.php';

global $childtheme_settings;

$blockpressChild = new BlockpressChild();

$childtheme_settings = $blockpressChild->get_settings();

// Maintenance
$pos = strpos( $_SERVER['REQUEST_URI'] , 'wp-login.php');

if( $pos == false ){

	global $childtheme_settings;

	$site_maintenance = is_array($childtheme_settings) && isset($childtheme_settings['site_maintenance']) ? (boolean)$childtheme_settings['site_maintenance'] : false;

	if( !is_user_logged_in() && $site_maintenance ){

		if($_SERVER['REQUEST_URI'] != '/maintenance/'){
			wp_redirect(get_home_url().'/maintenance/');
			exit();
		}
	}
}

// Enqueue Style & Scripts
add_action( 'wp_enqueue_scripts', '_child_custom_blockpress_styles' );

function _child_custom_blockpress_styles() {
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

	wp_register_script(
		'theme-scripts',
		get_theme_file_uri( 'assets/js/theme-scripts'.$suffix.'.js' ),
		array(),
		true
	);

	wp_enqueue_script( 'theme-scripts' );

	wp_register_script(
		'theme-popup-login',
		get_theme_file_uri( 'assets/js/theme-popup-login'.$suffix.'.js' ),
		array( 'jquery' ),
		true
	);

	$localize_script = array( 
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'wp_is_mobile'         			=> wp_is_mobile() ? true : false,
		'is_user_logged_in'         	=> is_user_logged_in() ? true : false,
	);

	$localize_script = apply_filters( 'blockpress_child_localize_script', $localize_script );
		
	wp_localize_script( 'theme-popup-login', 'blockpress_child_params', $localize_script );
	
	
}

// Tweak custom login
add_action('login_footer', 'tweak_custom_login', 99 );

if (!function_exists('tweak_custom_login')) {
    function tweak_custom_login()
    {

    	global $childtheme_settings;

    	$login_form_css = is_array($childtheme_settings) && isset($childtheme_settings['login_form_css']) ? $childtheme_settings['login_form_css'] : false;

    	$login_form_logo = is_array($childtheme_settings) && isset($childtheme_settings['login_form_logo']) ? $childtheme_settings['login_form_logo'] : '';

    	echo '<style type="text/css">';
    	if( $login_form_css !== false ){
			echo $login_form_css;
		}
		if( !empty($login_form_logo) ){
			echo 'body #login h1 a {
	            background: none !important;
	            width: auto;
	            height: auto !important;
	            text-indent: inherit !important;
	            margin: 0
	        }';
	    }
		echo '</style>';

		if( !empty($login_form_logo) ){
	    	?>
			<script type="text/javascript">
				document.addEventListener('DOMContentLoaded', function() {
				  	var loginTitleLink = document.querySelector('#login h1 a');
				  	loginTitleLink.innerHTML = '<img src="<?php echo $login_form_logo;?>" />';
				});
			</script>
			<?php
		}
    }
}

// Add open tag for "woocommerce-product-summary" woocommerce_before_single_product_summary
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_before_single_product_summary_open', 0 );

function woocommerce_before_single_product_summary_open(){
	echo '<div class="woocommerce-product-summary">';
}

// Add close tag for "woocommerce-product-summary" woocommerce_after_single_product_summary
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_after_single_product_summary_close', 0 );

function woocommerce_after_single_product_summary_close(){
	echo '</div>';
}

// Change position On Salse Single product page
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_sale_flash', 0 );

$single_product_title = is_array($childtheme_settings) && isset($childtheme_settings['single_product_title']) ? (boolean)$childtheme_settings['single_product_title'] : false;

if( $single_product_title === false ){
	// Remove title
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
}

// post_thumbnail_html
//add_filter( 'post_thumbnail_html', 'blank_child_tweak_post_thumbnail_html', 10, 5 ) ;

function blank_child_tweak_post_thumbnail_html( $html, $postID, $post_thumbnail_id, $size, $attr ){
	global $post;
	if( $post && $post->ID == $postID && $post->post_type == 'product' ){
		if( $html == '' ){
			if( function_exists( 'wc_placeholder_img' ) ){
				$html = wc_placeholder_img(  $size, $attr );
			}
		}
	}
	return $html;
}

add_filter( 'woocommerce_placeholder_img_src', 'child_woocommerce_placeholder_img_src' ) ;

function child_woocommerce_placeholder_img_src( $src ){
	global $post;
	$src = get_stylesheet_directory_uri() .'/assets/images/no-image.jpg';
	return $src;
}


add_filter( 'woocommerce_placeholder_img', 'child_woocommerce_placeholder_img', 10, 3 ) ;

function child_woocommerce_placeholder_img( $image_html, $size, $dimensions ) {

	$attr = '';

	$dimensions        = wc_get_image_size( $size );

	$default_attr = array(
		'class' => 'woocommerce-placeholder wp-post-image',
		'alt'   => __( 'Placeholder', 'woocommerce' ),
	);

	$attr = wp_parse_args( $attr, $default_attr );

	$image      =  get_stylesheet_directory_uri() .'/assets/images/no-image.jpg';
	$hwstring   = image_hwstring( $dimensions['width'], $dimensions['height'] );
	$attributes = array();

	foreach ( $attr as $name => $value ) {
		$attribute[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
	}

	$image_html = '<img src="' . esc_url( $image ) . '" ' . $hwstring . implode( ' ', $attribute ) . '/>';

	return $image_html;
}

add_filter('get_custom_logo', 'child_get_custom_logo', 9, 2);

function child_get_custom_logo( $html, $blog_id ){
    if ('' === $html) {
        $html = '
		<div class="is-default-child wp-block-site-logo">
			<a href="'.esc_url(home_url('/')).'" class="custom-logo-link" rel="home" aria-current="page">
				<img alt="'.get_bloginfo('name', 'display').'" width="640" height="360" loading="no" class="default-child-logo" src="'.esc_url(get_stylesheet_directory_uri()).'/assets/images/logo.png" />
			</a>
		</div>';
    }
    return $html ;
}

// Popup login
add_action( 'wp_footer', 'childtheme_popup_login' );

function childtheme_popup_login(){

	global $childtheme_settings;

    $login_modal = is_array($childtheme_settings) && isset($childtheme_settings['login_modal']) ? $childtheme_settings['login_modal'] : false;
    $signup_text = is_array($childtheme_settings) && isset($childtheme_settings['signup_text']) && !empty($childtheme_settings['signup_text']) ? $childtheme_settings['signup_text'] : __('Sign Up');
    $signup_url  = is_array($childtheme_settings) && isset($childtheme_settings['signup_url']) ? $childtheme_settings['signup_url'] : '';

    if( $login_modal === false || $login_modal === 'false' ){
    	return;
    }

	if( function_exists('is_account_page') && is_account_page()) {
		return;
	}

	if( function_exists('is_checkout') && is_checkout()) {
		return;
	}

    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
    if (! wp_style_is('bootstrap-modal', 'registered')) {
     	wp_register_style('bootstrap-modal', get_stylesheet_directory_uri() . '/assets/bootstrap/modal' . $suffix . '.css', array(), '4.1.1', 'all');
    }

    if (! wp_style_is('doorkeeper', 'registered')) {
     	wp_register_style('doorkeeper', get_stylesheet_directory_uri() . '/assets/bootstrap/doorkeeper' . $suffix . '.css', array(), '4.1.1', 'all');
    }

    if (! wp_script_is('bootstrap-util', 'registered')) {
        wp_register_script('bootstrap-util', get_stylesheet_directory_uri() . '/assets/bootstrap/util' . $suffix . '.js', array( 'jquery' ), '4.1.1', false);
    }

    if (! wp_script_is('bootstrap-modal', 'registered')) {
	    wp_register_script('bootstrap-modal', get_stylesheet_directory_uri() . '/assets/bootstrap/modal' . $suffix . '.js', array( 'jquery', 'bootstrap-util' ), '4.1.1', false);
	}

	wp_enqueue_style( 'bootstrap-modal');
	wp_enqueue_style( 'doorkeeper');
	wp_enqueue_script( 'bootstrap-util' );
	wp_enqueue_script( 'bootstrap-modal' );
	wp_enqueue_script( 'theme-popup-login' );

 	?>
	<div id="login-modal" class="modal doorkeeper-modal doorkeeper-modal-centered">
		<div class="modal-dialog modal-dialog-centered" data-active-tab="">
			<div class="modal-content">
				<div class="modal-body">
					<div class="block-modal" style="background-color: rgba(0, 0, 0, 0.3) !important;"><div style="margin: auto;height:250px;width: 100px;"><div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><i class="" style="font-size: 50px;"><svg width="50" height="50" class="icon-loading" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path fill="currentColor" d="M50.287 32A18.287 18.287 0 1 1 32 13.713a1.5 1.5 0 1 1 0 3A15.287 15.287 0 1 0 47.287 32a1.5 1.5 0 0 1 3 0Z" data-name="Loading"></path></svg></i></div></div></div>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				    <div class="login-core">
			      		<h2><?php _e('Login');?></h2>
			      		<div id="ajax-login-message"></div>
			      		<?php
			      		do_action('login_enqueue_scripts');
			      		ob_start();
			      		wp_login_form( array(

			      			'echo'           => true,
							// Default 'redirect' value takes the user back to the request URI.
							'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
							'form_id'        => 'loginform',
							'label_username' => __( 'Username or email address' ),
							'label_password' => __( 'Password' ),
							'label_remember' => __( 'Remember Me' ),
							'label_log_in'   => __( 'Log In' ),
							'id_username'    => 'user_login',
							'id_password'    => 'user_pass',
							'id_remember'    => 'rememberme',
							'id_submit'      => 'wp-submit',
							'remember'       => true,
							'value_username' => '',
							// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
							'value_remember' => false,
						    
						) );

						$login_form_html = ob_get_clean();
						$login_form_html = str_replace( 'button button-primary', 'button button-primary wp-element-button popup-signin-btn', $login_form_html );
						$login_form_html = str_replace( '<label for="user_login">Username or email address</label>', '<label for="user_login">Username or email address <span class="required">*</span></label>', $login_form_html );
						$login_form_html = str_replace( '<label for="user_pass">Password</label>', '<label for="user_pass">Password <span class="required">*</span></label>', $login_form_html );
						
						echo $login_form_html;

						?>
						<p id="nav">
							<?php

							if ( get_option( 'users_can_register' ) ) {
								$login_link_separator = apply_filters( 'login_link_separator', ' | ' );

								$registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register' ) );

								/** This filter is documented in wp-includes/general-template.php */
								echo apply_filters( 'register', $registration_url );

								echo esc_html( $login_link_separator );
							}

							$html_link = sprintf( '<a href="%s">%s</a>', esc_url( wp_lostpassword_url() ), __( 'Lost your password?' ) );

							/**
							 * Filters the link that allows the user to reset the lost password.
							 *
							 * @since 6.1.0
							 *
							 * @param string $html_link HTML link to the lost password form.
							 */
							echo apply_filters( 'lost_password_html_link', $html_link );

							?>
						</p>
						<?php

						if ( !empty( $signup_url ) ) {
							$signup_url = sprintf( '<div class="signup-wrapper"><a href="%s" class="popup-signup-btn button wp-element-button">%s</a></div>', esc_url( $signup_url ), $signup_text );
							/** This filter is documented in wp-includes/general-template.php */
							echo apply_filters( 'signup', $signup_url );
						}

			        	?>
				   
			     	</div>
	    		</div>
	       </div>
	    </div>
	</div>

<?php
}

add_action('wp_ajax_nopriv_ajax_login', 'blockpress_child_ajax_login');
add_action('wp_ajax_ajax_login', 'blockpress_child_ajax_login');

function blockpress_child_ajax_login() {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = $_POST['remember'];
    
    $creds = array(
        'user_login' => $username,
        'user_password' => $password,
        'remember' => $remember,
    );

    $secure_cookie   = '';
		
	// If the user wants SSL but the session is not SSL, force a secure cookie.
	if ( ! empty( $_POST['username'] ) && ! force_ssl_admin() ) {
		$user_name = sanitize_user( wp_unslash( $_POST['username'] ) );
		$user      = get_user_by( 'login', $user_name );

		if ( ! $user && strpos( $user_name, '@' ) ) {
			$user = get_user_by( 'email', $user_name );
		}

		if ( $user ) {
			if ( get_user_option( 'use_ssl', $user->ID ) ) {
				$secure_cookie = true;
				force_ssl_admin( true );
			}
		}
	}

    $user = wp_signon( $creds, $secure_cookie);

    if (is_wp_error($user)) {
        if (!empty($user->errors)) {
        	foreach( $user->errors as $error_code ){
        		$err .= $error_code[0];
        	}
        } else {
            $err = "invalid credentials username or password.";
        }
        echo json_encode(array('message' => $err));
    } else {
        echo 'success';
    }

    wp_die();
}
?>
