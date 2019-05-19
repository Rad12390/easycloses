var Roles = null;
$(document).ready(function () {
    Roles = new RoleClass();
    Roles.initEvent();
});

var Utils = new UtilsClass();
var Modal = new ModalClass();

function RoleClass() {
    var main = this;
    main.container = $(".page-role");
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
        main.role_list = getRoleList();
        main.template_list = getTemplateInfoList();
        var template_data = _getTemplateData();
        if (template_data) {
            _setTemplateData(getTemplateData(template_data["id"], main.creator_info["id"]));
            template_data = _getTemplateData();
            $("span.template_name").html(template_data["name"]);
            $("#btn_save", main.container).show();
            $("#btn_add_sign_role", main.container).show();
            // $("#btn_go_template", main.container).show();

            // default sign role item for sender
            var role_list = _getRoleListFromTemplate();
            if (role_list == null) {
                role_list = [];
            }
            if (role_list.length == 0) {
                var role_item = {};
                role_item['id'] = '1';
                role_item['name'] = 'Sender';
                role_item['type'] = '1';
                role_item['seq'] = '0';
                role_list = [];
                role_list.push(role_item);
                _setRoleListToTemplate(role_list);
            }
        }
    }
    main.initEvent = function () {
        $("#btn_save", main.container).click(function () {
            setTemplateDataToDst(_getTemplateData(), main.creator_info["id"], 'input');
        });
        $("#btn_add_sign_role", main.container).click(function () {
            var $modal = $($('#modal-small-center').html());
            var $content = $("<div class='content container'></div>");
            // sign role id
            var $sign_role_id = $('<input name="role_id" type="text" class="form-control mb-4" placeholder="SIGN ROLE ID" value="' + main.createSignRoleId('role-') + '" style="display:none">');
            $content.append($sign_role_id);
            // role type
            var $role_type = $('<select name="role_type" class="form-control mb-4" ></select>');
            var selected = false;
            var $role_option = '';
            for (var role_item of main.role_list) {
                if (!selected) {
                    $role_option = $('<option value=' + role_item['id'] + ' selected>' + role_item['name'] + '</option>');
                    selected = true;
                } else {
                    $role_option = $('<option value=' + role_item['id'] + '>' + role_item['name'] + '</option>');
                }
                $role_type.append($role_option);
            }
            $content.append($role_type);
            // sign role name
            var $sign_role_name = $('<input id="input_role_name" name="role_name" type="text" class="form-control mb-4" placeholder="Sign Role Name" value="">');
            $sign_role_name.on('focus', function() {
                $('p[name="role_name"]').text('');
            });
            $content.append($sign_role_name);

            var $error = $('<p name="role_name" class="mb-4" placeholder="Sign Role Name" value="" style="color:red;"></p>');
            $content.append($error);
            Modal.initModal($modal, "Add a Sign Role", $content, "Add", function () {
                if ($sign_role_name.val().trim() == '') {
                    $content.find('p').text('Please fill out the name of role.');
                } else {
                    if (!main.isValidSignRoleId($sign_role_id.val())) {
                        $sign_role_id.val(main.createSignRoleId(main.getRoleNameById($role_type.val()))).focus;
                    } else {
                        var sign_role_list = _getRoleListFromTemplate();
                        for (var sign_role_list1 of sign_role_list) {
                            var role_array=[];
                            role_array.push(sign_role_list1['name']);
                            var str = role_array.join();
                            var duplicates = str.match($sign_role_name.val());
                            if(duplicates!=null){
                                var duplicateNumber = duplicates.length;
                                //show message for duplicate role added
                                if(duplicateNumber){
                                    var $_modal = $($('#modal-notification').html());
                                    var $content1 = "<p>This role is already created.</p>";
                                    Modal.initModal($_modal, "Notification", $content1, "Ok", function () {
                                        $_modal.modal('hide');
                                    });
                                    $_modal.modal('show');
                                    $modal.modal('hide');
                                    return false;
                                }
                            }
                            else{
                                if($sign_role_name.val().trim()==''){
                                    
                                }
                            }
                        }
                        var sign_role_item = {};
                        sign_role_item["id"] = $sign_role_id.val();
                        sign_role_item["name"] = $sign_role_name.val();
                        sign_role_item["type"] = $role_type.val();
                        sign_role_item["seq"] = sign_role_list.length;
                        sign_role_list.push(sign_role_item);
                        _setRoleListToTemplate(sign_role_list);
                        main.drawSubTable();
                        return true;
                    }
                    return false;
                }
            })
            $modal.modal('show');
        });

        // $("#btn_go_template").click(function() {
        //     $("#template-tab.nav-link").click();
        // });
    }
    main.initScreen = function () {
        //set role list////////
        main.drawMainTable();
        main.drawSubTable();
    }
    main.setCheckMainTable = function (role_id) {
        $("table.main tbody tr", main.container).each(function () {
            var $td_action = $(this).find("td.action");
            if ($td_action.attr("data-id") == role_id) {
                $td_action.find("input").prop("checked", true);
            }
        });
    }
    main.drawMainTable = function () {
        var role_list = main.role_list;
        $tbody = $("table.main tbody", main.container);
        $tbody.html("");
        for (var i = 0; i < role_list.length; i++) {
            var $td_index = $("<th scope='row'>" + (i + 1) + "</th>");
            var $td_name = $("<td>" + role_list[i]["name"] + "</td>");
            var action_html = '<div class="custom-control custom-checkbox">';
            action_html += '<input type="checkbox" class="custom-control-input" id="action_' + role_list[i]["id"] + '">';
            action_html += '<label class="custom-control-label" for="action_' + role_list[i]["id"] + '">add</label>';
            action_html += '</div>';
            var $td_action = $("<td class='action' data-id='" + role_list[i]["id"] + "'>" + action_html + "</td>");
            var $tr = $("<tr></tr>").append($td_index).append($td_name).append($td_action);
            $tbody.append($tr);
            $tr.find("td.action input").click(function () {
                var role_list = _getRoleListFromTemplate();
                var role_id = $(this).closest("td").attr("data-id");
                if (role_list) {
                    if ($(this).prop("checked")) {
                        for (var role_item of main.role_list) {
                            if (role_item["id"] == role_id) {
                                role_item["seq"] = role_list.length + 1;
                                role_list.push(role_item);
                                break;
                            }
                        }
                    } else {
                        for (var role_item of role_list) {
                            if (role_item["id"] == role_id) {
                                var index = role_list.indexOf(role_item);
                                if (index !== -1) role_list.splice(index, 1);
                            }
                        }
                    }
                    _setRoleListToTemplate(role_list);
                    main.drawSubTable();
                }
            });
        }
        var role_list = _getRoleListFromTemplate();
        if (role_list) {
            for (var role_item of role_list) {
                main.setCheckMainTable(role_item["id"]);
            }
        }
    }

    main.drawSubTable = function () {
        var role_list = _getRoleListFromTemplate();
        $tbody = $("table.sub tbody", main.container);
        $tbody.html("");
        if (role_list) {
            for (var i = 0; i < role_list.length; i++) {
                var $td_index = $("<th scope='row'>" + (i + 1) + "</th>");
                $seq = $('<select class=seq data-id=' + role_list[i]['id'] + '></select>');
                for (var j = 1; j < role_list.length; j++) {
                    var $opt = '';
                    if (j == role_list[i]["seq"]) {
                        $opt = $('<option selected>' + j + '</option>');
                    } else {
                        $opt = $('<option>' + j + '</option>');
                    }
                    var $seq = $seq.append($opt);
                }
                $td_seq = (i == 0) ? $('<td></td>') : $('<td></td>').append($seq);
                var $td_name = $("<td>" + role_list[i]["name"] + "</td>");
                var role_type_id = role_list[i]["type"];
                var role_type_name = main.getRoleNameById(role_type_id);
                var $td_type = $("<td>" + role_type_name + "</td>");
                var action_html = (i == 0) ? '' : '<i class="action-item down fas fa fa-times"></i>';
                var $td_action = $("<td class='action' data-id='" + role_list[i]["id"] + "'>" +  action_html + "</td>");
                // var $tr = $("<tr></tr>").append($td_index).append($td_seq).append($td_name).append($td_type).append($td_action);
                var $tr = $("<tr></tr>").append($td_seq).append($td_name).append($td_action);
                $tbody.append($tr);
                $tr.find("td.action .action-item").click(function () {
                    var sign_role_list = _getRoleListFromTemplate();
                    var sign_role_id = $(this).closest("td").attr("data-id");

                    $(this).closest("tr").fadeOut(1000, function () {
                        main.drawSubTable();
                    });
                    // determine new data
                    var index = -1;
                    if (sign_role_list) {
                        for (var i = 0; i < sign_role_list.length; i++) {
                            if (sign_role_list[i]["id"] == sign_role_id) {
                                index = i;
                                break;
                            }
                        }
                        sign_role_list.splice(index, 1);

                        //sort
                        main.sortSeq(sign_role_list);
                        var seq = 0;
                        var old = 0;
                        for (var role_item of sign_role_list) {
                            if (role_item['seq'] == 0) continue;
                            if (old == role_item['seq']) {
                                role_item['seq'] = seq;
                            } else if (role_item['seq'] > old) {
                                seq++;
                                role_item['seq'] = seq;
                            } else {
                                role_item['seq'] = seq;
                            }
                            old = role_item['seq'];
                        }
                        _setRoleListToTemplate(sign_role_list);
                        // drowSubTable is called by fadeOut
                    }
                });
            }
            $trs = $tbody.find("tr");
            $( ".seq" ).change(function() {
                role_id = $(this).data('id');
                sel_seq = $( 'select[data-id=' + role_id + '] option:selected' ).text();
                var role_list = _getRoleListFromTemplate();
                for (var role_item of role_list) {
                    if (role_item["id"] == role_id) {
                        role_item['seq'] = sel_seq;
                    }
                }
                //sort
                main.sortSeq(role_list);
                _setRoleListToTemplate(role_list);
                main.drawSubTable();
                // animation

            });
        }
    }
    main.isValidSignRoleId = function (sign_role_id) {
        var check_flag = true;
        var sign_role_list = _getRoleListFromTemplate();

        for (var role_item of sign_role_list) {
            if (role_item["id"] == sign_role_id) {
                check_flag = false;
                break;
            }
        }
        return check_flag;
    }
    main.createSignRoleId = function (pref) {
        var num_str = "001";
        var sign_role_id = pref + num_str;
        while (true) {
            if (main.isValidSignRoleId(sign_role_id)) {
                break;
            }
            num_str = Utils.increaseInt(num_str);
            sign_role_id = pref + num_str;
        }
        return sign_role_id;
    }
    main.sortSeq = function (role_list) {
        role_list.sort(function(a, b){
            if (a['seq'] == b['seq']) return 0;
            if (a['seq'] > b['seq']) return 1;
            if (a['seq'] < b['seq']) return -1;
            return 0;
        });
    }
    main.getRoleNameById = function (role_type_id) {
        var role_type_name = '';
        for (var role_item of main.role_list) {
            if (role_item["id"] == role_type_id) {
                role_type_name = role_item["name"];
                break;
            }
        }
        return role_type_name;
    }
 }