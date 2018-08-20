<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
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
#TCTABLEACTIONCOLUMN
        $view->tableColumns = $columns;
        $view->toolTable = $melisTool->getDataTableConfiguration('#moduleTplTableContent');

        return $view;
    }

    public function getListAction()
    {
        $melisTool = $this->getServiceLocator()->get('MelisCoreTool');
        $melisTool->setMelisToolKey('moduletpl', 'moduletpl_tools');

        $moduleTplTable = $this->getServiceLocator()->get('ModuleTplTable');

        $draw = 0;
        $dataCount = 0;
        $tableData = array();

        if($this->getRequest()->isPost())
        {
            $draw = $this->getRequest()->getPost('draw');

            $sortOrder = $this->getRequest()->getPost('order');
            $sortOrder = $sortOrder[0]['dir'];

            $colId = array_keys($melisTool->getColumns());
            $selCol = $this->getRequest()->getPost('order');
            $selCol = $colId[$selCol[0]['column']];

            $start = $this->getRequest()->getPost('start');
            $length =  $this->getRequest()->getPost('length');

            $search = $this->getRequest()->getPost('search');
            $search = $search['value'];

            $dataCount = $moduleTplTable->getTotalData();

            $getData = $moduleTplTable->getPagedData(array(
                'where' => array(
                    'key' => true,
                    'value' => $search,
                ),
                'order' => array(
                    'key' => $selCol,
                    'dir' => $sortOrder,
                ),
                'start' => $start,
                'limit' => $length,
                'columns' => $melisTool->getSearchableColumns(),
                'date_filter' => array(),
            ));

            // store fetched Object Data into array so we can apply any string modifications
            $tableData = $getData->toArray();
        }

        return new JsonModel(array(
            'draw' => (int) $draw,
            'recordsTotal' => $dataCount,
            'recordsFiltered' =>  $moduleTplTable->getTotalFiltered(),
            'data' => $tableData,
        ));
    }

    public function renderTableFilterLimitAction()
    {
        $view = new ViewModel();
        return $view;
    }

    public function renderTableFilterSearchAction()
    {
        $view = new ViewModel();
        return $view;
    }

    public function renderTableFilterRefreshAction()
    {
        $view = new ViewModel();
        return $view;
    }

    public function renderModalFormAction()
    {
        $view = new ViewModel();
        $moduleTplForm = $this->getForm();

#TCFORMMODAL

        $view->moduleTplForm = $moduleTplForm;
        return $view;
    }

    public function saveAction()
    {
        $translator = $this->getServiceLocator()->get('translator');

        $success = false;
        $textTitle = $translator->translate('tr_moduletpl_title');
        $textMessage = $translator->translate('tr_moduletpl_unable_to_save');
        $errors = array();

        $request = $this->getRequest();
        $id = null;

        if ($request->isPost()){

            $postData = $request->getPost()->toArray();

            $moduleTplForm = $this->getForm();

            $moduleTplForm->setData($postData);

            if ($moduleTplForm->isValid()){

                $formData = $moduleTplForm->getData();

#TCSAVINGITEM
            }else{

                $errors = $moduleTplForm->getMessages();

                foreach ($errors as $keyError => $valueError){
                    $errors[$keyError]['label'] = $translator->translate("tr_moduletpl_".$keyError);
                }
            }
        }

        $response = array(
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors
        );

        return new JsonModel($response);
    }

    private function getForm()
    {
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('moduletpl/tools/moduletpl_tools/forms/moduletpl_generic_form','moduletpl_generic_form');

        // Factoring Calendar event and pass to view
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        return $form;
    }

#TCEDITDELETEFUNCTIONACTION
}