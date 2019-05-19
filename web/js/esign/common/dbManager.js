
function DatabaseClass() {
    var main = this;
    // main.getPdfTypes = function () {
    //     var pdftypes = localStorage.getItem("tb_pdf_type");
    //     if (!pdftypes) pdftypes = "[]";
    //     return JSON.parse(pdftypes);
    // }
    main.insertPdfType = function (pdftype) {
        var pdftypes = main.getPdfTypes();
        pdftypes.push(pdftype);
        localStorage.setItem("tb_pdf_type", JSON.stringify(pdftypes));
    }
    main.getpdfData = function (pdftype) {
        return localStorage.getItem("tb_pdf_data_" + pdftype);
    }
    main.setPdfData = function (pdftype, data) {
        localStorage.setItem("tb_pdf_data_" + pdftype, data);
    }
    main.saveInputInfo = function (pdftype, overlay, page_num, input_id, input_data) {
        if (!overlay){
            return [];
        }
        var inputInfos = main.getInputInfos(pdftype, overlay);
        if (!inputInfos[page_num]) inputInfos[page_num] = {};
        inputInfos[page_num][input_id] = input_data;
        localStorage.setItem("tb_input_info_:" + overlay + ":" + pdftype, JSON.stringify(inputInfos));
        return inputInfos;
    }
    main.deleteInputInfo = function (pdftype, overlay, page_num, input_id) {
        if (!overlay){
            return [];
        }
        var inputInfos = main.getInputInfos(pdftype, overlay);
        if (!inputInfos[page_num]) inputInfos[page_num] = {};
        delete (inputInfos[page_num][input_id]);

        localStorage.setItem("tb_input_info_:" + overlay + ":" + pdftype, JSON.stringify(inputInfos));
        return inputInfos;
    }
    main.getInputInfos = function (pdftype, overlay) {
        if (!overlay) return [];
        var inputInfos = localStorage.getItem("tb_input_info_:" + overlay + ":" + pdftype);
        if (!inputInfos) inputInfos = "[]";
        return JSON.parse(inputInfos);
    }
}