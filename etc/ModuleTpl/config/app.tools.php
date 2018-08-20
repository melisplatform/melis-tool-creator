<?php

return array(
    'plugins' => array(
        'moduletpl' => array(
            'tools' => array(
                'moduletpl_tools' => array(
                    'conf' => array(
                        'title' => 'tr_moduletpl_templates',
                        'id' => 'id_moduletpl_templates',
                    ),
                    'table' => array(
                        // table ID
                        'target' => '#tableToolModuleTpl',
                        'ajaxUrl' => '/melis/ModuleTpl/Index/getList',
                        'dataFunction' => '',
                        'ajaxCallback' => '',
                        'filters' => array(
                            'left' => array(
                                'moduletpl-tbl-filter-limit' => array(
                                    'module' => 'ModuleTpl',
                                    'controller' => 'Index',
                                    'action' => 'render-table-filter-limit',
                                ),
                            ),
                            'center' => array(
                                'moduletpl-tbl-filter-search' => array(
                                    'module' => 'ModuleTpl',
                                    'controller' => 'Index',
                                    'action' => 'render-table-filter-search',
                                ),
                            ),
                            'right' => array(
                                'moduletpl-tbl-filter-refresh' => array(
                                    'module' => 'ModuleTpl',
                                    'controller' => 'Index',
                                    'action' => 'render-table-filter-refresh',
                                ),
                            ),
                        ),
                        'columns' => array(
#TCTABLECOLUMNS
                        ),
                        // define what columns can be used in searching
                        'searchables' => array(
#TCTABLESEARCHCOLUMNS
                        ),
                        'actionButtons' => array(
#TCTABLEACTIONBUTTONS
                        )
                    ),
#TCFORMELEMENTS
                )
            )
        )
    )
);