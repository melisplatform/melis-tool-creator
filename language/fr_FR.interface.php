<?php
return array(
    'tr_melistoolcreator' => 'Créateur de l\'outil',
    'tr_melistoolcreator_description' => 'Le créateur d\'outils génère un nouvel outil de travail',

    // Interfaces
    'tr_melistoolcreator_header' => 'En-tête de créateur d\'outils',
    'tr_melistoolcreator_content' => 'Contenu du créateur d\'outil',
    'tr_melistoolcreator_steps' => 'Étapes du créateur de l\'outil',

    // Buttons
    'tr_melistoolcreator_next' => 'Suivant',
    'tr_melistoolcreator_back' => 'Retour',
    'tr_melistoolcreator_finish' => 'Terminer et créer l\'outil',

    // Warnings
    'tr_melistoolcreator_fp_title' => 'Autorisation de fichier refusée',
    'tr_melistoolcreator_fp_msg' => 'Afin de créer des outils à l\'aide de cet outil, veuillez l\'autoriser pour écrire dans le répertoire de fichiers ci-dessous ou contactez l\'administrateur si le problème persiste.',
    'tr_melistoolcreator_fp_config' => '<b>/config/melis.module.load.php</b> - ce fichier permet d’activer le ou les outils après la création',
    'tr_melistoolcreator_fp_cache' => '<b>/cache</b> - le répertoire où la liste des tables de la base de données stocke le fichier cache, cette procédure permet d’éviter un processus lent pendant la configuration de l’outil',
    'tr_melistoolcreator_fp_module' => '<b>/module</b> - le répertoire où sont stockés les nouveaux outils créés',
    
    // Steps
    'tr_melistoolcreator_module' => 'Outil',
    'tr_melistoolcreator_tcf-name tooltip' => 'Les caractères alphanumériques seuls caractères valides autorisés et ne peuvent pas commencer par un nom numérique pour nommer un nom d\'outil',
    'tr_melistoolcreator_module_desc' => 'Entrez le nom de l\'outil que vous voulez créer',
    'tr_melistoolcreator_texts' => 'Textes',
    'tr_melistoolcreator_texts_title' => 'Traductions du texte du module',
    'tr_melistoolcreator_texts_desc' => 'Entrer les traductions du texte dans différentes langues, au moins une langue doit être remplie',
    'tr_melistoolcreator_database' => 'Database',
    'tr_melistoolcreator_database_title' => 'Table de base de données du module',
    'tr_melistoolcreator_database_desc' => 'Sélectionnez une table de base de données qui utilisera le module generate',
    'tr_melistoolcreator_database_reload_cached' => 'Actualiser la liste des tables de base de donnée',
    'tr_melistoolcreator_database_reload_cached_tooltip' => 'Actualiser la liste des tables de base de données peut prendre quelques minutes',
    'tr_melistoolcreator_table_cols' => 'Colonnes de table',
    'tr_melistoolcreator_table_cols_title' => 'Colonnes de la table de base de données',
    'tr_melistoolcreator_table_cols_desc' => 'Veuillez sélectionner la ou les colonnes à afficher dans la liste des tables d\'outils générées',
    'tr_melistoolcreator_add_update_form' => 'Ajouter / Mettre à jour le formulaire',
    'tr_melistoolcreator_add_update_form_title' => 'Ajouter / Mettre à jour les champs du formulaire',
    'tr_melistoolcreator_add_update_form_desc' => 'Veuillez sélectionner la ou les colonnes qui seront modifiables, obligatoires et leur type de champ',
    'tr_melistoolcreator_cols_translations' => 'Traductions de colonnes',
    'tr_melistoolcreator_cols_translations_title' => 'Traductions du texte du module',
    'tr_melistoolcreator_cols_translations_desc' => 'Entrez les traductions du texte dans différentes langues, au moins une langue doit être remplie',
    'tr_melistoolcreator_summary' => 'Résumé',
    'tr_melistoolcreator_finalization' => 'Finalisation',
    'tr_melistoolcreator_finalization_desc' => 'Avant de procéder à la création du nouvel outil, voici une autre option d\'activation de l\'outil',
    'tr_melistoolcreator_finalization_activate_module' => 'Activer l\'outil après la création',
    'tr_melistoolcreator_finalization_activation_note' => '<strong> Remarque: </strong> Si vous choisissez d\'activer l\'outil, cela nécessitera un redémarrage de la plate-forme',
    'tr_melistoolcreator_finalization_success_title' => 'Le module a été créé avec succès',
    'tr_melistoolcreator_finalization_success_desc_with_counter' => 'La plate-forme sera actualisée dans <strong> <span id = "tc-restart-cd"> 5 </span> </strong>',
    'tr_melistoolcreator_finalization_success_desc' => 'Vous pouvez maintenant activer manuellement l\'outil en modifiant l\'état de l\'outil de la liste des outil dans configuration système / Modules',

    // Texts
    'tr_melistoolcreator_db_tables' => 'Tables de base de données',
    'tr_melistoolcreator_db_tables_cols' => 'Colonnes de tables',
    'tr_melistoolcreator_col_pk' => 'PK',
    'tr_melistoolcreator_col_name' => 'Nom',
    'tr_melistoolcreator_col_type' => 'Type',
    'tr_melistoolcreator_col_null' => 'Null',
    'tr_melistoolcreator_col_default' => 'Default',
    'tr_melistoolcreator_col_extra' => 'Extra',
    'tr_melistoolcreator_col_editable' => 'Modifiable',
    'tr_melistoolcreator_col_mandatory' => 'Obligatoire',
    'tr_melistoolcreator_col_field_type' => 'Type de champ',
    'tr_melistoolcreator_columns' => 'Colonnes',
    'tr_melistoolcreator_columns_desciption' => 'Description des colonnes',
    'tr_melistoolcreator_refreshing' => 'Rafraîchissement....',

    // Forms
    'tr_melistoolcreator_tcf-name' => 'Nom de l\'outil',
    'tr_melistoolcreator_tcf-module-toolstree' => 'Outil de l\'arbre',
    'tr_melistoolcreator_tcf-module-toolstree tooltip' => 'Outil de l\'arbre',
    'tr_melistoolcreator_tcf-title' => 'Titre de l\'outil',
    'tr_melistoolcreator_tcf-title tooltip' => 'Titre de l\'outil',
    'tr_melistoolcreator_tcf-desc' => 'Description de l\'outil',
    'tr_melistoolcreator_tcf-desc tooltip' => 'Description de l\'outil',

    // Warning message
    'tr_melistoolcreator_warning_message' => 'pour une meilleure expérience de cet outil, veuillez utiliser un ordinateur de bureau',

    // Error messages
    'tr_melistoolcreator_err_message' => 'Impossible de passer à l\'étape suivante, veuillez réessayer',
    'tr_melistoolcreator_err_invalid_module' => 'Alphanumerique les seuls caractères valides autorisés et ne peuvent pas commencer par un nom numérique pour un nom d\'outil.',
    'tr_melistoolcreator_err_empty' => 'L\'entrée est obligatoire et ne peut pas être vide.',
    'tr_melistoolcreator_err_long_50' => 'La valeur est trop longue, elle devrait être inférieure à 50 caractères.',
    'tr_melistoolcreator_err_long_100' => 'La valeur est trop longue, elle devrait être inférieure à 100 caractères.',
    'tr_melistoolcreator_err_long_250' => 'La valeur est trop longue, elle devrait être inférieure à 250 caractères.',
    'tr_melistoolcreator_err_no_selected_db' => 'Veuillez sélectionner une table de base de données avant de passer à l\'étape suivante.',
    'tr_melistoolcreator_err_no_selected_col' => 'Veuillez sélectionner au moins une colonne de table de base de données pour passer à l\'étape suivante.',
    'tr_melistoolcreator_err_module_exist' => '"% s" existe déjà, veuillez en essayer un autre.',

    // Target Module translation
    'tr_melistoolcreator_common_table_edit_button' => 'Modifier',
    'tr_melistoolcreator_common_table_delete_button' => 'Supprimer',
    'tr_melistoolcreator_common_table_column_action' => 'Action',
    'tr_melistoolcreator_common_button_add' => 'Ajouter',
    'tr_melistoolcreator_common_button_close' => 'Fermer',
    'tr_melistoolcreator_common_button_save' => 'Enregistrer',
    'tr_melistoolcreator_common_button_yes' => 'Oui',
    'tr_melistoolcreator_common_button_no' => 'Non',
    'tr_melistoolcreator_unable_to_save' => 'Impossible de sauvegarder',
    'tr_melistoolcreator_save_success' => 'Enregistré avec succès',
    'tr_melistoolcreator_value_must_not_is_empty' => 'L\'entrée est obligatoire et ne peut pas être vide',
    'tr_melistoolcreator_delete_title' => 'Supprimer l\'élément',
    'tr_melistoolcreator_delete_confirm_msg' => 'Êtes-vous sûr de vouloir supprimer cet élément?',
);