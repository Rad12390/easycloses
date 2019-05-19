var Template = null;
$(document).ready(function () {
    //initApiDatas();
    Templates = new TemplateClass();
    Templates.init();
});

function TemplateClass() {
    var main = this;
    main.container = $(".page-template");
    main.init = function () {
        main.initData();
        main.initScreen();
        main.initEvent();
    }
    main.initData = function () {
        main.signee_info = getSigneeInfo();
        main.sign_doc_list = getSignDocList();
    }
    main.initScreen = function () {
        // var sign_doc_list = main.sign_doc_list;
        var sign_doc_list = getSignDocInfoList();
        $tbody = $("table tbody", main.container);
        $p = $("p", main.container);
        if (sign_doc_list.length == 0) {
            $tbody.closest("table").css("display", "none");
            $p.css("display", "block");
        } else {
            $tbody.closest("table").css("display", "table");
            $p.css("display", "none");
        }
        $tbody.html("");
        for (var i = 0; i < sign_doc_list.length; i++) {
            var $td_index = $("<th scope='row'>" + (i + 1) + "</th>");
            var $td_status = $("<td data-id='" + sign_doc_list[i]['id'] + "' class='status'></td>");
            if (sign_doc_list[i]['status'] == STATUS_EDIT) {
                $td_status.append('<div class="editing"></div>');
            } else if (sign_doc_list[i]['status'] == STATUS_PUBLISHED) {
                $td_status.append('<div class="published"></div>');
            } else if (sign_doc_list[i]['status'] == STATUS_SIGNED) {
                $td_status.append('<div class="signed"></div>');
            } else if (sign_doc_list[i]['status'] == STATUS_APPROVED) {
                $td_status.append('<div class="approved"></div>');
            }
            var $td_name = $("<td data-id='" + sign_doc_list[i]['id'] + "' class='name'><a href='#' class='view'>" + sign_doc_list[i]["name"] + "</a></td>");
            var $td_created_date = $("<td>" + sign_doc_list[i]['created'] + "</td>");
            var action_html = '';
            if (sign_doc_list[i]['status'] == STATUS_SIGNED || sign_doc_list[i]['status'] == STATUS_APPROVED) {
                // action_html += '<i class="action-item download fas fa-download"></i>';
                action_html += '<a href="#" class="action-item download">DOWNLOAD</span>';
            } else {
                action_html += '<a href="#" class="action-item view">START</span>';
                //action_html += '<i class="action-item edit fas fa-edit"></i>';
                //action_html += '<i class="action-item delete fas fa-times"></i>';
                // action_html += '<i class="action-item input fas fa-align-justify"></i>';
                // action_html += '<i class="action-item delete fas fa-times"></i>';
            }
            var $td_action = $("<td class='action name' data-id='" + sign_doc_list[i]["id"] + "' class='action'>" + action_html + "</td>");
            // var $tr = $("<tr></tr>").append($td_index).append($td_status).append($td_name).append($td_action);
            var $tr = $("<tr></tr>").append($td_status).append($td_name).append($td_created_date).append($td_action);
            $tbody.append($tr);
            $tr.find("div[class='published']").click(function () {
                var sign_doc_id = $(this).closest("td").attr("data-id");
                main.showSigneeListForm(sign_doc_id);
            });
            $tr.find("td.name a.view").click(function () {
                var sign_doc_id = $(this).closest("td").attr("data-id");
                var sign_doc = getSignDocData(sign_doc_id);
                var signed = false;
                for (var signee of sign_doc.signee_list) {
                    if (main.signee_info.id == signee.id
                        && signee.sign_role_id != ROLE_ID_SENDER
                        && signee.status == SIGNEE_STATUS_SIGNED) {

                            signed = true;

                            var $modal = $($('#modal-notification').html());
                            var $content = '<p>You have already signed this document.</p>';
                            Modal.initModal($modal, "Notification", $content, "Ok", function () {
                                $modal.modal('hide');
                                return false;
                            });
                            $modal.modal('show');
                    }
                }
                if (!signed) {
                    _setSignDocData(sign_doc);
                    $("#sign-tab.nav-link").click();
                }
            });
            $tr.find("td.action .download").each(function () {
                $(this).click(function () {
                    var sign_doc_id = $(this).closest("td").attr("data-id");
                        main.showPdfListForm(sign_doc_id);
                });
            });
        }
    }
    main.initEvent = function () {
    }

    main.showSigneeListForm = function (sign_doc_id) {
        var sign_doc_data = getSignDocData(sign_doc_id);
        var $modal = $($('#modal-center').html());

        var $content = '<p>We have sent email to signees.</p>';
        $content += '<table class="table"><thead><tr><th scope="col">#</th><th scope="col">Role</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Sequence</th><th scope="col">URL</th></tr></thead><tbody>';
        
        for (var i = 0; i < sign_doc_data.signee_list.length; i++) {
            var signee = sign_doc_data.signee_list[i];

            if (signee.sign_role_id != '1') {
                $content += '<tr>'
                $content += "<th scope='row'>" + i + "</th>";
                $content += '<td>' + signee.sign_role_name + '</td>';
                $content += '<td>' + signee.name + '</td>';
                $content += '<td>' + signee.email + '</td>';
                $content += '<td>' + signee.sign_role_seq + '</td>';
                if (signee.status == 'signed') {
                    $content += '<td>Already Signed</td>';
                } else {
                    $content += '<td style="color:red">Not Signed</td>';
                }
                $content += '</tr>';
            }
        }
        $content += '</tbody></table>';
        
        Modal.initModal($modal, "Notification", $content, "Ok", function () {
            $modal.modal('hide');
            return false;
        });
        $modal.modal('show');
    }
    main.showPdfListForm = function (sign_doc_id) {
        var sign_doc_data = getSignDocData(sign_doc_id);
        var $modal = $($('#modal-center').html());

        var $content = '<p>The list of the document is as below.</p>';
        $content += '<table class="table"><thead><tr><th scope="col">#</th><th scope="col">Name</th><th scope="col">Download</th></tr></thead><tbody>';
        
        for (var i = 0; i < sign_doc_data.pdf_list.length; i++) {
            var pdf = sign_doc_data.pdf_list[i];

            $content += '<tr>'
            $content += "<th scope='row'>" + (i + 1) + "</th>";
            $content += '<td>' + pdf.title + '</td>';
            $content += '<td><a style="color:red" href="' + window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + $("#route-home").data("route") + 'esign/api/make-pdf/' + sign_doc_id + '/' + pdf.id + '">Click here</a></td>';
            $content += '</tr>';
        }
        $content += '</tbody></table>';
        
        Modal.initModal($modal, "Notification", $content, "Ok", function () {
            $modal.modal('hide');
            return false;
        });
        $modal.modal('show');
    }
}