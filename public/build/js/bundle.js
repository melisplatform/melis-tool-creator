$((function(){var e=$("body"),a='<div id="loader" class="overlay-loader"><img class="loader-icon spinning-cog" src="/MelisCore/assets/images/cog12.svg" data-cog="cog12"></div>';e.on("click",".melis-tool-creator .btn-steps",(function(){var e=$(this).data("curstep"),t=$(this).data("nxtstep"),s=new Array,l=$(".melis-toolcreator-steps-content form.tool-creator-step-"+e),o="step-form";l.length>1&&(o="step-form[%s]"),l.each((function(e,a){var t=$(this).serializeArray();$.each(t,(function(a,t){var l="";-1!=$.inArray(t.name,["tcf-db-table-cols","tcf-db-table-col-display","tcf-db-table-col-editable","tcf-db-table-col-required","tcf-db-table-col-type",""])&&(l="[]"),s.push({name:o.replace(/%s/g,e)+"["+t.name+"]"+l,value:t.value})}))})),s.push({name:"curStep",value:e}),s.push({name:"nxtStep",value:t}),$(this).hasClass("tcf-validate")&&s.push({name:"validate",value:!0}),$("#id_melistoolcreator_steps").append(a),$.post("/melis/tool-creator-validate-cur-step",s).done((function(a){$("#id_melistoolcreator_steps #loader img").removeClass("spinning-cog").addClass("shrinking-cog"),setTimeout((function(){if(a.errors)melisHelper.melisKoNotification(a.textTitle,a.textMessage,a.errors),s=a.errors,l=".tool-creator-step-"+e,$(l+" .form-group label").css("color","inherit"),$(l+" .form-group label").css("color","#686868"),$.each(s,(function(e,a){$(l+" .form-control[name='"+e+"']").prev("label").css("color","red")})),$("#id_melistoolcreator_steps #loader").remove();else{$("#id_melistoolcreator_steps").html(a.html),$(".melis-toolcreator-steps li").removeClass("active");var t=$("#id_melistoolcreator_steps .steps-content").attr("id");$("#tc_"+t).addClass("active")}var s,l}),500)})).fail((function(e,a,t){console.log(translations.tr_meliscore_error_message)}))})),e.on("change","input[name='tcf-tool-type']",(function(){$(".tcf-tool-type").parents(".form-group").hide(),$(".tcf-tool-type-"+$(this).val()).parents(".form-group").show(),$("input[name='tcf-tool-framework'].tcf-tool-type-"+$(this).val()).length&&($("input[name='tcf-create-framework-tool']").parents(".make-switch").find(".switch-animate").hasClass("switch-on")?$("input[name='tcf-tool-framework'].tcf-tool-type-"+$(this).val()).parents(".form-group").show():$("input[name='tcf-tool-framework'].tcf-tool-type-"+$(this).val()).parents(".form-group").hide())})),e.on("click",".tc-reload-dbtbl-cached",(function(){$("#id_melistoolcreator_steps").append(a),$.post("/melis/tool-creator-validate-cur-step",{reloadDbTblCached:!0,curStep:2,nxtStep:3}).done((function(e){setTimeout((function(){$("#id_melistoolcreator_steps #loader").remove(),$("#id_melistoolcreator_steps").html(e)}),500)}))})),e.on("click",".melis-toolcreator-steps-table-tabs .widget-head a[data-bs-toggle='tab']",(function(){$(this).data("type")})),e.on("click",".melis-toolcreator-steps-table-list li",(function(){if(!$(this).find(".fa").hasClass("fa-check-square-o")&&!$(this).hasClass("melis-toolcreator-disable-db-tbl-item")){var e=$(this).parent("ul").data("type"),t=".melis-toolcreator-steps-"+e+"-table-list";$(".melis-toolcreator-steps-table-list"+t+" li .fa").removeClass("fa-check-square-o").addClass("fa-square-o").removeClass("text-success"),$(this).find(".fa").addClass("fa-check-square-o"),$(this).find(".fa").removeClass("fa-square-o"),$(this).find(".fa").addClass("text-success"),$(".melis-toolcreator-steps-table-list"+t).append(a),$(".melis-toolcreator-steps-"+e+"-table-columns").append(a);var s="tcf-db-table";"primary-db"!==e&&(s="tcf-db-table-language-tbl"),$(".melis-toolcreator-steps-"+e+"-table-list input[name='"+s+"']").val($(this).data("table-name")),"primary-db"===e?($("#melistoolcreator_step3 .alert").hide("slow"),$(".melis-toolcreator-steps-table-list.melis-toolcreator-steps-language-db-table-list").append(a),""!==$(".melis-toolcreator-steps-language-db-table-columns").html().trim()&&$(".melis-toolcreator-steps-language-db-table-columns").append(a)):$("#melistoolcreator_step3 .melis-toolcreator-steps-language-db-table-columns .alert").hide("slow");var l=$(this).data("table-name");$.post("/melis/tool-creator-get-tbl-cols",{tableName:l,type:e}).done((function(a){$(".melis-toolcreator-steps-table-list"+t+" #loader img").removeClass("spinning-cog").addClass("shrinking-cog"),$(".melis-toolcreator-steps-"+e+"-table-columns #loader img").removeClass("spinning-cog").addClass("shrinking-cog"),setTimeout((function(){var s;$(".melis-toolcreator-steps-table-list"+t+" #loader").remove(),$(".melis-toolcreator-steps-"+e+"-table-columns").html(a.html),"primary-db"===e?(s=l,$(".melis-toolcreator-steps-language-db-table-list li").removeClass("melis-toolcreator-disable-db-tbl-item"),void 0!==s&&$(".melis-toolcreator-steps-language-db-table-list li[data-table-name='"+s+"']").addClass("melis-toolcreator-disable-db-tbl-item"),$(".melis-toolcreator-steps-language-db-table-list li .fa").removeClass("fa-check-square-o").addClass("fa-square-o").removeClass("text-success"),$(".melis-toolcreator-steps-language-db-table-columns").html(""),$(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-tbl']").val(""),$(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-pri-fk']").val(""),$(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-lang-fk']").val(""),$(".melis-toolcreator-steps-table-list.melis-toolcreator-steps-language-db-table-list #loader").remove()):($(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-pri-fk']").val(""),$(".melis-toolcreator-steps-language-db-table-list input[name='tcf-db-table-language-lang-fk']").val(""))}),500)}))}})),e.on("click",".melis-toolcreator-steps-tbl-cols .tcf-fa-checkbox",(function(){if($(this).hasClass("fa-check-square-o")){if($(this).hasClass("tcf-fa-checkall")?($(".tcf-fa-checkbox.tcf-fa-checkitem[data-col-type='"+$(this).data("col-type")+"'").addClass("fa-square-o").removeClass("text-success").removeClass("fa-check-square-o").next("input").attr("checked",!1),$(this).hasClass("tfc-table-list")&&$("select[name='tcf-db-table-col-display'][data-col-type='"+$(this).data("col-type")+"']").attr("disabled",!0)):$(".tcf-fa-checkbox.tcf-fa-checkall[data-col-type='"+$(this).data("col-type")+"']").addClass("fa-square-o").removeClass("text-success").removeClass("fa-check-square-o"),$(this).addClass("fa-square-o").removeClass("text-success").removeClass("fa-check-square-o").next("input").attr("checked",!1),$(this).hasClass("tcf-fa-checkbox-editable")){var e=$("input[name='tcf-db-table-col-required'][value='"+$(this).next("input").val()+"']"),a=$("input[name='tcf-db-table-col-required'][value='"+$(this).next("input").val()+"']").prev();a.hasClass("fa-check-square-o")&&(a.addClass("fa-square-o"),a.removeClass("text-success"),a.removeClass("fa-check-square-o"),e.attr("checked",!1)),$(this).parents("tr").find("select[name='tcf-db-table-col-type']").attr("disabled",!0)}$(this).hasClass("tfc-table-list")&&$(this).parents("tr").find("select[name='tcf-db-table-col-display']").attr("disabled",!0)}else{if($(this).hasClass("tcf-fa-checkall")&&($(".tcf-fa-checkbox.tcf-fa-checkitem[data-col-type='"+$(this).data("col-type")+"']").removeClass("fa-square-o").addClass("fa-check-square-o").addClass("text-success").next("input").attr("checked",!0),$(this).hasClass("tfc-table-list")&&$("select[name='tcf-db-table-col-display'][data-col-type='"+$(this).data("col-type")+"']").attr("disabled",!1)),$(this).removeClass("fa-square-o").addClass("fa-check-square-o").addClass("text-success").next("input").attr("checked",!0),$(".tcf-fa-checkbox.tcf-fa-checkitem").length===$(".tcf-fa-checkbox.tcf-fa-checkitem.fa-check-square-o").length&&$(".tcf-fa-checkbox.tcf-fa-checkall[data-col-type='"+$(this).data("col-type")+"']").removeClass("fa-square-o").addClass("fa-check-square-o").addClass("text-success"),$(this).hasClass("tcf-fa-checkbox-required")){var t=$("input[name='tcf-db-table-col-editable'][value='"+$(this).next("input").val()+"']"),s=$("input[name='tcf-db-table-col-editable'][value='"+$(this).next("input").val()+"']").prev();s.hasClass("fa-square-o")&&(s.removeClass("fa-square-o"),s.addClass("fa-check-square-o"),s.addClass("text-success"),t.attr("checked",!0),$(this).parents("tr").find("select[name='tcf-db-table-col-type']").attr("disabled",!1))}$(this).hasClass("tcf-fa-checkbox-editable")&&$(this).parents("tr").find("select[name='tcf-db-table-col-type']").attr("disabled",!1),$(this).hasClass("tfc-table-list")&&$(this).parents("tr").find("select[name='tcf-db-table-col-display']").attr("disabled",!1)}})),e.on("click",".melis-tc-tool-language.fa",(function(){$(this).hasClass("fa-check-square-o")?($(this).addClass("fa-square-o"),$(this).removeClass("text-success"),$(this).removeClass("fa-check-square-o"),$(".melis-tc-tool-language-db-list").hide(1e3,"linear",(function(){$(".tool-creator-step-3 input[name='tcf-db-table-has-language']").val(""),$(".tool-creator-step-3 input[name='tcf-db-table-language-tbl']").val(""),$(".tool-creator-step-3 input[name='tcf-db-table-language-pri-fk']").val(""),$(".tool-creator-step-3 input[name='tcf-db-table-language-lang-fk']").val("")}))):($(this).removeClass("fa-square-o"),$(this).addClass("fa-check-square-o"),$(this).addClass("text-success"),$(".melis-tc-tool-language-db-list").show(1e3,"linear",(function(){$(".tool-creator-step-3 input[name='tcf-db-table-has-language']").val(1)})))})),e.on("click",".melis-tc-lang-tbl-pt-fk.fa, .melis-tc-lang-tbl-lt-fk.fa",(function(){if($(this).hasClass("fa-check-square-o"))$(this).addClass("fa-square-o"),$(this).removeClass("text-success"),$(this).removeClass("fa-check-square-o"),$(".tool-creator-step-3 input[name='"+$(this).data("field-name")+"']").val("");else{var e;if($(this).hasClass("melis-tc-lang-tbl-pt-fk")){$(".melis-tc-lang-tbl-pt-fk.fa").addClass("fa-square-o").removeClass("text-success").removeClass("fa-check-square-o");var a=$(".melis-tc-lang-tbl-lt-fk.fa[data-tbl-name='"+$(this).data("tbl-name")+"']");a.addClass("fa-square-o").removeClass("text-success").removeClass("fa-check-square-o"),(e=$(".tool-creator-step-3 input[name='"+a.data("field-name")+"']")).val()===$(this).data("tbl-name")&&e.val("")}else{$(".melis-tc-lang-tbl-lt-fk.fa").addClass("fa-square-o").removeClass("text-success").removeClass("fa-check-square-o");var t=$(".melis-tc-lang-tbl-pt-fk.fa[data-tbl-name='"+$(this).data("tbl-name")+"']");t.addClass("fa-square-o").removeClass("text-success").removeClass("fa-check-square-o"),(e=$(".tool-creator-step-3 input[name='"+t.data("field-name")+"']")).val()===$(this).data("tbl-name")&&e.val("")}$(".tool-creator-step-3 input[name='"+$(this).data("field-name")+"']").val($(this).data("tbl-name")),$(this).removeClass("fa-square-o"),$(this).addClass("fa-check-square-o"),$(this).addClass("text-success")}})),e.on("switch-change","div.make-switch[data-input-name='tcf-create-framework-tool']",(function(e,a){!1===a.value?$("label[for='tcf-tool-framework']").parents(".form-group").hide():$("label[for='tcf-tool-framework']").parents(".form-group").show()})),e.on("click",".melis-tc-final-content .fa",(function(){$(this).hasClass("fa-check-square-o")?($(this).addClass("fa-square-o"),$(this).removeClass("text-success"),$(this).removeClass("fa-check-square-o"),$(this).next("input").attr("checked",!1)):($(this).removeClass("fa-square-o"),$(this).addClass("fa-check-square-o"),$(this).addClass("text-success"),$(this).next("input").attr("checked",!0))})),e.on("click",".tc-final-step",(function(){var e=!1;$(".melis-tc-final-content .fa").hasClass("fa-check-square-o")&&(e=!0),$.get("/melis/MelisToolCreator/ToolCreator/finalize",{activateModule:e}).done((function(e){$(".melis-tc-final-content").hide(),$(".melis-tc-final-content").hide()})).fail((function(){alert(translations.tr_meliscore_error_message)}))}))}));
