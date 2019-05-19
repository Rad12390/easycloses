function _getPdfData(pdf_id) {
    return localStorage.getItem("api_pdf_data_" + pdf_id);
}

function error_dialog(data) {
    console.log(data);
    var $modal = $($('#modal-notification').html());
    var $content = "<p>There is some error on server. (Error: " + data + ")</p>";
    Modal.initModal($modal, "Notification", $content, "Ok", function () {
        $modal.modal('hide');
        return false;
    });
    $modal.modal('show');
}