<?php

return array(
    'plugins' => array(
        'melistoolcreator' => array(
            'forms' => array(
                'melistoolcreator_step1_form' => array(
                    'attributes' => array(
                        'name' => 'tool-creator-step-1',
                        'id' => 'tool-creator-step-1',
                        'class' => 'tool-creator-step-1',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'tcf-name',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_melistoolcreator_tcf-name',
                                    'tooltip' => 'tr_melistoolcreator_tcf-name tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'moudle-name',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ),
                            ),
                        ),
                        /*array(
                            'spec' => array(
                                'name' => 'tcf-module-toolstree',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'Tools tree',
                                    'tooltip' => 'Tools tree',
                                ),
                                'attributes' => array(
                                    'id' => 'tcf-module-toolstree',
                                    'class' => 'hidden',
                                ),
                            ),
                        ),*/
                    ),
                    'input_filter' => array(
                        'tcf-name' => array(
                            'name'     => 'tcf-name',
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name' => 'regex',
                                    'options' => array(
                                        'pattern' => '/^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff]*$/',
                                        'messages' => array(\Zend\Validator\Regex::NOT_MATCH => 'tr_melistoolcreator_err_invalid_module'),
                                        'encoding' => 'UTF-8',
                                    ),
                                ),
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 50,
                                        'messages' => array(
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_50',
                                        ),
                                    ),
                                ),
                                array(
                                    'name' => 'NotEmpty',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        /*'tcf-module-toolstree' => array(
                            'name'     => 'tcf-module-toolstree',
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name' => 'NotEmpty',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),*/
                    ),
                ),
                'melistoolcreator_step2_form' => array(
                    'attributes' => array(
                        'name' => 'tool-creator-step-2',
                        'id' => 'tool-creator-step-2',
                        'class' => 'tool-creator-step-2',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'tcf-lang-local',
                                'type' => 'Hidden',
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'tcf-title',
                                'type' => 'MelisText',
                                'options' => array(
                                    'label' => 'tr_melistoolcreator_tcf-title',
                                    'tooltip' => 'tr_melistoolcreator_tcf-title tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'tcf-title',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ),
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'tcf-desc',
                                'type' => 'Textarea',
                                'options' => array(
                                    'label' => 'tr_melistoolcreator_tcf-desc',
                                    'tooltip' => 'tr_melistoolcreator_tcf-desc tooltip',
                                ),
                                'attributes' => array(
                                    'id' => 'tcf-desc',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                    'class' => 'form-control',
                                    'rows' => 4
                                ),
                            ),
                        )
                    ),
                    'input_filter' => array(
                        'tcf-title' => array(
                            'name'     => 'tcf-title',
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 100,
                                        'messages' => array(
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_100',
                                        ),
                                    ),
                                ),
                                array(
                                    'name' => 'NotEmpty',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'tcf-desc' => array(
                            'name'     => 'tcf-desc',
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max'      => 250,
                                        'messages' => array(
                                            \Zend\Validator\StringLength::TOO_LONG => 'tr_melistoolcreator_err_long_250',
                                        ),
                                    ),
                                ),
                                array(
                                    'name' => 'NotEmpty',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                'melistoolcreator_step3_form' => array(
                    'attributes' => array(
                        'name' => 'tool-creator-step-3',
                        'id' => 'tool-creator-step-3',
                        'class' => 'tool-creator-step-3 hidden',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'tcf-db-table',
                                'type' => 'Hidden',
                            ),
                        ),
                    ),
                    'input_filter' => array(
                        'tcf-db-table' => array(
                            'name'     => 'tcf-db-table',
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name' => 'NotEmpty',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                'melistoolcreator_step4_form' => array(
                    'attributes' => array(
                        'name' => 'tool-creator-step-4',
                        'id' => 'tool-creator-step-4',
                        'class' => 'tool-creator-step-4',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'tcf-db-table-cols',
                                'type' => 'Checkbox',
                                'options' => array(
                                    'use_hidden_element' => false,
                                ),
                                'attributes' => array(
                                    'class' => 'hidden'
                                )
                            ),
                        ),
                    ),
                    'input_filter' => array(
                        'tcf-db-table-cols' => array(
                            'name'     => 'tcf-db-table-cols',
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name' => 'NotEmpty',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                'melistoolcreator_step5_form' => array(
                    'attributes' => array(
                        'name' => 'tool-creator-step-5',
                        'id' => 'tool-creator-step-5',
                        'class' => 'tool-creator-step-5',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'tcf-db-table-col-editable',
                                'type' => 'Checkbox',
                                'options' => array(
                                    'use_hidden_element' => false,
                                ),
                                'attributes' => array(
                                    'class' => 'hidden'
                                )
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'tcf-db-table-col-required',
                                'type' => 'Checkbox',
                                'options' => array(
                                    'use_hidden_element' => false,
                                ),
                                'attributes' => array(
                                    'class' => 'hidden'
                                )
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'tcf-db-table-col-type',
                                'type' => 'Select',
                                'options' => array(
                                    'value_options' => array(
                                        'MelisText' => 'Text',
                                        'TextArea' => 'Textarea',
                                        'MelisCoreTinyMCE' => 'Textarea (TinyMCE)',
                                        'Datepicker' => 'Date Picker',
                                        'Datetimepicker' => 'Datetime Picker',
                                        'MelisCmsPluginSiteSelect' => 'Melis site',
                                        'MelisCmsLanguageSelect' => 'Melis Page Language',
                                        'MelisCmsTemplateSelect' => 'Melis Template',
                                        'MelisCoreUserSelect' => 'Melis BO Users',
                                    ),
                                ),
                                'attributes' => array(
                                    'class' => 'form-control',
                                )
                            ),
                        ),
                    ),
                    'input_filter' => array(
                        'tcf-db-table-col-editable' => array(
                            'name'     => 'tcf-db-table-col-editable',
                            'required' => false,
                            'validators' => array(

                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'tcf-db-table-col-required' => array(
                            'name'     => 'tcf-db-table-col-required',
                            'required' => false,
                            'validators' => array(

                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'tcf-db-table-col-type' => array(
                            'name'     => 'tcf-db-table-col-type',
                            'required' => false,
                            'validators' => array(

                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                'melistoolcreator_step6_form' => array(
                    'attributes' => array(
                        'name' => 'tool-creator-step-6',
                        'id' => 'tool-creator-step-6',
                        'class' => 'tool-creator-step-6',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'tcf-lang-local',
                                'type' => 'Hidden',
                            ),
                        ),
                    ),
                    'input_filter' => array(
                        'tcf-lang-local' => array(
                            'name'     => 'tcf-lang-local',
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name' => 'NotEmpty',
                                    'options' => array(
                                        'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                        ),
                                    ),
                                ),
                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                'melistoolcreator_step8_form' => array(
                    'attributes' => array(
                        'name' => 'tool-creator-step-8',
                        'id' => 'tool-creator-step-8',
                        'class' => 'tool-creator-step-8',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'tcf-activate-tool',
                                'type' => 'Checkbox',
                                'options' => array(
                                    'use_hidden_element' => false,
                                ),
                                'attributes' => array(
                                    'class' => 'hidden'
                                )
                            ),
                        ),
                    ),
                    'input_filter' => array(
                        'tcf-activate-tool' => array(
                            'name'     => 'tcf-activate-tool',
                            'required' => true,
                            'validators' => array(

                            ),
                            'filters'  => array(
                                array('name' => 'StripTags'),
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);