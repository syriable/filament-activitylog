<?php

return [
    'data' => [
        'timeline-configuration-data' => [
            'empty-state-heading' => 'Nog geen activiteiten',
            'empty-state-description' => 'Deze :modelLabel heeft momenteel nog geen activiteiten geregistreerd.',
        ],
    ],
    'components' => [
        'timeline' => [
            'search' => [
                'placeholder' => 'Typ om te zoeken',
            ],
            'collapsible' => [
                'show_more' => '{1} Toon :count extra activiteit|[2,*] Toon :count extra activiteiten',
                'show_less' => 'Toon minder',
            ],
        ],
    ],
    'actions' => [
        'timeline-action' => [
            'label' => 'Geschiedenis',
            'modal_cancel_action_label' => 'Sluiten',
        ],
    ],
    'activity-timeline-item' => [
        'event-descriptions' => [
            'created' => [
                'causer' => '**:causerName** heeft het :modelLabel-record aangemaakt.',
                'causer-relationship' => '**:causerName** heeft een gerelateerd :relationshipName-record aangemaakt.',
                'causer-relationship-related-record-title' => '**:causerName** heeft een gerelateerde :relationshipName **:relatedRecordTitle** aangemaakt.',
                'without-causer' => 'Het :modelLabel-record is aangemaakt.',
                'without-causer-relationship' => 'Een gerelateerd **:relationshipName**-record is aangemaakt.',
                'without-causer-relationship-related-record-title' => 'Gerelateerde :relationshipName **:relatedRecordTitle** is aangemaakt.',
            ],
            'updated' => [
                'causer-changes-summary' => '**:causerName** heeft :changesSummary bijgewerkt.',
                'causer-changes-summary-relationship' => '**:causerName** heeft bij een gerelateerd :relationshipName-record :changesSummary bijgewerkt.',
                'causer-changes-summary-relationship-related-record-title' => '**:causerName** heeft bij gerelateerde :relationshipName **:relatedRecordTitle** :changesSummary bijgewerkt.',
                'causer-without-changes-summary' => '**:causerName** heeft het :modelLabel-record bijgewerkt.',
                'causer-without-changes-summary-relationship' => '**:causerName** heeft een gerelateerd :relationshipName-record bijgewerkt.',
                'causer-without-changes-summary-relationship-related-record-title' => '**:causerName** heeft gerelateerde :relationshipName **:relatedRecordTitle** bijgewerkt.',
                'without-causer-changes-summary' => 'Het :modelLabel-record is bijgewerkt door :changesSummary aan te passen.',
                'without-causer-changes-summary-relationship' => 'Een gerelateerd :relationshipName-record is bijgewerkt door :changesSummary aan te passen.',
                'without-causer-changes-summary-relationship-related-record-title' => 'Gerelateerde :relationshipName **:relatedRecordTitle** is bijgewerkt door :changesSummary aan te passen.',
                'without-causer-without-changes-summary' => 'Het :modelLabel-record is bijgewerkt.',
                'without-causer-without-changes-summary-relationship' => 'Een gerelateerd :relationshipName-record is bijgewerkt.',
                'without-causer-without-changes-summary-relationship-related-record-title' => 'Gerelateerde :relationshipName **:relatedRecordTitle** is bijgewerkt.',
            ],
            'deleted' => [
                'causer' => '**:causerName** heeft het :modelLabel-record verwijderd.',
                'causer-relationship' => '**:causerName** heeft een gerelateerd :relationshipName-record verwijderd.',
                'causer-relationship-related-record-title' => '**:causerName** heeft gerelateerde :relationshipName **:relatedRecordTitle** verwijderd.',
                'without-causer' => 'Het :modelLabel-record is verwijderd.',
                'without-causer-relationship' => 'Een gerelateerd :relationshipName-record is verwijderd.',
                'without-causer-relationship-related-record-title' => 'Gerelateerd :relationshipName **:relatedRecordTitle** is verwijderd.',
            ],
            'restored' => [
                'causer' => '**:causerName** heeft het :modelLabel-record hersteld.',
                'causer-relationship' => '**:causerName** heeft een gerelateerd :relationshipName-record hersteld.',
                'causer-relationship-related-record-title' => '**:causerName** heeft gerelateerde :relationshipName **:relatedRecordTitle** hersteld.',
                'without-causer' => 'Het :modelLabel-record is hersteld.',
                'without-causer-relationship' => 'Een gerelateerd :relationshipName-record is hersteld.',
                'without-causer-relationship-related-record-title' => 'Gerelateerd :relationshipName **:relatedRecordTitle** is hersteld.',
            ],
            'custom' => [
                'causer' => '**:causerName** heeft het :modelLabel-record :event.',
                'causer-relationship' => '**:causerName** heeft een gerelateerd :relationshipName-record :event.',
                'causer-relationship-related-record-title' => '**:causerName** heeft gerelateerde :relationshipName **:relatedRecordTitle** :event.',
                'without-causer' => 'Het :modelLabel-record is :event.',
                'without-causer-relationship' => 'Een gerelateerd :relationshipName-record is :event.',
                'without-causer-relationship-related-record-title' => 'Gerelateerde :relationshipName **:relatedRecordTitle** is :event.',
            ],
            'no-subject' => [
                'causer' => '**:causerName** heeft :event.',
                'causer-relationship' => '**:causerName** heeft gerelateerd :relationshipName-record :event.',
                'causer-relationship-related-record-title' => '**:causerName** heeft gerelateerde :relationshipName **:relatedRecordTitle** :event.',
                'without-causer' => ':event.',
                'without-causer-relationship' => 'Gerelateerd :relationshipName-record is :event.',
                'without-causer-relationship-related-record-title' => 'Gerelateerde :relationshipName **:relatedRecordTitle** is :event.',
            ],
            'changesSummary' => [
                'attribute' => '**:attributeLabel** naar **:newAttributeValue**',
                'attributeWithOld' => '**:attributeLabel** van :oldAttributeValue naar **:newAttributeValue**',
                'attributeFromBlankWithOld' => '**:attributeLabel** van een leeg veld naar **:newAttributeValue**',
                'attributeFromBlankToBlankWithOld' => '**:attributeLabel** van een leeg veld naar een leeg veld',
                'attributeToBlank' => '**:attributeLabel** naar een leeg veld',
                'attributeToBlankWithOld' => '**:attributeLabel** van :oldAttributeValue naar een leeg veld',
                'finalGlue' => ' en ',
                'values' => [
                    'boolean-1' => 'waar',
                    'boolean-0' => 'onwaar',
                ],
            ],
        ],
        'actions' => [
            'view_batch' => [
                'label' => 'Gerelateerde geschiedenis',
                'modal_heading' => 'Bekijk gerelateerde geschiedenis',
                'modal_description' => 'Dit event maakt deel uit van een reeks events. Hieronder kunt u zien welke andere events deel uitmaakten van deze reeks, inclusief het door u geselecteerde event.',
            ],
        ],
    ],
];
