<?php
/*
  Plugin Name: SendGrid
  Plugin URI: http://wordpress.org
  Description:Sending mail using SENDGRID SMTP
  Author: Yega
  Author URI: http://cartrabbit.io/
  Version: 1.0
*/

function SendGrid_API(){
    
    if(isset($_POST['send_mail'])){
      $Email=$_POST['email'];
      $Subject=$_POST['subject'];
      $Message=$_POST['message'];
    }

    if(isset($Email) && isset($Subject) && isset($Message)){

        $email = sanitize_email($Email);  
        $body =  sanitize_text_field($Message);  
        $subject = $Subject; 
        $name = "Yegappan S";

        $headers = array(
            'Authorization: Bearer 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',
            'Content-Type: application/json',
        );

        $data = array(
            "personalizations" => array(
                array(
                    "to" => array(
                        array(
                            "email" => $email,
                            "name" => $name
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
                    "type" => "text/html",
                    "value" => $body
                )
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com/v3/mail/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $Err_Arr=json_decode($response,true);

        if(is_array($Err_Arr)){
            wpb_admin_notice_error($Err_Arr);
        } 
         
        else{
            wpb_admin_notice_success();    
        }     
    }

    echo '<div>';
        echo '<center>';
            echo '<h1>Enter the details to sent mail </h1><br><br>
            <form action="#" method="post" name="Email-form">
                Email-id :  <input type="text" required name="email"><br><br><br>
                Subject :  <input type="text" required name="subject"><br><br><br>
                Message :  <textarea required name="message"></textarea><br><br><br>
                <input type="submit" name="send_mail" value="Send Mail">
            </form>';
        echo'</center>';
    echo '</div>'; 
}

function wpb_admin_notice_error($Err_Arr) {
    echo '<div class="notice notice-error is-dismissible"><p>';
    echo ($Err_Arr["errors"][0]["message"]);
    echo '</p></div>';
}

function wpb_admin_notice_success() {
    echo '<div class="notice notice-success is-dismissible"><p>';
    echo "E-Mail Sent Successfully";
    echo '</p></div>';
}


function SendGrid_Menu(){

    add_menu_page(
      'SMTP',                 // page title  
      'SMTP',                // menu title  
      'manage_options',     // capability  
      'SMTP',              // menu slug  
      'SendGrid_API',     // callback function  
    );
}

add_action( 'admin_menu', 'SendGrid_Menu' );

?>