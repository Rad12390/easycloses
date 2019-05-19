loading_cnt = 0;

function initApiDatas() {

    $.LoadingOverlay("show");
    setTimeout(function() {
        $.LoadingOverlay("hide");
        
        // var tab_id = $(".tab-pane.active.show").attr("id");
        // $("#" + tab_id + "-tab.nav-link").click();
    }, 10000);

    apiGetUser();
    apiGetSignDocList();
    apiGetTemplateList();
    apiGetSigneeCandidateList();
    apiGetRoleList();
}

////////// AJAX API ////////////////////////////

function apiGetUser() {
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
}

function apiGetSignDocList() {
    $.ajax({
        url: $('#route-api_get_sign_doc_list').data('route'),
        success: function(data) {
            localStorage.setItem("api_sign_doc_list", JSON.stringify(data.sign_doc_list));
            loading_cnt = procLoading(loading_cnt);
        },
        error: function(data) {
            loading_cnt = procLoading(loading_cnt);
        }
    });
}

function apiAddSignDoc(sign_doc_data) {
    // API
    $.post({
        url: $('#route-api_add_sign_doc').data('route'),
        data: { "data": JSON.stringify(sign_doc_data) },
        success: function(data) {
            loading_cnt = procLoading(loading_cnt);
        },
        error: function(data) {
            loading_cnt = procLoading(loading_cnt);
        }
    });
}

function apiGetTemplateList() {
    // template list from server to local storage
    $.ajax({
        url: $('#route-api_get_template_list').data('route'),
        success: function(data) {
            localStorage.setItem("api_template_list", JSON.stringify(data.template));
            localStorage.setItem("api_public_temp_list", JSON.stringify(data.template));
            loading_cnt = procLoading(loading_cnt);
        },
        error: function(data) {
            loading_cnt = procLoading(loading_cnt);
        }
    });
}

function apiGetSigneeCandidateList() {
    $.ajax({
        url: $('#route-api_get_signee_candidate_list').data('route'),
        success: function(data) {
            localStorage.setItem("api_signee_candidate_list", JSON.stringify(data.user));
            loading_cnt = procLoading(loading_cnt);
        },
        error: function(data) {
            loading_cnt = procLoading(loading_cnt);
        }
    });
}

function apiGetRoleList() {
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
}

function procLoading(loading_cnt) {
    loading_cnt++;
    if (loading_cnt >= 5) {
        $.LoadingOverlay("hide");
        // refresh
        var tab_id = $(".tab-pane.active.show").attr("id");
        $("#" + tab_id + "-tab.nav-link").click();
    }
    return loading_cnt;
}

/////////// Local Storage /////////////////////////

function getSenderInfo() {
    sender_info = JSON.parse(localStorage.getItem("api_user"));
    return sender_info;
}

function getSignDocList() {
    var sign_doc_list = localStorage.getItem("api_sign_doc_list");
    if (sign_doc_list != undefined && sign_doc_list != "" && sign_doc_list != "undefined") {
        sign_doc_list = JSON.parse(sign_doc_list);
    } else {
        apiGetSignDocList();
        sign_doc_list = [];
    }
    return sign_doc_list;
}

function addSignDoc(sign_doc_data) {

    var create_flag = true;
    var sign_doc_list = localStorage.getItem("api_sign_doc_list");

    if (sign_doc_list != undefined && sign_doc_list != "") {
        sign_doc_list = JSON.parse(sign_doc_list);
    } else {
        sign_doc_list = [];
    }

    for (var sign_doc_item of sign_doc_list) {
        if (sign_doc_item["id"] == sign_doc_data["id"]) {
            create_flag = false;
            var index = sign_doc_list.indexOf(sign_doc_item);
            sign_doc_list[index] = sign_doc_data;

            break;
        }
    }

    if (create_flag) {
        sign_doc_list.push(sign_doc_data);
        apiAddSignDoc(sign_doc_data);
    }

    localStorage.setItem("api_sign_doc_list", JSON.stringify(sign_doc_list));
}

function deleteSignDocData(sign_doc_id, sender_id) {
    var sign_doc_info_list = [];
    var sign_doc_list = localStorage.getItem("api_sign_doc_list");
    if (sign_doc_list != undefined && sign_doc_list != "") {
        sign_doc_list = JSON.parse(sign_doc_list);
    } else {
        sign_doc_list = [];
    }
    for (var sign_doc_data of sign_doc_list) {
        if (sign_doc_data["id"] == sign_doc_id) {
            var index = sign_doc_list.indexOf(sign_doc_data);
            if (index !== -1) sign_doc_list.splice(index, 1);
        } else {
            sign_doc_info_list.push({
                id: sign_doc_data["id"],
                name: sign_doc_data["name"]
            });
        }
    }
    localStorage.setItem("api_sign_doc_list", JSON.stringify(sign_doc_list));

    // API
    $.LoadingOverlay("show");
    $.post({
        url: $('#route-api_delete_sign_doc').data('route'),
        data: { "data": JSON.stringify({id: sign_doc_id}) },
        success: function(data) {
            $.LoadingOverlay("hide");
            $("#template-tab.nav-link").click();
        },
        error: function(data) {
            $.LoadingOverlay("hide");
            error_dialog(data);
        }
    });

    return sign_doc_info_list;
}

function getRoleList(creator_id) {
    role_list = JSON.parse(localStorage.getItem("api_role_list"));
    return role_list;
}

function getSigneeCandidateList() {
    var signee_candidate_list = localStorage.getItem("api_signee_candidate_list");
    if (signee_candidate_list != undefined && signee_candidate_list != "" && signee_candidate_list != "undefined") {
        signee_candidate_list = JSON.parse(signee_candidate_list);
    } else {
        apiGetSigneeCandidateList();
        signee_candidate_list = [];
    }
    
    return signee_candidate_list;
}

function getTemplateInfoList(sender_id) {
    // virtual code start
    var template_info_list = [];
    var template_list = localStorage.getItem("api_template_list");
    if (template_list != undefined && template_list != "") {
        template_list = JSON.parse(template_list);
    } else {
        apiGetTemplateList();
        template_list = [];
    }
    for (var template_data of template_list) {
        template_info_list.push({
            id: template_data["id"],
            name: template_data["name"],
            created: template_data["created"],
            updated: template_data["updated"]
        });
    }
    // virtual code end
    return template_info_list;
}

function getTemplateList(sender_id) {
    // virtual code start
    var template_list = localStorage.getItem("api_template_list");
    if (template_list != undefined && template_list != "") {
        template_list = JSON.parse(template_list);
    } else {
        apiGetTemplateList();
        template_list = [];
    }
    // virtual code end
    return template_list;
}

function getPublicTempList(sender_id) {
    var real_temp_list = [];
    var public_temp_list = localStorage.getItem("api_public_temp_list");
    if (public_temp_list != undefined && public_temp_list != "") {
        public_temp_list = JSON.parse(public_temp_list);
    } else {
        apiGetTemplateList();
        public_temp_list = [];
    }

    for (var template_item of public_temp_list) {
        if (template_item["input_list"].length != 0) {
            real_temp_list.push(template_item);
        }
    }
    return real_temp_list;
}
//>>>>>>>>>>>>>>>>>>>>>>>> create or update
function getTemplateData(template_id, createor_id=0) {
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

    if (create_flag) {
        template_list.push(template_data);
        template_info_list.push({
            id: template_data["id"],
            name: template_data["name"]
        });
    }

    localStorage.setItem("api_template_list", JSON.stringify(template_list));

    // API
    $.post({
        url: $('#route-api_set_template_list').data('route'),
        data: { "data": JSON.stringify(template_list) },
        success: function(data) {
            console.log(data);
        },
        error: function(data) {
        }
    });

    // virtual code end
    return template_info_list;
}



function getSignDocInfoList(sender_id) {
    var sign_doc_info_list = [];
    var sign_doc_list = localStorage.getItem("api_sign_doc_list");
    if (sign_doc_list != undefined && sign_doc_list != "") {
        sign_doc_list = JSON.parse(sign_doc_list);
    } else {
        apiGetSignDocList();
        sign_doc_list = [];
    }
    for (var sign_doc_data of sign_doc_list) {
        sign_doc_info_list.push({
            id: sign_doc_data["id"],
            name: sign_doc_data["name"]
        });
    }
    // virtual code end
    return sign_doc_info_list;
}

function getSignDocList(sender_id) {
    // virtual code start
    var sign_doc_list = localStorage.getItem("api_sign_doc_list");
    if (sign_doc_list != undefined && sign_doc_list != "") {
        sign_doc_list = JSON.parse(sign_doc_list);
    } else {
        apiGetSignDocList();
        sign_doc_list = [];
    }
    // virtual code end
    return sign_doc_list;
}

//>>>>>>>>>>>>>>>>>>>>>>>> create or update
function getSignDocData(sign_doc_id, createor_id=0) {
    var sign_doc_list = localStorage.getItem("api_sign_doc_list");
    if (sign_doc_list != undefined && sign_doc_list != "") {
        sign_doc_list = JSON.parse(sign_doc_list);
    } else {
        return null
    }
    for (var sign_doc_item of sign_doc_list) {
        if (sign_doc_item["id"] == sign_doc_id) {
            return sign_doc_item;
        }
    }
    return null;
}

function setSignDocData(sign_doc_data, creator_id) {
    
    var create_flag = true;
    var sign_doc_info_list = [];
    var sign_doc_list = localStorage.getItem("api_sign_doc_list");
    if (sign_doc_list != undefined && sign_doc_list != "") {
        sign_doc_list = JSON.parse(sign_doc_list);
    } else {
        sign_doc_list = [];
    }
    for (var sign_doc_item of sign_doc_list) {
        if (sign_doc_item["id"] == sign_doc_data["id"]) {
            create_flag = false;
            var index = sign_doc_list.indexOf(sign_doc_item);
            sign_doc_list[index] = sign_doc_data;
            sign_doc_info_list.push({
                id: sign_doc_data["id"],
                name: sign_doc_data["name"]
            });
        } else {
            sign_doc_info_list.push({
                id: sign_doc_item["id"],
                name: sign_doc_item["name"]
            });
        }
    }

    $.LoadingOverlay("show");
    if (create_flag) {

        // API
        $.post({
            url: $('#route-api_add_sign_doc').data('route'),
            data: { "data": JSON.stringify(sign_doc_data) },
            success: function(data) {
                $.LoadingOverlay("hide");
                sign_doc_data["id"] = data.id;

                sign_doc_list.push(sign_doc_data);
                sign_doc_info_list.push({
                    id: sign_doc_data["id"],
                    name: sign_doc_data["name"]
                });

                localStorage.setItem("api_sign_doc_list", JSON.stringify(sign_doc_list));
                _setSignDocData(sign_doc_data);

                // refresh
                Templates.initData();
                Templates.initScreen();
            },
            error: function(data) {
                $.LoadingOverlay("hide");
                error_dialog(data);
            }
        });
    } else {
        // API
        $.post({
            url: $('#route-api_update_sign_doc').data('route'),
            data: { "data": JSON.stringify(sign_doc_data) },
            success: function(data) {
                $.LoadingOverlay("hide");
            },
            error: function(data) {
                $.LoadingOverlay("hide");
                error_dialog(data);
            }
        });
    }

    localStorage.setItem("api_sign_doc_list", JSON.stringify(sign_doc_list));

    return sign_doc_info_list;
}

function publishSignDoc(sign_doc_id) {
    var sign_doc_data = {
        id: sign_doc_id,
    };
    $.LoadingOverlay("show");
    $.post({
        url: $("#route-api_publish_sign_doc").data('route'),
        data: { "data": JSON.stringify(sign_doc_data) },
        success: function(data) {
            $.LoadingOverlay("hide");

            sign_doc_data = _getSignDocData();
            sign_doc_data.status = data.status;
            setSignDocData(sign_doc_data);
            $("#template-tab.nav-link").click();

            Templates.showSigneeListForm(sign_doc_data.id);
        },
        error: function(data) {
            $.LoadingOverlay("hide");
            error_dialog(data);
        }
    });
}

function approveSignDoc(sign_doc_id) {
    var sign_doc_data = {
        id: sign_doc_id,
    };
    $.LoadingOverlay("show");
    $.post({
        url: $('#route-api_approve_sign_doc').data('route'),
        data: { "data": JSON.stringify(sign_doc_data) },
        success: function(data) {
            $.LoadingOverlay("hide");

            sign_doc_data = _getSignDocData();
            sign_doc_data.status = data.status;
            setSignDocData(sign_doc_data);
            $("#template-tab.nav-link").click();
        },
        error: function(data) {
            $.LoadingOverlay("hide");
            error_dialog(data);
        }
    });
}
//>>>>>>>>>>>>>>>>>>>>>>>>>
var empty_template_data = {
    id: "",
    name: "",
    signee_role_list: [],
    pdf_list: [],
    input_list: []
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

////////// sign doc data management ///////////////////////
var empty_sign_doc_data = {
    id: "",
    name: "",
    template_id: "",
    signee_list: [],
    pdf_list: [],
    input_list: []
}
var empty_signee_data = {
    signee_role_id: "",
    id: "",
    name: "",
    email: ""
}

const SIGN_ROLE_ID_SENDER = "1";

function _setSignDocData(data) {
    localStorage.setItem("sign_doc_data", JSON.stringify(data));
}

function _getSignDocData() {
    var sign_doc_data = localStorage.getItem("sign_doc_data");
    if (sign_doc_data) {
        return JSON.parse(sign_doc_data);
    }
    return false;
}

function _getPdfListFromSignDoc() {
    var ret = [];
    var sign_doc_data = _getSignDocData();
    if (sign_doc_data) {
        ret = sign_doc_data["pdf_list"];
    } else {
        ret = _getPdfListForCreate();
    }
    return ret;
}

function _getSigneeListFromSignDoc(sign_doc_data=null) {
    var ret = [];
    if (sign_doc_data == null) sign_doc_data = _getSignDocData();
    if (sign_doc_data) {
        ret = sign_doc_data["signee_list"];
    }
    return ret;
}

function _getSigneeFromSignDoc(signee_role_id, sign_doc) {
    var ret = JSON.parse(JSON.stringify(empty_signee_data));;
    var signee_list = _getSigneeListFromSignDoc(sign_doc);
    for (var signee of signee_list) {
        if (signee['sign_role_id'] == signee_role_id) {
            return signee;
        }
    }
    return ret;
}

function _getInputListFromSignDoc() {
    var ret = [];
    var sign_doc_data = _getSignDocData();
    if (sign_doc_data) {
        ret = sign_doc_data["input_list"];
    }
    return ret;
}

function _setInputListToSignDoc(input_list) {
    var sign_doc_data = _getSignDocData();
    if (sign_doc_data) {
        sign_doc_data["input_list"] = input_list;
    }
    _setSignDocData(sign_doc_data);
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
        Inputs.initScreen();
        Inputs.initEvent();
    });
});