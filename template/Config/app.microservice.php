<?php
return [
    'plugins' => [
        'microservice' => [
            'ModuleTpl' => [
                'ModuleTplService' => [
                    'getItemById' => [
                        'attributes' => [
                            'name'	=> 'microservice_form',
                            'id'	=> 'microservice_form',
                            'method'=> 'POST',
                            'action'=> $_SERVER['REQUEST_URI'],
                        ],
                        'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                        'elements' => [
                            array(
                                'spec' => array(
                                    'name' => 'Id',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'Id',
                                    ),
                                    'attributes' => array(
                                        'id' => 'Id',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => '0',
                                        'data-type' => 'int'
                                    ),
                                ),
                            ),
                        ]
                    ],
                    'getList' => [
                        'attributes' => [
                            'name'	=> 'microservice_form',
                            'id'	=> 'microservice_form',
                            'method'=> 'POST',
                            'action'=> $_SERVER['REQUEST_URI'],
                        ],
                        'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                        'elements' => [
                            array(
                                'spec' => array(
                                    'name' => 'start',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'start',
                                    ),
                                    'attributes' => array(
                                        'id' => 'start',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => '0',
                                        'data-type' => 'int'
                                    ),
                                ),
                            ),
                            array(
                                'spec' => array(
                                    'name' => 'limit',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'limit',
                                    ),
                                    'attributes' => array(
                                        'id' => 'limit',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => '0',
                                        'data-type' => 'int'
                                    ),
                                ),
                            ),
                            array(
                                'spec' => array(
                                    'name' => 'searchKeys[]',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'search keys',
                                    ),
                                    'attributes' => array(
                                        'id' => 'searchKeys',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => 'separate by comma',
                                        'data-type' => 'array',
                                    ),
                                ),
                            ),
                            array(
                                'spec' => array(
                                    'name' => 'searchValue',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'search value',
                                    ),
                                    'attributes' => array(
                                        'id' => 'searchValue',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => 'search1',
                                        'data-type' => 'string',
                                    ),
                                ),
                            ),
                            array(
                                'spec' => array(
                                    'name' => 'orderKey',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'order key',
                                    ),
                                    'attributes' => array(
                                        'id' => 'orderKey',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => 'col2',
                                        'data-type' => 'string',
                                    ),
                                ),
                            ),
                            array(
                                'spec' => array(
                                    'name' => 'order',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'order',
                                    ),
                                    'attributes' => array(
                                        'id' => 'order',
                                        'value' => '',
                                        'placeholder' => 'ASC',
                                        'data-type' => 'string',
                                    ),
                                ),
                            ),
                            array(
                                'spec' => array(
                                    'name' => 'langId',
                                    'type' => 'Text',
                                    'options' => array(
                                        'label' => 'lang id',
                                    ),
                                    'attributes' => array(
                                        'id' => 'langId',
                                        'value' => '',
                                        'class' => '',
                                        'placeholder' => '0',
                                        'data-type' => 'int'
                                    ),
                                ),
                            ),
                        ]
                    ]
                ]
            ]
        ]
    ]
];