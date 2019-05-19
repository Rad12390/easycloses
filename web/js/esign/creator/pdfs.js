var Pdfs = null;
$(document).ready(function () {
    Pdfs = new PdfClass();
    Pdfs.init();
});

var Utils = new UtilsClass();
var Modal = new ModalClass();

function PdfClass() {
    var main = this;
    main.container = $(".page-pdf");
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
        main.pdf_list = getPdfList();
        main.template_list = getTemplateInfoList();
        _clearPdfListForCreate();
        var template_data = _getTemplateData();
        
        if (template_data) {
            _setTemplateData(getTemplateData(template_data["id"], main.creator_info["id"]));
            template_data = _getTemplateData();
            if (template_data) {
                $("span.template_name").html(template_data["name"]);
                $("#btn_save", main.container).show();
                $("#btn_template", main.container).hide();
            }
        } else {
            $("#btn_template", main.container).show();
            $("#btn_save", main.container).hide();
        }
    }
    main.initEvent = function () {
        //add pdf
        $("#btn_pdf", main.container).click(function () {
            $('#file_pdf', main.container).click();
        });
        $("#file_pdf", main.container).change(function () {
            // read file
            var fileInput = $(this);
            var fileToLoad = $(this)[0].files[0];
            ///
            var $modal = $($('#fullHeightModalLeft').html());
            var $content = $("<div class='content container'></div>");
            var $pdf_id = $('<input name="pdf_id" type="text" class="form-control mb-4" placeholder="PDF ID" value="' + main.createPdfId() + '">');
            $content.append($pdf_id);
            var $pdf_name = $('<input name="pdf_name" type="text" class="form-control mb-4" placeholder="PDF Title" value="' + main.createPdfId() + '">');
            $content.append($pdf_name);
            // FileReader function for read the file.
            var fileReader = new FileReader();
            var base64;
            // Onload of file read the file content
            fileReader.onload = function (fileLoadedEvent) {
                base64 = fileLoadedEvent.target.result;
                Modal.initModal($modal, "Add Pdf File", $content, "Add", function () {
                    var $pdf_id = $content.find("input[name='pdf_id']");
                    if (!main.isValidPdfId($pdf_id.val())) {
                        $pdf_id.val(main.createPdfId()).focus;
                    } else {
                        main.pdf_list = getPdfList();
                        main.drawMainTable();
                        main.drawSubTable();
                        return true;
                    }
                    return false;
                })
                fileInput.val("");
                $modal.modal('show');
            };
            // Convert data to base64
            fileReader.readAsDataURL(fileToLoad);
        });
        $("#btn_save", main.container).click(function () {
            setTemplateDataToDst(_getTemplateData(), main.creator_info["id"], 'role');
        });
        $("#btn_template", main.container).click(function () {
            var pdf_list = _getPdfListFromTemplate();
            if (pdf_list.length != 0) {
                var $modal = $($('#fullHeightModalLeft').html());
                var $content = $("<div class='content container'></div>");
                // var $template_name = $('<input name="template_name" type="text" class="form-control mb-4" placeholder="TEMPLATE Title" value="' + main.createTemplateId() + '">');
                var $template_name = $('<input name="template_name" type="text" class="form-control mb-4" placeholder="Add Name" value="' + main.createTemplateId() + '">');
                $content.append($template_name);
                Modal.initModal($modal, "Name Your Template", $content, "Add", function () {
                    var value= $("input[name='template_name']").val();
                    if(value==''){
                        var $modal = $($('#modal-small-center').html());
                        var $content = '<p>Add a template name before save data</p>';
                        Modal.initModal($modal, "Notification", $content, "Ok", function () {
                            $modal.modal('hide');
                            return false;
                        });
                        $modal.modal('show');
                    }
                    else{
                        var template_data = JSON.parse(JSON.stringify(empty_template_data));
                        template_data["id"] = ''; // from backend database
                        template_data["name"] = $template_name.val();
                        template_data["pdf_list"] = pdf_list;
                        template_data["creator_id"] = main.creator_info["id"];
                        setTemplateData(template_data, main.creator_info["id"]);

                        _setTemplateData(template_data);

                        return true;
                    }
                });
                $modal.modal('show');
            } else {
                var $modal = $($('#modal-notification').html());
                var $content = "<p>Please select some blank documents on the left side table which will belong to this template document.</p>";
                Modal.initModal($modal, "Notification", $content, "Ok", function () {
                    $modal.modal('hide');
                    return false;
                });
                $modal.modal('show');
            }
        });

        $("#sortable-pdf").sortable({
            helper: function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            },
            start: function (event, ui) {
                var start_pos = ui.item.index();
                ui.item.data('start_pos', start_pos);
            },
            update: function (event, ui) {
                var start_pos = ui.item.data('start_pos');
                var end_pos = ui.item.index();
                var pdf_list = _getPdfListFromTemplate();
                var tmp;
                if (end_pos < start_pos) {
                    tmp = pdf_list[start_pos];
                    for (var i = start_pos; i > end_pos; i--) {
                        pdf_list[i] = pdf_list[i - 1];
                    }
                    pdf_list[end_pos] = tmp;
                } else {
                    tmp = pdf_list[start_pos];
                    for (var i = start_pos; i < end_pos; i++) {
                        pdf_list[i] = pdf_list[i + 1];
                    }
                    pdf_list[end_pos] = tmp;
                }

                _setPdfListToTemplate(pdf_list);
                main.drawSubTable();
            }
        }).disableSelection();
    }
    main.initScreen = function () {
        //set pdf list////////
        main.drawMainTable();
        main.drawSubTable();
    }
    main.setCheckMainTable = function (pdf_id) {
        $("table.main tbody tr", main.container).each(function () {
            var $td_action = $(this).find("td.action");
            if ($td_action.attr("data-id") == pdf_id) {
                $td_action.find("input").prop("checked", true);
            }
        });
    }
    main.drawMainTable = function () {
        var pdf_list = main.pdf_list;
        $tbody = $("table.main tbody", main.container);
        $tbody.html("");
        for (var i = 0; i < pdf_list.length; i++) {
            var $td_index = $("<th scope='row'>" + (i + 1) + "</th>");
            var $td_name = $("<td>" + pdf_list[i]["title"] + "</td>");
            var action_html = '<div class="custom-control custom-checkbox">';
            action_html += '<input type="checkbox" class="custom-control-input" id="action_' + pdf_list[i]["id"] + '">';
            action_html += '<label class="custom-control-label" for="action_' + pdf_list[i]["id"] + '">add</label>';
            action_html += '</div>';
            var $td_action = $("<td class='action' data-id='" + pdf_list[i]["id"] + "'>" + action_html + "</td>");
            // var $tr = $("<tr></tr>").append($td_index).append($td_name).append($td_action);
            var $tr = $("<tr></tr>").append($td_name).append($td_action);
            $tbody.append($tr);
            $tr.find("td.action input").click(function () {
                var pdf_list = _getPdfListFromTemplate();
                var pdf_id = $(this).closest("td").attr("data-id");
                if (pdf_list) {
                    if ($(this).prop("checked")) {
                        for (var pdf_item of main.pdf_list) {
                            if (pdf_item["id"] == pdf_id) {
                                pdf_list.push(pdf_item);
                                break;
                            }
                        }
                    } else {
                        for (var pdf_item of pdf_list) {
                            if (pdf_item["id"] == pdf_id) {
                                var index = pdf_list.indexOf(pdf_item);
                                if (index !== -1) pdf_list.splice(index, 1);
                            }
                        }
                    }
                    _setPdfListToTemplate(pdf_list);
                    main.drawSubTable();
                }
            });
        }
        var pdf_list = _getPdfListFromTemplate();
        if (pdf_list) {
            for (var pdf_item of pdf_list) {
                main.setCheckMainTable(pdf_item["id"]);
            }
        }
    }

    main.drawSubTable = function () {
        var pdf_list = _getPdfListFromTemplate();
        $tbody = $("table.sub tbody", main.container);
        $tbody.html("");
        if (pdf_list) {
            for (var i = 0; i < pdf_list.length; i++) {
                var $td_index = $("<th scope='row'>" + (i + 1) + "</th>");
                var $td_name = $("<td>" + pdf_list[i]["title"] + "</td>");
                var action_html = '<i class="action-item down fas fa-arrow-down"></i>';
                action_html += '<i class="action-item up fas fa-arrow-up"></i>';
                var $td_action = $("<td class='action' data-id='" + pdf_list[i]["id"] + "'>" + action_html + "</td>");
                // var $tr = $("<tr></tr>").append($td_index).append($td_name).append($td_action);
                var $tr = $("<tr></tr>").append($td_name).append($td_action);
                $tbody.append($tr);
                $tr.find("td.action .action-item").click(function () {
                    var pdf_list = _getPdfListFromTemplate();
                    var pdf_id = $(this).closest("td").attr("data-id");

                    $(this).closest("tr").fadeOut(1000, function () {
                        main.drawSubTable();
                    });
                    // determine new data
                    var index = -1;
                    if (pdf_list) {
                        for (var i = 0; i < pdf_list.length; i++) {
                            if (pdf_list[i]["id"] == pdf_id) {
                                index = i;
                                break;
                            }
                        }
                        if (index > 0 && index < pdf_list.length && $(this).hasClass("up")) {
                            // replace with index - 1, index
                            var tmp = pdf_list[index];
                            pdf_list[index] = pdf_list[index - 1];
                            pdf_list[index - 1] = tmp;
                        } else if (index >= 0 && index < (pdf_list.length - 1) && $(this).hasClass("down")) {
                            // replace with index, index + 1
                            var tmp = pdf_list[index];
                            pdf_list[index] = pdf_list[index + 1];
                            pdf_list[index + 1] = tmp;
                        }
                        _setPdfListToTemplate(pdf_list);
                        //main.drawSubTable();
                    }
                });
            }
            $trs = $tbody.find("tr");
            $($trs[0]).find("td.action .action-item.up").remove();
            $($trs[$trs.length - 1]).find("td.action .action-item.down").remove();
        }
    }
    main.isValidPdfId = function (pdf_id) {
        var check_flag = true;

        for (var pdf_item of main.pdf_list) {
            if (pdf_item["id"] == pdf_id) {
                check_flag = false;
                break;
            }
        }
        return check_flag;
    }
    main.createPdfId = function () {
        var pref = "pdf-"
        var num_str = "001";
        var pdf_id = pref + num_str;
        while (true) {
            if (main.isValidPdfId(pdf_id)) {
                break;
            }
            num_str = Utils.increaseInt(num_str);
            pdf_id = pref + num_str;
        }
        return pdf_id;
    }
    main.isValidTemplateId = function (template_id) {
        var check_flag = true;
        for (var template_item of main.template_list) {
            if (template_item["id"] == template_id) {
                check_flag = false;
                break;
            }
        }
        return check_flag;
    }
    main.createTemplateId = function () {
        var pref = "template-"
        var num_str = "001";
        var template_id = pref + num_str;
        while (true) {
            if (main.isValidTemplateId(template_id)) {
                break;
            }
            num_str = Utils.increaseInt(num_str);
            template_id = pref + num_str;
        }
        return template_id;
    }
}