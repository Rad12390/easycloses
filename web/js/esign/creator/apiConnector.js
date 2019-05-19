function initApiDatas() {

    var loading_cnt = 0;
    $.LoadingOverlay("show");
    setTimeout(function() {
        $.LoadingOverlay("hide");
        // var tab_id = $(".tab-pane.active.show").attr("id");
        // $("#" + tab_id + "-tab.nav-link").click();
    }, 10000);

    // get user
    $.ajax({
        url: $('#route-api_get_user').data('route'),
        success: function(data) {
            localStorage.setItem("api_user", JSON.stringify(data));
            loading_cnt = procLoading(loading_cnt);
        },
        error: function(data) {
            loading_cnt = procLoading(loading_cnt);
        }
    });

    // template list from server to local storage
    $.ajax({
        url: $('#route-api_get_template_list').data('route'),
        success: function(data) {
            localStorage.setItem("api_template_list", JSON.stringify(data.template));
            loading_cnt = procLoading(loading_cnt);
        },
        error: function(data) {
            loading_cnt = procLoading(loading_cnt);
        }
    });

    // pdf list from server to local storage
    $.ajax({
        url: $('#route-api_get_pdf_list').data('route'),
        success: function(data) {
            localStorage.setItem("api_pdf_list", JSON.stringify(data.pdf));
            loading_cnt = procLoading(loading_cnt);
        },
        error: function(data) {
            loading_cnt = procLoading(loading_cnt);
        }
    });

    // role list from server to local storage
    $.ajax({
        url: $('#route-api_get_role_list').data('route'),
        success: function(data) {
            localStorage.setItem("api_role_list", JSON.stringify(data.role));
            loading_cnt = procLoading(loading_cnt);
        },
        error: function(data) {
            loading_cnt = procLoading(loading_cnt);
        }
    });

    // // get pdf
    // $.ajax({
    //     url: $('#route-api_get_pdf').data('route'),
    //     success: function(pdf_data) {
    //         localStorage.setItem("api_pdf_data", pdf_data);
    //         loading_cnt = procLoading(loading_cnt);
    //     }
    // });
}

function getCreatorInfo() {
    user_info = JSON.parse(localStorage.getItem("api_user"));
    return user_info;
}

function procLoading(loading_cnt) {
    loading_cnt++;
    if (loading_cnt >= 4) {
        $.LoadingOverlay("hide");

        localStorage.removeItem("template_data");

        // refresh
        var tab_id = $(".tab-pane.active.show").attr("id");
        $("#" + tab_id + "-tab.nav-link").click();
    }
    return loading_cnt;
}

// pdf_list = [
//     {
// 	    id:"",
//      name:"",
//      data:""
//     },
//     {
// 	    id:"",
//      name:"",
//      data:""
//     },
// ]

function getPdfList(creator_id) {
    // virtual code start
    var pdf_list = localStorage.getItem("api_pdf_list");
    if (pdf_list != undefined && pdf_list != "") {
        pdf_list = JSON.parse(localStorage.getItem("api_pdf_list"));
    } else {
        pdf_list = [];
    }
    // for (var pdf_info of pdf_list) {
    //     pdf_info["data"] = localStorage.getItem("api_pdf_data_" + pdf_info["id"]);
    // }
    // virtual code end
    return pdf_list;
}

// role_list = [
//     {
// 	    id: "",
//         name:"",
//     },
//     {
// 	    id:"",
//         name:"",
//     },
// ]
function getRoleList(creator_id) {
    // virtual code start
    role_list = JSON.parse(localStorage.getItem("api_role_list"));
    // virtual code end
    return role_list;
}

//>>>>>>>>>>>>>>>>>>>>>>>
// template_info_list = [
//     {
//         id: "",
//         name: "",
//     },
//     {
//         id: "",
//         name: "",
//     },
// ]
function getTemplateInfoList(creator_id) {
    // virtual code start
    var template_info_list = [];
    var template_list = localStorage.getItem("api_template_list");
    if (template_list != undefined && template_list != "") {
        template_list = JSON.parse(template_list);
    } else {
        template_list = [];
    }
    for (var template_data of template_list) {
        template_info_list.unshift({
            id: template_data["id"],
            name: template_data["name"],
            created: template_data["created"],
            updated: template_data["updated"]
        });
    }
    // virtual code end
    return template_info_list;
}

function deleteTemplateData(template_id, creator_id) {
    // virtual code start
    var template_info_list = [];
    var template_list = localStorage.getItem("api_template_list");
    if (template_list != undefined && template_list != "") {
        template_list = JSON.parse(template_list);
    } else {
        template_list = [];
    }
    for (var template_data of template_list) {
        if (template_data["id"] == template_id) {
            var index = template_list.indexOf(template_data);
            if (index !== -1) template_list.splice(index, 1);
        } else {
            template_info_list.push({
                id: template_data["id"],
                name: template_data["name"]
            });
        }
    }
    localStorage.setItem("api_template_list", JSON.stringify(template_list));
    // virtual code end

    // API
    $.LoadingOverlay("show");
    $.post({
        url: $('#route-api_delete_template').data('route'),
        data: { "data": JSON.stringify({id: template_id}) },
        success: function(data) {
            $.LoadingOverlay("hide");
            $("#template-tab.nav-link").click();
        },
        error: function(data) {
            $.LoadingOverlay("hide");
            error_dialog(data);
        }
    });

    return template_info_list;
}

//>>>>>>>>>>>>>>>>>>>>>>>> create or update
function getTemplateData(template_id, createor_id) {
    var template_list = localStorage.getItem("api_template_list");
    if (template_list != undefined && template_list != "") {
        template_list = JSON.parse(template_list);
    } else {
        return null
    }
    for (var template_item of template_list) {
        if (template_item["id"] == template_id) {
            return template_item;
        }
    }
    return null;
}

function setTemplateDataToDst(template_data, creator_id, dest) {
    // virtual code start
    var create_flag = true;
    var template_info_list = [];
    var template_list = localStorage.getItem("api_template_list");
    if (template_list != undefined && template_list != "") {
        template_list = JSON.parse(template_list);
    } else {
        template_list = [];
    }
    for (var template_item of template_list) {
        if (template_item["id"] == template_data["id"]) {
            create_flag = false;
            var index = template_list.indexOf(template_item);
            template_list[index] = template_data;
            template_info_list.push({
                id: template_data["id"],
                name: template_data["name"]
            });
        } else {
            template_info_list.push({
                id: template_item["id"],
                name: template_item["name"]
            });
        }
    }

    $.LoadingOverlay("show");

    if (create_flag) {

        // API
        $.post({
            url: $('#route-api_add_template').data('route'),
            data: { "data": JSON.stringify(template_data) },
            success: function(data) {
                $.LoadingOverlay("hide");

                template_data["id"] = data.id;
                template_list.push(template_data);
                template_info_list.push({
                    id: template_data["id"],
                    name: template_data["name"]
                });
                localStorage.setItem("api_template_list", JSON.stringify(template_list));
                _setTemplateData(template_data);

                // refresh
                $("#pdf-tab.nav-link").click();
            },
            error: function(data) {
                $.LoadingOverlay("hide");
                error_dialog(data);
            }
        });
    } else {
        // API
        $.post({
            url: $('#route-api_update_template').data('route'),
            data: { "data": JSON.stringify(template_data) },
            success: function(data) {
                $.LoadingOverlay("hide");
                var $_modal = $($('#modal-notification').html());
                var $content = "<p>Your Template has been Saved.</p>";
                Modal.initModal($_modal, "Notification", $content, "Ok", function () {
                    $_modal.modal('hide');
                    if (dest == 'input') {
                        $("#input-tab.nav-link").click();
                        $("#input-tab.nav-link").click();
                    } else {
                        $("#role-tab.nav-link").click();
                    }
                    return false;
                });
                $_modal.modal('show');
            },
            error: function(data) {
                $.LoadingOverlay("hide");
                error_dialog(data);
            }
        });
    }

    localStorage.setItem("api_template_list", JSON.stringify(template_list));

    // virtual code end
    return template_info_list;
}

function setTemplateData(template_data, creator_id) {
    // virtual code start
    var create_flag = true;
    var template_info_list = [];
    var template_list = localStorage.getItem("api_template_list");
    if (template_list != undefined && template_list != "") {
        template_list = JSON.parse(template_list);
    } else {
        template_list = [];
    }
    for (var template_item of template_list) {
        if (template_item["id"] == template_data["id"]) {
            create_flag = false;
            var index = template_list.indexOf(template_item);
            template_list[index] = template_data;
            template_info_list.push({
                id: template_data["id"],
                name: template_data["name"]
            });
        } else {
            template_info_list.push({
                id: template_item["id"],
                name: template_item["name"]
            });
        }
    }

    $.LoadingOverlay("show");

    if (create_flag) {

        // API
        $.post({
            url: $('#route-api_add_template').data('route'),
            data: { "data": JSON.stringify(template_data) },
            success: function(data) {
                $.LoadingOverlay("hide");

                template_data["id"] = data.id;
                template_list.push(template_data);
                template_info_list.push({
                    id: template_data["id"],
                    name: template_data["name"]
                });
                localStorage.setItem("api_template_list", JSON.stringify(template_list));
                _setTemplateData(template_data);

                // refresh
                $("#pdf-tab.nav-link").click();
            },
            error: function(data) {
                $.LoadingOverlay("hide");
                error_dialog(data);
            }
        });
    } else {
        // API
        $.post({
            url: $('#route-api_update_template').data('route'),
            data: { "data": JSON.stringify(template_data) },
            success: function(data) {
                $.LoadingOverlay("hide");
                var $_modal = $($('.modal-notification').html());
                var $content = "<p>Your Template has been Saved.</p>";
                Modal.initModal($_modal, "Notification", $content, "Ok", function () {
                    $_modal.modal('hide');
                    $("#template-tab.nav-link").click();
                    location.reload();
                    return false;
                });
                $_modal.modal('show');
            },
            error: function(data) {
                $.LoadingOverlay("hide");
                error_dialog(data);
            }
        });
    }

    localStorage.setItem("api_template_list", JSON.stringify(template_list));

    // virtual code end
    return template_info_list;
}

var alert_html = '<div class="alert alert-success alert-dismissible fade show" role="alert">';
alert_html += '<p>You should check in on some of those fields below.</p>';
alert_html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
alert_html += '    <span aria-hidden="true">&times;</span>';
alert_html += '</button>';
alert_html += '</div>';

function showAlert(msg) {
    var $container = $(".notification-container");
    var $alert = $(alert_html);
    $alert.find("p").html(msg);
    $container.append($alert);
    var $cur_alert = $alert;
    setTimeout(function () {
        $cur_alert.alert("close");
    }, 3000);
}
//>>>>>>>>>>>>>>>>>>>>>>>>>
var empty_template_data = {
    id: "",
    name: "",
    signee_role_list: [],
    pdf_list: [],
    input_list: [],
    created: '0000-00-00 00:00:00',
    updated: '0000-00-00 00:00:00'
}
// //>>>>>>>>>>>>>>>>>>>>>>>>
// unit_role = {
//     id: "",
//     name: "",
// }
// //>>>>>>>>>>>>>>>>>>>>>>>>
// unit_pdf = {
//     id: "",
//     name: "",
//     data: ""
// }
// //>>>>>>>>>>>>>>>>>>>>>>>>
// uint_input = {
//     id: "",
//     role_id: "",
//     pdf_id: "",
//     page_num: "1",
//     data: {
//         input_type: "1", // 1: Signature, 2: Initials, 3: Date/Time, 4: Form Field, 5: CheckBox, 6: RedioButton
//         x_pos: "0",
//         y_pos: "0",
//         width: "10",
//         height: "10",
//         optional: "0", // 0: optional, 1: required
//         detail: {
//             // for every inputs. ex: font, relatives...
//         }
//     }
// }
//>>>>>>>>>>>>>>>>>>>>>>>>
// template_data = {
//     id: "",
//     name: "",
//     role_list: [
//         {
//             id: "",
//             name: "",
//         },
//         {
//             id: "",
//             name: "",
//         }],
//     pdf_list: [
//         {
//             id: "",
//             name: "",
//             data: ""
//         },
//         {
//             id: "",
//             name: "",
//             data: ""
//         }],
//     input_list: [
//         {
//             id: "",
//             role_id: "",
//             pdf_id: "",
//             page_num: "",
//             data: {
//                 input_type: "", // 1: Signature, 2: Initials, 3: Date/Time, 4: Form Field, 5: CheckBox, 6: RedioButton
//                 x_pos: "",
//                 y_pos: "",
//                 width: "",
//                 height: "",
//                 optional: "",
//                 detail: {
//                     // for every inputs. ex: font, relatives...
//                 }
//             }
//         },
//     ]
// }
///////////////////Local functions/////////////////////
function _clearCreatorInfo() {
    localStorage.setItem("creator_info", "");
}

function _setCreatorInfo(creator_info) {
    localStorage.setItem("creator_info", creator_info);
}

function _clearTemplateData() {
    localStorage.setItem("template_data", "");
}

function _setTemplateData(data) {
    localStorage.setItem("template_data", JSON.stringify(data));
}

function _getTemplateData() {
    var template_data = localStorage.getItem("template_data");
    if (template_data) {
        return JSON.parse(template_data);
    }
    return false;
}


function _clearPdfListForCreate() {
    localStorage.setItem("pdfs_for_create", "[]");
}

function _getPdfListForCreate() {
    var pdf_list = localStorage.getItem("pdfs_for_create");
    if (!pdf_list) {
        pdf_list = [];
    } else {
        pdf_list = JSON.parse(pdf_list);
    }
    return pdf_list;
}

function _setPdfListForCreate(pdf_list) {
    localStorage.setItem("pdfs_for_create", JSON.stringify(pdf_list));
}

function _getPdfListFromTemplate() {
    var ret = [];
    var template_data = _getTemplateData();
    if (template_data) {
        ret = template_data["pdf_list"];
    } else {
        ret = _getPdfListForCreate();
    }
    return ret;
}

function _setPdfListToTemplate(pdf_list) {
    var template_data = _getTemplateData();
    if (template_data) {
        template_data["pdf_list"] = pdf_list;
    } else {
        _setPdfListForCreate(pdf_list);
        return;
    }
    _setTemplateData(template_data);
}

function _getRoleListFromTemplate() {
    var ret = [];
    var template_data = _getTemplateData();
    if (template_data) {
        ret = template_data["signee_role_list"];
    }
    return ret;
}

function _setRoleListToTemplate(role_list) {
    var template_data = _getTemplateData();
    if (template_data) {
        template_data["signee_role_list"] = role_list;
    }
    _setTemplateData(template_data);
}

function _getInputListFromTemplate() {
    var ret = [];
    var template_data = _getTemplateData();
    if (template_data) {
        ret = template_data["input_list"];
    }
    return ret;
}

function _setInputListToTemplate(input_list) {
    var template_data = _getTemplateData();
    if (template_data) {
        template_data["input_list"] = input_list;
    }
    _setTemplateData(template_data);
}

// local util

function _setInputToList(input_list, input_data) {
    var create_flag = true;
    if (!input_list) {
        input_list = [];
    }
    for (var input_item of input_list) {
        if (input_item["id"] == input_data["id"]) {
            create_flag = false;
            var index = input_list.indexOf(input_item);
            input_list[index] = input_data;
        }
    }
}
var templates = null;
var pdfs = null;
// temp
$(document).ready(function () {
    $("#template-tab.nav-link").click(function () {
        Templates.initData();
        Templates.initScreen();
    });
    $("#pdf-tab.nav-link").click(function () {
        Pdfs.initData();
        Pdfs.initScreen();
    });
    $("#role-tab.nav-link").click(function () {
        Roles.initData();
        Roles.initScreen();
    });
    $("#input-tab.nav-link").click(function () {
        Inputs.initData();
        Inputs.initEvent();
        Inputs.initScreen();
    });
});