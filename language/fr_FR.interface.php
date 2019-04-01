<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'tr_melistoolcreator' => 'Créateur d\'outils',
    'tr_melistoolcreator_description' => 'Le créateur d\'outils génère de nouveaux modules',

    // Interfaces
    'tr_melistoolcreator_header' => 'En-tête du créateur d\'outils',
    'tr_melistoolcreator_content' => 'Contenu du créateur d\'outils',
    'tr_melistoolcreator_steps' => 'Etapes du créateur d\'outils',

    // Buttons
    'tr_melistoolcreator_next' => 'Suivant',
    'tr_melistoolcreator_back' => 'Précédent',
    'tr_melistoolcreator_finish' => 'Terminer et créer l\'outil',

    // Tabs
    'tr_melistoolcreator_db_single_tool' => 'Table unique',
    'tr_melistoolcreator_db_language_tool' => 'Langage de l\'outil',

    // Warnings
    'tr_melistoolcreator_fp_title' => 'Droits d\'écriture des fichiers refusés',
    'tr_melistoolcreator_fp_msg' => 'Pour créer des outils via ce module, veuillez donner les droits d\'écriture sur les dossiers suivants ou contactez l\'administrateur si le problème persiste',
    'tr_melistoolcreator_fp_config' => '<b>/config/melis.module.load.php</b> - Ce fichier est requis pour activer un outil après sa création',
    'tr_melistoolcreator_fp_cache' => '<b>/cache</b> - Le répertoire où le fichier cache de la liste des tables est enregistré, celà accélère le processus d\'installation de l\'outil ',
    'tr_melistoolcreator_fp_module' => '<b>/module</b> - Le répertoire dans lequel les outils créés sont enregistrés',

    // Steps
    'tr_melistoolcreator_module' => 'Outil',
    'tr_melistoolcreator_tcf-name tooltip' => 'Les caractères autorisés sont alphanumerique et underscore. Le nom de l\'outil ne peut pas commencer par un numéro ni ne contenir d\'espace',
    'tr_melistoolcreator_module_desc' => 'Saisissez le nom de l\'outil',
    'tr_melistoolcreator_texts' => 'Textes',
    'tr_melistoolcreator_texts_title' => 'Module de traductions de texte',
    'tr_melistoolcreator_texts_desc' => 'Saisissez les traductions de texte dans les différentes langues, au moins un champ langue doit être saisi',
    'tr_melistoolcreator_database' => 'Base de données',
    'tr_melistoolcreator_database_title' => 'Table du module',
    'tr_melistoolcreator_database_desc' => 'Sélectionnez la table qui sera utilisée par l\'outil',
    'tr_melistoolcreator_database_reload_cached' => 'Rafraîchir la liste des tables',
    'tr_melistoolcreator_database_reload_cached_tooltip' => 'Le rafraîchissement peut prendre quelques minutes',
    'tr_melistoolcreator_table_cols' => 'Colonnes du tableau',
    'tr_melistoolcreator_table_cols_title' => 'Colonnes du tableau',
    'tr_melistoolcreator_table_cols_desc' => 'Veuillez choisir la ou les colonnes à afficher dans le tableau de l\outil',
    'tr_melistoolcreator_add_update_form' => 'Ajouter/mettre à jour le formulaire',
    'tr_melistoolcreator_add_update_form_title' => 'Ajouter/Mettre à jour les champs du formulaire',
    'tr_melistoolcreator_add_update_form_desc' => 'Veuillez sélectionner les colonnes qui seront éditables, obligatoires ainsi que leur type de champ',
    'tr_melistoolcreator_cols_translations' => 'Traductions des colonnes',
    'tr_melistoolcreator_cols_translations_title' => 'Module des traductions de texte',
    'tr_melistoolcreator_cols_translations_desc' => 'Saisissez les traductions de texte dans les différentes langues, au moins un champ langue doit être saisi',
    'tr_melistoolcreator_summary' => 'Récapitulatif',
    'tr_melistoolcreator_finalization' => 'Finalisation',
    'tr_melistoolcreator_finalization_desc' => 'Cochez la case ci-dessous pour activer l\'outil lors de sa création',
    'tr_melistoolcreator_finalization_activate_module' => 'Activer l\'outil après sa création',
    'tr_melistoolcreator_finalization_activation_note' => '<strong>Note :</strong> Activer l\'outil nécéssitera un rechargement de la plateforme',
    'tr_melistoolcreator_finalization_success_title' => 'l\'outil a été créé avec succès',
    'tr_melistoolcreator_finalization_success_desc_with_counter' => 'La plateforme va se recharger dans <strong><span id="tc-restart-cd">5</span></strong>',
    'tr_melistoolcreator_finalization_success_desc' => 'Vous pouvez activer/désactiver l\'outil en changeant son statut depuis la liste des modules dans Configuration système / Modules',

    // Texts
    'tr_melistoolcreator_db_tables' => 'Tables de la base de données',
    'tr_melistoolcreator_db_tables_cols' => 'Colonnes des tables',
    'tr_melistoolcreator_col_pk' => 'PK',
    'tr_melistoolcreator_col_fk' => 'FK',
    'tr_melistoolcreator_col_name' => 'Nom',
    'tr_melistoolcreator_col_type' => 'Type',
    'tr_melistoolcreator_col_null' => 'Null',
    'tr_melistoolcreator_col_default' => 'Défaut',
    'tr_melistoolcreator_col_extra' => 'Extra',
    'tr_melistoolcreator_col_editable' => 'Editable',
    'tr_melistoolcreator_col_mandatory' => 'Obligatoire',
    'tr_melistoolcreator_col_field_type' => 'Type de champ',
    'tr_melistoolcreator_columns' => 'Colonnes',
    'tr_melistoolcreator_columns_desciption' => 'Description des colonnes',
    'tr_melistoolcreator_refreshing' => 'Rafraîchissement...',
    'tr_melistoolcreator_pri_tbl' => 'Table primaire',
    'tr_melistoolcreator_lang_tbl' => 'Table des langues',
    'tr_melistoolcreator_pri_db_tbl' => 'Table primaire',
    'tr_melistoolcreator_lang_db_tbl' => 'Table langue',
    'tr_melistoolcreator_pri_tbl_cols' => 'Colonnes de la table primaire',
    'tr_melistoolcreator_lang_tbl_cols' => 'Colonnes de la table langue',
    'tr_melistoolcreator_pri_tbl_cols_select' => 'Sélectionnez la table primaire',
    'tr_melistoolcreator_int_lang_tab' => 'Intégrer l\'onglet du module langue',
    'tr_melistoolcreator_lang_tbl_lst' => 'Table des langues',
    'tr_melistoolcreator_select_lang_tbl_lst' => 'Sélectionnez la table des langues',
    'tr_melistoolcreator_pk_fk_pri_tbl_lbl' => '<b>PTFK (Primary table foreign key)</b> - La clef relationnelle de la table primaire',
    'tr_melistoolcreator_pk_fk_lang_tbl_lbl' => '<b>LTFK (Language table foreign key)</b> - La clef relationnelle de la table de langues des pages CMS',
    'tr_melistoolcreator_lang_txt' => 'Langue',
    'tr_melistoolcreator_col_txt' => 'Colonnes',
    'tr_melistoolcreator_name_txt' => 'Nom',
    'tr_melistoolcreator_desc_txt' => 'Description',

    // Forms
    'tr_melistoolcreator_tcf-name' => 'Nom de l\'outil',
    'tr_melistoolcreator_tcf-module-toolstree' => 'Arbre des outils',
    'tr_melistoolcreator_tcf-module-toolstree tooltip' => 'Arbre des outils',
    'tr_melistoolcreator_tcf-title' => 'Titre de l\'outil',
    'tr_melistoolcreator_tcf-title tooltip' => 'Titre de l\'outil',
    'tr_melistoolcreator_tcf-desc' => 'Description de l\'outil',
    'tr_melistoolcreator_tcf-desc tooltip' => 'Description de l\'outil',
    'tr_melistoolcreator_inpt_name' => 'Nom',
    'tr_melistoolcreator_inpt_name tooltip' => 'Description tooltip',

    // Warning message
    'tr_melistoolcreator_warning_message' => 'For a better experience of this tool we recommend to use a wider screen',//TO REMOVE

    // Error messages
    'tr_melistoolcreator_err_message' => 'Impossible de passer à l\'étape suivante, veuillez réessayer',
    'tr_melistoolcreator_err_invalid_module' => 'Les caractères autorisés sont alphanumerique et underscore. Le nom de l\'outil ne peut pas commencer par un numéro',
    'tr_melistoolcreator_err_empty' => 'Champ requis, ne peut être vide',
    'tr_melistoolcreator_err_long_50' => 'Valeur trop longue, elle doit être de moins de 50 caractères',
    'tr_melistoolcreator_err_long_100' => 'Valeur trop longue, elle doit être de moins de 100 caractères',
    'tr_melistoolcreator_err_long_250' => 'Valeur trop longue, elle doit être de moins de 250 caractères',
    'tr_melistoolcreator_err_no_selected_db' => 'Veuillez choisir une table avant de procéder à l\'étape suivante',
    'tr_melistoolcreator_err_no_primary_key' => 'La table sélectionnée n\'a pas de clef primaire',
    'tr_melistoolcreator_err_no_selected_col' => 'Veuillez choisir au moins une colonne de table pour procéder à l\'étape suivante',
    'tr_melistoolcreator_err_module_exist' => '"%s" existe déjà, veuillez en choisir un autre',

    // Target Module translation
    'tr_melistoolcreator_common_id' => 'ID',
    'tr_melistoolcreator_common_table_edit_button' => 'Editer',
    'tr_melistoolcreator_common_table_delete_button' => 'Supprimer',
    'tr_melistoolcreator_common_table_column_action' => 'Action',
    'tr_melistoolcreator_common_button_add' => 'Ajouter',
    'tr_melistoolcreator_common_button_close' => 'Fermer',
    'tr_melistoolcreator_common_button_save' => 'Enregistrer',
    'tr_melistoolcreator_common_button_yes' => 'Oui',
    'tr_melistoolcreator_common_button_no' => 'Non',
    'tr_melistoolcreator_unable_to_save' => 'Impossible d\'enregistrer',
    'tr_melistoolcreator_save_success' => 'Enregistré avec succès',
    'tr_melistoolcreator_value_must_not_is_empty' => 'Champ requis, ne peut être vide',
    'tr_melistoolcreator_delete_title' => 'Supprimer l\'élément',
    'tr_melistoolcreator_delete_confirm_msg' => 'Etes-vous sûr de vouloir supprimer cet élément ?',
    'tr_melistoolcreator_properties' => 'Propriétés',
    'tr_melistoolcreator_entry_details' => '',
];