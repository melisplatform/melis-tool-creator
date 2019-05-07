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
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tcf-name',
                                'type' => 'MelisText',
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
                                'type' => 'Zend\Form\Element\Radio',
                                'name' => 'tcf-tool-type',
                                'options' => [
                                    'label' => 'tr_melistoolcreator_tcf_tool_type',
                                    'tooltip' => 'tr_melistoolcreator_tcf_tool_type tooltip',
                                    'label_options' => [
                                        'disable_html_escape' => true,
                                    ],
                                    'label_attributes' => [
                                        'class' => 'melis-radio-box'
                                    ],
                                    'value_options' => [
                                        'modal' => 'Modal <span class="melis-radio-box-circle"></span>',
                                        'tab' => 'Tabulation <span class="melis-radio-box-circle"></span>',
                                    ],
                                ],
                                'attributes' => [
                                    'value' => 'modal',
                                    'class' => 'moudle-name',
                                    'required' => 'required',
                                ],
                            ]
                        ]
                        /*[
                            'spec' => [
                                'name' => 'tcf-module-toolstree',
                                'type' => 'MelisText',
                                'options' => [
                                    'label' => 'Tools tree',
                                    'tooltip' => 'Tools tree',
                                ],
                                'attributes' => [
                                    'id' => 'tcf-module-toolstree',
                                    'class' => 'hidden',
                                ],
                            ],
                        ],*/
                    ],
                    'input_filter' => [
                        'tcf-name' => [
                            'name'     => 'tcf-name',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'regex',
                                    'options' => [
                                        'pattern' => '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',
                                        'messages' => [\Zend\Validator\Regex::NOT_MATCH => 'tr_melistoolcreator_err_invalid_module'],
                                        'encoding' => 'UTF-8',
                                    ],
                                ],
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 50,
                                        'messages' => [
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_50',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        /*'tcf-module-toolstree' => [
                            'name'     => 'tcf-module-toolstree',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],*/
                    ],
                ],
                'melistoolcreator_step2_form' => [
                    'attributes' => [
                        'name' => 'tool-creator-step-2',
                        'id' => 'tool-creator-step-2',
                        'class' => 'tool-creator-step-2',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
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
                                    'id' => 'tcf-title',
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
                                    'id' => 'tcf-desc',
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
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_100',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
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
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_250',
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
                        'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
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
                                                \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_select_pri_tbl',
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
                        'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                        'elements' => [
                            [
                                'spec' => [
                                    'name' => 'tcf-db-table-has-language',
                                    'type' => 'Hidden',
                                    'attributes' => [
                                        'value' => true
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
                                                \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_select_lang_tbl',
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
                                                \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_select_pri_key_tbl',
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
                                                \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_select_lang_key_tbl',
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
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
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
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
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
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
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
                                        'MelisText' => 'Text',
                                        'Switch' => 'Switch',
                                        'File' => 'File upload',
                                        'TextArea' => 'Textarea',
                                        'MelisCoreTinyMCE' => 'Textarea (TinyMCE)',
                                        'Datepicker' => 'Date Picker',
                                        'Datetimepicker' => 'Datetime Picker',
                                        'MelisCmsPluginSiteSelect' => 'Melis site',
                                        'MelisCmsLanguageSelect' => 'Melis Page Language',
                                        'MelisCmsTemplateSelect' => 'Melis Template',
                                        'MelisCoreUserSelect' => 'Melis BO Users',
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
                        'name' => '',
                        'id' => '',
                        'class' => 'tool-creator-step-6',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
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
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
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
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
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
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
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