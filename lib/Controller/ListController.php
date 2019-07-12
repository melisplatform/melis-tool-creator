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

        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $melisTool->setMelisToolKey('moduletpl', 'moduletpl_tools');

        $columns = $melisTool->getColumns();
        $translator = $this->getServiceLocator()->get('translator');
        $columns['actions'] = ['text' => $translator->translate('tr_moduletpl_common_table_column_action')];

        $view->tableColumns = $columns;
        $view->toolTable = $melisTool->getDataTableConfiguration('#moduleTplTableContent', true, false, ['order' => [[ 0, 'desc' ]]]);

        return $view;
    }

    public function getListAction()
    {
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $melisTool->setMelisToolKey('moduletpl', 'moduletpl_tools');

        $moduleTplTable = $this->getServiceLocator()->get('ModuleTplTable');

        $draw = 0;
        $dataCount = 0;
        $tableData = [];

        if($this->getRequest()->isPost()){

            // Get the locale used from meliscore session
            $container = new Container('meliscore');
            $langId = $container['melis-lang-id'];

            $draw = $this->getRequest()->getPost('draw');

            $start = $this->getRequest()->getPost('start');
            $length =  $this->getRequest()->getPost('length');

            $search = $this->getRequest()->getPost('search');
            $search = $search['value'];

            $selCol = $this->getRequest()->getPost('order');
            $colId = array_keys($melisTool->getColumns());
            $selCol = $colId[$selCol[0]['column']];

            $sortOrder = $this->getRequest()->getPost('order');
            $sortOrder = $sortOrder[0]['dir'];

            $tableData = $moduleTplTable->getList($start, $length, $melisTool->getSearchableColumns(), $search, $selCol, $sortOrder, $langId)->toArray();
            $dataCount = count($moduleTplTable->getList(null, null, $melisTool->getSearchableColumns(), null, null, 'ASC', $langId)->toArray());

            #TCCOREEVENTSERVICE

#TCDATAEMPTYFILTER

#TCBLOBDATAFILTER

#TCTABLECOLDISPLAYFILTER

        }

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

    public function renderTableActionEditAction()
    {
        return new ViewModel();
    }

    public function renderTableActionDeleteAction()
    {
        return new ViewModel();
    }

#TCMODALVIEWMODEL

    public function deleteItemAction()
    {
        $this->getEventManager()->trigger('moduletpl_delete_end', $this, $this->getRequest());

        return new JsonModel([
            'success' => true,
            'textTitle' => 'tr_moduletpl_delete_item',
            'textMessage' => 'tr_moduletpl_delete_success',
        ]);
    }
}