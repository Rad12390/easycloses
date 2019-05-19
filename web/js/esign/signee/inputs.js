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
    main.last_overlay_index = -1;
    main.need_require_cnt = 0;
    main.filled_require_cnt = 0;
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
        main.user = getSigneeInfo();
        main.setRoleId();
        main.cur_sign_doc_data = _getSignDocData();
        
        main.sel_pdf = $("#sel_pdf", main.container);
        main.sel_page = $("#sel_page", main.container);
        
        if (main.cur_sign_doc_data) {
            main.cur_sign_doc_data.input_list.sort(function(a, b) {
                result = a.page_num - b.page_num;
                if (result != 0) return result;
                result = a.y_pos - b.y_pos;
                if (result != 0) return result;
                result = a.x_pos - b.x_pos;
                return result;
            });
            _setSignDocData(main.cur_sign_doc_data);
            $("#btn_save", main.container).show();
	
        }
        main.need_require_cnt = 0;
        main.calcNeedCnt();

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
            // var pdf_data = getPdfData(main.cur_pdf_id);
            // main.pdfMan.drawPdf(pdf_data);
            // main.pdfMan.showInputInfos();
        });
        main.sel_page.change(function () {
            main.cur_page_num = $(this).val();
            $(".pdf .pdf-page")[main.cur_page_num - 1].scrollIntoView();
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
            $(".btn-group.overlay-next").hide();
            $(".btn-group.overlay-sign").hide();
            $(".btn-group.overlay-setting").hide();
        });
        $(".page.page-input #btn_start").click(function () {
            main.nextElement();
        });
        $(".btn-group.overlay-next .next").click(function () {
            main.nextElement();
        });
        $(".btn-group.overlay-sign .sign").click(function () {
            id = $(this).closest('div').attr("data-id");
            var $elem = $(".overlay-item[data-id='" + id + "']");
            main.autoFillOverlay($elem);
        });
        $(".page.page-input #btn_save").click(function () {
            var sign_doc_data = _getSignDocData();
            setSignDocData(sign_doc_data);
        });
        $(".page.page-input #btn_publish").click(function () {
            if (main.need_require_cnt > main.filled_require_cnt) {
                var $modal = $($('#modal-notification').html());
                var $content = '<p>Please fill in all. You do not fill in ' + (main.need_require_cnt - main.filled_require_cnt) + ' places.</p>';
                Modal.initModal($modal, "Notification", $content, "Ok", function () {
                    $modal.modal('hide');
                    return false;
                });
                $modal.modal('show');
                return;
            }
            var $modal = $($('#modal-small-center').html());
            var $content = '<p>Your document has been completed. </p><p>Would you like to send it now?</p>';
            Modal.initModal($modal, "Final Step:", $content, "SEND", function () {
                $modal.modal('hide');

                var sign_doc_data = _getSignDocData();
                var signee_list = sign_doc_data.signee_list;
                var cur_signee = {};
                for (var role_item of signee_list) {
                    if (main.user.id == role_item.id && role_item.sign_role_id != ROLE_ID_SENDER/* && main.user.sign_role_id == role_item.sign_role_id */) {
                        role_item.status = "signed";
                        cur_signee = role_item;
                        _setSignDocData(sign_doc_data);
                        break;
                    }
                }
                signedSignDoc(sign_doc_data.id, cur_signee);
                return false;
            });
            $modal.modal('show');
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
    }
    main.refreshSelections = function () {
        main.refreshPdfSel();
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
    main.refreshPageSel = function () {

    }
    main.setRoleId = function () {
        main.cur_signee_list = _getSigneeListFromSignDoc();
        for (var role_item of main.cur_signee_list) {
            if (main.user.id == role_item.id && role_item.sign_role_id != ROLE_ID_SENDER) {
                main.cur_role = role_item;
                return;
            }
        }
    }
    main.calcNeedCnt = function () {
        var arr = main.cur_sign_doc_data.input_list;
        for (var i = 0; i < arr.length; i++) {
            if (arr[i].role_id == main.cur_role.sign_role_id && arr[i].optional == INPUT_REQUIRED) {
                main.need_require_cnt++;
            }
        }
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
    main.nextElement = function () {
        
        var arr = main.cur_sign_doc_data.input_list;
        main.last_overlay_index++;
        main.last_overlay_index %= arr.length;
        var find = false;
        for (var i = main.last_overlay_index; i < arr.length; i++) {
            if (arr[i].role_id == main.cur_role.sign_role_id) {
                if (arr[i].status != "signed") {
                    main.last_overlay_index = i;
                    find = true;
                    break;
                }
            }
        }
        if (find) {
            var elem = arr[main.last_overlay_index];
            var $elem = $('.overlay-item[data-id=' + elem.id + ']');
            $elem[0].scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});
            main.showActionBar($elem);
        } else {
            if (main.need_require_cnt <= main.filled_require_cnt) {
                $(".page.page-input #btn_publish").click();
            } else {
                main.last_overlay_index = -1;
                main.nextElement();
            }
        }
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

                if ($overlay.val() != "") {
                    if ($overlay.attr("data-optional") == INPUT_REQUIRED) {
                        main.filled_require_cnt++;
                    }
                }
            });
        } else if (input_data["type"] == INPUT_DATETIME && input_data["role_id"] != "1") {
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
        } else if (input_data["type"] == INPUT_CHECKBOX) {
            $overlay = $("<input type='checkbox'>");

            if (input_data["detail"] != "") {
                $overlay.prop('checked', input_data["detail"]);
            }

            $overlay.on('blur', function() {
                $overlay.attr("data-detail", $overlay.prop('checked'));
                main.updateInputInfo($overlay);
            });
        } else if (input_data["type"] == INPUT_RADIOBOX) {
            if (input_data["detail"] == "true") {
                $overlay = $("<input type='radio' id='" + input_data["id"] + "' name='" + input_data["radio_group_id"] + "' checked>");
            } else {
                $overlay = $("<input type='radio' id='" + input_data["id"] + "' name='" + input_data["radio_group_id"] + "'>");
            }
            $overlay.on('click', function() {
                $radios = $('input[name="' + input_data['radio_group_id'] + '"]');
                for (var i = 0; i < $radios.length; i++) {
                    $radios[i].dataset.detail = $radios[i].checked;
                    main.updateInputInfo($('input[name="' + input_data['radio_group_id'] + '"][id="' + $radios[i].id + '"]'));
                }
            });
        } else if (input_data["type"] == INPUT_CHECKMARK) {
            $overlay = $('<i class="fas fa-check"></i>');
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
            main.showActionBar($(this));
            main.autoFillOverlay($(this));
        });
        return $overlay
    }
    main.showActionBar = function ($overlay_item) {
        var $_container = $overlay_item.closest('.pdf-page');
        var $sign_bar = $(".btn-group.overlay-sign");
        $sign_bar.hide();
        if ($overlay_item.data('type') == INPUT_SIGNATURE || $overlay_item.data('type') == INPUT_INITIALS) {
            $sign_bar.find('button').html('Sign<i class="fas fa-arrow-alt-circle-right"></i>');
            $sign_bar.find('button').addClass('btn-yellow');
            $sign_bar.find('button').removeClass('btn-blue');
            $sign_bar.attr("data-type", 'sign');
            $_container.append($sign_bar);
            $sign_bar.css("top", $overlay_item.offset().top - $_container.offset().top);
            $sign_bar.css("left", $overlay_item.offset().left - $_container.offset().left - $sign_bar.width() - 5);
            $sign_bar.attr("data-id", $overlay_item.attr("data-id"));
            $sign_bar.show();
        } else if ($overlay_item.data('type') == INPUT_TEXT || $overlay_item.data('type') == INPUT_STRIKETHRU || 
        $overlay_item.data('type') == INPUT_UNDERLINE || $overlay_item.data('type') == INPUT_HIGHLIGHT || $overlay_item.data('type') == INPUT_CHECKMARK) {
            return;
        } else {
            // $sign_bar.find('button').html('Edit<i class="fas fa-cog"></i>');
            // $sign_bar.find('button').addClass('btn-blue');
            // $sign_bar.find('button').removeClass('btn-yellow');
            // $sign_bar.attr("data-type", 'edit');
            $overlay_item.focus();
        }

        var $next_bar = $(".btn-group.overlay-next");
        $_container.append($next_bar);
        $next_bar.css("top", $overlay_item.offset().top - $_container.offset().top);
        $next_bar.css("left", $overlay_item.offset().left - $_container.offset().left + $overlay_item.width() + 10);
        $next_bar.attr("data-id", $overlay_item.attr("data-id"));
        $next_bar.show();
    }
    main.showInputInfos = function (pdf_id = false, page_num = false) {
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
                    if (input_item["role_id"] == main.cur_role.sign_role_id || input_item["role_id"] == "1") {
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
                if (input_item["role_id"] == main.cur_role.sign_role_id) {
                    $container = $(".pdf[pdf_id='" + input_item["pdf_id"] + "'] .pdf-page[page_num='" + input_item["page_num"] + "']");
                    if ($container) {
                        main.setOverlay(input_item, $container);
                    }
                }
            }
        }
        
        $(".tool-item[data-type='text'], .overlay-item[data-type='text']").each(function() {
            $(this).css("color", "black");
            $(this).css("border-style", "none");
        });

        $(".tool-item[data-type='underline'], .overlay-item[data-type='underline']").each(function() {
            $(this).css("background", "black");
        });
        for (var input of input_list) { 
            var role_name= input['role_name'];
            var data_id= input['id'];
            if(role_name=='Sender'){
               $(".overlay-item[data-id='" + data_id + "']").css("border","none");
               $(".overlay-item[data-id='" + data_id + "']").css("background","none");
            }
        }
    }
    main.autoFillOverlay = function ($elem) {
        var elem;
        var id = $elem.data("id");
      
        for (var input of main.cur_sign_doc_data.input_list) {
            if (input.id == id) {
                elem = input;
                break;
            }
        }

        if ($elem.data('type') == INPUT_SIGNATURE || $elem.data('type') == INPUT_INITIALS) {
            if (Sign.is_draw_sign) {
                $img = $('<img src="' + _getSignImg() + '">');
                // $img.css('width', $elem.width());
                // $img.css('height', $elem.height());
                $img.css('width', SIGNATURE_WIDTH);
                $img.css('height', SIGNATURE_HEIGHT);
                $elem.text('');
                $elem.append($img);
            } else {
                $elem.css('font-family', Sign.font_list[Sign.font_index]);
                if ($elem.data('type') == INPUT_SIGNATURE) {
                    $elem.text(main.user.name);
                } else if ($elem.data('type') == INPUT_INITIALS) {
                    $elem.text(main.user.initials);
                }
            }

            if ($elem.data('type') == INPUT_SIGNATURE) {
                $elem.attr("data-detail", main.user.name);
            } else if ($elem.data('type') == INPUT_INITIALS) {
                $elem.attr("data-detail", main.user.initials);
            }
            main.updateInputInfo($elem);
            
            elem.status = "signed";
            if (elem.optional == INPUT_REQUIRED) {
                main.filled_require_cnt++;
            }
        } else if ($elem.data('type') == INPUT_AUTOTIME) {
            var currentdate = new Date(); 
            var datetime = currentdate.getDate() + "/"
                            + (currentdate.getMonth()+1)  + "/" 
                            + currentdate.getFullYear() + " @ "  
                            + currentdate.getHours() + ":"  
                            + currentdate.getMinutes() + ":" 
                            + currentdate.getSeconds();
                            
            $elem.text(datetime);
            $elem.attr("data-detail", datetime);
            main.updateInputInfo($elem);
            
            elem.status = "signed";
            if (elem.optional == INPUT_REQUIRED) {
                main.filled_require_cnt++;
            }
        }
    }
}