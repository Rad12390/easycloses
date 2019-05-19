$(function(){
    $('.add-deposit-alert').click(function(){
        var row = $(this).closest('tr.deposit-row');
        var newRowHtml = row.data('prototype').replace(/\$\$name\$\$/g, 5);
    });
});