<?php

return array(
    'plugins' => array(
        'meliscore' => array(
            'datas' => array(),
            'interface' => array(
                /*'meliscore_leftmenu' => array(
                    'interface' => array(
                        'meliscore_toolstree' => array(
                            'interface' => array(
                                'meliscore_tool_system_config' => array(
                                    'interface' => array(
                                        'melistoolcreator_left_menu' => array(
                                            'conf' => array(
                                                'type' => 'melistoolcreator/interface/melistoolcreator_conf'
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    ),
                ),*/
            ),
        ),
        'melistoolcreator' => array(
            'conf' => array(
                'id' => '',
                'name' => 'tr_melistool_toolcreator',
                'rightsDisplay' => 'none',
            ),
            'ressources' => array(
                'js' => array(
                    '/MelisToolCreator/js/tool-creator.js',
                ),
                'css' => array(
                    '/MelisToolCreator/css/style.css',
                ),
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
            ),
            'datas' => array(
                'steps' => array(
                    'melistoolcreator_step1' => array(
                        'name' => 'tr_melistoolcreator_module',
                        'icon' => 'fa-puzzle-piece'
                    ),
                    'melistoolcreator_step2' => array(
                        'name' => 'tr_melistoolcreator_texts',
                        'icon' => 'fa-language'
                    ),
                    'melistoolcreator_step3' => array(
                        'name' => 'tr_melistoolcreator_database',
                        'icon' => 'fa-database'
                    ),
                    'melistoolcreator_step4' => array(
                        'name' => 'tr_melistoolcreator_table_cols',
                        'icon' => 'fa-table'
                    ),
                    'melistoolcreator_step5' => array(
                        'name' => 'tr_melistoolcreator_add_update_form',
                        'icon' => 'fa-list-alt'
                    ),
                    'melistoolcreator_step6' => array(
                        'name' => 'tr_melistoolcreator_cols_translations',
                        'icon' => 'fa-language'
                    ),
                    'melistoolcreator_step7' => array(
                        'name' => 'tr_melistoolcreator_summary',
                        'icon' => 'fa-list'
                    ),
                    'melistoolcreator_step8' => array(
                        'name' => 'tr_melistoolcreator_finalization',
                        'icon' => 'fa-cogs'
                    )
                )
            ),
            'interface' => array(
                'melistoolcreator_conf' => array(
                    'conf' => array(
                        'id' => 'id_melistoolcreator_tool',
                        'melisKey' => 'melistoolcreator_tool',
                        'name' => 'tr_melistoolcreator',
                        'icon' => 'fa fa-magic',
                    ),
                    'forward' => array(
                        'module' => 'MelisToolCreator',
                        'controller' => 'ToolCreator',
                        'action' => 'render-tool-creator',
                        'jscallback' => '',
                        'jsdatas' => array()
                    ),
                    'interface' => array(
                        'melistoolcreator_header' => array(
                            'conf' => array(
                                'id' => 'id_melistoolcreator_header',
                                'melisKey' => 'melistoolcreator_header',
                                'name' => 'tr_melistoolcreator_header',
                            ),
                            'forward' => array(
                                'module' => 'MelisToolCreator',
                                'controller' => 'ToolCreator',
                                'action' => 'render-tool-creator-header',
                                'jscallback' => '',
                                'jsdatas' => array()
                            ),
                        ),
                        'melistoolcreator_content' => array(
                            'conf' => array(
                                'id' => 'id_melistoolcreator_content',
                                'melisKey' => 'melistoolcreator_content',
                                'name' => 'tr_melistoolcreator_content',
                            ),
                            'forward' => array(
                                'module' => 'MelisToolCreator',
                                'controller' => 'ToolCreator',
                                'action' => 'render-tool-creator-content',
                                'jscallback' => '',
                                'jsdatas' => array()
                            ),
                            'interface' => array(
                                'melistoolcreator_steps' => array(
                                    'conf' => array(
                                        'id' => 'id_melistoolcreator_steps',
                                        'melisKey' => 'melistoolcreator_steps',
                                        'name' => 'tr_melistoolcreator_steps',
                                    ),
                                    'forward' => array(
                                        'module' => 'MelisToolCreator',
                                        'controller' => 'ToolCreator',
                                        'action' => 'render-tool-creator-steps',
                                        'jscallback' => '',
                                        'jsdatas' => array()
                                    ),
                                )
                            )
                        )
                    )
                )
            )
        )
    )
);