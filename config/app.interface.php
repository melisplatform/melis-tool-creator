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
            'conf' => [
                'id' => '',
                'name' => 'tr_melistool_toolcreator',
                'rightsDisplay' => 'none',
            ],
            'ressources' => [
                'js' => [
                    '/MelisToolCreator/js/tool-creator.js',
                ],
                'css' => [
                    '/MelisToolCreator/css/style.css',
                ],
                /**
                 * the "build" configuration compiles all assets into one file to make
                 * lesser requests
                 */
                'build' => [
                    // configuration to override "use_build_assets" configuration, if you want to use the normal assets for this module.
                    'disable_bundle' => true,

                    // lists of assets that will be loaded in the layout
                    'css' => [
                        '/MelisToolCreator/build/css/bundle.css',
                    ],
                    'js' => [
                        '/MelisToolCreator/build/js/bundle.js',
                    ]
                ]
            ],
            'datas' => [
                'steps' => [
                    'melistoolcreator_step1' => [
                        'name' => 'tr_melistoolcreator_module',
                        'icon' => 'fa-puzzle-piece'
                    ],
                    'melistoolcreator_step2' => [
                        'name' => 'tr_melistoolcreator_texts',
                        'icon' => 'fa-language'
                    ],
                    'melistoolcreator_step3' => [
                        'name' => 'tr_melistoolcreator_database',
                        'icon' => 'fa-database'
                    ],
                    'melistoolcreator_step4' => [
                        'name' => 'tr_melistoolcreator_table_cols',
                        'icon' => 'fa-table'
                    ],
                    'melistoolcreator_step5' => [
                        'name' => 'tr_melistoolcreator_add_update_form',
                        'icon' => 'fa-list-alt'
                    ],
                    'melistoolcreator_step6' => [
                        'name' => 'tr_melistoolcreator_cols_translations',
                        'icon' => 'fa-language'
                    ],
                    'melistoolcreator_step7' => [
                        'name' => 'tr_melistoolcreator_summary',
                        'icon' => 'fa-list'
                    ],
                    'melistoolcreator_step8' => [
                        'name' => 'tr_melistoolcreator_finalization',
                        'icon' => 'fa-cogs'
                    ]
                ],
                'input_types' => [
                    'MelisCoreTinyMCE' => [
                        'text',
                        'tinytext',
                        'mediumtext',
                        'longtext',
                    ],
                    'Datepicker' => [
                        'date'
                    ],
                    'Datetimepicker' => [
                        'datetime',
                        'timestamp'
                    ],
                    'Switch' => [
                        'tinyint',
                        'boolean',
                    ],
                ]
            ],
            'interface' => [
                'melistoolcreator_conf' => [
                    'conf' => [
                        'id' => 'id_melistoolcreator_tool',
                        'melisKey' => 'melistoolcreator_tool',
                        'name' => 'tr_melistoolcreator',
                        'icon' => 'fa fa-magic',
                    ],
                    'forward' => [
                        'module' => 'MelisToolCreator',
                        'controller' => 'ToolCreator',
                        'action' => 'render-tool-creator',
                        'jscallback' => '',
                        'jsdatas' => []
                    ],
                    'interface' => [
                        'melistoolcreator_header' => [
                            'conf' => [
                                'id' => 'id_melistoolcreator_header',
                                'melisKey' => 'melistoolcreator_header',
                                'name' => 'tr_melistoolcreator_header',
                            ],
                            'forward' => [
                                'module' => 'MelisToolCreator',
                                'controller' => 'ToolCreator',
                                'action' => 'render-tool-creator-header',
                                'jscallback' => '',
                                'jsdatas' => []
                            ],
                        ],
                        'melistoolcreator_content' => [
                            'conf' => [
                                'id' => 'id_melistoolcreator_content',
                                'melisKey' => 'melistoolcreator_content',
                                'name' => 'tr_melistoolcreator_content',
                            ],
                            'forward' => [
                                'module' => 'MelisToolCreator',
                                'controller' => 'ToolCreator',
                                'action' => 'render-tool-creator-content',
                                'jscallback' => '',
                                'jsdatas' => []
                            ],
                            'interface' => [
                                'melistoolcreator_steps' => [
                                    'conf' => [
                                        'id' => 'id_melistoolcreator_steps',
                                        'melisKey' => 'melistoolcreator_steps',
                                        'name' => 'tr_melistoolcreator_steps',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisToolCreator',
                                        'controller' => 'ToolCreator',
                                        'action' => 'render-tool-creator-steps',
                                        'jscallback' => '',
                                        'jsdatas' => []
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];