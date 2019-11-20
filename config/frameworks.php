<?php

return [
    'frameworks' => [
        'laravel',
        'symfony',
        'lumen',
        'silex',
    ],
    'form-elements' => [
        'elements' => [
            [
                'spec' => [
                    'type' => 'Checkbox',
                    'name' => 'tcf-create-framework-tool',
                    'options' => [
                        'label' => 'Create framework tool',
                        'tooltip' => 'Create framework tool',
                        'switch_options' => [
                            'label-on' => 'tr_meliscore_common_yes',
                            'label-off' => 'tr_meliscore_common_no',
                            'icon' => "glyphicon glyphicon-resize-horizontal",
                        ],
                    ],
                    'attributes' => [
                        'id' => 'tcf-create-framework-tool',
                        'class' => 'tcf-tool-type tcf-tool-type-db tcf-tool-type-blank',
                    ],
                ],
            ],
            [
                'spec' => [
                    'type' => 'Radio',
                    'name' => 'tcf-tool-framework',
                    'options' => [
                        'label' => 'Select Framework',
                        'tooltip' => 'tr_melistoolcreator_tcf_tool_type tooltip',
                        'radio-button' => true,
                        'label_options' => [
                            'disable_html_escape' => true,
                        ],
                        'label_attributes' => [
                            'class' => 'melis-radio-box'
                        ],
                        'value_options' => [],
                    ],
                    'attributes' => [
                        'required' => 'required',
                        'class' => 'tcf-tool-type tcf-tool-type-db tcf-tool-type-blank'
                    ],
                ]
            ],
        ],
        'input_filter' => [
            'tcf-create-framework-tool' => [
                'name'     => 'tcf-create-framework-tool',
                'required' => true,
                'validators' => [],
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
            'tcf-tool-framework' => [
                'name'     => 'tcf-tool-framework',
                'required' => false,
                'validators' => [],
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
        ]
    ]
];