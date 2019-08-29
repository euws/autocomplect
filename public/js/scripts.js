$(document).ready(function(){

    // Fancybox

    $('[data-fancybox="images"]').fancybox({
        afterLoad : function(instance, current) {
            var pixelRatio = window.devicePixelRatio || 1;

            if ( pixelRatio > 1.5 ) {
                current.width  = current.width  / pixelRatio;
                current.height = current.height / pixelRatio;
            }
        }
    });

    $('#auto-fotos-details-page').slick({
        lazyLoad: 'ondemand',
        slidesToShow: 5,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    // Details form ajax

    $(".save-details-btn").click(
        function(){
            sendAjaxForm('details_form', '');
            return false;
        }
    );

    function sendAjaxForm(ajax_form, url) {
        $.ajax({
            url:     url,
            type:     "POST",
            dataType: "html",
            data: $("#"+ajax_form).serialize(),
            success: function(response) {
                $('.alert-success').removeClass('d-none');
                $('.alert-danger').addClass('d-none');
            },
            error: function(response) {
                $('.alert-danger').removeClass('d-none');
                $('.alert-success').addClass('d-none');
            }
        });
    }
});