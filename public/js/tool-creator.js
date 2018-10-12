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
                 * Special case for step 4
                 * this form contains input with the same name attribute of "tcf-db-table-cols"
                 */
                var multInpt = "";
                if ($.inArray(v.name, ["tcf-db-table-cols", "tcf-db-table-col-editable", "tcf-db-table-col-required", "tcf-db-table-col-type"]) != -1){
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
                    // melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                    // melisCoreTool.highlightErrors(0, data.errors, "tool-creator-step-"+curStep);


                    melisHelper.melisMultiKoNotification(data.textTitle, data.textMessage, data.errors);
                    melisHelper.highlightMultiErrors(0, data.errors, ".tool-creator-step-"+curStep);


                    $("#id_melistoolcreator_steps #loader").remove()
                }
            }, 500);

        }).error(function(xhr, textStatus, errorThrown){
            // alert( translations.tr_meliscore_error_message );
            alert(xhr.responseText);
        });
    });

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

    $body.on("click", ".melis-toolcreator-steps-table-list li", function(){

        $(".melis-toolcreator-steps-table-list li .fa").removeClass("fa-check-square-o");
        $(".melis-toolcreator-steps-table-list li .fa").addClass("fa-square-o");
        $(".melis-toolcreator-steps-table-list li .fa").removeClass("text-success");
        $(this).find(".fa").addClass("fa-check-square-o");
        $(this).find(".fa").removeClass("fa-square-o");
        $(this).find(".fa").addClass("text-success");

        $(".melis-toolcreator-steps-table-list").append(loader);

        $(".melis-toolcreator-steps-table-columns").parent().removeClass("hidden")
        $(".melis-toolcreator-steps-table-columns").append(loader);

        $(".melis-toolcreator-steps-table-list input[name='tcf-db-table']").val($(this).data("table-name"));

        $("#melistoolcreator_step3 .alert").hide("slow");

        $.post('/melis/tool-creator-get-tbl-cols', {tableName : $(this).data("table-name")}).done(function(res){

            $(".melis-toolcreator-steps-table-list #loader img").removeClass('spinning-cog').addClass('shrinking-cog');
            $(".melis-toolcreator-steps-table-columns #loader img").removeClass('spinning-cog').addClass('shrinking-cog');

            setTimeout(function(){

                $(".melis-toolcreator-steps-table-list #loader").remove();

                $(".melis-toolcreator-steps-table-columns").html(res.html);

            }, 500);
        });
    });

    $body.on("click", ".melis-toolcreator-steps-tbl-cols .tcf-fa-checkbox", function(){

        if ($(this).hasClass("fa-check-square-o")){
            // Unchecking
            if ($(this).hasClass("tcf-fa-checkall")){
                $(".tcf-fa-checkbox.tcf-fa-checkitem").addClass("fa-square-o");
                $(".tcf-fa-checkbox.tcf-fa-checkitem").removeClass("text-success");
                $(".tcf-fa-checkbox.tcf-fa-checkitem").removeClass("fa-check-square-o");
                $(".tcf-fa-checkbox.tcf-fa-checkitem").next("input").attr("checked", false);
            }else{
                // Unchecking select all checkbox
                $(".tcf-fa-checkbox.tcf-fa-checkall").addClass("fa-square-o");
                $(".tcf-fa-checkbox.tcf-fa-checkall").removeClass("text-success");
                $(".tcf-fa-checkbox.tcf-fa-checkall").removeClass("fa-check-square-o");
            }

            $(this).addClass("fa-square-o");
            $(this).removeClass("text-success");
            $(this).removeClass("fa-check-square-o");
            $(this).next("input").attr("checked", false);

            if ($(this).hasClass("tcf-fa-checkbox-editable") || $(this).hasClass("tcf-fa-checkbox-required")) {
                console.log($(this).attr("class"));
            }

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
                $(".tcf-fa-checkbox.tcf-fa-checkitem").removeClass("fa-square-o");
                $(".tcf-fa-checkbox.tcf-fa-checkitem").addClass("fa-check-square-o");
                $(".tcf-fa-checkbox.tcf-fa-checkitem").addClass("text-success");
                $(".tcf-fa-checkbox.tcf-fa-checkitem").next("input").attr("checked", true);
            }

            $(this).removeClass("fa-square-o");
            $(this).addClass("fa-check-square-o");
            $(this).addClass("text-success");
            $(this).next("input").attr("checked", true);

            // Set check "select all checkbox"
            if ($(".tcf-fa-checkbox.tcf-fa-checkitem").length == $(".tcf-fa-checkbox.tcf-fa-checkitem.fa-check-square-o").length){
                $(".tcf-fa-checkbox.tcf-fa-checkall").removeClass("fa-square-o")
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