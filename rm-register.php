<?php



add_shortcode('rm_registration_form', 'rm_register_form');


function rm_register_form(){

       
    if ( is_user_logged_in() ) {
       return <<<HTMLBody

       <div class="rm-popup-container hide">

        <form class="rm-registration-form">

            <a class="rm-close-modal">X</a>

            <h4>You are already registered</h4>

        </form>

       </div>
       HTMLBody;
    }


    $content = <<<HTMLBody

    <div class="rm-popup-container hide">
    
        <form class="rm-registration-form">

            <div class="grid-two-cols">
                <h4>Sign up</h4>
                <a class="rm-close-modal">X</a>
            </div>

            <div class="registration-form-container">
                
                

                <div class="field-container">
                    <input type="text" placeholder="First Name" id="rm_register_first_name">
                    <span></span>
                </div>

            
                <div  class="field-container">
                    <input type="text" placeholder="Last Name" id="rm_register_last_name">
                    <span></span>
                </div>
            
                <div  class="field-container">
                    <input type="email" placeholder="Email" id="rm_register_email">
                    <span></span>
                </div>
                
                <div  class="field-container">
                    <input type="text" placeholder="Phone Number" id="rm_register_phone_number">
                    <span></span>
                </div>
            
                <div  class="field-container">
                    <input type="password" placeholder="Password" id="rm_register_password">
                    <span></span>
                </div>
                
                <a class="rm-button rm-submit-registration">Register</a>
            
            </div>

        </form>
    
    </div>

    HTMLBody;



    return $content;
}




add_action('wp_ajax_nopriv_rm_registration','rm_register');
add_action('wp_ajax_rm_registration','rm_register');


function rm_register(){

    	//check ajax nonce
	if(check_ajax_referer('rm-registration-obj','nonce')){
        

       $data = [
            'firstName' => sanitize_text_field($_POST['firstName']),
            'lastName' => sanitize_text_field($_POST['lastName']),
            'email' => sanitize_text_field($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'password' => sanitize_text_field($_POST['password'])
            
       ];

       $errors['errors'] = [];

       foreach($data as $key => $d){

                if(empty($d))
                {
                  
                    $errors['errors'][$key] = 'Should not be empty';
                }else{

                    if($key === 'firstName' || $key === 'lastName' )
                    {
                        if(!preg_match("/^([a-zA-Z' ]+)$/",$d)){

                            $errors['errors'][$key] = 'Only alphabetical letters allowed';
                        }
                    }
                    if($key === 'email')
                    {
                        if(!is_email($d)){
                            $errors['errors'][$key] = ' is invalid';
                        }else{
                            if(email_exists($d)){
                                $errors['errors'][$key] = ' already exists';
                            }
                        }
                    }
                    if($key === 'phone')
                    {
                        if(!preg_match("/^\\d+$/",$d))
                        {
                            $errors['errors'][$key] = 'Only numbers allowed';
                        }
                    }
                 
                }


       }

       if($errors['errors']){
        wp_send_json($errors);
       }

       try{
     
            // register user to wordpress users
                $user_data = [
                                'user_login' => $data['email'],
                                'user_pass' => $data['password'],
                                'first_name' => $data['firstName'],
                                'last_name' => $data['lastName'],
                                'role' => 'subscriber',
                                'user_email' => $data['email'],
                            ];
            //  return the registered user id
            $userID = wp_insert_user( $user_data );
            
            if($userID instanceof WP_Error){
				wp_send_json([
                    'general_error' => [
                        'message' =>  $userID
                    ]
            ]);
			}
            // update phone number field if didn't exists
            update_user_meta($userID,'phone_number',$data['phone'],true);
            if($userID){
                wp_send_json(['success' =>'success']);
            }
       }catch(e){

        wp_send_json([
            'general_error' => [
               'message' => $e->getMessage(),
                'code' =>$e->getCode(),
                'user' => $userID ?? 'not registered'
            ]
        ]);
       }

    }
}