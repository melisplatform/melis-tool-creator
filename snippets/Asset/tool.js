$(function(){
    $("body").on("click", ".btnAddModuleTpl", function(){
#TCADDBTN
    });

    $("body").on("click", ".btnSaveModuleTpl", function(){
        var btn = $(this);
        btn.attr('disabled', true);
#TCSAVE
    });

    $("body").on("click", ".btnEditModuleTpl", function(){
#TCEDIT
    });

    $("body").on("click", ".btnDeleteModuleTpl", function(){
        var id = $(this).parents("tr").children(":first").text();

        melisCoreTool.confirm(
            translations.tr_moduletpl_common_button_yes,
            translations.tr_moduletpl_common_button_no,
            translations.tr_moduletpl_delete_title,
            translations.tr_moduletpl_delete_confirm_msg,
            function() {
                $.ajax({
                    type        : 'GET',
                    url         : '/melis/ModuleTpl/List/deleteItem?id='+id,
                    dataType    : 'json',
                    encode		: true,
                    success		: function(){
                        // refresh the table after deleting an item
                        melisHelper.zoneReload("id_moduletpl_content", "moduletpl_content");
                        #TCCLOSETABDELETE
                    }
                });
            });
    });
});