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
                        'meliscore_toolstree_section' => [
                            'interface' => [
                                'meliscore_tool_system_config' => [
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
                        ]
                    ]
                ]
            ]
        ]
    ]
];