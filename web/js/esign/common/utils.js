function ModalClass() {
    var main = this;
    main.initModal = function ($modal, title, $content, ok_btn, ok_func) {
        $modal.find(".modal-header .modal-title").html(title);
        $modal.find(".modal-body").html("").append($content);
        $modal.find(".modal-footer .btn-primary").html(ok_btn);
        $modal.find(".modal-footer .btn-primary").click(function () {
            if (ok_func($modal)) {
                $modal.modal("hide");
            }
        });
    }
}

function UtilsClass() {
    var main = this;
    main.increaseInt = function (pre_val) {
        var pre_val_num = parseInt(pre_val.toString());
        return (pre_val_num + 1001).toString().substr(1);
    }
}