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
            'modal_cancel_action_label' => 'Fermer',
        ],
    ],
    'activity-timeline-item' => [
        'event-descriptions' => [
            'created' => [
                'causer' => '**:causerName** a créé la ressource :modelLabel.',
                'without-causer' => 'La ressource :modelLabel a été créée.',
            ],
            'updated' => [
                'causer-changes-summary' => '**:causerName** a mis à jour :changesSummary.',
                'causer-without-changes-summary' => '**:causerName** a mis à jour la ressource :modelLabel.',
                'without-causer-changes-summary' => 'La ressource :modelLabel a été mise à jour avec :changesSummary.',
                'without-causer-without-changes-summary' => 'La ressource :modelLabel a été mise à jour.',
            ],
            'deleted' => [
                'causer' => '**:causerName** a supprimé la ressource :modelLabel.',
                'without-causer' => 'La ressource :modelLabel a été supprimée.',
            ],
            'restored' => [
                'causer' => '**:causerName** a restauré la ressource :modelLabel.',
                'without-causer' => 'La ressource :modelLabel a été restaurée.',
            ],
            'custom' => [
                'causer' => '**:causerName** :event la ressource :modelLabel.',
                'without-causer' => 'La ressource :modelLabel a été :event.',
            ],
            'changesSummary' => [
                'attribute' => '**:attributeLabel** à **:newAttributeValue**',
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
