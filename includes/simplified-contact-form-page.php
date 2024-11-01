<?php
function simplified_contact_form_admin_enqueue($hook) {
	if ($hook == 'toplevel_page_simplified-contact-form'){
		
	    wp_enqueue_style( 'simplified-contact-form-admin-style', SCF_URL . '/assets/admin/css/style.css' );
	    wp_enqueue_script('simplified-contact-form-admin-script', SCF_URL .'/assets/admin/js/script.js', array(), null, true);

	    $data = array( 
	    	'ajax_url' => admin_url( 'admin-ajax.php' ),
	    ) ;
		wp_localize_script( 'simplified-contact-form-admin-script', 'site', $data);

		// media
		wp_enqueue_media();

	}
}
add_action( 'admin_enqueue_scripts', 'simplified_contact_form_admin_enqueue' );

function simplified_contact_form_menu_item(){
    
    // Simple Contact Form Page 
	add_menu_page("Simplified Contact Form", "Simplified CF", "manage_options", "simplified-contact-form", "simplified_contact_form_page", "dashicons-phone");
	
	// Simple Contact Form Setting 
	add_action( 'admin_init', 'simplified_contact_form_settings' );
	
}

add_action("admin_menu", "simplified_contact_form_menu_item");


function simplified_contact_form_page(){
	simplified_contact_form_header();
    simplified_contact_form_settings_page();
	simplified_contact_form_footer();
}

function simplified_contact_form_settings() {
	register_setting( 'simplified-contact-form-settings', 'scf_to' );
	// register_setting( 'simplified-contact-form-settings', 'scf_from' );
	// register_setting( 'simplified-contact-form-settings', 'scf_subject' );
	register_setting( 'simplified-contact-form-settings', 'scf_headers' );
	// register_setting( 'simplified-contact-form-settings', 'scf_body' );
}

/*
$header = wpautop(get_option('rnz_ar_header'));
$footer = wpautop(get_option('rnz_ar_footer'));
$content = wpautop($post->post_content);
*/

function simplified_contact_form_settings_page(){
    ?>
	<div class="wrap">
        <form method="post" action="options.php">
            
            <?php 
            settings_fields( 'simplified-contact-form-settings' ); 
            do_settings_sections( 'simplified-contact-form-settings' ); 
            ?>
            <div class="form-container">
                <div class="form-group">
                    <label>To</label>
                    <input type="text" name="scf_to" value="<?php echo esc_attr( get_option('scf_to') ); ?>" />
                </div>
                
                <!-- <div class="form-group">
                    <label>From</label>
                    <pre>Name &lt;email_address&gt;</pre>
                    <input type="text" name="scf_from" value="<?php echo esc_attr( get_option('scf_from') ); ?>" />
                </div> 

                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="scf_subject" value="<?php echo esc_attr( get_option('scf_subject') ); ?>" />
                </div> -->

                <div class="form-group">
                    <label>Additonal Headers</label>
                    <pre>Bcc: Name &lt;email_address&gt;<br>Cc: Name &lt;email_address&gt;</pre>
                    <textarea name="scf_headers" rows="3"><?php echo esc_attr( get_option('scf_headers') ); ?></textarea>
                </div>

                <!-- <div class="form-group">
                    <label>Body</label>
                    <?php
                    // $scf_body = get_option('scf_body'); 
                    // $editor_id = 'scf_body';
                    // $settings = array( 'media_buttons' => true );
                    // wp_editor( $scf_body , $editor_id, $settings );
                    ?>
                </div>  -->

            </div>
            <?php submit_button(); ?>
        
        </form>
    </div>
	<?php
}

function simplified_contact_form_header(){
	?>
	<div class="wrap">
    	<h1>Simplified Contact Form <small><?php echo SCF_VERSION; ?></small></h1>
 		<div class="content-container">

	<?php
}
function simplified_contact_form_footer(){
	?>
		</div>
	</div>
	<small class="developer">Developed by <a href="https://www.renzramos.com" target="_blank">Renz Ramos</a></small>
	<?php
}

?>
