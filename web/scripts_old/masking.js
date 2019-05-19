$(document).ready(function(){
    $(".modal-content").on('click', '#query-search', function(){
        var btn = $(this);

        btn.attr('disabled', true);

        var query = $('#query').val();

        var business = $('#query-business').val();

        $("#masking-spinner").show();

        $.ajax({
            'url': '/users-for-masking',
            'data': {
                'query': query,
                'business': business
            },
            'dataType': "html",
            'success': function (data) {
                $("#masking-spinner").hide();
                $('#masking-results').html(data);
                btn.attr('disabled', false);
            }
        });
    });
});