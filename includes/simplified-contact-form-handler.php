<?php
class SimpleContactFormHandler{
    
    public function __construct(){
        // Ajax
        add_action( 'wp_ajax_simplified_contact_form_handler_lead_action', array($this,'simplified_contact_form_handler_lead_action') );
        add_action( 'wp_ajax_nopriv_simplified_contact_form_handler_lead_action', array($this,'simplified_contact_form_handler_lead_action') );
        
    }
    
    function get_form_open($form_id, $form_type = 'contact'){
        $form_id = time();
        $output = "<form class='scf-form scf-form-{$form_type}'>";
        $output.= '<input type="hidden" name="security" value="' . wp_create_nonce( $form_type.$form_id ) . '">';
        $output.= '<input type="hidden" name="form_type" value="' . $form_type . '">';
        $output.= '<input type="hidden" name="form_id" value="' . $form_id . '">';
        return $output;
    }
    
    function get_form_close(){
        return '</form>';
    }
    
    function get_form_loader(){
        return '<div class="scf-loading-container"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>';
    }
    
    function get_form_notification(){
        return '<div class="scf-notification-container"></div>';
    }
    
    function get_form_field( $type = "text", $name='', $class = "form-control", $placeholder = "", $required = "no"){

        if ($type == 'textarea'){
             return "<textarea name='{$name}' class='{$class}' rows='3' data-required='{$required}'>{$placeholder}</textarea>";
        }else{
             return "<input type='{$type}' name='{$name}' class='{$class}' data-required='{$required}' placeholder='{$placeholder}'/>";
        }
    }
    
    function get_form_submit($class = "form-control", $value = ""){
        return "<input type='button' class='{$class}' value='{$value}'/>";
    }
    
    function get_acceptance_text(){
        return '<label class="scf-acceptance"><input type="checkbox" class="acceptance-checkbox" data-required="yes"/> I have been fully informed of the ' . get_bloginfo('name') . ' Privacy Policy, and I give my full consent to the collection and processing of my Personal Data by ' . get_bloginfo('name') . '.</label>';
    }

    function simplified_contact_form_handler_lead_action(){

        $response = array();
        
    	if (isset($_REQUEST)){ 
            
            
            $form_id = $_REQUEST['form_id'];
            $form_type = $_REQUEST['form_type'];
            check_ajax_referer( $form_type.$form_id, 'security' );

    	    if ($_REQUEST['email_address']):
                
                $site_name = get_bloginfo('name');

                $site_url = get_site_url();
                $site_url = trim($site_url, '/');

                $server_name = $_SERVER['SERVER_NAME'];
                $server_name = str_replace('www.','',$server_name);

    	        $email_address = $_REQUEST['email_address'];
    	        $name = $_REQUEST['name'];
    	        $subject = $_REQUEST['subject'];
                $message = $_REQUEST['message'];
                
                // Email Contents
                $email_to = get_option('scf_to');
                $email_from = get_option('scf_from');
                // $email_body = wpautop(get_option('scf_body'));
                $email_subject = get_option('scf_subject');

                $email_to = $email_to;
                $email_from = $site_name . ' <noreply@' . $server_name .  '>';
                $email_subject = $site_name . ' - Contact Request by [email_address]';
                $email_body = '';
                $email_body.= '<div class="contact-request-message">';
                $email_body.= '<h1>Contact Request</h1>';
                $email_body.= '<p style="margin:0px;">Name: <strong>[name]</strong></p>';
                $email_body.= '<p style="margin:0px;">Email Address: <strong>[email_address]</strong></p>';
                $email_body.= '<p style="margin:0px;">Subject: <strong>[subject]</strong></p>';
                $email_body.= '<p style="margin:0px;">Message: <strong>[message]</strong></p>';
                $email_body.= '</div>';

                $shortcodes = array(
                    'email_address' => $email_address,
                    'name' => $name,
                    'subject' => $subject,
                    'message' => $message,
                );

                // Replace Shortcodes
                foreach ($shortcodes as $key => $shortcode){
                    $email_body = str_replace('[' . $key . ']', $shortcode, $email_body);
                    $email_subject = str_replace('[' . $key . ']', $shortcode, $email_subject);
                }

                $email_headers = array();
                $email_headers[] = 'Content-Type: text/html; charset=UTF-8';
                $email_headers[] = 'Reply-To: ' . $name . ' <' . $email_address . '>';

                if (!empty($email_from)){
                    $email_headers[] = 'From: ' . $email_from;
                }
                
                if (!empty(get_option('scf_headers'))){
                    $additional_headers = explode("\n", str_replace("\r", "", get_option('scf_headers')));
                    $email_headers = array_merge($email_headers,$additional_headers);
                }
                
                $email_body.= '<div class="other-information">';
                $email_body.= '<h2>Additional Information</h2>';
                $email_body.= '<p style="margin:0px;">Website URL: <strong>' . $site_url . '</strong></p>';
                $email_body.= '<p style="margin:0px;">IP Address: <strong>' . $_SERVER['REMOTE_ADDR'] . '</strong></p>';
                $email_body.= '<p style="margin:0px;">Date and Time: <strong>' . date("Y-m-d h:i:sa") . '</strong></p><br>';
                $email_body.= '<small>Simple Contact Form version ' . SCF_VERSION . '<small>';
                $email_body.= '</div>';

                // $response['email_to'] = $email_to;
                $response['email_body'] = $email_body;
                $response['email_subject'] = $email_subject;
                $response['email_body'] = $email_body;
                $response['email_headers'] = $email_headers;

                $email_status = wp_mail($email_to, $email_subject ,$email_body, $email_headers);
                $response['email_status'] = $email_status;
                
            else:
                $response['status'] = 'failed';
                $response['error'] = 'no-data';
            endif; 
            
            echo json_encode($response);
    		wp_die();
    	}
    	
    }
}
$simplified_contact_form_handler = new SimpleContactFormHandler();

function simplified_contact_form_shortcode( $atts ) {
    $simplified_contact_form_handler = new SimpleContactFormHandler();
    $output = '';
    $output.= $simplified_contact_form_handler->get_form_open(99 ,'contact');
    
    $output.= '<div class="scf-form-group">';
    $output.= '<label>Your Name<span class="scf-required">*</span></label>';
    $output.= $simplified_contact_form_handler->get_form_field($type = "text", $name='name', $class = "form-control", $placeholder = "", $required = "yes");
    $output.= '</div>';

    $output.= '<div class="scf-form-group">';
    $output.= '<label>Your Email Address<span class="scf-required">*</span></label>';
    $output.= $simplified_contact_form_handler->get_form_field($type = "email", $name='email_address', $class = "form-control", $placeholder = "", $required = "yes");
    $output.= '</div>';

    $output.= '<div class="scf-form-group">';
    $output.= '<label>Your Subject<span class="scf-required">*</span></label>';
    $output.= $simplified_contact_form_handler->get_form_field($type = "text", $name='subject', $class = "form-control", $placeholder = "", $required = "yes");
    $output.= '</div>';

    $output.= '<div class="scf-form-group">';
    $output.= '<label>Your Message<span class="scf-required">*</span></label>';
    $output.= $simplified_contact_form_handler->get_form_field($type = "textarea", $name='message', $class = "form-control", $placeholder = "", $required = "yes");
    $output.= '</div>';
    
    $output.= $simplified_contact_form_handler->get_acceptance_text();
    $output.= '<div class="scf-form-button-container">';
    $output.= $simplified_contact_form_handler->get_form_submit('form-control','Submit');
    $output.= '</div>';
    $output.= $simplified_contact_form_handler->get_form_loader();
    $output.= $simplified_contact_form_handler->get_form_notification();
    $output.= $simplified_contact_form_handler->get_form_close();
    return $output;
}
add_shortcode( 'simplified_contact_form', 'simplified_contact_form_shortcode' );
?>
