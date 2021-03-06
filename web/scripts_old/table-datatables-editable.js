var TableDatatablesEditable = function () {

    var handleTable = function () {

        function restoreRow(oTable, nRow) {
            var aData = oTable.fnGetData(nRow);
            var jqTds = $('>td', nRow);

            for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                oTable.fnUpdate(aData[i], nRow, i, false);
            }

            oTable.fnDraw();
        }

        function createDropDown(name, options, selected) {
            var result = '<select class="form-control input-sm" name="' + name + '">';
            for (var i = 0, len = options.length; i < len; i++) {

                var isSelected = '';

                if( selected == options[i] ) {
                    isSelected = ' selected '
                }

                result = result + '<option ' + isSelected + ' value="' + options[i] + '">' + options[i] + '</option>'
            }
            result = result + '</select>';

            return result;
        }

        function editRow(oTable, nRow, ruleId) {
            var aData = oTable.fnGetData(nRow);
            var jqTds = $('>td', nRow);
            jqTds[0].innerHTML = createDropDown('creating', ['Closing', 'Listing'], aData[0]);
            jqTds[1].innerHTML = createDropDown('represent', ['Any', 'Buyer', 'Buyer&Seller', 'Landlord', 'Landlord&Tenant', 'Seller', 'Tenant'], aData[1]);
            jqTds[2].innerHTML = createDropDown('propertyType', ['Any', 'Commercial', 'Condo/Co-op', 'Manufactured Home', 'Multi-Family', 'Single Family Home', 'Vacant Land'], aData[2]);
            jqTds[3].innerHTML = createDropDown('status', ['Any', 'Active', 'Contract fell thru', 'Expired', 'Leased not Paid', 'Leased Paid', 'Pending', 'Rejected Offer', 'Sold not Paid', 'Sold Paid', 'Temporarily Off Market', 'Withdrawn'], aData[3]);
            jqTds[4].innerHTML = createDropDown('transactionType', ['Any', 'Bank Sale or REO', 'Lease', 'New Construction', 'Regular Sale', 'Short Sale'], aData[4]);
            jqTds[5].innerHTML = '<input type="text" name="yearBuiltAfter" class="form-control input-sm" value="' + aData[5] + '">';
            jqTds[6].innerHTML = '<input type="text" name="yearBuiltBefore" class="form-control input-sm" value="' + aData[6] + '">';
            jqTds[7].innerHTML = '<input type="text" name="documentName" class="form-control input-sm" value="' + aData[7] + '">';
            jqTds[8].innerHTML = '<a data-container="body" data-placement="left" data-original-title="Save" data-id="' + ruleId + '" class="tooltips btn btn-xs btn-success edit" href="javascript:;"><i class="fa fa-save"></i></a> <a data-container="body" data-placement="left" data-original-title="Cancel" class="tooltips btn btn-xs btn-default cancel" href="javascript:;"><i class="fa fa-times"></i></a>';
        }

        function saveRow(oTable, nRow, ruleId) {
            var jqSelect = $('select', nRow);
            var jqInputs = $('input', nRow);
            oTable.fnUpdate(jqSelect[0].value, nRow, 0, false);
            oTable.fnUpdate(jqSelect[1].value, nRow, 1, false);
            oTable.fnUpdate(jqSelect[2].value, nRow, 2, false);
            oTable.fnUpdate(jqSelect[3].value, nRow, 3, false);
            oTable.fnUpdate(jqSelect[4].value, nRow, 4, false);
            oTable.fnUpdate(jqInputs[0].value != '' ? jqInputs[0].value : 'Any', nRow, 5, false);
            oTable.fnUpdate(jqInputs[1].value != '' ? jqInputs[1].value : 'Any', nRow, 6, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 7, false);
            oTable.fnUpdate('<a data-container="body" data-placement="left" data-original-title="Edit" data-id="' + ruleId + '" class="tooltips btn btn-xs btn-warning edit" href="javascript:;"><i class="fa fa-edit"></i></a> <a data-id="' + ruleId + '" class="tooltips btn btn-xs btn-danger delete" href="javascript:;"><i class="fa fa-trash-o"></i></a>', nRow, 8, false);
            oTable.fnDraw();
        }

        function cancelEditRow(oTable, nRow) {
            var jqSelect = $('select', nRow);
            var jqInputs = $('input', nRow);
            oTable.fnUpdate(jqSelect[0].value, nRow, 0, false);
            oTable.fnUpdate(jqSelect[1].value, nRow, 1, false);
            oTable.fnUpdate(jqSelect[2].value, nRow, 2, false);
            oTable.fnUpdate(jqSelect[3].value, nRow, 3, false);
            oTable.fnUpdate(jqSelect[4].value, nRow, 4, false);
            oTable.fnUpdate(jqInputs[0].value, nRow, 5, false);
            oTable.fnUpdate(jqInputs[1].value, nRow, 6, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 7, false);
            oTable.fnUpdate('<a data-container="body" data-placement="left" data-original-title="Save" class="tooltips btn btn-xs btn-success edit" href="javascript:;"><i class="fa fa-save"></i></a> <a data-container="body" data-placement="left" data-original-title="Cancel" class="tooltips btn btn-xs btn-default cancel" href="javascript:;"><i class="fa fa-times"></i></a>', nRow, 8, false);
            oTable.fnDraw();
        }

        var table = $('#doc_rule_editable');

        var oTable = table.dataTable({

            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],

            // set the initial value
            "pageLength": 5,

            "columnDefs": [{ // set default column settings
                'orderable': true,
                'targets': [0]
            }, {
                "searchable": true,
                "targets": [0]
            }],
            "order": [
                [0, "asc"]
            ] // set first column as a default sort by asc
        });

        var tableWrapper = $("#sample_editable_1_wrapper");

        var nEditing = null;
        var nNew = false;

        $('#sample_editable_1_new').click(function (e) {
            e.preventDefault();

            if (nNew && nEditing) {
                if (confirm("Previose row not saved. Do you want to save it ?")) {
                    saveRow(oTable, nEditing); // save
                    $(nEditing).find("td:first").html("Untitled");
                    nEditing = null;
                    nNew = false;

                } else {
                    oTable.fnDeleteRow(nEditing); // cancel
                    nEditing = null;
                    nNew = false;
                    
                    return;
                }
            }

            var aiNew = oTable.fnAddData(['', '', '', '', '', '', '', '', '']);
            var nRow = oTable.fnGetNodes(aiNew[0]);
            editRow(oTable, nRow);
            nEditing = nRow;
            nNew = true;

            $('.tooltips').tooltip()
        });

        table.on('click', '.delete', function (e) {
            e.preventDefault();

            if (confirm("Are you sure to delete this row ?") == false) {
                return;
            }

            ruleId = $(this).data('id');

            $.ajax({
                type: 'POST',
                url: '/doc-rule/remove/' + ruleId,
                dataType: 'json',
                async:false
            });

            var nRow = $(this).parents('tr')[0];
            oTable.fnDeleteRow(nRow);
        });

        table.on('click', '.cancel', function (e) {
            e.preventDefault();
            if (nNew) {
                oTable.fnDeleteRow(nEditing);
                nEditing = null;
                nNew = false;
            } else {
                restoreRow(oTable, nEditing);
                nEditing = null;
            }

            $('.tooltips').tooltip()
        });

        table.on('click', '.edit', function (e) {
            e.preventDefault();
            nNew = false;

            ruleId = $(this).data('id');

            /* Get the row as a parent of the link that was clicked on */
            var nRow = $(this).parents('tr')[0];

            if (nEditing !== null && nEditing != nRow) {
                /* Currently editing - but not this row - restore the old before continuing to edit mode */
                restoreRow(oTable, nEditing);
                editRow(oTable, nRow, ruleId);
                nEditing = nRow;
            } else if (nEditing == nRow && this.innerHTML == '<i class="fa fa-save"></i>') {
                /* Editing this row and want to save it */
                formData = $('#doc-rule-form').serialize();

                if(ruleId == "undefined") {
                    $.ajax({
                        type: 'POST',
                        url: '/doc-rule/create',
                        data: formData,
                        dataType: 'json',
                        async:false
                    }).done(function( data ) {
                        ruleId = data.id;
                    });
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '/doc-rule/' + ruleId,
                        data: formData,
                        dataType: 'json',
                        async:false
                    }).done(function( data ) {
                        ruleId = data.id;
                    });
                }
                saveRow(oTable, nEditing, ruleId);
                nEditing = null;
            } else {
                /* No edit in progress - let's start one */
                editRow(oTable, nRow, ruleId);
                nEditing = nRow;
            }

            $('.tooltips').tooltip()
        });
    };

    return {

        //main function to initiate the module
        init: function () {
            handleTable();
        }

    };

}();

jQuery(document).ready(function() {
    TableDatatablesEditable.init();
});