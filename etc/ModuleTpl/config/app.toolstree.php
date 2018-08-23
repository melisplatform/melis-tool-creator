<?php
return array(
    'plugins' => array(
        'meliscore' => array(
            'interface' => array(
                'meliscore_leftmenu' => array(
                    'interface' => array(
                        'meliscustom_toolstree_section' => array(
                            'interface' => array(
                                'moduletpl_conf' => array(
                                    'conf' => array(
                                        'id' => 'id_moduletpl_leftnemu',
                                        'melisKey' => 'moduletpl_leftnemu',
                                        'name' => 'tr_moduletpl_title',
                                        'icon' => 'fa fa-puzzle-piece',
                                    ),
                                    'interface' => array(
                                        'moduletpl_tool' => array(
                                            'conf' => array(
                                                'id' => 'id_moduletpl_tool',
                                                'melisKey' => 'moduletpl_tool',
                                                'name' => 'tr_moduletpl_title',
                                                'icon' => 'fa fa-puzzle-piece',
                                            ),
                                            'forward' => array(
                                                'module' => 'ModuleTpl',
                                                'controller' => 'Index',
                                                'action' => 'render-tool',
                                                'jscallback' => '',
                                                'jsdatas' => array()
                                            ),
                                            'interface' => array(
                                                'moduletpl_header' => array(
                                                    'conf' => array(
                                                        'id' => 'id_moduletpl_header',
                                                        'melisKey' => 'moduletpl_header',
                                                        'name' => 'tr_moduletpl_header',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'ModuleTpl',
                                                        'controller' => 'Index',
                                                        'action' => 'render-tool-header',
                                                        'jscallback' => '',
                                                        'jsdatas' => array()
                                                    ),
                                                ),
                                                'moduletpl_content' => array(
                                                    'conf' => array(
                                                        'id' => 'id_moduletpl_content',
                                                        'melisKey' => 'moduletpl_content',
                                                        'name' => 'tr_moduletpl_content',
                                                    ),
                                                    'forward' => array(
                                                        'module' => 'ModuleTpl',
                                                        'controller' => 'Index',
                                                        'action' => 'render-tool-content',
                                                        'jscallback' => '',
                                                        'jsdatas' => array()
                                                    ),
                                                    'interface' => array(
                                                        'moduletpl_modal' => array(
                                                            'conf' => array(
                                                                'id' => 'id_moduletpl_modal',
                                                                'melisKey' => 'moduletpl_modal',
                                                                'name' => 'tr_moduletpl_modal',
                                                            ),
                                                            'forward' => array(
                                                                'module' => 'ModuleTpl',
                                                                'controller' => 'Index',
                                                                'action' => 'render-modal-form',
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
                        )
                    )
                )
            )
        )
    )
);