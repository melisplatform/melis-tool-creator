<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'plugins' => [
        'moduletpl' => [
            'conf' => [
                'id' => '',
                'name' => 'tr_moduletpl_tool_name',
                'rightsDisplay' => 'none',
            ],
            'ressources' => [
                'js' => [
                    '/ModuleTpl/js/tool.js'
                ],
                'css' => [
                    '/ModuleTpl/css/tool.css'
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
                        '/ModuleTpl/build/css/bundle.css',
                    ],
                    'js' => [
                        '/ModuleTpl/build/js/bundle.js',
                    ]
                ]
            ],
            'datas' => [],
        ]
    ]
];