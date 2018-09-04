<?php
return array(
    'tr_melistoolcreator' => 'Tool creator',
    'tr_melistoolcreator_description' => 'Tool creator generate a new working tool',

    // Interfaces
    'tr_melistoolcreator_header' => 'Tool creator header',
    'tr_melistoolcreator_content' => 'Tool creator content',
    'tr_melistoolcreator_steps' => 'Tool creator steps',

    // Buttons
    'tr_melistoolcreator_next' => 'Next',
    'tr_melistoolcreator_back' => 'Back',
    'tr_melistoolcreator_finish' => 'Finish and create the tool',

    // Warnings
    'tr_melistoolcreator_fp_title' => 'File permission denied',
    'tr_melistoolcreator_fp_msg' => 'In-order to create tool(s) using this tool, please grant this to write to the following file directory below or contact the administrator if the problem persists.',
    'tr_melistoolcreator_fp_config' => '<b>/config/melis.module.load.php</b> - this file use to activate the tool(s) after creation',
    'tr_melistoolcreator_fp_cache' => '<b>/cache</b> - the directory where the database tables list cache file stored, this procedure will avoid slow process during setup of the tool',
    'tr_melistoolcreator_fp_module' => '<b>/module</b> - the directory where the new tool(s) created are stored',

    // Steps
    'tr_melistoolcreator_module' => 'Tool',
    'tr_melistoolcreator_tcf-name tooltip' => 'Alphanumeric and underscore are the only valid characters allowed and can\'t start with numeric for naming a tool name',
    'tr_melistoolcreator_module_desc' => 'Enter the name of the tool you want to create',
    'tr_melistoolcreator_texts' => 'Texts',
    'tr_melistoolcreator_texts_title' => 'Module text translations',
    'tr_melistoolcreator_texts_desc' => 'Enter the text translations in different language, atleast one language fieldset should be filled up',
    'tr_melistoolcreator_database' => 'Database',
    'tr_melistoolcreator_database_title' => 'Module database table',
    'tr_melistoolcreator_database_desc' => 'Select a database table that will use by the generate module',
    'tr_melistoolcreator_database_reload_cached' => 'Refresh database tables list',
    'tr_melistoolcreator_database_reload_cached_tooltip' => 'Refreshing database tables list might take a few minutes',
    'tr_melistoolcreator_table_cols' => 'Table columns',
    'tr_melistoolcreator_table_cols_title' => 'Database table columns',
    'tr_melistoolcreator_table_cols_desc' => 'Please select column(s) to be displayed on the generated tool table list',
    'tr_melistoolcreator_add_update_form' => 'Add/Update form',
    'tr_melistoolcreator_add_update_form_title' => 'Add/Update form fields',
    'tr_melistoolcreator_add_update_form_desc' => 'Please select column(s) that will be editable, mandatory and its type of field',
    'tr_melistoolcreator_cols_translations' => 'Columns translations',
    'tr_melistoolcreator_cols_translations_title' => 'Module text translations',
    'tr_melistoolcreator_cols_translations_desc' => 'Enter the text translations in different language, atleast one language fieldset should be filled up',
    'tr_melistoolcreator_summary' => 'Summary',
    'tr_melistoolcreator_finalization' => 'Finalization',
    'tr_melistoolcreator_finalization_desc' => 'Before proceeding of creation of the new tool here is another option of the activation of the tool',
    'tr_melistoolcreator_finalization_activate_module' => 'Activate tool after creation',
    'tr_melistoolcreator_finalization_activation_note' => '<strong>Note:</strong> If you choose to activate the tool this will required a restart of the of the platform',
    'tr_melistoolcreator_finalization_success_title' => 'Module has been successfully created',
    'tr_melistoolcreator_finalization_success_desc_with_counter' => 'The platform will refresh in <strong><span id="tc-restart-cd">5</span></strong>',
    'tr_melistoolcreator_finalization_success_desc' => 'You can now manually activate the tool by changing the status of the tool form the list of the Modules in System configuration / Modules',

    // Texts
    'tr_melistoolcreator_db_tables' => 'Database tables',
    'tr_melistoolcreator_db_tables_cols' => 'Tables columns',
    'tr_melistoolcreator_col_pk' => 'PK',
    'tr_melistoolcreator_col_name' => 'Name',
    'tr_melistoolcreator_col_type' => 'Type',
    'tr_melistoolcreator_col_null' => 'Null',
    'tr_melistoolcreator_col_default' => 'Default',
    'tr_melistoolcreator_col_extra' => 'Extra',
    'tr_melistoolcreator_col_editable' => 'Editable',
    'tr_melistoolcreator_col_mandatory' => 'Mandatory',
    'tr_melistoolcreator_col_field_type' => 'Field Type',
    'tr_melistoolcreator_columns' => 'Columns',
    'tr_melistoolcreator_columns_desciption' => 'Columns description',
    'tr_melistoolcreator_refreshing' => 'Resfreshing....',

    // Forms
    'tr_melistoolcreator_tcf-name' => 'Tool name',
    'tr_melistoolcreator_tcf-module-toolstree' => 'Toolstree',
    'tr_melistoolcreator_tcf-module-toolstree tooltip' => 'Toolstree',
    'tr_melistoolcreator_tcf-title' => 'Tool title',
    'tr_melistoolcreator_tcf-title tooltip' => 'Tool title',
    'tr_melistoolcreator_tcf-desc' => 'Tool description',
    'tr_melistoolcreator_tcf-desc tooltip' => 'Tool description',

    // Warning message
    'tr_melistoolcreator_warning_message' => 'For a better experience of this tool please use desktop',

    // Error messages
    'tr_melistoolcreator_err_message' => 'Unable to proceed to the next step, please try again',
    'tr_melistoolcreator_err_invalid_module' => 'Alphanumeric and underscore are the only valid characters allowed and can\'t start with numeric for naming a tool name.',
    'tr_melistoolcreator_err_empty' => 'The input is required and can\'t be empty.',
    'tr_melistoolcreator_err_long_50' => 'Value is too long, it should be less than 50 characters.',
    'tr_melistoolcreator_err_long_100' => 'Value is too long, it should be less than 100 characters.',
    'tr_melistoolcreator_err_long_250' => 'Value is too long, it should be less than 250 characters.',
    'tr_melistoolcreator_err_no_selected_db' => 'Please select a database table before proceed to the next step.',
    'tr_melistoolcreator_err_no_selected_col' => 'Please select atleast one database table column(s) proceed to the next step.',
    'tr_melistoolcreator_err_module_exist' => '"%s" is already exist, please try another one.',

    // Target Module translation
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
    'tr_melistoolcreator_value_must_not_is_empty' => 'The input is required and can\'t be empty',
    'tr_melistoolcreator_delete_title' => 'Delete item',
    'tr_melistoolcreator_delete_confirm_msg' => 'Are you sure you want to delete this item?',
);