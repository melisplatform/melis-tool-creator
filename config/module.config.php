<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

return array(
    'router' => array(
        'routes' => array(
        	'melis-backoffice' => array(
                'child_routes' => array(
                    'application-MelisToolCreator' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'MelisToolCreator',
                            'defaults' => array(
                                '__NAMESPACE__' => 'MelisToolCreator\Controller',
                                'controller'    => 'Index',
                                'action'        => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'default' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:action]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'tool-creator-validate-cur-step' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => 'tool-creator-validate-cur-step',
                            'defaults' => array(
                                'controller' => 'MelisToolCreator\Controller\ToolCreator',
                                'action' => 'renderToolCreatorSteps',
                            ),
                        ),
                    ),
                    'tool-creator-get-tbl-cols' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => 'tool-creator-get-tbl-cols',
                            'defaults' => array(
                                'controller' => 'MelisToolCreator\Controller\ToolCreator',
                                'action' => 'renderStep3TableColumns',
                            ),
                        ),
                    ),
                    'tool-creator-reload-dbtbl-cached' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => 'tool-creator-reload-dbtbl-cached',
                            'defaults' => array(
                                'controller' => 'MelisToolCreator\Controller\ToolCreator',
                                'action' => 'renderStep3DbTables',
                            ),
                        ),
                    ),
                ),    
        	),
            /*
            * This route will handle the
            * alone setup of a module
            */
            /*'setup-melis-calendar' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/MelisCalendar',
                    'defaults' => array(
                        '__NAMESPACE__' => 'MelisCalendar\Controller',
                        'controller'    => '',
                        'action'        => '',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    'setup' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/setup',
                            'defaults' => array(
                                'controller' => 'MelisCalendar\Controller\MelisSetup',
                                'action' => 'setup-form',
                            ),
                        ),
                    ),
                ),
            ),*/
        ),
    ),
    'translator' => array(
        'locale' => 'en_EN',
    ),
    'service_manager' => array(
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'MelisToolCreator\Controller\ToolCreator' => 'MelisToolCreator\Controller\ToolCreatorController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
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
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'caches' => array(
        'toolcreator_database' => array(
            'active' => true, // activate or deactivate Melis Cache for this conf
            'adapter' => array(
                'name'    => 'Filesystem',
                'options' => array(
                    'ttl' => 0, // 24hrs
                    'namespace' => 'melistoolcreator',
                    'cache_dir' => $_SERVER['DOCUMENT_ROOT'] . '/../cache'
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => false),
                'Serializer'
            ),
        ),
    )
);
