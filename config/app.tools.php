<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'plugins' => [
        'melistoolcreator' => [
            'forms' => [
                'melistoolcreator_step1_form' => [
                    'attributes' => [
                        'name' => 'tool-creator-step-1',
                        'id' => 'tool-creator-step-1',
                        'class' => 'tool-creator-step-1',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                    'elements' => [
                        [
                            'spec' => [
                                'type' => 'MelisText',
                                'name' => 'tcf-name',
                                'options' => [
                                    'label' => 'tr_melistoolcreator_tcf-name',
                                    'tooltip' => 'tr_melistoolcreator_tcf-name tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'moudle-name',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'type' => 'Radio',
                                'name' => 'tcf-tool-type',
                                'options' => [
                                    'label' => 'tr_melistoolcreator_tcf_tool_type',
                                    'tooltip' => 'tr_melistoolcreator_tcf_tool_type tooltip',
                                    'radio-button' => true,
                                    'label_options' => [
                                        'disable_html_escape' => true,
                                    ],
                                    'value_options' => [
                                        'db' => 'tr_melistoolcreator_tcf_tool_type_db',
                                        'iframe' => 'tr_melistoolcreator_tcf_tool_type_iframe',
                                        'blank' => 'tr_melistoolcreator_tcf_tool_type_blank'
                                    ],
                                ],
                                'attributes' => [
                                    'value' => 'db',
                                    'required' => 'required',
                                ],
                            ]
                        ],
                        [
                            'spec' => [
                                'type' => 'MelisText',
                                'name' => 'tcf-tool-iframe-url',
                                'options' => [
                                    'label' => 'tr_melistoolcreator_tcf_tool_iframe_url',
                                    'tooltip' => 'tr_melistoolcreator_tcf_tool_iframe_url tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'tcf-tool-iframe',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                    'class' => 'tcf-tool-type tcf-tool-type-iframe form-control'
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'type' => 'Radio',
                                'name' => 'tcf-tool-edit-type',
                                'options' => [
                                    'label' => 'tr_melistoolcreator_tcf_tool_edit_type',
                                    'tooltip' => 'tr_melistoolcreator_tcf_tool_edit_type tooltip',
                                    'radio-button' => true,
                                    'label_options' => [
                                        'disable_html_escape' => true,
                                    ],
                                    'value_options' => [
                                        'modal' => 'Modal',
                                        'tab' => 'Tabulation',
                                    ],
                                ],
                                'attributes' => [
                                    'value' => 'modal',
                                    'required' => 'required',
                                    'class' => 'tcf-tool-type tcf-tool-type-db'
                                ],
                            ]
                        ],
                    ],
                    'input_filter' => [
                        'tcf-name' => [
                            'name'     => 'tcf-name',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'regex',
                                    'options' => [
                                        'pattern' => '/^[a-zA-Z\x7f-\xff][a-zA-Z\x7f-\xff]*$/',
                                        'messages' => [\Laminas\Validator\Regex::NOT_MATCH => 'tr_melistoolcreator_err_invalid_module'],
                                        'encoding' => 'UTF-8',
                                    ],
                                ],
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 50,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_50',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tcf-tool-iframe-url' => [
                            'name'     => 'tcf-tool-iframe-url',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'regex',
                                    'options' => [
                                        'pattern' => '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu',
                                        'messages' => [\Laminas\Validator\Regex::NOT_MATCH => 'tr_melistoolcreator_invalid_url'],
                                        'encoding' => 'UTF-8',
                                    ],
                                ]
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],
                'melistoolcreator_step2_form' => [
                    'attributes' => [
                        'name' => 'tool-creator-step-2',
                        'class' => 'tool-creator-step-2',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tcf-lang-local',
                                'type' => 'Hidden',
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tcf-title',
                                'type' => 'MelisText',
                                'options' => [
                                    'label' => 'tr_melistoolcreator_tcf-title',
                                    'tooltip' => 'tr_melistoolcreator_tcf-title tooltip',
                                ],
                                'attributes' => [
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tcf-desc',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_melistoolcreator_tcf-desc',
                                    'tooltip' => 'tr_melistoolcreator_tcf-desc tooltip',
                                ],
                                'attributes' => [
                                    'value' => '',
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                    'rows' => 4
                                ],
                            ],
                        ]
                    ],
                    'input_filter' => [
                        'tcf-title' => [
                            'name'     => 'tcf-title',
                            'required' => true,
                            'validators' => [
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 100,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_100',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tcf-desc' => [
                            'name'     => 'tcf-desc',
                            'required' => false,
                            'validators' => [
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 250,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_250',
                                        ],
                                    ],
                                ]
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],
                'melistoolcreator_step3_form' => [
                    'melistoolcreator_step3_primary_tbl' => [
                        'attributes' => [
                            'name' => 'tool-creator-step-3',
                            'id' => 'tool-creator-step-3',
                            'class' => 'tool-creator-step-3 hidden',
                            'method' => 'POST',
                            'action' => '',
                        ],
                        'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                        'elements' => [
                            [
                                'spec' => [
                                    'name' => 'tcf-db-table',
                                    'type' => 'Hidden',
                                ],
                            ],
                        ],
                        'input_filter' => [
                            'tcf-db-table' => [
                                'name'     => 'tcf-db-table',
                                'required' => true,
                                'validators' => [
                                    [
                                        'name' => 'NotEmpty',
                                        'options' => [
                                            'messages' => [
                                                \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_select_pri_tbl',
                                            ],
                                        ],
                                    ],
                                ],
                                'filters'  => [
                                    ['name' => 'StripTags'],
                                    ['name' => 'StringTrim'],
                                ],
                            ],
                        ],
                    ],
                    'melistoolcreator_step3_language_tbl' => [
                        'attributes' => [
                            'name' => 'tool-creator-step-3',
                            'id' => 'tool-creator-step-3',
                            'class' => 'tool-creator-step-3 hidden',
                            'method' => 'POST',
                            'action' => '',
                        ],
                        'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                        'elements' => [
                            [
                                'spec' => [
                                    'name' => 'tcf-db-table-has-language',
                                    'type' => 'Hidden',
                                    'attributes' => [
                                        'value' => false
                                    ]
                                ],
                            ],
                            [
                                'spec' => [
                                    'name' => 'tcf-db-table-language-tbl',
                                    'type' => 'Hidden',
                                ],
                            ],
                            [
                                'spec' => [
                                    'name' => 'tcf-db-table-language-pri-fk',
                                    'type' => 'Hidden',
                                ],
                            ],
                            [
                                'spec' => [
                                    'name' => 'tcf-db-table-language-lang-fk',
                                    'type' => 'Hidden',
                                ],
                            ],
                        ],
                        'input_filter' => [
                            'tcf-db-table-has-language' => [
                                'name'     => 'tcf-db-table-has-language',
                                'required' => false,
                                'validators' => [
                                ],
                                'filters'  => [
                                    ['name' => 'StripTags'],
                                    ['name' => 'StringTrim'],
                                ],
                            ],
                            'tcf-db-table-language-tbl' => [
                                'name'     => 'tcf-db-table-language-tbl',
                                'required' => true,
                                'validators' => [
                                    [
                                        'name' => 'NotEmpty',
                                        'options' => [
                                            'messages' => [
                                                \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_select_lang_tbl',
                                            ],
                                        ],
                                    ],
                                ],
                                'filters'  => [
                                    ['name' => 'StripTags'],
                                    ['name' => 'StringTrim'],
                                ],
                            ],
                            'tcf-db-table-language-pri-fk' => [
                                'name'     => 'tcf-db-table-language-pri-fk',
                                'required' => true,
                                'validators' => [
                                    [
                                        'name' => 'NotEmpty',
                                        'options' => [
                                            'messages' => [
                                                \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_select_pri_key_tbl',
                                            ],
                                        ],
                                    ],
                                ],
                                'filters'  => [
                                    ['name' => 'StripTags'],
                                    ['name' => 'StringTrim'],
                                ],
                            ],
                            'tcf-db-table-language-lang-fk' => [
                                'name'     => 'tcf-db-table-language-lang-fk',
                                'required' => true,
                                'validators' => [
                                    [
                                        'name' => 'NotEmpty',
                                        'options' => [
                                            'messages' => [
                                                \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_select_lang_key_tbl',
                                            ],
                                        ],
                                    ],
                                ],
                                'filters'  => [
                                    ['name' => 'StripTags'],
                                    ['name' => 'StringTrim'],
                                ],
                            ],
                        ],
                    ]
                ],
                'melistoolcreator_step4_form' => [
                    'attributes' => [
                        'name' => 'tool-creator-step-4',
                        'id' => 'tool-creator-step-4',
                        'class' => 'tool-creator-step-4',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tcf-db-table-cols',
                                'type' => 'Checkbox',
                                'options' => [
                                    'use_hidden_element' => false,
                                ],
                                'attributes' => [
                                    'class' => 'hidden'
                                ]
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tcf-db-table-col-display',
                                'type' => 'Select',
                                'options' => [
                                    'value_options' => [
                                        'raw_view' => 'tr_melistoolcreator_select_raw_view',
                                        'char_length_limit' => 'tr_melistoolcreator_select_char_len_50',
                                        'dot_color' => 'tr_melistoolcreator_select_dot_color',
                                        'admin_name' => 'tr_melistoolcreator_select_admin_name'
                                    ],
                                ],
                                'attributes' => [
                                    'class' => 'form-control',
                                ]
                            ],
                        ],
                    ],
                    'input_filter' => [
                        'tcf-db-table-cols' => [
                            'name'     => 'tcf-db-table-cols',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tcf-db-table-col-display' => [
                            'name'     => 'tcf-db-table-col-display',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],
                'melistoolcreator_step5_form' => [
                    'attributes' => [
                        'name' => 'tool-creator-step-5',
                        'id' => 'tool-creator-step-5',
                        'class' => 'tool-creator-step-5',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tcf-db-table-col-editable',
                                'type' => 'Checkbox',
                                'options' => [
                                    'use_hidden_element' => false,
                                ],
                                'attributes' => [
                                    'class' => 'hidden'
                                ]
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tcf-db-table-col-required',
                                'type' => 'Checkbox',
                                'options' => [
                                    'use_hidden_element' => false,
                                ],
                                'attributes' => [
                                    'class' => 'hidden'
                                ]
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tcf-db-table-col-type',
                                'type' => 'Select',
                                'options' => [
                                    'value_options' => [
                                        'MelisText' => 'tr_melistoolcreator_select_text',
                                        'Switch' => 'tr_melistoolcreator_select_switch',
                                        'File' => 'File upload',
                                        'Textarea' => 'Textarea',
                                        'MelisCoreTinyMCE' => 'tr_melistoolcreator_select_textarea_tinymce',
                                        'DatePicker' => 'Date Picker',
                                        'DateTimePicker' => 'Datetime Picker',
                                        'MelisCoreUserSelect' => 'Melis BO Users'
                                    ],
                                ],
                                'attributes' => [
                                    'class' => 'form-control',
                                ]
                            ],
                        ],
                    ],
                    'input_filter' => [
                        'tcf-db-table-col-editable' => [
                            'name'     => 'tcf-db-table-col-editable',
                            'required' => false,
                            'validators' => [

                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tcf-db-table-col-required' => [
                            'name'     => 'tcf-db-table-col-required',
                            'required' => false,
                            'validators' => [

                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tcf-db-table-col-type' => [
                            'name'     => 'tcf-db-table-col-type',
                            'required' => false,
                            'validators' => [

                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],
                'melistoolcreator_step6_form' => [
                    'attributes' => [
                        'name' => 'tool-creator-step-6',
                        'id' => 'tool-creator-step-6',
                        'class' => 'tool-creator-step-6',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tcf-lang-local',
                                'type' => 'Hidden',
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tcf-tbl-type',
                                'type' => 'Hidden',
                            ],
                        ],
                    ],
                    'input_filter' => [
                        'tcf-lang-local' => [
                            'name'     => 'tcf-lang-local',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tcf-tbl-type' => [
                            'name'     => 'tcf-tbl-type',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],
                'melistoolcreator_step8_form' => [
                    'attributes' => [
                        'name' => 'tool-creator-step-8',
                        'id' => 'tool-creator-step-8',
                        'class' => 'tool-creator-step-8',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializableHydrator',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tcf-activate-tool',
                                'type' => 'Checkbox',
                                'options' => [
                                    'use_hidden_element' => false,
                                ],
                                'attributes' => [
                                    'class' => 'hidden'
                                ]
                            ],
                        ],
                    ],
                    'input_filter' => [
                        'tcf-activate-tool' => [
                            'name'     => 'tcf-activate-tool',
                            'required' => true,
                            'validators' => [

                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];