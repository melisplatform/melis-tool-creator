$(function(){
    $("body").on("click", ".btnAddModuleTpl", function(){
#TCADDBTN
    });

#TCSAVE

    $("body").on("click", ".btnEditModuleTpl", function(){
#TCEDIT
    });

    $("body").on("click", ".btnDeleteModuleTpl", function(){
        var id = $(this).parents("tr").attr("id");

        melisCoreTool.confirm(
            translations.tr_moduletpl_common_button_yes,
            translations.tr_moduletpl_common_button_no,
            translations.tr_moduletpl_delete_title,
            translations.tr_moduletpl_delete_confirm_msg,
            function(data) {
                $.ajax({
                    type        : 'GET',
                    url         : '/melis/ModuleTpl/List/deleteItem?id='+id,
                    dataType    : 'json',
                    encode		: true,
                    success		: function(data){
                        // refresh the table after deleting an item
                        melisHelper.zoneReload("id_moduletpl_content", "moduletpl_content");

                        // Notifications
                        melisHelper.melisOkNotification(data.textTitle, data.textMessage);

                        #TCCLOSETABDELETE
                    }
                });
            });
    });
});