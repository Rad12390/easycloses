var Inputs = null;
$(document).ready(function () {
    Inputs = new InputClass();
    //Inputs.initData();
    //Inputs.initEvent();
});

var Modal = new ModalClass();
// var PdfManager = new PdfManagerClass();
var Utils = new UtilsClass();
// var Db = new DbClass();

function InputClass() {
    var main = this;
    main.last_overlay_id = 0;
    main.container = $(".page-input");
    main.pdfMan = new PdfManagerClass(main);
    main.init = function () {
        main.initData();
        main.initScreen();
        main.initEvent();
    }
    main.tabInit = function () {
        main.initData();
        main.initScreen();
    }
    main.initData = function () {
        main.creator_info = getSenderInfo();
        main.cur_sign_doc_data = _getSignDocData();

        main.sel_pdf = $("#sel_pdf", main.container);
        main.sel_role = $("#sel_role", main.container);
        main.sel_page = $("#sel_page", main.container);
        main.btn_go_templates = $("#btn-go-templates", main.container);

        if (main.cur_sign_doc_data) {
            $("#btn_save", main.container).show();
        }

        $.LoadingOverlay("show");
        main.pdfMan.loadPdfDatas(main.cur_sign_doc_data.pdf_list, function () {
            $.LoadingOverlay("hide");
            // main.refreshPdfSel();
        });
    }
    main.initScreen = function () {
        // draw teplate selection.
        main.refreshSelections();
    }
    main.initEvent = function () {
        main.sel_pdf.change(function () {
            main.cur_pdf_id = $(this).val();
            $(".pdf[pdf_id='" + main.cur_pdf_id + "']")[0].scrollIntoView();
            // var pdf_data = getPdfData(main.cur_pdf_id, main.creator_info["id"]);
            // main.pdfMan.drawPdf(pdf_data);
            // main.pdfMan.showInputInfos();
        });
        main.sel_page.change(function () {
            main.cur_page_num = $(this).val();
            $(".pdf .pdf-page")[main.cur_page_num - 1].scrollIntoView();
        });
        main.sel_role.change(function () {
            main.cur_role_id = $(this).val();
            $(".btn-group.overlay-action").hide();
            main.last_overlay_id = 0;
            main.showInputInfos();
        });

        $(".tool-item").each(function () {
            $(this).draggable({
                zIndex: 999,
                revert: true,
                revertDuration: 0,
            });
        });

        $(".btn-group.scale").bind('click', 'button', function (e) {
            $target = $(e.target);
            if ($target.hasClass("zoomin")) {
                main.pdfMan.scale += main.pdfMan.scale_interval;
            } else {
                main.pdfMan.scale -= main.pdfMan.scale_interval;
            }
            main.pdfMan.scalePdf(main.pdfMan.scale);
            var $action_bar = $(".btn-group.overlay-action");
            $action_bar.hide();
        });
        $(".page.page-input #btn_save").click(function () {
            var sign_doc_data = _getSignDocData();
            setSignDocData(sign_doc_data, main.creator_info["id"]);
            $("#template-tab.nav-link").click();
        });
        $(".page.page-input #btn_publish").click(function () {
             var sign_doc_data = _getSignDocData();
        	var status= false;
            for (var input of sign_doc_data['input_list']) {
                var details= input['detail'];
                var optional= input['optional'];
                var role_name= input['role_name'];
                if(optional==1 && role_name=='Sender'){
                  // if optional value is 1
                   if(details.indexOf("Sender")>0){
                     var status= true;
                   }
                }
            console.log(status);
           }
            if(status){
                var $modal1 = $($('#modal-small-center').html());
                var $content1 = '<p>Please fill all the mandatory fields</p>';
                Modal.initModal($modal1, "Notification", $content1, "Ok", function () {
                    $modal1.modal('hide');
                    return false;
                });
                $modal1.modal('show');
		 return false;
            }
            var $modal = $($('#modal-small-center').html());
            var $content = '<p>Do you really want to publish this document for E-Sign?</p>';
            Modal.initModal($modal, "Notification", $content, "Ok", function () {
                $modal.modal('hide');
                var sign_doc_data = _getSignDocData();
                publishSignDoc(sign_doc_data.id);
                return false;
            });
            $modal.modal('show');
        });

        main.btn_go_templates.click(function() {
            $("#template-tab.nav-link").click();
        });

        function dragMoveListener(event) {
            var $target = $(event.target),
                // keep the dragged position in the data-x/data-y attributes
                x = (parseFloat($target.attr('data-x') * main.pdfMan.scale) || 0) + event.dx,
                y = (parseFloat($target.attr('data-y') * main.pdfMan.scale) || 0) + event.dy;
            $target.attr('data-x', x / main.pdfMan.scale);
            $target.attr('data-y', y / main.pdfMan.scale);
            $target.css("top", y);
            $target.css("left", x);
            main.updateInputInfo($target);
        }

        // this is used later in the resizing and gesture demos
        window.dragMoveListener = dragMoveListener;
        $(".btn-group.overlay-action .setting").click(function () {
            var $action_bar = $(".btn-group.overlay-action");
            $action_bar.hide();
            var $overlay_item = $(".overlay-item[data-id='" + $action_bar.attr("data-id") + "']");
            main.cur_overlay = $overlay_item;
            // main.showProperties($overlay_item);
        });
        $(".overlay-properties .btn-save").click(function () {
            // have to add options to save of overlay.
            var detail = $(".overlay-properties .optional input").val();
            main.cur_overlay.attr("data-detail", detail);
            main.updateInputInfo(main.cur_overlay);

            if (detail != "") {
                main.cur_overlay.text(detail);
            }

            $(".overlay-properties").hide("slow");
        });
        $(".overlay-properties").blur(function () {
            $(".overlay-properties").hide("slow");
        });
        $(".overlay-properties .btn-close").click(function () {
            $(".overlay-properties").hide("slow");
        });
        $(".tab-content").bind('click', '.overlay-item[data-type="formfield"]', function (e) {
            $target = $(e.target);
            if ($target.attr('class') == 'overlay-item' && $target.attr('data-type') == 'formfield') {
                $target.val('');
            }
        });
    }
    main.showProperties = function ($overlay_item) {
        var $properties = $(".overlay-properties");
        $properties.attr("data-id", $overlay_item.attr("data-id"));
        $(".type-role .data-type", $properties).html($overlay_item.attr("data-type"));
        $(".type-role .data-id", $properties).html($overlay_item.attr("data-id"));
        $(".type-role .role-name", $properties).html($overlay_item.attr("data-role-name"));
        $(".optional input[name='optional']", $properties).each(function () {
            if ($(this).val() == $overlay_item.attr("data-optional")) {
                $(this).prop('checked', true);
            } else {
                $(this).prop("checked", false);
            }
        });
        $properties.fadeIn("slow");
    }
    main.refreshSelections = function () {
        main.refreshPdfSel();
        main.refreshRolSel();
        main.refreshPageSel();
    }
    main.refreshPdfSel = function () {
        main.cur_pdf_list = _getPdfListFromSignDoc();
        main.pdfMan.drawPdfs(main.cur_pdf_list);
        main.sel_pdf.html("");
        // main.sel_pdf.append($("<option value=''>Select Pdf</option>"));
        for (var pdf_item of main.cur_pdf_list) {
            main.sel_pdf.append($("<option value='" + pdf_item["id"] + "'>" + pdf_item["title"] + "</option>"));
        }
        main.cur_pdf_id = main.sel_pdf.val();
    }
    main.refreshRolSel = function () {
        main.cur_signee_list = _getSigneeListFromSignDoc();
        main.sel_role.html("");
        // main.sel_role.append($("<option value=''>Select Role</option>"));
        for (var role_item of main.cur_signee_list) {
            if (role_item.sign_role_id != SIGN_ROLE_ID_SENDER) {
                main.sel_role.append($("<option value='" + role_item["sign_role_id"] + "'>" + role_item["name"] + "</option>"));
            } else {
                main.sel_role.append($("<option value='" + role_item["sign_role_id"] + "'>Sender</option>"));
            }
        }
        main.sel_role.append($("<option value='" + ROLE_ID_ALL + "' selected='selected'>" + ROLE_NAME_ALL + "</option>"));
        main.cur_role_id = main.sel_role.val();
    }
    main.refreshPageSel = function () {

    }
    main.deleteInputInfo = function ($overlay) {
        var overlay_data = main.getInputData($overlay);
        var input_list = _getInputListFromSignDoc();
        for (var i = 0; i < input_list.length; i++) {
            if (input_list[i]["id"] == overlay_data["id"]) {
                input_list.splice(i, 1);
                break;
            }
        }
        _setInputListToSignDoc(input_list);
    }
    main.updateInputInfo = function ($overlay) {
        var overlay_data = main.getInputData($overlay);
        var input_list = _getInputListFromSignDoc();
        for (var i = 0; i < input_list.length; i++) {
            if (input_list[i]["id"] == overlay_data["id"]) {
                input_list[i] = overlay_data;
            }
        }
        _setInputListToSignDoc(input_list);
    }
    main.insertInputInfo = function ($overlay) {
        var overlay_data = main.getInputData($overlay);
        var input_list = _getInputListFromSignDoc();
        input_list.push(overlay_data);
        _setInputListToSignDoc(input_list);
    }
    main.getInputData = function ($overlay) {
        var overlay_data = {
            id: $overlay.attr("data-id"),
            role_id: $overlay.attr("data-role-id"),
            role_name: $overlay.attr("data-role-name"),
            pdf_id: $overlay.attr("data-pdf-id"),
            page_num: $overlay.attr("data-page-num"),
            type: $overlay.attr("data-type"),
            x_pos: $overlay.attr("data-x"),
            y_pos: $overlay.attr("data-y"),
            width: $overlay.attr("data-width"),
            height: $overlay.attr("data-height"),
            optional: $overlay.attr("data-optional"),
            detail: $overlay.attr("data-detail"),
            placeholder: $overlay.attr("data-placeholder"),
            radio_group_id: $overlay.attr("data-radio-group-id"),
        }
        return overlay_data;
    }
    main.setOverlay = function (input_data, $container) {
        var scale = main.pdfMan.scale;
        var width = input_data["width"] * scale;
        var height = input_data["height"] * scale;
        var top = input_data["y_pos"] * scale;
        var left = input_data["x_pos"] * scale;
        var $overlay = $("<div></div>");

        if (input_data["type"] == INPUT_FORM_FIELD) {
            $overlay = $("<input type='text'>");
            $overlay.on('blur', function() {
                $overlay.attr("data-detail", $overlay.val());
                main.updateInputInfo($overlay);
            });
        } else if (input_data["type"] == INPUT_CHECKBOX) {
            $overlay = $("<input type='checkbox'>");
            $overlay.on('blur', function() {
                $overlay.attr("data-detail", $overlay.prop('checked'));
                main.updateInputInfo($overlay);
            });
        } else if (input_data["type"] == INPUT_RADIOBOX) {
            $overlay = $("<input type='radio' id='" + input_data["id"] + "' name='" + input_data["radio_group_id"] + "'>");
            $overlay.on('click', function() {
                $radios = $('input[name="' + input_data['radio_group_id'] + '"]');
                for (var i = 0; i < $radios.length; i++) {
                    $radios[i].dataset.detail = $radios[i].checked;
                    main.updateInputInfo($('input[name="' + input_data['radio_group_id'] + '"][id="' + $radios[i].id + '"]'));
                }
            });
        } else if (input_data["type"] == INPUT_CHECKMARK) {
            $overlay = $('<i class="fas fa-check"></i>');
        } else if (input_data["type"] == INPUT_DATETIME && input_data["role_id"] == "1") {
            $overlay = $("<input type='text' id='datepicker'>");

            var $datepicker = $overlay.pikaday({
                firstDay: 1,
                minDate: new Date(2000, 0, 1),
                maxDate: new Date(2020, 12, 31),
                yearRange: [2000,2020]
            });

            $overlay.on('blur', function() {
                $overlay.attr("data-detail", $overlay.val());
                main.updateInputInfo($overlay);

                if ($overlay.val() != "") {
                    if ($overlay.attr("data-optional") == INPUT_REQUIRED) {
                        main.filled_require_cnt++;
                    }
                }
            });
        }

        $overlay.addClass("overlay-item");
        $overlay.width(width);
        $overlay.height(height);


        $container.append($overlay);
        $overlay.css("top", top);
        $overlay.css("left", left);

        $overlay.attr("data-id", input_data["id"]);
        $overlay.attr("data-role-id", input_data["role_id"]);
        $overlay.attr("data-role-name", input_data["role_name"]);
        $overlay.attr("data-pdf-id", input_data["pdf_id"]);
        $overlay.attr("data-page-num", input_data["page_num"]);
        $overlay.attr("data-type", input_data["type"]);
        $overlay.attr("data-x", input_data["x_pos"]);
        $overlay.attr("data-y", input_data["y_pos"]);
        $overlay.attr("data-width", input_data["width"]);
        $overlay.attr("data-height", input_data["height"]);
        $overlay.attr("data-optional", input_data["optional"]);
        $overlay.attr("data-detail", input_data["detail"]);
        $overlay.attr("data-radio-group-id", input_data["radio_group_id"]);
        // add title
        if (input_data["type"] != INPUT_CHECKMARK && input_data["type"] != INPUT_HIGHLIGHT && input_data["type"] != INPUT_STRIKETHRU && input_data["type"] != INPUT_UNDERLINE) {
            if (input_data["detail"] != "") {
                $overlay.text(input_data["detail"]);
                $overlay.val(input_data["detail"]);
            } else {
                $overlay.text(input_data["placeholder"]);
                $overlay.val(input_data["placeholder"]);
            }
        }
        $overlay.css("display", "-webkit-flex");
        $overlay.css("display", "flex");
        $overlay.css("align-items", "center");
        $overlay.css("justify-content", "center");

        var $_container = $container;
        $overlay.click(function () {
            if ($(this).data('type') != 'signature' && $(this).data('type') != 'initials' && $(this).data('type') != INPUT_FORM_FIELD) {
                var $action_bar = $(".btn-group.overlay-action");
                $_container.append($action_bar);
                $action_bar.css("top", $(this).offset().top - $_container.offset().top);
                $action_bar.css("left", $(this).offset().left - $_container.offset().left + $(this).width() + 10);
                $action_bar.attr("data-id", $(this).attr("data-id"));
                $action_bar.show();
            }
        });
        return $overlay
    }
    main.showInputInfos = function (pdf_id = false, page_num = false) {
        console.log("pdf_id: " + pdf_id + " page_num: " + page_num);
        var input_list = _getInputListFromSignDoc();
        var $container;
        if (pdf_id && page_num) {
            $container = $(".pdf[pdf_id='" + pdf_id + "'] .pdf-page[page_num='" + page_num + "']");
            if ($container) {
                $(".overlay-item", $container).each(function () {
                    $(this).remove();
                });
                for (var input_item of input_list) {
                    if (input_item["pdf_id"] != pdf_id || input_item["page_num"] != page_num) {
                        continue;
                    }
                    if (input_item["id"] > main.last_overlay_id) {
                        main.last_overlay_id = input_item["id"];
                    }
                    if (main.cur_role_id == ROLE_ID_ALL || input_item["role_id"] == main.cur_role_id) {
                        if ($container) {
                            main.setOverlay(input_item, $container);
                        }
                    }
                }
            }
        } else {
            $(".overlay-item").each(function () {
                $(this).remove();
            });
            for (var input_item of input_list) {
                if (input_item["id"] > main.last_overlay_id) {
                    main.last_overlay_id = input_item["id"];
                }
                if (main.cur_role_id == ROLE_ID_ALL || input_item["role_id"] == main.cur_role_id) {
                    $container = $(".pdf[pdf_id='" + input_item["pdf_id"] + "'] .pdf-page[page_num='" + input_item["page_num"] + "']");
                    if ($container) {
                        main.setOverlay(input_item, $container);
                    }
                }
            }
        }
        $(".tool-item[data-type='text'], .overlay-item[data-type='text']").each(function() {
            $(this).css('border-style', 'none');
            $(this).css("color", "black");
        });
        for (var input of input_list) { 
            var role_name= input['role_name'];
            var data_id= input['id'];
            if(role_name!='Sender'){
               $(".overlay-item[data-id='" + data_id + "']").addClass("disabled");
               $(".overlay-item[data-id='" + data_id + "']").addClass("disabled");
            }
        }
    }
    main.setLastId = function (input_id) {
        var input_num = parseInt((input_id).substr(6));
        if (input_num > main.last_overlay_id) {
            main.last_overlay_id = input_num;
        }
    }
}
