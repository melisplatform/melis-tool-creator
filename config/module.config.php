<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'router' => [
        'routes' => [
            'melis-backoffice' => [
                'child_routes' => [
                    'application-MelisToolCreator' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'MelisToolCreator',
                            'defaults' => [
                                '__NAMESPACE__' => 'MelisToolCreator\Controller',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'default' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/[:controller[/:action]]',
                                    'constraints' => [
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [],
                                ],
                            ],
                        ],
                    ],
                    'tool-creator-validate-cur-step' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'tool-creator-validate-cur-step',
                            'defaults' => [
                                'controller' => 'MelisToolCreator\Controller\ToolCreator',
                                'action' => 'renderToolCreatorSteps',
                            ],
                        ],
                    ],
                    'tool-creator-get-tbl-cols' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'tool-creator-get-tbl-cols',
                            'defaults' => [
                                'controller' => 'MelisToolCreator\Controller\ToolCreator',
                                'action' => 'renderStep3TableColumns',
                            ],
                        ],
                    ],
                    'tool-creator-reload-dbtbl-cached' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'tool-creator-reload-dbtbl-cached',
                            'defaults' => [
                                'controller' => 'MelisToolCreator\Controller\ToolCreator',
                                'action' => 'renderStep3PrimaryDbTables',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'locale' => 'en_EN',
    ],
    'service_manager' => [
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
        'factories' => [
            'MelisToolCreatorService' => 'MelisToolCreator\Service\Factory\MelisToolCreatorServiceFactory',
        ],
    ],
    'controllers' => [
        'invokables' => [
            'MelisToolCreator\Controller\ToolCreator' => 'MelisToolCreator\Controller\ToolCreatorController',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => [
            'melis-tool-creator/step1'          => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step1.phtml',
            'melis-tool-creator/step2'          => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step2.phtml',
            'melis-tool-creator/step3'          => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step3.phtml',
            'melis-tool-creator/step3-db_tble'  => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step3-db-tables.phtml',
            'melis-tool-creator/step3-tbl-col'  => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step3-table-columns.phtml',
            'melis-tool-creator/step4'          => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step4.phtml',
            'melis-tool-creator/step5'          => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step5.phtml',
            'melis-tool-creator/step6'          => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step6.phtml',
            'melis-tool-creator/step7'          => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step7.phtml',
            'melis-tool-creator/step8'          => __DIR__ . '/../view/melis-tool-creator/tool-creator/render-step8.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'caches' => [
        'toolcreator_database' => [
            'active' => true, // activate or deactivate Melis Cache for this conf
            'adapter' => [
                'name'    => 'Filesystem',
                'options' => [
                    'ttl' => 0, // 24hrs
                    'namespace' => 'melistoolcreator',
                    'cache_dir' => $_SERVER['DOCUMENT_ROOT'] . '/../cache'
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false
                ],
                'Serializer'
            ],
        ],
    ]
];
