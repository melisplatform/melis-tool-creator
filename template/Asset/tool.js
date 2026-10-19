$(function(){
    // Bind once: when this file is loaded twice, every click ran its handler twice (2 delete requests per confirmation)
    if (window.moduletplToolJsBound) return;
    window.moduletplToolJsBound = true;

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
                // POST: the back-office CSRF token is only sent (and checked) on non-GET requests
                $.ajax({
                    type        : 'POST',
                    url         : '/melis/ModuleTpl/List/deleteItem',
                    data        : {id: id},
                    dataType    : 'json',
                    encode		: true
                })
                .done(function(data){
                    if (!data.success) {
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage);
                        return;
                    }

                    // refresh the table after deleting an item
                    melisHelper.zoneReload("id_moduletpl_content", "moduletpl_content");

                    // Notifications
                    melisHelper.melisOkNotification(data.textTitle, data.textMessage);

                    #TCCLOSETABDELETE
                });

        });
    });
});