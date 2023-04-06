(function ($) {

    $(document).ready(function() {

        if( !blockpress_child_params.is_user_logged_in ) {

            $(document).on( 'click', '.login-popup', function(e) {
                
                e.preventDefault();
                
                $("#response-info").html('');
                
                $('#login-modal').modal({
                    backdrop: false,
                    keyboard: true,
                    focus: true,
                    show: true
                });
             
                return false;

            });

            $('#loginform').on('submit', function(e) {

                if( $(this).find('#wfls-prompt-overlay').length <= 0 ){

                    e.preventDefault();

                    $('#ajax-login-message').html('');
                    
                    $.ajax({
                        type: 'POST',
                        url: blockpress_child_params.ajax_url,
                        data: {
                            action: 'ajax_login',
                            username: $('#user_login').val(),
                            password: $('#user_pass').val(),
                            remember: $('#rememberme').is(":checked"),
                        },
                        beforeSend: function() {
                            $( '#login-modal .block-modal' ).show();
                            $(this).prop('disabled', false);  
                        },
                        success: function(data) {

                            if (data === 'success') {
                                window.location.reload();
                            } else {
                                var returnedData = JSON.parse(data);
                                $('#ajax-login-message').html('<div class="modal-message">'+returnedData.message+'</div>');
                            }
                            $( '#login-modal .block-modal' ).hide();
                            $(this).prop('disabled', true);  
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }else{
                    return true;
                }
            });
            
        }
    });

})(jQuery);