<?php
/*
  Plugin Name:      SendGrid SMTP
  Plugin URI:       http://wordpress.org
  Description:      Sending mail using Sendgrid SMTP
  Author:           Yegappan
  Author URI:       http://cartrabbit.io/
  Text Domains:     sendgrid-smtp
  Version:          2.0
  Requires at least:5.2
  Requires PHP:     7.2
  License:          GPL v2 or later
  License URI:      http://www.gnu.org/licenses/gpl-2.0.txt
*/

function sendgrid_API() {
    
    if(isset($_POST['send_mail'])) {
        
        $name       =   sanitize_text_field($_POST['uname']);
        $email      =   sanitize_email($_POST['email']);
        $subject    =   sanitize_text_field($_POST['subject']);   
        $message    =   sanitize_textarea_field($_POST['message']);  

        sendgrid_SendMail($name,$email,$subject,$message); 

    }

    include plugin_dir_path(__FILE__) . '/Form.php';
}

function sendgrid_SendMail($name,$email,$subject,$message) {

    $data = array(
        "personalizations" => array(
            array(
                "to" => array(
                    array(
                        "email" => $email,
                        "name"  => $name
                    )
                )
            )
        ),

        "from" => array(
            "email" => "nivithann06@gmail.com"
        ),

        "subject" => $subject,
        
        "content" => array(
            array(
                "type"  => "text",
                "value" => $message
            )
        )
    );

    $headers = array(
        'Authorization' => 'Bearer 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',
        'Content-Type'  => 'application/json',
    );

    $arguments = array(
        'headers'       => $headers,
        'body'          => json_encode($data),
        'method'        => 'POST',
        'httpversion'   => '1.0',
        'timeout'       => 10,
        'redirection'   => 5,
        'sslverify'     => true,
        'data_format'   => 'body',
    );

    $response = wp_remote_post("https://api.sendgrid.com/v3/mail/send",$arguments);

    if ( is_wp_error( $response ) ) {
        echo '<div class="notice notice-error is-dismissible"><p>';
        echo "Sorry , Something went wrong : Please Check Your Internet Connection !";
        echo '</p></div>';
    } else {
        if (!empty($response[ "body" ])) {
            sendgrid_ErrorMessage( $response );
        } else {
            sendgrid_SuccessMessage( $email,$name );    
        }
    }
}

function sendgrid_ErrorMessage( $response ) {
    echo '<div class="notice notice-error is-dismissible"><p>';
    $result = json_decode( $response["body"] ,true);
    $result = ($result["errors"][0]["message"]);
    echo "Sorry , Something went wrong : ". $result;
    echo '</p></div>';
}

function sendgrid_SuccessMessage( $email,$name ) {
    echo '<div class="notice notice-success is-dismissible"><p>';
    echo "Your E-Mail Sent Successfully to " . $name ." (". $email .")";
    echo '</p></div>';
}

function sendgrid_Menu() {

    add_menu_page(
      'SMTP',                 // page title  
      'SMTP',                // menu title  
      'manage_options',     // capability  
      'sendgrid-smtp',     // menu slug  
      'sendgrid_API',     // callback function  
    );
}

add_action( 'admin_menu', 'sendgrid_Menu' );

?>