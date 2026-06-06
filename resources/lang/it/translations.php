<?php

return [
    'data' => [
        'timeline-configuration-data' => [
            'empty-state-heading' => 'Nessuna attività',
            'empty-state-description' => 'Attualmente :modelLabel non ha registrato alcuna attività.',
        ],
    ],
    'components' => [
        'timeline' => [
            'search' => [
                'placeholder' => 'Digita per cercare',
            ],
            'collapsible' => [
                'show_more' => '{1} Mostra :count altra attività|[2,*] Mostra altre :count attività',
                'show_less' => 'Mostra meno',
            ],
        ],
    ],
    'actions' => [
        'timeline-action' => [
            'label' => 'Attività',
            'modal_cancel_action_label' => 'Chiudi',
        ],
    ],
    'activity-timeline-item' => [
        'event-descriptions' => [
            'created' => [
                'causer' => '**:causerName** ha creato la risorsa :modelLabel.',
                'causer-relationship' => '**:causerName** ha aggiunto una relazione :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** ha aggiunto la relazione :relationshipName **:relatedRecordTitle**.',
                'without-causer' => 'La risorsa :modelLabel è stata creata.',
                'without-causer-relationship' => 'È stata creata una relazione :relationshipName.',
                'without-causer-relationship-related-record-title' => 'È stata creata la relazione :relationshipName **:relatedRecordTitle**.',
            ],
            'updated' => [
                'causer-changes-summary' => '**:causerName** ha aggiornato :changesSummary.',
                'causer-changes-summary-relationship' => '**:causerName** ha aggiornato la relazione :relationshipName :changesSummary.',
                'causer-changes-summary-relationship-related-record-title' => '**:causerName** ha aggiornato la relazione :relationshipName **:relatedRecordTitle** :changesSummary.',
                'causer-without-changes-summary' => '**:causerName** ha aggiornato la risorsa :modelLabel.',
                'causer-without-changes-summary-relationship' => '**:causerName** ha aggiornato la relazione :relationshipName.',
                'causer-without-changes-summary-relationship-related-record-title' => '**:causerName** ha aggiornato la relazione :relationshipName **:relatedRecordTitle**.',
                'without-causer-changes-summary' => 'La risorsa :modelLabel è stata aggiornata impostando :changesSummary.',
                'without-causer-changes-summary-relationship' => 'È stata aggiornata una relazione :relationshipName impostando :changesSummary.',
                'without-causer-changes-summary-relationship-related-record-title' => 'La relazione :relationshipName **:relatedRecordTitle** è stata aggiornata impostando :changesSummary.',
                'without-causer-without-changes-summary' => 'La risorsa :modelLabel è stata aggiornata.',
                'without-causer-without-changes-summary-relationship' => 'È stata aggiornata una relazione :relationshipName.',
                'without-causer-without-changes-summary-relationship-related-record-title' => 'La relazione :relationshipName **:relatedRecordTitle** è stata aggiornata.',
            ],
            'deleted' => [
                'causer' => '**:causerName** ha eliminato la risorsa :modelLabel.',
                'causer-relationship' => '**:causerName** ha eliminato una relazione :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** ha eliminato la relazione :relationshipName **:relatedRecordTitle**.',
                'without-causer' => 'La risorsa :modelLabel è stata eliminata.',
                'without-causer-relationship' => 'È stata eliminata una relazione :relationshipName.',
                'without-causer-relationship-related-record-title' => 'È stata eliminata la relazione :relationshipName **:relatedRecordTitle**.',
            ],
            'restored' => [
                'causer' => '**:causerName** ha ripristinato la risorsa :modelLabel.',
                'causer-relationship' => '**:causerName** ha ripristinato una relazione :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** ha ripristinato la relazione :relationshipName **:relatedRecordTitle**.',
                'without-causer' => 'La risorsa :modelLabel è stata ripristinata.',
                'without-causer-relationship' => 'È stata ripristinata una relazione :relationshipName.',
                'without-causer-relationship-related-record-title' => 'È stata ripristinata la relazione :relationshipName **:relatedRecordTitle**.',
            ],
            'custom' => [
                'causer' => '**:causerName** ha :event la risorsa :modelLabel.',
                'causer-relationship' => '**:causerName** ha :event una relazione :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** ha :event la relazione :relationshipName **:relatedRecordTitle**.',
                'without-causer' => 'La risorsa :modelLabel è stata :event.',
                'without-causer-relationship' => 'Una relazione :relationshipName è stata :event.',
                'without-causer-relationship-related-record-title' => 'La relazione :relationshipName **:relatedRecordTitle** è stata :event.',
            ],
            'no-subject' => [
                'causer' => '**:causerName** ha :event.',
                'causer-relationship' => '**:causerName** ha :event la relazione :relationshipName.',
                'causer-relationship-related-record-title' => '**:causerName** ha :event la relazione :relationshipName **:relatedRecordTitle**.',
                'without-causer' => ':event.',
                'without-causer-relationship' => 'Relazione :relationshipName :event.',
                'without-causer-relationship-related-record-title' => 'Relazione :relationshipName **:relatedRecordTitle** :event.',
            ],
            'changesSummary' => [
                'attribute' => '**:attributeLabel** a **:newAttributeValue**',
                'attributeWithOld' => '**:attributeLabel** da :oldAttributeValue a **:newAttributeValue**',
                'attributeFromBlankWithOld' => '**:attributeLabel** da un campo vuoto a **:newAttributeValue**',
                'attributeFromBlankToBlankWithOld' => '**:attributeLabel** da un campo vuoto a un altro campo vuoto',
                'attributeToBlank' => '**:attributeLabel** a un campo vuoto',
                'attributeToBlankWithOld' => '**:attributeLabel** da :oldAttributeValue a un campo vuoto',
                'finalGlue' => ' e ',
                'values' => [
                    'boolean-1' => 'vero',
                    'boolean-0' => 'falso',
                ],
            ],
        ],
        'actions' => [
            'view_batch' => [
                'label' => 'Cronologia correlata',
                'modal_heading' => 'Visualizza cronologia correlata',
                'modal_description' => 'Questo evento fa parte di un gruppo di eventi. Di seguito puoi vedere quali altri eventi fanno parte di questo gruppo, incluso quello selezionato.',
            ],
        ],
    ],
];
