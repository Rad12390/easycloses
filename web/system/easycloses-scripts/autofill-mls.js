$(document).ready(function(){
    $("#fetch_mls").on("click", function(){
        var type = $("#create_form").data('type');
        var userId = $(this).data('user');
        var businessId = $(this).data('business');

        if (type == 'listing') {
            mls = $("#transactionListing_transactionProperty_mlsNumber").val();
        } else {
            mls = $("#transactionClosing_transactionProperty_mlsNumber").val();
        }

        if (businessId == 50) {
            mls = mls.replace("*", "P");

            if (type == 'listing') {
                $("#transactionListing_transactionProperty_mlsNumber").val(mls);
            } else {
                $("#transactionClosing_transactionProperty_mlsNumber").val(mls);
            }
        }

        if (mls != '') {
            var url = '/mls/autofill/' + userId + '/' + mls;
            var json = (function () {
                var json = null;
                $.ajax({
                    'async': false,
                    'global': false,
                    'url': url,
                    'dataType': "json",
                    'success': function (data) {
                        json = data;
                    }
                });

                return json;
            })();

            if (json !== null && json.result == 'success') {
                populate(type, json.mls_data);
            } else {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "1000",
                    "hideDuration": "1000",
                    "timeOut": "10000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr.error('Please open a support ticket if you feel it should be.', 'This MLS # is not in the System');
            }
        }
    });

    function populate(type, pop_data) {
        $.each(pop_data, function (key, value) {
            if (type == 'listing') {
                if (key == 'mls_number') {
                    $('#transactionListing_transactionProperty_mlsNumber').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'address') {
                    field = $('#transactionListing_transactionProperty_property_address_street');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'city') {
                    field = $('#transactionListing_transactionProperty_property_address_city');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'state') {
                    field = $('#transactionListing_transactionProperty_property_address_state');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'unit') {
                    field = $('#transactionListing_transactionProperty_mlsBoard');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'zip') {
                    field = $('#transactionListing_transactionProperty_property_address_zip');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'property_type') {
                    value = value.replace(/ /g, "_");
                    field = $('#transactionListing_transactionProperty_property_type');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'unit_number') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'year_built') {
                    field = $('#transactionListing_transactionProperty_yearBuilt');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'bedrooms') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'full_bath') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_office_name') {
                    $('#transactionListing_listing_listingOfficeContact_contactName').val(value.replace(/&amp;/g, '&'));

                    $('#transactionListing_listing_listingOfficeContact_company').val(value.replace(/&amp;/g, '&'));
                    $('#transactionListing_listing_listingAgentContact_company').val(value.replace(/&amp;/g, '&'));
                }
                if (key == 'list_office_email') {
                    field = $('#transactionClosing_closing_listingOfficeContact_email');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                }
                if (key == 'list_office_phone') {
                    field = $('#transactionClosing_closing_listingOfficeContact_phone');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                }
                if (key == 'office_phone') {
                    if (type == 'listing') {
                        field = $('#transactionListing_listing_listingOfficeContact_phone');
                        field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                        field.trigger('change');
                    } else {
                        $('#transactionClosing_closing_listingOfficeContact_phone').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                        $('#transactionClosing_closing_buyersOfficeContact_phone').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    }
                }
                if (key == 'list_agent_name') {
                    if (type == 'listing') {
                        field = $('#transactionListing_listing_listingAgentContact_contactName');
                        field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success')
                        field.trigger('change');
                    } else {
                        $('#transactionClosing_closing_listingAgentContact_contactName').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                        $('#transactionClosing_closing_buyersAgentContact_contactName').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    }
                }
                if (key == 'list_agent_email') {
                    if (type == 'listing') {
                        field = $('#transactionListing_listing_listingAgentContact_email');
                        field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success')
                        field.trigger('change');
                    } else {
                        $('#transactionClosing_closing_listingAgentContact_email').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                        $('#transactionClosing_closing_buyersAgentContact_email').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    }
                }
                if (key == 'list_agent_phone') {
                    if (type == 'listing') {
                        field = $('#transactionListing_listing_listingAgentContact_phone');
                        field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success')
                        field.trigger('change');
                    } else {
                        $('#transactionClosing_closing_listingAgentContact_phone').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                        $('#transactionClosing_closing_buyersAgentContact_phone').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    }
                }
                if (key == 'agent_id') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'property_status') {
                    $('#transactionListing_transactionProperty_aboutProperty').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'price') {
                    field = $('#transactionListing_listing_moneyBox_contractPrice');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', '');
                }
                if (key == 'remarks_notes') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'legal') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'half_bath') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_date' && value != '' ) {
                    date = new Date(value);
                    $("#transactionListing_listing_listingContacts_0_time").datepicker("setDate", date).trigger('change');
                }
                if (key == 'expiration_date' && value != '' ) {
                    date = new Date(value);
                    $("#transactionListing_listing_listingContacts_1_time").datepicker("setDate", date).trigger('change');
                }
            } else {
                if (key == 'mls_number') {
                    $('#transactionClosing_transactionProperty_mlsNumber').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'address') {
                    field = $('#transactionClosing_transactionProperty_property_address_street');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'city') {
                    field = $('#transactionClosing_transactionProperty_property_address_city');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'state') {
                    field = $('#transactionClosing_transactionProperty_property_address_state');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'unit') {
                    field = $('#transactionClosing_transactionProperty_mlsBoard');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'zip') {
                    field = $('#transactionClosing_transactionProperty_property_address_zip');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'property_type') {
                    value = value.replace(/ /g, "_");
                    field = $('#transactionClosing_transactionProperty_property_type');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'unit_number') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'year_built') {
                    field = $('#transactionClosing_transactionProperty_yearBuilt');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    asterisk = field.parent().find('i');
                    asterisk.css('color', 'rgb(0, 128, 0)');
                }
                if (key == 'bedrooms') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'full_bath') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_office_name') {
                    field = $('#transactionClosing_closing_listingOfficeContact_contactName');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');

                    $('#transactionClosing_closing_listingAgentContact_company').val(value);
                    $('#transactionClosing_closing_listingOfficeContact_company').val(value);
                    //$('#transactionClosing_closing_buyersOfficeContact_contactName').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_office_phone') {
                    field = $('#transactionClosing_closing_listingOfficeContact_phone');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    //$('#transactionClosing_closing_buyersOfficeContact_phone').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_office_email') {
                    field = $('#transactionClosing_closing_listingOfficeContact_email');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    //$('#transactionClosing_closing_buyersOfficeContact_email').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_agent_name') {
                    field = $('#transactionClosing_closing_listingAgentContact_contactName');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    //$('#transactionClosing_closing_buyersAgentContact_contactName').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_agent_phone') {
                    field = $('#transactionClosing_closing_listingAgentContact_phone');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    //$('#transactionClosing_closing_buyersAgentContact_phone').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_agent_email') {
                    field = $('#transactionClosing_closing_listingAgentContact_email');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                    //$('#transactionClosing_closing_buyersAgentContact_email').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'agent_id') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'property_status') {
                    field = $('#transactionClosing_transactionProperty_aboutProperty');
                    field.val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                    field.trigger('change');
                }
                /*if (key == 'price') {
                    $('#transactionClosing_closing_moneyBox_contractPrice').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }*/
                if (key == 'remarks_notes') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'legal') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'half_bath') {
                    //$('#').val(value).parent().removeClass('error').addClass('success').parent().removeClass('error').addClass('success');
                }
                if (key == 'list_date' && value != '') {
                    //date = new Date(value);
                    //$("#transactionClosing_closing_closingContacts_0_time").datepicker("setDate", date);
                }
                if (key == 'expiration_date' && value != '') {
                    //date = new Date(value);
                    //$("#transactionClosing_closing_closingContacts_1_time").datepicker("setDate", date);
                }
            }
        });
        $('#mls').trigger('focus');
    }
});