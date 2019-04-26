$(function(){

    var $body = $("body");

    var loader = '<div id="loader" class="overlay-loader"><img class="loader-icon spinning-cog" src="/MelisCore/assets/images/cog12.svg" data-cog="cog12"></div>';

    $body.on("click", ".melis-tool-creator .btn-steps", function(){
        var curStep = $(this).data("curstep");
        var nxtStep = $(this).data("nxtstep");

        var dataString = new Array;

        var stepForm = $(".melis-toolcreator-steps-content form.tool-creator-step-"+curStep);

        var dataName = "step-form";
        if (stepForm.length > 1){
            dataName = "step-form[%s]";
        }

        stepForm.each(function(index, val){

            var formData = $(this).serializeArray();

            $.each(formData, function(i, v){
                /**
                 * This form contains input with the same name attribute of "tcf-db-table-cols"
                 */
                var multInpt = "";
                if ($.inArray(v.name, ["tcf-db-table-cols", "tcf-db-table-col-editable", "tcf-db-table-col-required", "tcf-db-table-col-type", ""]) != -1){
                    multInpt = "[]";
                }

                dataString.push({
                    name : dataName.replace(/%s/g, index)+"["+v.name+"]"+multInpt,
                    value : v.value
                });
            });
        });

        dataString.push({
            name : "curStep",
            value : curStep,
        });

        dataString.push({
            name : "nxtStep",
            value : nxtStep,
        });

        if ($(this).hasClass("tcf-validate")){
            dataString.push({
                name : "validate",
                value : true,
            });
        }

        $("#id_melistoolcreator_steps").append(loader);

        $.post("/melis/tool-creator-validate-cur-step", dataString).done(function(data){

            $("#id_melistoolcreator_steps #loader img").removeClass('spinning-cog').addClass('shrinking-cog');

            setTimeout(function(){
                if(!data.errors) {
                    $("#id_melistoolcreator_steps").html(data.html);

                    $(".melis-toolcreator-steps li").removeClass("active");
                    var targetId = $("#id_melistoolcreator_steps .steps-content").attr("id");
                    $("#tc_"+targetId).addClass("active");

                }else{
                    melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                    tcHighlightErrors(0, data.errors, ".tool-creator-step-"+curStep);
                    $("#id_melistoolcreator_steps #loader").remove()
                }
            }, 500);

        }).error(function(xhr, textStatus, errorThrown){
            alert( translations.tr_meliscore_error_message );
            // alert(xhr.responseText);
        });
    });

    function tcHighlightErrors(success, errors, divContainer) {
        // if all form fields are error color them red
        $(divContainer + " .form-group label").css("color","inherit");

        $(divContainer + " .form-group label").css("color","#686868");
        $.each( errors, function( key, error ) {
            $(divContainer + " .form-control[name='"+key +"']").prev("label").css("color","red");
        });
    }

    $body.on("click", ".tc-reload-dbtbl-cached", function(){
        var reloadBtn = $(this);
        reloadBtn.attr("disabled", true);

        $(".melis-toolcreator-steps-table-list").append(loader);

        $.post('/melis/tool-creator-reload-dbtbl-cached', {reloadDbTblCached : true, selectedTbl: $(".melis-toolcreator-steps-table-list input[name='tcf-db-table']").val()}).done(function(res){

            reloadBtn.attr("disabled", false);

            $(".melis-toolcreator-steps-table-list #loader img").removeClass('spinning-cog').addClass('shrinking-cog');

            setTimeout(function(){

                $(".melis-toolcreator-steps-table-list #loader").remove();

                $(".melis-toolcreator-steps-table-list").html(res.html);

            }, 500);
        });
    });

    $body.on("click", ".melis-toolcreator-steps-table-tabs .widget-head a[data-toggle='tab']", function(){
       var type =$(this).data("type");
    });

    $body.on("click", ".melis-toolcreator-steps-table-list li", function(){

        if ($(this).find(".fa").hasClass("fa-check-square-o") || $(this).hasClass("melis-toolcreator-disable-db-tbl-item")) {
            return;
        }

        var type = $(this).parent("ul").data("type");

        var typeSelector = ".melis-toolcreator-steps-"+type+"-table-list";

        $(".melis-toolcreator-steps-table-list"+typeSelector+" li .fa").removeClass("fa-check-square-o")
                                                                        .addClass("fa-square-o")
                                                                        .removeClass("text-success");
        $(this).find(".fa").addClass("fa-check-square-o");
        $(this).find(".fa").removeClass("fa-square-o");
        $(this).find(".fa").addClass("text-success");

        $(".melis-toolcreator-steps-table-list"+typeSelector).append(loader);

        $(".melis-toolcreator-steps-"+type+"-table-columns").append(loader);

        var input = "tcf-db-table";
        if (type !== "primary-db"){
            input = "tcf-db-table-language-tbl";
        }

        $(".melis-toolcreator-steps-"+type+"-table-list input[name='"+input+"']").val($(this).data("table-name"));

        if (type === "primary-db") {
            $("#melistoolcreator_step3 .alert").hide("slow");

            $(".melis-toolcreator-steps-table-list.melis-toolcreator-steps-language-db-table-list").append(loader);
            if ($.trim($(".melis-toolcreator-steps-language-db-table-columns").html()) !== ""){
                $(".melis-toolcreator-steps-language-db-table-columns").append(loader);
            }
        }else{
            $("#melistoolcreator_step3 .melis-toolcreator-steps-language-db-table-columns .alert").hide("slow");
        }

        var tableName = $(this).data("table-name");

        $.post('/melis/tool-creator-get-tbl-cols', {tableName : tableName, type : type}).done(function(res){

            $(".melis-toolcreator-steps-table-list"+typeSelector+" #loader img").removeClass('spinning-cog').addClass('shrinking-cog');
            $(".melis-toolcreator-steps-"+type+"-table-columns #loader img").removeClass('spinning-cog').addClass('shrinking-cog');

            setTimeout(function(){
                $(".melis-toolcreator-steps-table-list"+typeSelector+" #loader").remove();
                $(".melis-toolcreator-steps-"+type+"-table-columns").html(res.html);

                if (type === "primary-db"){
                    resetLangDbSelection(tableName);
                }else{
                    // Reset language Foreign key fields
                    $(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-pri-fk']").val("");
                    $(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-lang-fk']").val("");
                }
            }, 500);
        });
    });

    function resetLangDbSelection(disableTlb){

        $(".melis-toolcreator-steps-language-db-table-list li").removeClass("melis-toolcreator-disable-db-tbl-item");
        if (typeof disableTlb !== "undefined"){
            $(".melis-toolcreator-steps-language-db-table-list li[data-table-name='"+disableTlb+"']").addClass("melis-toolcreator-disable-db-tbl-item");
        }

        $(".melis-toolcreator-steps-language-db-table-list li .fa").removeClass("fa-check-square-o")
                                                                    .addClass("fa-square-o")
                                                                    .removeClass("text-success");
        $(".melis-toolcreator-steps-language-db-table-columns").html("");
        $(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-tbl']").val("");
        $(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-pri-fk']").val("");
        $(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-lang-fk']").val("");
        $(".melis-toolcreator-steps-table-list.melis-toolcreator-steps-language-db-table-list #loader").remove();
    }

    $body.on("click", ".melis-toolcreator-steps-tbl-cols .tcf-fa-checkbox", function(){

        if ($(this).hasClass("fa-check-square-o")){
            // Unchecking
            if ($(this).hasClass("tcf-fa-checkall")){
                $(".tcf-fa-checkbox.tcf-fa-checkitem[data-col-type='"+$(this).data("col-type")+"'").addClass("fa-square-o")
                                                                                                .removeClass("text-success")
                                                                                                .removeClass("fa-check-square-o")
                                                                                                .next("input").attr("checked", false);
            }else{
                // Unchecking select all checkbox
                $(".tcf-fa-checkbox.tcf-fa-checkall[data-col-type='"+$(this).data("col-type")+"'").addClass("fa-square-o")
                                                                                                .removeClass("text-success")
                                                                                                .removeClass("fa-check-square-o");
            }

            $(this).addClass("fa-square-o")
                .removeClass("text-success")
                .removeClass("fa-check-square-o")
                .next("input").attr("checked", false);


            // Unchecking required if the editable is unchecked
            if ($(this).hasClass("tcf-fa-checkbox-editable")) {

                var requiredInput =  $("input[name='tcf-db-table-col-required'][value='"+$(this).next("input").val()+"']");
                var requiredIcon =  $("input[name='tcf-db-table-col-required'][value='"+$(this).next("input").val()+"']").prev();

                if (requiredIcon.hasClass("fa-check-square-o")) {
                    requiredIcon.addClass("fa-square-o");
                    requiredIcon.removeClass("text-success");
                    requiredIcon.removeClass("fa-check-square-o");
                    requiredInput.attr("checked", false);
                }

                // Disabling field type select input
                $(this).parents("tr").find("select[name='tcf-db-table-col-type']").attr("disabled", true);
            }

        }else{
            // Checking
            if ($(this).hasClass("tcf-fa-checkall")){
                $(".tcf-fa-checkbox.tcf-fa-checkitem[data-col-type='"+$(this).data("col-type")+"'").removeClass("fa-square-o")
                                                                                                .addClass("fa-check-square-o")
                                                                                                .addClass("text-success")
                                                                                                .next("input").attr("checked", true);
            }

            $(this).removeClass("fa-square-o")
                .addClass("fa-check-square-o")
                .addClass("text-success")
                .next("input").attr("checked", true);

            // Set check "select all checkbox"
            if ($(".tcf-fa-checkbox.tcf-fa-checkitem").length === $(".tcf-fa-checkbox.tcf-fa-checkitem.fa-check-square-o").length){
                $(".tcf-fa-checkbox.tcf-fa-checkall[data-col-type='"+$(this).data("col-type")+"'").removeClass("fa-square-o")
                                                                                            .addClass("fa-check-square-o")
                                                                                            .addClass("text-success");
            }

            // Checking editable if the required is checked
            if ($(this).hasClass("tcf-fa-checkbox-required")) {

                var editableInput =  $("input[name='tcf-db-table-col-editable'][value='"+$(this).next("input").val()+"']");
                var editableIcon =  $("input[name='tcf-db-table-col-editable'][value='"+$(this).next("input").val()+"']").prev();

                if (editableIcon.hasClass("fa-square-o")){
                    editableIcon.removeClass("fa-square-o");
                    editableIcon.addClass("fa-check-square-o");
                    editableIcon.addClass("text-success");
                    editableInput.attr("checked", true);

                    // Enabling field type select input
                    $(this).parents("tr").find("select[name='tcf-db-table-col-type']").attr("disabled", false);
                }
            }

            // Checking required if the editable is unchecked
            if ($(this).hasClass("tcf-fa-checkbox-editable")) {
                // Enabling field type select input
                $(this).parents("tr").find("select[name='tcf-db-table-col-type']").attr("disabled", false);
            }
        }
    });

    $body.on("click", ".melis-tc-tool-language.fa", function(){
        if ($(this).hasClass("fa-check-square-o")){
            // unChecking
            $(this).addClass("fa-square-o");
            $(this).removeClass("text-success");
            $(this).removeClass("fa-check-square-o");
            $(".melis-tc-tool-language-db-list").hide(1000, "linear", function(){
                $("#tool-creator-step-3 input[name='tcf-db-table-has-language']").val("");
                $("#tool-creator-step-3 input[name='tcf-db-table-language-tbl']").val("");
                $("#tool-creator-step-3 input[name='tcf-db-table-language-pri-fk']").val("");
                $("#tool-creator-step-3 input[name='tcf-db-table-language-lang-fk']").val("");
            });
        }else{
            // Checking
            $(this).removeClass("fa-square-o");
            $(this).addClass("fa-check-square-o");
            $(this).addClass("text-success");
            $(".melis-tc-tool-language-db-list").show(1000, "linear", function(){
                $("#tool-creator-step-3 input[name='tcf-db-table-has-language']").val(1);
            });
        }
    });

    $body.on("click", ".melis-tc-lang-tbl-pt-fk.fa, .melis-tc-lang-tbl-lt-fk.fa", function(){
        if ($(this).hasClass("fa-check-square-o")){
            // unChecking
            $(this).addClass("fa-square-o");
            $(this).removeClass("text-success");
            $(this).removeClass("fa-check-square-o");
            $("#tool-creator-step-3 input[name='"+$(this).data("field-name")+"']").val("");
        }else{
            // Checking
            if ($(this).hasClass("melis-tc-lang-tbl-pt-fk")){
                $(".melis-tc-lang-tbl-pt-fk.fa").addClass("fa-square-o")
                                            .removeClass("text-success")
                                            .removeClass("fa-check-square-o");

                var ltFk = $(".melis-tc-lang-tbl-lt-fk.fa[data-tbl-name='"+$(this).data("tbl-name")+"']");
                ltFk.addClass("fa-square-o")
                    .removeClass("text-success")
                    .removeClass("fa-check-square-o");

                $ptFkInpt = $("#tool-creator-step-3 input[name='"+ltFk.data("field-name")+"']");
                if ($ptFkInpt.val() === $(this).data("tbl-name")) {
                    $ptFkInpt.val("");
                }

            }else{
                $(".melis-tc-lang-tbl-lt-fk.fa").addClass("fa-square-o")
                                            .removeClass("text-success")
                                            .removeClass("fa-check-square-o");

                var ptFk = $(".melis-tc-lang-tbl-pt-fk.fa[data-tbl-name='"+$(this).data("tbl-name")+"']");
                ptFk.addClass("fa-square-o")
                    .removeClass("text-success")
                    .removeClass("fa-check-square-o");

                $ptFkInpt = $("#tool-creator-step-3 input[name='"+ptFk.data("field-name")+"']");
                if ($ptFkInpt.val() === $(this).data("tbl-name")) {
                    $ptFkInpt.val("");
                }
            }

            $("#tool-creator-step-3 input[name='"+$(this).data("field-name")+"']").val($(this).data("tbl-name"));

            $(this).removeClass("fa-square-o");
            $(this).addClass("fa-check-square-o");
            $(this).addClass("text-success");
        }
    });

    $body.on("click", ".melis-tc-final-content .fa", function(){
        if ($(this).hasClass("fa-check-square-o")){
            // unChecking
            $(this).addClass("fa-square-o");
            $(this).removeClass("text-success");
            $(this).removeClass("fa-check-square-o");
            $(this).next("input").attr("checked", false);
        }else{
            // Checking
            $(this).removeClass("fa-square-o");
            $(this).addClass("fa-check-square-o");
            $(this).addClass("text-success");
            $(this).next("input").attr("checked", true);
        }
    });

    $body.on("click", ".tc-final-step", function(){

        var activateModule = false;

        if ($(".melis-tc-final-content .fa").hasClass("fa-check-square-o")){
            activateModule = true;
        }

        $.get("/melis/MelisToolCreator/ToolCreator/finalize", {activateModule : activateModule}).done(function(res){
            $(".melis-tc-final-content").hide();
            $(".melis-tc-final-content").hide();
        }).fail(function(){

        });
    });
});