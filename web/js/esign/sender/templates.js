var Template = null;
$(document).ready(function () {
    initApiDatas();
    Templates = new TemplateClass();
    Templates.init();
});

function TemplateClass() {
    var main = this;
    main.container = $(".page-template");
    $("#input-tab.nav-link").hide();

    main.init = function () {
        main.initData();
        main.initScreen();
        main.initEvent();
    }
    main.initData = function () {
        main.sender_info = getSenderInfo();
        main.sign_doc_list = getSignDocList();
        main.template_list = getTemplateList();
        main.signee_candidate_list = getSigneeCandidateList();
        main.public_temp_list = getPublicTempList();
    }
    main.initScreen = function () {
        var sign_doc_list = main.sign_doc_list;
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
            var $td_name = $("<td data-id='" + sign_doc_list[i]['id'] + "' class='name'><a href='#'>" + sign_doc_list[i]["name"] + "</a></td>");
            var $td_last_updated = $("<td>" + sign_doc_list[i]["updated"] + "</td>");
            var $td_created_date = $("<td>" + sign_doc_list[i]["created"] + "</td>");
            var action_html = '';
            if (sign_doc_list[i]['status'] == STATUS_SIGNED) {
                action_html += '<div class="btn btn-primary btn-sm btn_approve">Approve</div>';
            } else if (sign_doc_list[i]['status'] != STATUS_APPROVED) {
                action_html += '<i class="action-item edit fas fa-edit"></i>';
                //action_html += '<i class="action-item delete fas fa-times"></i>';
                action_html += '<i class="action-item input fas fa-align-justify"></i>';
                action_html += '<i class="action-item delete fas fa-times"></i>';
            }
            var $td_action = $("<td class='action' data-id='" + sign_doc_list[i]["id"] + "' class='action'>" + action_html + "</td>");
            // var $tr = $("<tr></tr>").append($td_index).append($td_status).append($td_name).append($td_action);
            var $tr = $("<tr></tr>").append($td_status).append($td_name).append($td_last_updated).append($td_created_date).append($td_action);
            $tbody.append($tr);
            $tr.find("div[class='published']").click(function () {
                var sign_doc_id = $(this).closest("td").attr("data-id");
                main.showSigneeListForm(sign_doc_id);
            });
            $tr.find("td.action .action-item").each(function () {
                $(this).click(function () {
                    var sign_doc_id = $(this).closest("td").attr("data-id");
                    _setSignDocData(getSignDocData(sign_doc_id, main.sender_info["id"]));
                    if ($(this).hasClass("delete")) {
                        var $modal = $($('#modal-small-center').html());
                        var $content = '<p>Do you really want to delete this document?</p>';
                        Modal.initModal($modal, "Notification", $content, "Ok", function () {
                            $modal.modal('hide');
                            deleteSignDocData(sign_doc_id, main.sender_info["id"]);
                            main.initScreen();
                            return false;
                        });
                        $modal.modal('show');
                    } else if ($(this).hasClass("input")) {
                        $("#input-tab.nav-link").show();
                        $("#input-tab.nav-link").click();
                    } else if ($(this).hasClass("edit")) {
                        main.showSignDocForm(sign_doc_id);
                    }
                });
            });
            // approve by sender, green => blue
            $tr.find("td.action .btn_approve").click(function () {
                var sign_doc_id = $(this).closest("td").attr("data-id");
                var $modal = $($('#modal-small-center').html());
                var $content = '<p>Do you really want to approve this document?</p>';
                Modal.initModal($modal, "Notification", $content, "Ok", function () {
                    $modal.modal('hide');
                    approveSignDoc(sign_doc_id);
                    return false;
                });
                $modal.modal('show');
            });
            
        }
	$("td.name a").click(function () {
              var $tr= $(this).parent().parent();
               $tr.find("td.action .input").click();
            });
    }
    main.initEvent = function () {

        // add sign doc
        $("#btn_add_sign_doc").click(function () {
            if (main.template_list.length == 0) {
                var $modal = $($('#modal-notification').html());
                var $content = "<p>You don't have any template document.</p>";
                Modal.initModal($modal, "Notification", $content, "Ok", function () {
                    $modal.modal('hide');
                    return false;
                });
                $modal.modal('show');
            } else {
                main.showSignDocForm("-1");
            }
        });
    }

    main.showSignDocForm = function(sign_doc_id) {
        var create_flag = false;
        var sign_doc = JSON.parse(JSON.stringify(empty_sign_doc_data));
        if (sign_doc_id == "-1") {
            create_flag = true;
        } else {
            sign_doc = getSignDocData(sign_doc_id, main.sender_info["id"]);
        }
        $modal = $($('#modal-center').html());
        var $content = $("<div id='add-sign-doc-form-container' class='content container'></div>");

        // sign doc name
        var $sign_doc_name = $('<input name="sign_doc_name" type="text" class="form-control mb-4" placeholder="Sign Document Name" value="' + sign_doc['name'] + '">');
        $sign_doc_name.on('focus', function() {
            $('#error-text').text('');
        });
        $content.append($sign_doc_name);

        // template doc list
        var $template = $('<select name="template" class="form-control mb-4" data-sign-doc-id="' + sign_doc_id + '"></select>');
        var $template_option = '';
        var selected_template_id = sign_doc['template_id'];
        var selected_template = {};
        for (var template_item of main.public_temp_list) {
            if (selected_template_id == '') {
                selected_template_id = template_item['id'];
            }
            if (selected_template_id == template_item['id']) {
                selected_template = template_item;
                $template_option = $('<option value=' + template_item['id'] + ' selected>' + template_item['name'] + '</option>');
            } else {
                $template_option = $('<option value=' + template_item['id'] + '>' + template_item['name'] + '</option>');
            }
            $template.append($template_option);
        }
        $content.append($template);
        $template.change(function () {
            var sign_doc_id = $(this).data('sign-doc-id');
            main.appendSigneeList(sign_doc_id);
        });
        var $signee_list_container = $('<div id="signee-list-container"></div>');
        $content.append($signee_list_container);
        main.appendSigneeList(sign_doc_id, $signee_list_container, selected_template);

        var $error = $('<p id="error-text" type="text" class="mb-4" placeholder="" value="" style="color:red;"></p>');
        $content.append($error);

        // show modal
        var func = create_flag ? "Add" : "Save";
        title = func + " Sign Document";
        Modal.initModal($modal, title, $content, func, function ($modal) {
            var $sign_doc_name = $content.find("input[name='sign_doc_name']");
            var $template = $content.find("select[name='template']");
            
            var template_data = getTemplateData($template.val(), main.sender_info["id"]);

            var sign_doc_data = {};
            if (create_flag) {
                sign_doc_data["id"] = ''; // from backend database
            } else {
                sign_doc_data["id"] = sign_doc_id;
            }
            sign_doc_data["name"] = $sign_doc_name.val();
            sign_doc_data["template_id"] = $template.val();
            sign_doc_data["status"] = STATUS_EDIT;
            sign_doc_data["sender_id"] = main.sender_info["id"];

            sign_doc_data["signee_list"] = [];
            for (var sign_role_item of template_data.signee_role_list) {
                var signee = {};
                var $option = $('select[name="signee_candidate_list_' + sign_role_item.id +'"]', $modal).children("option:selected");
                signee['sign_role_id'] = sign_role_item.id;
                signee['sign_role_name'] = sign_role_item.name;
                signee['sign_role_seq'] = sign_role_item.seq;
                signee['id'] = $option.val();
                signee['email'] = $option.data('email');
                signee['name'] = $option.text();
                sign_doc_data["signee_list"].push(signee);
            }
            sign_doc_data["pdf_list"] = template_data["pdf_list"];
            sign_doc_data["input_list"] = template_data["input_list"];

            // check duplicate signee
            var duplicate = false;
            var dup = [];
            for (var signee of sign_doc_data["signee_list"]) {
                if (signee.sign_role_id != SIGN_ROLE_ID_SENDER) {
                    dup[signee.id] = 0;
                    for (var other_signee of sign_doc_data["signee_list"]) {
                        if (other_signee.sign_role_id != SIGN_ROLE_ID_SENDER) {
                            if (signee.id == other_signee.id) {
                                dup[signee.id]++;
                                if (dup[signee.id] > 1) {
                                    duplicate = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            if ($sign_doc_name.val().trim() == '') {
                $content.find('#error-text').text('Please fill out the name of document.');
            } else if (duplicate) {
                $content.find('#error-text').text('Signees are duplicated. Please select other signee.');
            } else {
                setSignDocData(sign_doc_data, main.sender_info["id"]);
                
                _setSignDocData(sign_doc_data);

                main.initData();
                main.initScreen();
                $("#input-tab.nav-link").show();
                $("#input-tab.nav-link").click();
                return true;
            }
        })
        $modal.modal('show');
    }

    main.appendSigneeList = function (sign_doc_id, $content=null, selected_template=null) {
        var sign_doc = JSON.parse(JSON.stringify(empty_sign_doc_data));
        if (sign_doc_id == "-1") {
            create_flag = true;
        } else {
            sign_doc = getSignDocData(sign_doc_id, main.sender_info["id"]);
        }

        if ($content == null) {
            $content = $('#signee-list-container');
        }
        if (selected_template == null) {
            selected_template_id = $('select[name="template"]').val();
            selected_template = getTemplateData(selected_template_id);
        }
        $content.html('');
        // template sign role list
        if (main.template_list.length > 0) {
            var sign_role_list = selected_template.signee_role_list;
            if (sign_role_list != null && sign_role_list != undefined && sign_role_list.length > 0) {
                for (var sign_role_item of sign_role_list) {
                    var $signee;
                    if (sign_role_item['id'] == SIGN_ROLE_ID_SENDER) {
                        $signee = $('<div class="row" data-sign-role-id="' + sign_role_item.id + '" style="display:none"></div>');
                    } else {
                        $signee = $('<div class="row" data-sign-role-id="' + sign_role_item.id + '"></div>');
                    }

                    var $sign_role_type = $('<div class="col-3"><label class="form-control mb-4" value="">' + sign_role_item.name + '</label></div>');
                    $signee.append($sign_role_type);

                    // signee candidate list
                    var $signee_candidates_container = $('<div class="col-4"></div>');
                    var $signee_candidate_list = $('<select name="signee_candidate_list_' + sign_role_item.id + '" class="form-control mb-4" data-id=' + sign_role_item.id + '></select>');
                    $signee_candidate_list.on('change', function() {
                        var email = $(this).children("option:selected").data('email');
                        $('label[id=signee_email_' + $(this).data('id') + ']').text(email);

                        $('#error-text').text('');
                    });
                    var selected_signee_candidate = _getSigneeFromSignDoc(sign_role_item.id, sign_doc);
                    var selected_signee_candidate_id = selected_signee_candidate['id'];
                    var $signee_candidate_option = '';
                    for (var signee_candidate_item of main.signee_candidate_list) {
                        if (selected_signee_candidate_id == '') {
                            selected_signee_candidate_id = signee_candidate_item['id'];
                            selected_signee_candidate = signee_candidate_item;
                        }
                        if (selected_signee_candidate_id == signee_candidate_item['id']) {
                            $signee_candidate_option = $('<option value=' + selected_signee_candidate['id'] + ' data-email=' + selected_signee_candidate['email'] + ' selected>' + selected_signee_candidate['name'] + '</option>');
                        } else {
                            $signee_candidate_option = $('<option value=' + signee_candidate_item['id'] + ' data-email=' + signee_candidate_item['email'] + '>' + signee_candidate_item['name'] + '</option>');
                        }
                        $signee_candidate_list.append($signee_candidate_option);
                    }
                    $signee_candidates_container.append($signee_candidate_list);
                    $signee.append($signee_candidates_container);

                    var $signee_email = $('<div class="col-5"><label id="signee_email_' + sign_role_item.id + '" class="form-control mb-4" value="" placeholder="Signee Email">' + email + '</label></div>');
                    $signee.append($signee_email);

                    $content.append($signee);

                    var email = $('select[name="signee_candidate_list_' + sign_role_item.id + '"]', $content).children("option:selected").data('email');
                    $('label[id="signee_email_' + sign_role_item.id + '"]', $content).text(email);
                }
            }
        }
    }
    main.showSigneeListForm = function (sign_doc_id) {
        var sign_doc_data = getSignDocData(sign_doc_id);
        var $modal = $($('#modal-center').html());

        var $content = '<p>An email has been sent to your Signees</p>';
        $content += '<table class="table"><thead><tr><th scope="col">Role</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Sequence</th><th scope="col">URL</th></tr></thead><tbody>';
        
        for (var i = 0; i < sign_doc_data.signee_list.length; i++) {
            var signee = sign_doc_data.signee_list[i];

            if (signee.sign_role_id != '1') {
                $content += '<tr>'
                // $content += "<th scope='row'>" + i + "</th>";
                $content += '<td>' + signee.sign_role_name + '</td>';
                $content += '<td>' + signee.name + '</td>';
                $content += '<td>' + signee.email + '</td>';
                $content += '<td>' + signee.sign_role_seq + '</td>';
                if (signee.status == 'signed') {
                    $content += '<td>Already Signed</td>';
                } else {
                    $content += '<td><a style="color:red" href="' + window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + $("#route-home").data("route") + 'esign/signee/sign/' + signee.id + '/' + signee.sign_role_id + '/' + sign_doc_data.id + '">Click here</a></td>';
                }
                $content += '</tr>';
            }
        }
        $content += '</tbody></table>';
        
        Modal.initModal($modal, "Notifications Sent", $content, "Ok", function () {
            $modal.modal('hide');
            return false;
        });
        $modal.modal('show');
    }
}