<?php

return [
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => [
                    'label' => 'lang:admin::lang.button_save',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                    'data-progress-indicator' => 'admin::lang.text_saving',
                ],
            ],
        ],
        'fields' => [
            'info' => [
                'type' => 'partial',
                'path' => 'webtronicie.giftcards::settings.info',
            ],
            'api_endpoint' => [
                'label' => 'lang:webtronicie.giftcards::default.label_api_endpoint',
                'type' => 'text',
                'span' => 'left',
            ],
            'api_key' => [
                'label' => 'lang:webtronicie.giftcards::default.label_api_key',
                'type' => 'text',
                'span' => 'left',
            ],

        ],
    ],
];
