<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'tr_melistoolcreator' => 'Tool creator',
    'tr_melistoolcreator_description' => 'The tool creator generates new modules',

    // Interfaces
    'tr_melistoolcreator_header' => 'Tool creator header',
    'tr_melistoolcreator_content' => 'Tool creator content',
    'tr_melistoolcreator_steps' => 'Tool creator steps',

    // Buttons
    'tr_melistoolcreator_next' => 'Next',
    'tr_melistoolcreator_back' => 'Back',
    'tr_melistoolcreator_finish' => 'Finish and create the tool',

    // Tabs
    'tr_melistoolcreator_db_single_tool' => 'Single table',
    'tr_melistoolcreator_db_language_tool' => 'Tool Language',

    // Warnings
    'tr_melistoolcreator_fp_title' => 'File permission denied',
    'tr_melistoolcreator_fp_msg' => 'In-order to create tools using this module, please give the rights to write in the following directories or contact the administrator if the problem persists',
    'tr_melistoolcreator_fp_config' => '<b>/config/melis.module.load.php</b> - this file is required to activate a tool after its creation',
    'tr_melistoolcreator_fp_cache' => '<b>/cache</b> - the directory where the table list cache file are saved, this accelerates the setup process of the tool',
    'tr_melistoolcreator_fp_module' => '<b>/module</b> - The directory where the created tools are saved',

    // Steps
    'tr_melistoolcreator_module' => 'Tool',
    'tr_melistoolcreator_tcf-name tooltip' => 'Name of the tool. Alphanumeric and underscore are the only valid characters allowed. The tool name cannot start by a number neither contain any space',
    'tr_melistoolcreator_tcf_tool_type' => 'Tool type',
    'tr_melistoolcreator_tcf_tool_type tooltip' => 'Select the tool type you want to create',
    'tr_melistoolcreator_module_desc' => 'Enter the name of the tool',
    'tr_melistoolcreator_texts' => 'Texts',
    'tr_melistoolcreator_texts_title' => 'Module text translations',
    'tr_melistoolcreator_texts_desc' => 'Enter the text translations in different languages, at least one language field should be filled in',
    'tr_melistoolcreator_database' => 'Database',
    'tr_melistoolcreator_database_title' => 'Module table',
    'tr_melistoolcreator_database_desc' => 'Select the table that will be used by the tool',
    'tr_melistoolcreator_database_reload_cached' => 'Refresh the table list',
    'tr_melistoolcreator_database_reload_cached_tooltip' => 'Refreshing the table list may take a few minutes',
    'tr_melistoolcreator_table_cols' => 'Table columns',
    'tr_melistoolcreator_table_cols_title' => 'Table columns',
    'tr_melistoolcreator_table_cols_desc' => 'Select the column(s) to be displayed on the generated tool table list',
    'tr_melistoolcreator_add_update_form' => 'Add/Update form',
    'tr_melistoolcreator_add_update_form_title' => 'Add/Update form fields',
    'tr_melistoolcreator_add_update_form_desc' => 'Please select the column(s) which will be editable, mandatory and its/their type of field',
    'tr_melistoolcreator_cols_translations' => 'Columns translations',
    'tr_melistoolcreator_cols_translations_title' => 'Module text translations',
    'tr_melistoolcreator_cols_translations_desc' => 'Enter the text translations in different languages, at least one language field should be filled in',
    'tr_melistoolcreator_summary' => 'Summary',
    'tr_melistoolcreator_finalization' => 'Finalization',
    'tr_melistoolcreator_finalization_desc' => 'Tick the box below if you wish to activate the tool upon creation',
    'tr_melistoolcreator_finalization_activate_module' => 'Activate the tool after creation',
    'tr_melistoolcreator_finalization_activation_note' => '<strong>Note:</strong> Activating the tool will require to restart the platform',
    'tr_melistoolcreator_finalization_success_title' => 'The tool has been successfully created',
    'tr_melistoolcreator_finalization_success_desc_with_counter' => 'The platform will refresh in <strong><span id="tc-restart-cd">5</span></strong>',
    'tr_melistoolcreator_finalization_success_desc' => 'You can manually activate/deactivate the tool now by changing its status from the list of modules in System configuration / Modules',

    // Texts
    'tr_melistoolcreator_db_tables' => 'Tables',
    'tr_melistoolcreator_db_tables_cols' => 'Tables columns',
    'tr_melistoolcreator_col_pk' => 'PK',
    'tr_melistoolcreator_col_fk' => 'FK',
    'tr_melistoolcreator_col_name' => 'Name',
    'tr_melistoolcreator_col_type' => 'Type',
    'tr_melistoolcreator_col_null' => 'Null',
    'tr_melistoolcreator_col_default' => 'Default',
    'tr_melistoolcreator_col_extra' => 'Extra',
    'tr_melistoolcreator_col_editable' => 'Editable',
    'tr_melistoolcreator_col_mandatory' => 'Mandatory',
    'tr_melistoolcreator_col_field_type' => 'Input type',
    'tr_melistoolcreator_columns' => 'Columns',
    'tr_melistoolcreator_columns_desciption' => 'Columns description',
    'tr_melistoolcreator_refreshing' => 'Resfreshing...',
    'tr_melistoolcreator_pri_tbl' => 'Primary table',
    'tr_melistoolcreator_lang_tbl' => 'Language table',
    'tr_melistoolcreator_pri_db_tbl' => 'Primary table',
    'tr_melistoolcreator_lang_db_tbl' => 'Language table',
    'tr_melistoolcreator_pri_tbl_cols' => 'Primary table columns',
    'tr_melistoolcreator_lang_tbl_cols' => 'Language table columns',
    'tr_melistoolcreator_pri_tbl_cols_select' => 'Select the primary table corresponding to your tool',
    'tr_melistoolcreator_int_lang_tab' => 'Integrate the language tool tabulation',
    'tr_melistoolcreator_lang_tbl_lst' => 'Language table',
    'tr_melistoolcreator_select_lang_tbl_lst' => 'Select the language table corresponding to your tool',
    'tr_melistoolcreator_pk_fk_pri_tbl_lbl' => '<b>PTFK (Primary table foreign key)</b> - the relation key of the Primary table',
    'tr_melistoolcreator_pk_fk_lang_tbl_lbl' => '<b>LTFK (Language table foreign key)</b> - the relation key of the Cms Page language table',
    'tr_melistoolcreator_lang_txt' => 'Language',
    'tr_melistoolcreator_col_txt' => 'Columns',
    'tr_melistoolcreator_name_txt' => 'Name',
    'tr_melistoolcreator_desc_txt' => 'description',
    'tr_melistoolcreator_activate' => 'Activate',
    'tr_melistoolcreator_deactivate' => 'Deactivate',



    // Forms
    'tr_melistoolcreator_tcf-name' => 'Tool name',
    'tr_melistoolcreator_tcf-module-toolstree' => 'Toolstree',
    'tr_melistoolcreator_tcf-module-toolstree tooltip' => 'Toolstree',
    'tr_melistoolcreator_tcf-title' => 'Tool title',
    'tr_melistoolcreator_tcf-title tooltip' => 'Tool title',
    'tr_melistoolcreator_tcf-desc' => 'Tool description',
    'tr_melistoolcreator_tcf-desc tooltip' => 'Tool description',
    'tr_melistoolcreator_inpt_name' => 'Name',
    'tr_melistoolcreator_inpt_name tooltip' => 'Tooltip description',

    // Warning message
    'tr_melistoolcreator_warning_message' => 'For a better experience of this tool we recommend to use a wider screen',//TO REMOVE

    // Error messages
    'tr_melistoolcreator_err_message' => 'Unable to proceed to the next step, please try again',
    'tr_melistoolcreator_err_invalid_module' => 'Alphanumeric and underscore are the only valid characters allowed. The tool name cannot start by a number neither contain any space',
    'tr_melistoolcreator_err_empty' => 'The input is required and cannot be empty',
    'tr_melistoolcreator_err_long_50' => 'Value is too long, it should be less than 50 characters',
    'tr_melistoolcreator_err_long_100' => 'Value is too long, it should be less than 100 characters',
    'tr_melistoolcreator_err_long_250' => 'Value is too long, it should be less than 250 characters',
    'tr_melistoolcreator_err_no_selected_db' => 'Please select a table before proceeding to the next step',
    'tr_melistoolcreator_err_no_primary_key' => 'The selected table has no primary key',
    'tr_melistoolcreator_err_no_selected_col' => 'Please select at least one table column to proceed to the next step',
    'tr_melistoolcreator_err_module_exist' => '"%s" already exists, please try another one',

    // Target Module translation
    'tr_melistoolcreator_common_id' => 'ID',
    'tr_melistoolcreator_common_table_edit_button' => 'Edit',
    'tr_melistoolcreator_common_table_delete_button' => 'Delete',
    'tr_melistoolcreator_common_table_column_action' => 'Action',
    'tr_melistoolcreator_common_button_add' => 'Add',
    'tr_melistoolcreator_common_button_close' => 'Close',
    'tr_melistoolcreator_common_button_save' => 'Save',
    'tr_melistoolcreator_common_button_yes' => 'Yes',
    'tr_melistoolcreator_common_button_no' => 'No',
    'tr_melistoolcreator_unable_to_save' => 'Unable to save',
    'tr_melistoolcreator_save_success' => 'Successfully saved',
    'tr_melistoolcreator_value_must_not_is_empty' => 'The input is required and cannot be empty',
    'tr_melistoolcreator_delete_title' => 'Delete item',
    'tr_melistoolcreator_delete_confirm_msg' => 'Are you sure you want to delete this item?',
    'tr_melistoolcreator_properties' => 'Properties',
    'tr_melistoolcreator_language' => 'Language',
];