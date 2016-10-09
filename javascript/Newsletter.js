 $(function() {
    $("button#newsletterSubmit").click(function(){
        var url = $('#Form_NewsletterForm').attr('action');
        console.log(url);
        console.log('clicked');
        $.ajax({
            type: "GET",
            url: url,
            data: $('form.newsletter').serialize(),
                success: function(msg){
                    $(".modal-body").html(msg);
                    //$("#newsletter-form").modal('hide');
                },
                error: function(){
                    alert("failure");
            }
       });
    });
});

// Pagination
/*if ($('.pagination').length) {
  $('.main').on('click','.pagination a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax(url)
            .done(function (response) {
                $('.main').html(response);
            })
            .fail (function (xhr) {
                alert('Error: ' + xhr.responseText);
            });
    });
}*/
