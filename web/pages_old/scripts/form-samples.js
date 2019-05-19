var FormSamples = function () {


    return {
        //main function to initiate the module
        init: function () {

            // use select2 dropdown instead of chosen as select2 works fine with bootstrap on responsive layouts.
            $('.select2_category').select2({
	            placeholder: "Select an option",
                minimumInputLength: 2,
	            allowClear: true
	        });

            $('.select2_businesses').select2({
                placeholder: "Select businesses",
                allowClear: true
            });

            $('.select2_sample1').select2({
                placeholder: "Select an Option",
                allowClear: true
            });

            $(".select2_sample2").select2({
                placeholder: "Type to select an option",
                allowClear: true,
                multiple:true,
                minimumInputLength: 1,
                ajax: {
                    url: "https://boilerprojects.com/organizations/search",
                    dataType: 'json',
                    quietMillis: 100,
                    data: function (term, page) {
                        return {
                            q: term,
                            page_limit: 10,
                            page: page //you need to send page number or your script do not know witch results to skip
                        };
                    },
                    results: function (data, page)
                    {
                        var more = (page * 10) < data.total;
                        return { results: data.results, more: more };
                    },
                    dropdownCssClass: "bigdrop"
                }
            });

            $(".select2_sample3").select2({
                tags: ["red", "green", "blue", "yellow", "pink"]
            });

        }
    };

}();