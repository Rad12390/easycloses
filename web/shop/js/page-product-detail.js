$(document).ready(function(){
    $(".to_cart").on('click', function(event){
        event.preventDefault();
        var product = $(this).data('value');
        var quantity = 1;
        var type = $(".product-type:checked").val();

        var parents = $(".parent-product-type:checked").map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: '/add-to-cart',
            method: 'POST',
            dataType: 'json',
            data: {
                product: product,
                type: type,
                quantity: quantity,
                parents: parents
            }
        })
        .success(function(data){
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            if(data.error == 0){
                toastr.success(data.message);
                swal("SUCCESS", data.message, "success")
            } else {
                toastr.error(data.message);
                swal("WARNING", data.message, "warning")
            }
        });
    });

    $('.kv-fa').rating({
        theme: 'krajee-fa',
        showCaption: false,
        showClear: false,
        filledStar: '<i class="fa fa-star"></i>',
        emptyStar: '<i class="fa fa-star-o"></i>'
    });
});
