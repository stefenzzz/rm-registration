(function($){


    $(function(){



     $('.rm-submit-registration').click(function(){
        

        const data = {
             firstName : $('#rm_register_first_name'),
             lastName : $('#rm_register_last_name'),
             email : $('#rm_register_email'),
             phone : $('#rm_register_phone_number'),
             password : $('#rm_register_password'),
        };
       
        const errors = [];
        for (const [key,obj] of Object.entries(data)){

            if(!obj.val()){
                obj.next('span').text(obj.attr('placeholder')+' shouldn`t be emtpy');
                errors[key] = 'empty';
                
            }else{

                if(key == 'firstName' || key == 'lastName' ){
                    if(!obj.val().match(/^([a-zA-Z' ]+)$/))
                    {
                        obj.next('span').text(obj.attr('placeholder')+' should be letters only');
                        errors[key] = 'not_letters';
                    }
                   
                }
                if(key == 'phone')
                {
                    if(!obj.val().match(/^\d+$/))
                    {
                        obj.next('span').text(obj.attr('placeholder')+' are digits only');
                        errors[key] = 'not_digits';
                    }
                 
                }
               
            }

            obj.focus(function(){
                $(this).next('span').text('');
              
            });

        
        }
  
        if(Object.keys(errors).length !==0 ){
           
            return;
        }
     
        $.post(

            
            registrationObj.ajaxURL,{
                'action':'rm_registration',
                'nonce':registrationObj.nonce,
                'firstName':data.firstName.val(),
                'lastName':data.lastName.val(),
                'email':data.email.val(),
                'phone':data.phone.val(),
                'password':data.password.val(),
            },
            function(response){
        
                if(response.errors)
                {
                    if(Object.keys(response.errors).length !== 0){
                        for (const [key,value] of Object.entries(response.errors)){
                           data[key].next('span').text(data[key].attr('placeholder')+' '+value);
                        }
                    }
                }
                if(response.success){
                    $('.rm-registration-form .registration-form-container').html('<h5>Thank you for applying for membership to our site. We will review your details and send you an email letting you know whether your application has been successful or not.</h5>');
                }
                if(response.general_error){
                    
                    $('.rm-registration-form .registration-form-container').html('<h5>'+response.general_error.message+'</h5>');
                }
            }

        ).fail(function(e) {
                  $('.rm-registration-form .registration-form-container').html('<h3>Were are having technical issues at this time, please try again later</h3>');
        });

     });
    

      
     
    });

})(jQuery);