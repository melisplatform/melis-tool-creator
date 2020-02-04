<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ListController extends AbstractActionController
{
    public function renderToolAction()
    {
        $view = new ViewModel();
        return $view;
    }

    public function renderToolHeaderAction()
    {
        $view = new ViewModel();
        return $view;
    }

    public function renderToolContentAction()
    {
        $view = new ViewModel();

        return $view;
    }

    public function getListAction()
    {
        $draw = 0;
        $dataCount = 0;
        $tableData = [];

        return new JsonModel([
            'draw' => (int) $draw,
            'recordsTotal' => count($tableData),
            'recordsFiltered' =>  $dataCount,
            'data' => $tableData,
        ]);
    }

    public function renderTableFilterLimitAction()
    {
        return new ViewModel();
    }

    public function renderTableFilterSearchAction()
    {
        return new ViewModel();
    }

    public function renderTableFilterRefreshAction()
    {
        return new ViewModel();
    }
}