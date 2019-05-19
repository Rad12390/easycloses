function windowForLogout(data) {
    try {
        var json = JSON.parse(data.responseText);
    } catch(e) {
        swal(
            {
                title: 'You were logged out due to inactivity. ',
                text: 'Please Click Here to log in again',
                type: "warning",
                confirmButtonText: "Login",
                showCancelButton: false,
                showLoaderOnConfirm: true,
                closeOnConfirm: true
            },
            function(){
                location.reload();
            }
        )
    }
}