var Template = null;
$(document).ready(function () {
    initApiDatas();
    Templates = new TemplateClass();
    $("#template-tab.nav-link").click();
});

function TemplateClass() {
    var main = this;
    main.container = $(".page-template");
    main.init = function () {
        main.initData();
        main.initScreen();
    }
    main.initData = function () {
        main.creator_info = getCreatorInfo();
    }
    main.initScreen = function () {
        var template_list = getTemplateInfoList();
        $tbody = $("table tbody", main.container);
        $p = $("p", main.container);
        if (template_list.length == 0) {
            $tbody.closest("table").css("display", "none");
            $p.css("display", "block");
        } else {
            $tbody.closest("table").css("display", "table");
            $p.css("display", "none");
        }
        $tbody.html("");
        for (var i = 0; i < template_list.length; i++) {
            var $td_index = $("<th scope='row'>" + (i + 1) + "</th>");
            var $td_name = $("<td>" + template_list[i]["name"] + "</td>");
            var $td_last_updated = $("<td>"+template_list[i]["updated"]+"</td>");
            var $td_created_date = $("<td>"+template_list[i]["created"]+"</td>");
            var action_html = '<i class="action-item pdf fas fa-file-pdf"></i>';
            action_html += '<i class="action-item role fas fa-users"></i>';
            action_html += '<i class="action-item input fas fa-align-justify"></i>';
            action_html += '<i class="action-item delete fas fa-times"></i>';
            var $td_action = $("<td class='action' data-id='" + template_list[i]["id"] + "'>" + action_html + "</td>");
            // var $tr = $("<tr></tr>").append($td_index).append($td_name).append($td_action);
            var $tr = $("<tr></tr>").append($td_name).append($td_last_updated).append($td_created_date).append($td_action);
            $tbody.append($tr);
            $tr.find("td.action .action-item").each(function () {
                $(this).click(function () {
                    var template_id = $(this).closest("td").attr("data-id");
                    _setTemplateData(getTemplateData(template_id, main.creator_info["id"]));
                    if ($(this).hasClass("pdf")) {
                        $("#pdf-tab.nav-link").click();
                    } else if ($(this).hasClass("role")) {
                        $("#role-tab.nav-link").click();
                    } else if ($(this).hasClass("input")) {
                        $("#input-tab.nav-link").click();
                    } else if ($(this).hasClass("delete")) {
                        var $modal = $($('#modal-small-center').html());
                        var $content = '<p>Do you really want to delete this template document?</p>';
                        Modal.initModal($modal, "Notification", $content, "Ok", function () {
                            $modal.modal('hide');
                            deleteTemplateData(template_id, main.creator_info["id"]);
                            main.initScreen();
                            return false;
                        });
                        $modal.modal('show');
                    }
                });
            });
        }
        $("#add_template", main.container).click(function() {
            $("#pdf-tab.nav-link").click();
        });
    }
}