<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'plugins' => [
        'meliscore' => [
            'interface' => [
                'meliscore_leftmenu' => [
                    'interface' => [
                        'meliscustom_toolstree_section' => [
                            'interface' => [
                                'moduletpl_conf' => [
                                    'conf' => [
                                        'id' => 'id_moduletpl_leftnemu',
                                        'melisKey' => 'moduletpl_leftnemu',
                                        'name' => 'tr_moduletpl_title',
                                        'icon' => 'fa fa-puzzle-piece',
                                    ],
                                    'interface' => [
                                        'moduletpl_tool' => [
                                            'conf' => [
                                                'id' => 'id_moduletpl_tool',
                                                'melisKey' => 'moduletpl_tool',
                                                'name' => 'tr_moduletpl_title',
                                                'icon' => 'fa fa-puzzle-piece',
                                            ],
                                            'forward' => [
                                                'module' => 'ModuleTpl',
                                                'controller' => 'List',
                                                'action' => 'render-tool',
                                                'jscallback' => '',
                                                'jsdatas' => []
                                            ],
                                            'interface' => [
                                                'moduletpl_header' => [
                                                    'conf' => [
                                                        'id' => 'id_moduletpl_header',
                                                        'melisKey' => 'moduletpl_header',
                                                        'name' => 'tr_moduletpl_header',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'ModuleTpl',
                                                        'controller' => 'List',
                                                        'action' => 'render-tool-header',
                                                        'jscallback' => '',
                                                        'jsdatas' => []
                                                    ],
                                                ],
                                                'moduletpl_content' => [
                                                    'conf' => [
                                                        'id' => 'id_moduletpl_content',
                                                        'melisKey' => 'moduletpl_content',
                                                        'name' => 'tr_moduletpl_content',
                                                    ],
                                                    'forward' => [
                                                        'module' => 'ModuleTpl',
                                                        'controller' => 'List',
                                                        'action' => 'render-tool-content',
                                                        'jscallback' => '',
                                                        'jsdatas' => []
                                                    ],
                                                    'interface' => [
#TCTOOLINTERFACE
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];