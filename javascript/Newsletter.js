$(function() {


    /*$('body').on('show.bs.modal', '.c-content-newsletter-form', function() {

        var modal = $(this);
        $.ajax('newsletter').done(function(html) {
            modal = $(".modal-content").html(html);
        });
            var form = $('#NewsletterForm_NewsletterForm');
            var url = $(form).attr('action');
            //console.log(url);
    });*/


    $('#NewsletterForm_NewsletterForm input[type="submit"]').click(function(event) {
        event.preventDefault();
        var form = $('#NewsletterForm_NewsletterForm');
        var url = $(form).attr('action');
        //console.log(url);
        $.ajax({
            type: $(form).attr('method'),
            url:  $(form).attr('action'),
            data: $(form).serialize(),
                success: function(msg){
                    //console.log(msg);
                    $("#newsletter-form div.modal-content").html(msg);
                    //$(form).modal('hide');
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                }
        });
    });


});
