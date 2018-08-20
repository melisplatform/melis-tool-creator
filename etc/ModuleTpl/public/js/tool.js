$(function(){
    $("body").on("click", ".btnAddModuleTpl", function(){
        modalUrl = '/melis/MelisCore/MelisGenericModal/emptyGenericModal';
        melisHelper.createModal("id_moduletpl_modal", "moduletpl_modal", false, {}, modalUrl);
    });

    $("body").on("click", ".btnSaveModuleTpl", function(){

        var dataString = $("form#moduleTplForm").serializeArray();

        dataString = $.param(dataString);

        $.ajax({
            type        : 'POST',
            url         : '/melis/ModuleTpl/Index/save',
            data		: dataString,
            dataType    : 'json',
            encode		: true
        }).done(function(data) {
            if(data.success) {
                // Notifications
                melisHelper.melisOkNotification(data.textTitle, data.textMessage);

                $("#id_moduletpl_modal_container").modal("hide");
                melisHelper.zoneReload("id_moduletpl_content", "moduletpl_content");
            }else{
                melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                melisCoreTool.highlightErrors(data.success, data.errors, "moduleTplForm");
            }

        }).fail(function(){
            alert( translations.tr_meliscore_error_message );
        });
    });

#TCEDITDELELTESCRIPTS
});