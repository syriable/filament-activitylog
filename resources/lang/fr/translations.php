<?php

return [
    'data' => [
        'timeline-configuration-data' => [
            'empty-state-heading' => 'Pas d\'activités pour l\'instant',
            'empty-state-description' => 'Cette ressource :modelLabel n\'a encore enregistré aucune activité.',
        ],
    ],
    'components' => [
        'timeline' => [
            'search' => [
                'placeholder' => 'Tapez pour rechercher',
            ],
            'collapsible' => [
                'show_more' => '{1} Afficher :count activité de plus|[2,*] Afficher :count activités de plus',
                'show_less' => 'Afficher moins',
            ],
        ],
    ],
    'actions' => [
        'timeline-action' => [
            'label' => 'Activités',
            'modal_cancel_action_label' => 'Fermer',
        ],
    ],
    'activity-timeline-item' => [
        'event-descriptions' => [
            'created' => [
                'causer' => '**:causerName** a créé la ressource :modelLabel.',
                'causer-relationship' => '**:causerName** a ajouté une ressource liée :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** a ajouté la ressource liée :relationshipName **:relatedRecordTitle**.',
                'without-causer' => 'La ressource :modelLabel a été créée.',
                'without-causer-relationship' => 'Une ressource liée :relationshipName a été créée.',
                'without-causer-relationship-related-record-title' => 'La ressource liée :relationshipName **:relatedRecordTitle** a été créée.',
            ],
            'updated' => [
                'causer-changes-summary' => '**:causerName** a mis à jour :changesSummary.',
                'causer-changes-summary-relationship' => '**:causerName** a mis à jour la ressource liée :relationshipName :changesSummary.',
                'causer-changes-summary-relationship-related-record-title' => '**:causerName** a mis à jour la ressource liée :relationshipName **:relatedRecordTitle** :changesSummary.',
                'causer-without-changes-summary' => '**:causerName** a mis à jour la ressource :modelLabel.',
                'causer-without-changes-summary-relationship' => '**:causerName** a mis à jour la ressource liée :relationshipName.',
                'causer-without-changes-summary-relationship-related-record-title' => '**:causerName** a mis à jour la ressource liée :relationshipName **:relatedRecordTitle**.',
                'without-causer-changes-summary' => 'La ressource :modelLabel a été mise à jour avec :changesSummary.',
                'without-causer-changes-summary-relationship' => 'Une ressource liée :relationshipName a été mise à jour avec :changesSummary.',
                'without-causer-changes-summary-relationship-related-record-title' => 'La ressource liée :relationshipName **:relatedRecordTitle** a été mise à jour avec :changesSummary.',
                'without-causer-without-changes-summary' => 'La ressource :modelLabel a été mise à jour.',
                'without-causer-without-changes-summary-relationship' => 'Une ressource liée :relationshipName a été mise à jour.',
                'without-causer-without-changes-summary-relationship-related-record-title' => 'La ressource liée :relationshipName **:relatedRecordTitle** a été mise à jour.',
            ],
            'deleted' => [
                'causer' => '**:causerName** a supprimé la ressource :modelLabel.',
                'causer-relationship' => '**:causerName** a supprimé une ressource liée :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** a supprimé la ressource liée :relationshipName **:relatedRecordTitle**.',
                'without-causer' => 'La ressource :modelLabel a été supprimée.',
                'without-causer-relationship' => 'Une ressource liée :relationshipName a été supprimée.',
                'without-causer-relationship-related-record-title' => 'La ressource liée :relationshipName **:relatedRecordTitle** a été supprimée.',
            ],
            'restored' => [
                'causer' => '**:causerName** a restauré la ressource :modelLabel.',
                'causer-relationship' => '**:causerName** a restauré une ressource liée :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** a restauré la ressource liée :relationshipName **:relatedRecordTitle**.',
                'without-causer' => 'La ressource :modelLabel a été restaurée.',
                'without-causer-relationship' => 'Une ressource liée :relationshipName a été restaurée.',
                'without-causer-relationship-related-record-title' => 'La ressource liée :relationshipName **:relatedRecordTitle** a été restaurée.',
            ],
            'custom' => [
                'causer' => '**:causerName** :event la ressource :modelLabel.',
                'causer-relationship' => '**:causerName** :event une ressource liée :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** :event la ressource liée :relationshipName **:relatedRecordTitle**.',
                'without-causer' => 'La ressource :modelLabel a été :event.',
                'without-causer-relationship' => 'Une ressource liée :relationshipName a été :event.',
                'without-causer-relationship-related-record-title' => 'La ressource liée :relationshipName **:relatedRecordTitle** a été :event.',
            ],
            'no-subject' => [
                'causer' => '**:causerName** :event.',
                'causer-relationship' => '**:causerName** :event la ressource liée :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** :event la ressource liée :relationshipName **:relatedRecordTitle**.',
                'without-causer' => ':event.',
                'without-causer-relationship' => 'Ressource liée :relationshipName :event.',
                'without-causer-relationship-related-record-title' => 'Ressource liée :relationshipName **:relatedRecordTitle** :event.',
            ],
            'changesSummary' => [
                'attribute' => '**:attributeLabel** à **:newAttributeValue**',
                'attributeWithOld' => '**:attributeLabel** de :oldAttributeValue à **:newAttributeValue**',
                'attributeFromBlankWithOld' => '**:attributeLabel** d\'un champ vide à **:newAttributeValue**',
                'attributeFromBlankToBlankWithOld' => '**:attributeLabel** d\'un champ vide à un champ vide',
                'attributeToBlank' => '**:attributeLabel** à un champ vide',
                'attributeToBlankWithOld' => '**:attributeLabel** de :oldAttributeValue à un champ vide',
                'finalGlue' => ' et ',
                'values' => [
                    'boolean-1' => 'vrai',
                    'boolean-0' => 'faux',
                ],
            ],
        ],
        'actions' => [
            'view_batch' => [
                'label' => 'Historique lié',
                'modal_heading' => 'Voir l\'historique lié',
                'modal_description' => 'Cet événement fait partie d\'un lot d\'événements. Ci-dessous, vous pouvez voir quels autres événements ont fait partie de ce lot, y compris l\'événement que vous avez sélectionné.',
            ],
        ],
    ],
];
