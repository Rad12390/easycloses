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
        main.creator_info = getCreatorInfo();
        main.template_list = getTemplateInfoList();
        main.cur_template_data = _getTemplateData();

        main.sel_template = $("#sel_template", main.container);
        main.sel_pdf = $("#sel_pdf", main.container);
        main.sel_role = $("#sel_role", main.container);
        main.sel_page = $("#sel_page", main.container);
        main.btn_go_role = $("#btn-go-role", main.container);

        if (main.cur_template_data) {
            $("#btn_save", main.container).show();
        }

        $.LoadingOverlay("show");
        main.pdfMan.loadPdfDatas(main.cur_template_data.pdf_list, function () {
            $.LoadingOverlay("hide");
            // main.refreshPdfSel();
        });
    }
    main.initScreen = function () {
        // draw teplate selection.
        main.refreshSelections();
    }
    main.initEvent = function () {
        main.sel_template.change(function () {
            main.cur_template_id = $(this).val();
            if (main.cur_template_id) {
                _setTemplateData(getTemplateData(main.cur_template_id, main.creator_info["id"]));
                main.setLastId();
                main.cur_template_data = _getTemplateData();
                $("span.template_name").html(main.cur_template_data["name"]);

                $.LoadingOverlay("show");
                main.pdfMan.loadPdfDatas(main.cur_template_data.pdf_list, function () {
                    $.LoadingOverlay("hide");
                    // main.refreshPdfSel();
                });

            } else {
                main.cur_template_data = null;
                _clearTemplateData();
            }

            main.refreshPdfSel();
            main.refreshRolSel();
            main.refreshPageSel();
        });
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
            if (main.cur_role_id == 1) {
                $(".tool-item[data-type='initials']").attr('class', 'c-initials');
                $(".tool-item[data-type='signature']").attr('class', 'c-signature');
            } else {
                $(".c-initials[data-type='initials']").attr('class', 'tool-item ui-draggable ui-draggable-handle');
                $(".c-signature[data-type='signature']").attr('class', 'tool-item ui-draggable ui-draggable-handle');
            }
            $(".btn-group.overlay-action").hide();
            main.showInputInfos();
        });

        $(".tool-item").each(function () {
            $(this).draggable({
                zIndex: 999,
                revert: true,
                revertDuration: 0,
            });
        });
        // code to drag event on toolbox
        $('.tool-item').on('dragstart', function (event) {
            var sel_role = $("#sel_role").val();
             if(sel_role==''){
                var $modal = $($('#modal-small-center').html());
                var $content = '<p>Please select a role</p>';
                Modal.initModal($modal, "Notification", $content, "Ok", function () {
                    $modal.modal('hide');
                    return false;
                 });
               $modal.modal('show');
            }
        });
        // end of code to drag event on toolbox

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

        main.btn_go_role.click(function() {
            $("#role-tab.nav-link").click();
        });

        $(".page.page-input #btn_save").one("click",function () {
            var template_data = _getTemplateData();
            setTemplateData(template_data, main.creator_info["id"]);
            return false;
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
        interact('.overlay-item:not([data-type="strikethru"]):not([data-type="underline"]')
            .draggable({
                onmove: window.dragMoveListener,
                restrict: {
                    restriction: 'parent',
                    elementRect: {
                        top: 0,
                        left: 0,
                        bottom: 1,
                        right: 1
                    }
                },
            })
            .resizable({
                // resize from all edges and corners
                edges: {
                    left: true,
                    right: true,
                    bottom: true,
                    top: true
                },
                margin: 5,
                // keep the edges inside the parent
                restrictEdges: {
                    outer: 'parent',
                    endOnly: true,
                },

                // minimum size
                restrictSize: {
                    min: {
                        width: 10,
                        height: 4
                    },
                },

                inertia: true,
            })
            .on('resizemove', function (event) {
                var $target = $(event.target);
                // keep the dragged position in the data-x/data-y attributes
                var x = (parseFloat($target.attr('data-x') * main.pdfMan.scale) || 0); // + event.dx;
                var y = (parseFloat($target.attr('data-y') * main.pdfMan.scale) || 0); // + event.dy;
                x += event.deltaRect.left;
                y += event.deltaRect.top;
                $target.attr('data-x', x / main.pdfMan.scale);
                $target.attr('data-y', y / main.pdfMan.scale);
                $target.css("top", y);
                $target.css("left", x);
                $target.css("width", event.rect.width + 'px');
                $target.css("height", event.rect.height + 'px');
                $target.attr('data-width', event.rect.width / main.pdfMan.scale);
                $target.attr('data-height', event.rect.height / main.pdfMan.scale);
                main.updateInputInfo($target);
            });
        interact('.overlay-item[data-type="strikethru"], .overlay-item[data-type="underline"]')
            .draggable({
                onmove: window.dragMoveListener,
                restrict: {
                    restriction: 'parent',
                    elementRect: {
                        top: 0,
                        left: 0,
                        bottom: 1,
                        right: 1
                    }
                },
            })
            .resizable({
                // resize from all edges and corners
                edges: {
                    left: true,
                    right: true,
                },
                margin: 5,
                // keep the edges inside the parent
                restrictEdges: {
                    outer: 'parent',
                    endOnly: true,
                },

                // minimum size
                restrictSize: {
                    min: {
                        width: 10,
                        height: 4
                    },
                },

                inertia: true,
            })
            .on('resizemove', function (event) {
                var $target = $(event.target);
                // keep the dragged position in the data-x/data-y attributes
                var x = (parseFloat($target.attr('data-x') * main.pdfMan.scale) || 0); // + event.dx;
                var y = (parseFloat($target.attr('data-y') * main.pdfMan.scale) || 0); // + event.dy;
                x += event.deltaRect.left;
                y += event.deltaRect.top;
                $target.attr('data-x', x / main.pdfMan.scale);
                $target.attr('data-y', y / main.pdfMan.scale);
                $target.css("top", y);
                $target.css("left", x);
                $target.css("width", event.rect.width + 'px');
                $target.css("height", event.rect.height + 'px');
                $target.attr('data-width', event.rect.width / main.pdfMan.scale);
                $target.attr('data-height', event.rect.height / main.pdfMan.scale);
                main.updateInputInfo($target);
            });


        $(".btn-group.overlay-action .delete").click(function () {
            var $action_bar = $(".btn-group.overlay-action");
            $action_bar.hide();
            var $overlay_item = $(".overlay-item[data-id='" + $action_bar.attr("data-id") + "']");
            $overlay_item.remove();
            main.deleteInputInfo($overlay_item);
        });
        $(".btn-group.overlay-action .setting").click(function () {
            var $action_bar = $(".btn-group.overlay-action");
            $action_bar.hide();
            var $overlay_item = $(".overlay-item[data-id='" + $action_bar.attr("data-id") + "']");
            main.cur_overlay = $overlay_item;
            main.showProperties($overlay_item);
        });
        $(".overlay-properties .btn-save").click(function () {
            // have to add options to save of overlay.
            main.cur_overlay.attr("data-placeholder", $(".overlay-properties .placeholder").val());
            main.cur_overlay.attr("data-detail", $(".overlay-properties .placeholder").val());
            main.cur_overlay.text($(".overlay-properties .placeholder").val());
            main.cur_overlay.attr("data-optional", $(".overlay-properties .optional input:checked").val());
            main.updateInputInfo(main.cur_overlay);
            $(".overlay-properties").hide("slow");
        });
        $(".overlay-properties").blur(function () {
            $(".overlay-properties").hide("slow");
        });
        $(".overlay-properties .btn-close").click(function () {
            $(".overlay-properties").hide("slow");
        });
    }
    main.showProperties = function ($overlay_item) {
        var $properties = $(".overlay-properties");
        $properties.attr("data-id", $overlay_item.attr("data-id"));
        $(".type-role .data-type", $properties).html($overlay_item.attr("data-type"));
        $(".type-role .data-id", $properties).html($overlay_item.attr("data-id"));
        $(".type-role .role-name", $properties).html($overlay_item.attr("data-role-name"));
        $(".overlay-properties .placeholder").val($overlay_item.attr("data-placeholder"));
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
        main.refreshTemSel();
        main.refreshPdfSel();
        main.refreshRolSel();
        main.refreshPageSel();
    }
    main.refreshTemSel = function () {
        main.cur_template_data = _getTemplateData();
        main.sel_template.html("");
        // main.sel_template.append($("<option value=''>Select Template</option>"));
        for (var template_item of main.template_list) {
            main.sel_template.append($("<option value='" + template_item["id"] + "'>" + template_item["name"] + "</option>"));
        }
        if (main.cur_template_data) {
            main.sel_template.val(main.cur_template_data["id"]);
            main.setLastId();
            $("span.template_name").html(main.cur_template_data["name"]);
        } else {
            main.cur_template_id = main.sel_template.val();
            main.sel_template.val(main.cur_template_id).trigger("change");
        }
    }
    main.refreshPdfSel = function () {
        main.cur_pdf_list = _getPdfListFromTemplate();
        main.pdfMan.drawPdfs(main.cur_pdf_list);
        main.sel_pdf.html("");
        // main.sel_pdf.append($("<option value=''>Select Pdf</option>"));
        for (var pdf_item of main.cur_pdf_list) {
            main.sel_pdf.append($("<option value='" + pdf_item["id"] + "'>" + pdf_item["title"] + "</option>"));
        }
        main.cur_pdf_id = main.sel_pdf.val();
    }
    main.refreshRolSel = function () {
        main.cur_role_list = _getRoleListFromTemplate();
        main.sel_role.html("");
        main.sel_role.append($("<option value=''>Choose Signee</option>"));
        for (var role_item of main.cur_role_list) {
            main.sel_role.append($("<option value='" + role_item["id"] + "'>" + role_item["name"] + "</option>"));
        }
        main.sel_role.append($("<option value='" + ROLE_ID_ALL + "'>" + ROLE_NAME_ALL + "</option>"));
        main.cur_role_id = main.sel_role.val();
    }
    main.refreshPageSel = function () {

    }
    main.deleteInputInfo = function ($overlay) {
        var overlay_data = main.getInputData($overlay);
        var input_list = _getInputListFromTemplate();
        for (var i = 0; i < input_list.length; i++) {
            if (input_list[i]["id"] == overlay_data["id"]) {
                input_list.splice(i, 1);
                break;
            }
        }
        _setInputListToTemplate(input_list);
    }
    main.updateInputInfo = function ($overlay) {
        var overlay_data = main.getInputData($overlay);
        var input_list = _getInputListFromTemplate();
        for (var i = 0; i < input_list.length; i++) {
            if (input_list[i]["id"] == overlay_data["id"]) {
                input_list[i] = overlay_data;
            }
        }
        _setInputListToTemplate(input_list);
    }
    main.insertInputInfo = function ($overlay) {
        var overlay_data = main.getInputData($overlay);
        var input_list = _getInputListFromTemplate();
        if (overlay_data.x_pos >= 0 && overlay_data.y_pos >= 0) {
            input_list.push(overlay_data);
            _setInputListToTemplate(input_list);
        }
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


        $overlay.attr("class", "overlay-item");
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
            if (input_data["type"] == INPUT_TEXT || input_data["type"] == INPUT_INITIALS || input_data["type"] == INPUT_FORM_FIELD) {
                $overlay.text(input_data["detail"]);
            } else {
                $overlay.text(input_data["role_name"]);
            }
        }

        $overlay.css("display", "-webkit-flex");
        $overlay.css("display", "flex");
        $overlay.css("align-items", "center");
        $overlay.css("justify-content", "center");

        var $_container = $container;
        $overlay.click(function () {
            var $action_bar = $(".btn-group.overlay-action");
            $_container.append($action_bar);
            $action_bar.css("top", $(this).offset().top - $_container.offset().top);
            $action_bar.css("left", $(this).offset().left - $_container.offset().left + $(this).width() + 10);
            $action_bar.attr("data-id", $(this).attr("data-id"));
            $action_bar.show();
        });
        return $overlay
    }
    main.showInputInfos = function (pdf_id = false, page_num = false) {
        var input_list = _getInputListFromTemplate();
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
                if (main.cur_role_id == ROLE_ID_ALL || input_item["role_id"] == main.cur_role_id) {
                    $container = $(".pdf[pdf_id='" + input_item["pdf_id"] + "'] .pdf-page[page_num='" + input_item["page_num"] + "']");
                    if ($container) {
                        main.setOverlay(input_item, $container);
                    }
                }
            }
        }
    }
    main.setLastId = function () {
        main.last_overlay_id = 0;
        var input_list = _getInputListFromTemplate();
        for (var input_item of input_list) {
            if (parseInt(input_item["id"]) > parseInt(main.last_overlay_id)) {
                main.last_overlay_id = input_item["id"];
            }
        }
    }
    main.createOverlaybyType = function (source, $container) {

        if (main.cur_role_id == ROLE_ID_ALL) {
            var $modal = $($('#modal-notification').html());
            var $content = "<p>Please select a role.</p>";
            Modal.initModal($modal, "Notification", $content, "Ok", function () {
                $modal.modal('hide');
                return false;
            });
            $modal.modal('show');
            return null;
        }

        var top = source.offset().top;
        var left = source.offset().left;
        var width = source.width();
        var height = source.height();
        var type = source.attr("data-type");

        if (type == INPUT_RADIOBOX) {
            $modal = $($('#modal-small-center').html());
            var $content = $("<div id='create-radio-dialog' class='content container'>How many options would you like to add to this group?</div>");

            var $radio_cnt = $('<input name="radio-cnt" type="number" class="form-control mb-4" placeholder="" value="" max=20>');
            $content.append($radio_cnt);

            Modal.initModal($modal, "Create a Radio Group", $content, "Create", function ($modal) {
                var $radio_cnt = $content.find("input[name='radio-cnt']");
                var radio_group_id = main.last_overlay_id + 1;
                for (var i = 0; i < $radio_cnt.val(); i++) {
                    left += width + 10;
                    main.makeOverlaybyType(top, left, width, height, type, $container, radio_group_id);
                }
                return true;
            })
            $modal.modal('show');
        } else {
            main.makeOverlaybyType(top, left, width, height, type, $container, 0);
        }
    }
    main.makeOverlaybyType = function (top, left, width, height, type, $container, radio_group_id) {

        top -= $container.offset().top;
        left -= $container.offset().left;
        if (top < 0 || left < 0) {
            return;
        }
        var $overlay = $("<div></div>");
        if (type == INPUT_TEXT) {
            $overlay = $("<input type='text'>");
            $overlay.on('blur', function () {
                $overlay.attr("data-detail", $overlay.val());
                main.updateInputInfo($overlay);
            });
        } else if (type == INPUT_CHECKMARK) {
            $overlay = $('<i class="fas fa-check"></i>');
        }

        var scale = main.pdfMan.scale;
        $overlay.addClass("overlay-item");

        $container.append($overlay);

        var input_id = ++main.last_overlay_id; //"input_" + (main.parent.last_overlay_id);
        // set info
        $overlay.attr("data-id", input_id);
        $overlay.attr("data-role-id", main.cur_role_id);
        var role_name = $("#sel_role option:selected").text();
        $overlay.attr("data-role-name", role_name);
        $overlay.attr("data-pdf-id", $container.closest("div.pdf").attr("pdf_id"));
        $overlay.attr("data-page-num", $container.attr("page_num"));
        $overlay.attr("data-type", type);
        $overlay.attr("data-x", left / scale);
        $overlay.attr("data-y", top / scale);
        if (type == INPUT_INITIALS) {
            $overlay.attr("data-width", 35 / scale);
            $overlay.attr("data-height", 35 / scale);
            $overlay.width(35);
            $overlay.height(35);
        } else if (type == INPUT_CHECKBOX || type == INPUT_RADIOBOX) {
            $overlay.attr("data-width", 25 / scale);
            $overlay.attr("data-height", 25 / scale);
            $overlay.width(25);
            $overlay.height(25);
        } else if (type == INPUT_CHECKMARK) {
            $overlay.attr("data-width", 25 / scale);
            $overlay.attr("data-height", 25 / scale);
            $overlay.width(35);
            $overlay.height(35);
        } else if (type == INPUT_STRIKETHRU || type == INPUT_UNDERLINE) {
            $overlay.attr("data-width", width * 2 / scale);
            $overlay.attr("data-height", 5 / scale);
            $overlay.width(width * 2);
            $overlay.height(5);
        } else if (type == INPUT_HIGHLIGHT) {
            $overlay.attr("data-width", width * 2 / scale);
            $overlay.attr("data-height", height * 2 / scale);
            $overlay.width(width * 2);
            $overlay.height(height * 2);
        } else {
            $overlay.attr("data-width", width * 2 / scale);
            $overlay.attr("data-height", height / 2 / scale);
            $overlay.width(width * 2);
            $overlay.height(height / 2);
        }
        $overlay.attr("data-radio-group-id", radio_group_id);
        if (type == INPUT_TEXT || type == INPUT_STRIKETHRU || type == INPUT_UNDERLINE || type == INPUT_HIGHLIGHT || type == INPUT_CHECKMARK) {
            $overlay.attr("data-optional", INPUT_OPTIONAL);
        } else if (type == INPUT_CHECKBOX || type == INPUT_RADIOBOX) {
            $overlay.attr("data-optional", INPUT_OPTIONAL);
        } else {
            $overlay.attr("data-optional", INPUT_REQUIRED);
        }
        $overlay.attr("data-detail", type + ": " + role_name);
        if (type == "formfield") {
            $overlay.attr("data-placeholder", "textbox: " + role_name);            
        } else {
            $overlay.attr("data-placeholder", type + ": " + role_name);
        }
        $overlay.css("top", top);
        $overlay.css("left", left);
        if (type != INPUT_HIGHLIGHT && type != INPUT_STRIKETHRU && type != INPUT_UNDERLINE && type != INPUT_CHECKMARK) {
            $overlay.text($overlay.attr("data-placeholder"));
        }
        $overlay.css("display", "-webkit-flex");
        $overlay.css("display", "flex");
        $overlay.css("align-items", "center");
        $overlay.css("justify-content", "center");

        var $_container = $container;
        $overlay.click(function () {
            var $action_bar = $(".btn-group.overlay-action");
            $_container.append($action_bar);
            $action_bar.css("top", $(this).offset().top - $_container.offset().top);
            $action_bar.css("left", $(this).offset().left - $_container.offset().left + $(this).width() + 10);
            $action_bar.attr("data-id", $(this).attr("data-id"));
            $action_bar.show();
        });
        main.insertInputInfo($overlay);
        return $overlay;
    }
}
