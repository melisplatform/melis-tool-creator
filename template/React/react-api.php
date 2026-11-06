<?php

/**
 * React back-office API routes for the ModuleTpl tool (UI shipped by the ModuleTpl brick).
 * Attached under the shared parent route `melis-react-api` (defined by MelisReactApi).
 * Merged via ModuleTpl\Module::getConfig().
 */

return [
    'router' => [
        'routes' => [
            'melis-backoffice' => [
                'child_routes' => [
                    'melis-react-api' => [
                        'child_routes' => [
                            // save/delete MUST stay before the /:id catch-all
                            'moduletpl-react-list' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/moduletpl[/]', 'defaults' => ['__NAMESPACE__' => 'ModuleTpl\\Controller', 'controller' => 'ModuleTplReactApi', 'action' => 'list']],
                            ],
                            'moduletpl-react-save' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/moduletpl/save[/]', 'defaults' => ['__NAMESPACE__' => 'ModuleTpl\\Controller', 'controller' => 'ModuleTplReactApi', 'action' => 'save']],
                            ],
                            'moduletpl-react-delete' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/moduletpl/delete/:id',
                                    'constraints' => ['id' => '[0-9]+'],
                                    'defaults'    => ['__NAMESPACE__' => 'ModuleTpl\\Controller', 'controller' => 'ModuleTplReactApi', 'action' => 'delete'],
                                ],
                            ],
                            'moduletpl-react-item' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/moduletpl/:id',
                                    'constraints' => ['id' => '[0-9]+'],
                                    'defaults'    => ['__NAMESPACE__' => 'ModuleTpl\\Controller', 'controller' => 'ModuleTplReactApi', 'action' => 'get'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'ModuleTpl\Controller\ModuleTplReactApi' => \ModuleTpl\Controller\ModuleTplReactApiController::class,
        ],
    ],
];
