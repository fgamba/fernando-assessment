jQuery(document).ready(function($){
    $('.news-form').submit(function(e){
      e.preventDefault();
      var isValidForm = false;
      if ($('#name').val() != ""){
        isValidForm = true;
      } else {
        $('.name.error-class').html('Please fill the name input.');
        $('.name.error-class').show();
      }
      let regex = new RegExp('[a-z0-9]+@[a-z]+\.[a-z]{2,3}');
      let isValidEmail = regex.test(news_form.email.value);
      
      if ($('#email').val() != "" && isValidEmail){
        isValidForm = true;
      } else {
        $('.email.error-class').html('Please fill the email input.');
        $('.email.error-class').show();
      }
      
      if (isValidForm) {
        jQuery.ajax({
            url: ajax_var.url,
            type:"POST",
            data: {
               action:'set_form',
               name:$('#name').val(),
               email:$('#email').val(),
            }, success: function(response){
                 $(".success_msg").css("display","block");
                 //setTimeout(jQuery('.news-container').hide(), 4000);
           }, error: function(response){
               alert('Got this from the server: ' + response);
               $(".error_msg").css("display","block");      }
         });
      $('.news-form')[0].reset();
      } else {
        return false;
      }
    });  
});

