<?php

if (! defined('ABSPATH')) {
    exit(); // Exit if accessed directly
}

class BlockpressChild{

    protected $default = array(
        'site_maintenance'  => false,
        'login_form_css'    => 'body.login{
    display: flex;
    align-items: center;
    flex-direction: column;
}
body #login * {
    font-weight: 300
}
body #login #wfls-token-submit{
   background: #1d1b1a;
    color: #ffffff;
    border-radius: 4px;
    border: 1px solid #1d1b1a;
    padding: 10px 20px;
    line-height: 1.7;
}
body #login #wfls-prompt-overlay{
    background-color: #fafafa;
        padding:10px;
}
body #login h1 a img {
    background: none !important;
    width: auto;
    height: auto !important;
    max-width: 100% !important;
}

div#login {
    background: #fafafa;
    padding: 20px;
    margin-top: 20px;
    margin-bottom: 20px;
    border-radius: 3px;
    box-shadow: 0 0 0 0;
}

.login form {
    margin-top: 0px;
    margin-left: 0;
    padding: 5px;
    overflow: hidden;
    background: none;
    border: 0px solid #333;
    box-shadow: none;
}

.wp-core-ui .wp-pwd .button,
.wp-core-ui .wp-pwd .button-secondary {
    background-color: transparent !important;
    border: none !important;
}

.login form .input,
.login input[type="text"],
.login input[type="password"] {
    font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.48rem) * 0.24), 1rem);
    line-height: 1.7;
    padding: 10px;
    margin: 0 6px 16px 0;
}

.login .forgetmenot label,
.login .pw-weak label {
    margin-top: 12px;
}

input#wp-submit {
    background: #1d1b1a;
    color: #ffffff;
    border-radius: 4px;
    border: 1px solid #1d1b1a;
    padding: 10px 20px;
    line-height: 1.7;
}

#nav,
#backtoblog {
    text-align: center
}',
        'login_form_logo'   => '/wp-content/themes/blockpress-child/assets/images/logo-login.png',
        'login_modal'       => false,
        'signup_text'       => 'Sign Up',
        'signup_url'        => '',
        'single_product_title'       => false,
        
    );

    public function __construct() {
        add_action( 'admin_menu', array( &$this, 'register_admin_screen' ), 1 );
        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {

    }

    public function register_admin_screen () {

        if ( current_user_can( 'manage_options' ) ){
            $add_submenu_page = add_submenu_page( 'themes.php', __( 'BlockPress' ), __( 'BlockPress' ), 'manage_options', 'blockpress-child', array( &$this, 'admin_screen' ) ); // Default
        }

    }

    public function admin_screen () {

        $message = '';

        if( isset( $_POST ) && isset( $_POST['submit'] ) ){
            if( check_ajax_referer( 'blockpress-child', '_wpnonce_blockpress_child', false) ){
                
                if( is_array( $_POST ) ){

                    $postSettings = $_POST;
                    unset( $postSettings['_wpnonce_blockpress_child'] );
                    unset( $postSettings['_wp_http_referer'] );
                    unset( $postSettings['submit'] );

                    if( is_array($postSettings) ){

                        foreach( $postSettings as $key => $value ){
                            if( isset( $_POST[ $key ] ) && $_POST[ $key ] == 'true' ){
                                $postSettings[$key] = true;
                            }elseif( isset( $_POST[ $key ] ) && $_POST[ $key ] == 'false' ){
                                $postSettings[$key] = false;
                            }else{
                                $postSettings[$key] = stripslashes( $postSettings[$key] );
                            }
                            
                        }

                        $settings =  array_merge( $this->default, $postSettings );
                        update_option( '_blockpress_child' , $settings );
                        $message = '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"> 
                        <p><strong>'.__( 'Settings updated.' ).'</strong></p></div>';
                    }
                }
            }
        }

        if( isset( $_POST ) && isset( $_POST['reset'] ) ){
            if( check_ajax_referer( 'blockpress-child', '_wpnonce_blockpress_child', false) ){
                delete_option( '_blockpress_child' );
                $message = '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"> 
                        <p><strong>'.__( 'Settings is reset.' ).'</strong></p></div>';
            }
        }

        $settings = get_option( '_blockpress_child', array() );

        if( !is_array($settings) ){
            $settings = $this->default;
        }

        $settings =  array_merge( $this->default, $settings );

        $login_css = '';

        ?>

        <div class="wrap">

            <h1><?php _e( 'BlockPress Settings' ); ?></h1>
            <?php echo $message; ?>
            <form method="post" action="themes.php?page=blockpress-child">
                <?php wp_nonce_field( 'blockpress-child', '_wpnonce_blockpress_child' ); ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="site_maintenance"><?php _e( 'Maintenance' ); ?></label></th>
                            <td>
                                <fieldset>
                                    <label for="site_maintenance">
                                        <input name="site_maintenance" type="checkbox" id="site_maintenance" value="true" <?php  echo ( isset( $settings['site_maintenance'] ) && $settings['site_maintenance'] == true ? ' checked="checked"' : '' )?>>
                                        <?php _e( 'Checked maintenance site' ); ?>
                                    </label>
                                    <p class="description"><?php _e( 'Create a page with slug is "maintenance"' ); ?></p>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="login_form_logo"><?php _e( 'Login Logo' ); ?></label></th>
                            <td>
                                <input name="login_form_logo" type="text" id="login_form_logo" value="<?php echo ( isset( $settings['login_form_logo'] ) ) ? esc_attr($settings['login_form_logo']) : '' ?>">
                                <p class="description"><?php _e( 'Enter Logo URL' ); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="login_form_css"><?php _e( 'Login CSS' ); ?></label></th>
                            <td>
                                <textarea cols="100" rows="10" name="login_form_css" id="login_form_css" aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4"><?php echo $settings['login_form_css'];?></textarea>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="login_modal"><?php _e( 'Login Modal' ); ?></label></th>
                            <td>
                                <fieldset>
            
                                    <label for="login_modal">
                                        <input name="login_modal" type="checkbox" id="login_modal" value="true" <?php  echo ( isset( $settings['login_modal'] ) && $settings['login_modal'] == true ? ' checked="checked"' : '' )?>>
                                        <?php _e( 'Checked will enable login modal.' ); ?>
                                    </label>
                                    <p class="description"><?php _e( 'While enable with class ".login-popup" click will open popup login.' ); ?></p>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="signup_text"><?php _e( 'Sign Up URL' ); ?></label></th>
                            <td>
                                <input name="signup_text" type="text" id="signup_text" value="<?php echo ( isset( $settings['signup_text'] ) ) ? esc_attr($settings['signup_text']) : __('Sign Up') ?>">
                                <p class="description"><?php _e( 'Enter text for Sign Up button. ' ); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="signup_url"><?php _e( 'Sign Up URL' ); ?></label></th>
                            <td>
                                <input name="signup_url" type="text" id="signup_url" value="<?php echo ( isset( $settings['signup_url'] ) ) ? esc_attr($settings['signup_url']) : '' ?>">
                                <p class="description"><?php _e( 'Enter Sign Up URL. ' ); ?></p>
                            </td>
                        </tr>
                        <?php
                        if( class_exists('WooCommerce') ){
                        ?>
                        <tr>
                            <th scope="row"><label for="single_product_title"><?php _e( 'Show Product Title' ); ?></label></th>
                            <td>
                                <fieldset>
                                    <label for="single_product_title">
                                        <input name="single_product_title" type="checkbox" id="single_product_title" value="true" <?php  echo ( isset( $settings['single_product_title'] ) && $settings['single_product_title'] == true ? ' checked="checked"' : '' )?>>
                                        <?php _e( 'Checked to show single product title on summary.' ); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        
                </tbody>
                </table>
                
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>"> <input type="submit" name="reset" id="reset" class="button button-secondary" value="<?php _e( 'Reset' ); ?>" onclick="return confirm('<?php _e( 'Are you sure you want reset to default settings?' ); ?>');"></p>
            </form>
        </div>

        <?php
    }

    public function get_settings() {
        $settings =  array_merge( $this->default, get_option( '_blockpress_child', array() ) );
        return $settings;
    }

}
?>
