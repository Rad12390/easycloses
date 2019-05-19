jQuery(document).ready(function () {
    
   $(".dynamic_button").click(function(){
     var id= $(this).attr("id");
     //send ajax so as to update count in database
     $.ajax({
            method: "POST",
            url: "updatecount",
            data: {
                id: id
            },
            dataType: "json"
        })
        .done(function (response) {
          
        })
   }); 
    
});