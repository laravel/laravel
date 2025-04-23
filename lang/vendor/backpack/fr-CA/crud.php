<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    // Forms
    'save_action_save_and_new' => 'Enregistrer et créer un nouveau',
    'save_action_save_and_edit' => 'Enregistrer et éditer',
    'save_action_save_and_back' => 'Enregistrer et retour',
    'save_action_changed_notification' => 'Action par défaut changée',

    // Create form
    'add' => 'Ajouter',
    'back_to_all' => 'Retour à la liste ',
    'cancel' => 'Annuler',
    'add_a_new' => 'Ajouter un nouvel élément ',

    // Edit form
    'edit' => 'Modifier',
    'save' => 'Enregistrer',

    // Translatable models
    'edit_translations' => 'EDITER LES TRADUCTIONS',
    'language' => 'Langue',

    // CRUD table view
    'all' => 'Tous les ',
    'in_the_database' => 'dans la base de données',
    'list' => 'Liste',
    'actions' => 'Actions',
    'preview' => 'Aperçu',
    'delete' => 'Supprimer',
    'admin' => 'Administration',
    'details_row' => 'Ligne de détail. Modifiez la à volonté.',
    'details_row_loading_error' => 'Une erreur est survenue en chargeant les détails. Veuillez réessayer.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Souhaitez-vous réellement supprimer cet élément?',
    'delete_confirmation_title' => 'Élément supprimé',
    'delete_confirmation_message' => 'L’élément a été supprimé avec succès.',
    'delete_confirmation_not_title' => 'NON supprimé',
    'delete_confirmation_not_message' => 'Une erreur est survenue. Votre élément n’a peut-être pas été effacé.',
    'delete_confirmation_not_deleted_title' => 'Non supprimé',
    'delete_confirmation_not_deleted_message' => 'Aucune modification. Votre élément a été conservé.',

    'ajax_error_title' => 'Erreur',
    'ajax_error_text' => 'Erreur lors du chargement. Merci de réactualiser la page.',

    // DataTables translation
    'emptyTable' => 'Aucune donnée à afficher.',
    'info' => 'Affichage des éléments _START_ à _END_ sur _TOTAL_',
    'infoEmpty' => '',
    'infoFiltered' => '(filtré à partir de _MAX_ éléments au total)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ enregistrements par page',
    'loadingRecords' => 'Chargement...',
    'processing' => 'Traitement...',
    'search' => 'Recherche',
    'zeroRecords' => 'Aucun enregistrement correspondant trouvé',
    'paginate' => [
        'first' => 'Premier',
        'last' => 'Dernier',
        'next' => 'Suivant',
        'previous' => 'Précédent',
    ],
    'aria' => [
        'sortAscending' => ': activez pour trier la colonne par ordre croissant',
        'sortDescending' => ': activez pour trier la colonne par ordre décroissant',
    ],
    'export' => [
        'export' => 'Exporter',
        'copy' => 'Copier',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Imprimer',
        'column_visibility' => 'Affichage des colonnes',
    ],

    // global crud - errors
    'unauthorized_access' => 'Accès non autorisé - vous n’avez pas les droits nécessaires à la consultation de cette page.',
    'please_fix' => 'Veuillez corriger les erreurs suivantes :',

    // global crud - success / error notification bubbles
    'insert_success' => 'L’élément a été ajouté avec succès.',
    'update_success' => 'L’élément a été modifié avec succès.',

    // CRUD reorder view
    'reorder' => 'Réordonner',
    'reorder_text' => 'Utilisez le glisser-déposer pour réordonner.',
    'reorder_success_title' => 'Fait',
    'reorder_success_message' => 'L’ordre a été enregistré.',
    'reorder_error_title' => 'Erreur',
    'reorder_error_message' => 'L’ordre n’a pas pu être enregistré.',

    // CRUD yes/no
    'yes' => 'Oui',
    'no' => 'Non',

    // CRUD filters navbar view
    'filters' => 'Filtres',
    'toggle_filters' => 'Activer les filtres',
    'remove_filters' => 'Retirer les filtres',

    //filters language strings
    'today' => 'Aujourd\'hui',
    'yesterday' => 'Hier',
    'last_7_days' => '7 derniers jours',
    'last_30_days' => '30 derniers jours',
    'this_month' => 'Ce mois-ci',
    'last_month' => 'Le mois dernier',
    'custom_range' => 'Durée personnalisé',
    'weekLabel' => 'S',

    // Fields
    'browse_uploads' => 'Parcourir les fichier chargés',
    'clear' => 'Effacer',
    'page_link' => 'Lien de la page',
    'page_link_placeholder' => 'http://example.com/votre-page',
    'internal_link' => 'Lien interne',
    'internal_link_placeholder' => 'Identifiant de lien interne. Ex: \'admin/page\' (sans guillemets) pour \':url\'',
    'external_link' => 'Lien externe',
    'choose_file' => 'Choisissez un fichier',

    //Table field
    'table_cant_add' => 'Impossible d’ajouter un nouveau :entity',
    'table_max_reached' => 'Nombre maximum :max atteint',

    // File manager
    'file_manager' => 'Gestionnaire de fichiers',
];
