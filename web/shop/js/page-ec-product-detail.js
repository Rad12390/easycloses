$(document).ready(function(){
    $("body").on('click', '.to_cart', function(event){
        event.preventDefault();
        var sku_id = $(this).data('sku');
        var quantity = 1;
        var payment_id = $(".payment-type:checked").val();
        var options = $(".options:checked").map(function() {
            var option_id = $(this).data('option_id');
            return option_id + "_" + $(this).val();
        }).get();

        $.ajax({
            url: '/ec-shop/cart/add',
            method: 'POST',
            dataType: 'json',
            data: {
                sku_id: sku_id,
                options: options,
                quantity: quantity,
                payment_id: payment_id
            }
        })
        .success(function(data) {
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
            var modalBox = $("#ajax");

            if (data.error === 0) {
                if (data.data.needToChoose === true) {
                    modalBox.modal('show');
                    $(".modal-content").html(data.data.html);
                } else {
                    if ((modalBox.data('bs.modal') || {}).isShown) {
                        modalBox.modal('toggle');
                    }
                    toastr.success(data.message);
                    swal("SUCCESS", data.message, "success");
                    $('.shopping-cart-box').html(data.header_box)
                }
            } else {
                if ((modalBox.data('bs.modal') || {}).isShown) {
                    modalBox.modal('toggle');
                }
                toastr.error(data.message);
                swal("WARNING", data.message, "warning")
            }

        });
    });

    $('#ajax').on('hidden.bs.modal', function () {
        $(".modal-content").html('');
    });

    $('.kv-fa').rating({
        theme: 'krajee-fa',
        showCaption: false,
        showClear: false,
        filledStar: '<i class="fa fa-star"></i>',
        emptyStar: '<i class="fa fa-star-o"></i>'
    });
    
    //script for modal popup on custom quotes buttom
    $(".custom-quote").click(function(){
        $("#customQuotes").modal("show");
    });
    
    //save custom quote data
    $(".quoteSave").click(function(){
        $(this).closest('form').valid();
    });
    
    //add validation on custom quote form
    $('.quoteSave').closest('form').validate({
        errorElement: 'span', //default input error message container
        errorClass: 'help-block', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: "",
        rules: {
            "quote": {
                required: true
            },
        },
        highlight: function(element) { // hightlight error inputs
            $(element).removeClass('modal-success'); 
            $(element).addClass('modal-error'); // set error class to the control group
        },
        unhighlight: function (element) {
            $(element).removeClass('modal-error'); 
            $(element).addClass('modal-success');
        },
        errorPlacement: function (error, element) {
            $("span.error").html("");
            $("<span class='error'>"+error.text()+"</span>").insertAfter(element);
        },
        submitHandler : function(form) {
            form.submit();
        }
    });
});
