jQuery(document).ready(function () {
    function loadTable(page) {
        jQuery.ajax({
            url: myAjax.ajaxurl,
            type: "POST",
            data: {
                action: 'load_table',
                page_no: page
            },
            success: function (data) {
                jQuery("#ulp_list_table").html(data);
            }
        });
    }
    loadTable();

    //Pagination Code
    jQuery(document).on("click", "#pagination a", function (e) {
        e.preventDefault();
        var page_id = jQuery(this).attr("id");
        loadTable(page_id);
    })

    // Fiter the table data
    jQuery("#filter_table").submit(function (e) {
        e.preventDefault();
        var role = jQuery('#ulp_role').val();
        var order = jQuery('#ulp_order').val();

        jQuery.ajax({
            url: myAjax.ajaxurl,
            type: "post",
            data: {
                action: 'filter',
                ulp_role: role,
                ulp_oder: order,
                security: myAjax.my_script_nonce
            },
            success: function (data) {
                console.log(data);
                jQuery('#ulp_list_table').html(data);
            }
        });
    });
});

