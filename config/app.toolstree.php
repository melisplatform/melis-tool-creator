<?php
    return array(
        'plugins' => array(
            'meliscore' => array(
                'interface' => array(
                    'meliscore_leftmenu' => array(
                        'interface' => array(
                            'meliscore_toolstree_section' => array(
                                'interface' => array(
                                    'meliscore_tool_system_config' => array(
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