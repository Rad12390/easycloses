function PdfManagerClass(parent) {
    var main = this;
    var BASE64_MARKER = ';base64,';
    main.parent = parent;
    main.scale = 2;
    main.scale_interval = 0.2;

    main.input_infos = [];

    main.getPdfType = function () {
        var pref = "pdftype-"
        var num_str = "001";
        var pdf_type = pref + num_str;
        while (true) {
            if (main.isValidPdfType(pdf_type)) {
                break;
            }
            num_str = Utils.increaseInt(num_str);
            pdf_type = pref + num_str;
        }
        return pdf_type;
    }
    main.isValidPdfType = function (pdf_type) {
        if (!pdf_type) {
            return false;
        }
        var pdftypes = Db.getPdfTypes();
        for (var pdftype of pdftypes) {
            if (pdf_type == pdftype) {
                return false;
            }
        }
        return true;
    }
    main.drawPdfs = function (pdf_list) {

        var $viewer = $('#pdf-viewer');
        $viewer.html("");
        main.pageCnt = 0;

        main.parent.sel_page.html("");
        // main.parent.sel_page.append($("<option value=''>Select Page</option>"));
        for (let pdf_item of pdf_list) {
            let base64data = _getPdfData(pdf_item["id"]);

            if (!base64data) return;
            let pdfjsLib = window['pdfjs-dist/build/pdf'];
            pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/esign/pdfjs/pdf.worker.js';
            let pdfAsDataUri = base64data;
            //
            let pdfData = main.convertDataURIToBinary(pdfAsDataUri);
            let loadingTask = pdfjsLib.getDocument({
                data: pdfData
            });
            let pdf_id = pdf_item["id"];
            let pdf_name = pdf_item["name"];
            let $pdf_container = $("<div class='pdf' pdf_id='" + pdf_id + "' pdf_name='" + pdf_name + "'></div>");
            $viewer.append($pdf_container);
            loadingTask.promise.then(function (pdf) {
                var thePdf = pdf;
                // var $pdf_container = $();
                for (let page_num = 1; page_num <= pdf.numPages; page_num++) {
                    canvas = document.createElement("canvas");
                    canvas.className = 'pdf-page-canvas';
                    let $page_container = $("<div class='pdf-page draggable' page_num='" + page_num + "'></div>");
                    $page_container.append($(canvas));
                    main.initPageContainer($page_container);

                    $pdf_container.append($page_container);
                    renderPage(thePdf, page_num, canvas);
                    main.pageCnt++;
                    main.parent.sel_page.append($("<option value='" + main.pageCnt + "'>Page " + main.pageCnt + "</option>"));
                    main.parent.showInputInfos(pdf_id, page_num);
                }
            });
        }

        function renderPage(thePdf, pageNumber, canvas) {
            thePdf.getPage(pageNumber).then(function (page) {
                var viewport = page.getViewport(main.scale);
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                page.render({
                    canvasContext: canvas.getContext('2d'),
                    viewport: viewport
                });
            });
        }
    }
    
    main.initPageContainer = function ($page_container) {
        
        $page_container.droppable({
            tolerance: "fit",
            drop: function (event, ui) {
                if (ui.draggable.hasClass('tool-item') && main.parent.cur_role_id != "" && main.parent.cur_role_id != undefined) {
                    var $overlay = main.parent.createOverlaybyType(ui.draggable, $(this));
                }
            }
        });
    }
    main.initEvents = function ($input_item) {

    }
    main.convertDataURIToBinary = function (dataURI) {
        // var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
        // var base64 = dataURI.substring(base64Index);
        var raw = window.atob(dataURI);
        return raw;
    }
    main.scalePdf = function (scale = false) {
        if (!scale) {
            scale = main.scale;
        }
        main.scale = scale;
        $("#pdf-viewer canvas").each(function () {
            $(this).width($(this).attr("width") * main.scale / 2);
        });
        $("#pdf-viewer div.pdf-page .overlay-item").each(function () {
            // $(this).attr("data-id")
            $(this).css("left", $(this).attr("data-x") * main.scale);
            $(this).css("top", $(this).attr("data-y") * main.scale);
            $(this).css("width", $(this).attr("data-width") * main.scale + "px");
            $(this).css("height", $(this).attr("data-height") * main.scale + "px");
        });
    }

    var load_pdf_cnt = 0;
    main.loadPdfDatas = function (pdf_list, callback) {
        if (pdf_list) {
            load_pdf_cnt = 0;
            for (var pdf of pdf_list) {
                var pdf_data = _getPdfData(pdf.id);
                if (pdf_data != undefined) {
                    load_pdf_cnt++;
                    if (pdf_list.length == load_pdf_cnt) {
                        callback();
                    }
                } else {
                    $.ajax({
                        url: $('#route-api_get_pdf').data('route') + '/blank/' + pdf.id,
                        success: function(pdf_data) {
                            localStorage.setItem("api_pdf_data_" + pdf_data['id'], pdf_data['data']);
                            load_pdf_cnt++;
                            if (pdf_list.length == load_pdf_cnt) {
                                callback();
                            }       
                        },
                        error: function(data) {
                            callback();
                        }
                    });
                }
            }
        }
    }
}