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
        $translator = $this->getServiceLocator()->get('translator');
        $columns['actions'] = array('text' => $translator->translate('tr_moduletpl_common_table_column_action'));

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

        $id = $this->params()->fromQuery('id', null);
        $view->id = $id;

        if (is_null($id)){
            $moduleTplForm->get('#TCKEY')->setAttribute("disabled", "disabled");
        }else{
            $moduleTplForm->get('#TCKEY')->setAttribute("readonly", "readonly");
            $moduleTplTable = $this->getServiceLocator()->get('ModuleTplTable');
            $data = $moduleTplTable->getEntryById($id)->current();
            $moduleTplForm->bind($data);
        }

        $view->moduleTplForm = $moduleTplForm;
        return $view;
    }

    public function saveAction()
    {
        $translator = $this->getServiceLocator()->get('translator');

        $success = 0;
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

                $pk = null;
                if (!empty($formData['#TCKEY'])){
                    $pk = $formData['#TCKEY'];
                    unset($formData['#TCKEY']);
                }

                $moduleTplTable = $this->getServiceLocator()->get('ModuleTplTable');
                $id = $moduleTplTable->save($formData, $pk);

                if ($id){
                    $success = 1;
                    $textMessage = $translator->translate('tr_moduletpl_save_success');
                }
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
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('moduletpl/tools/moduletpl_tools/forms/moduletpl_property_form','moduletpl_property_form');

        // Factoring Calendar event and pass to view
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        return $form;
    }

    public function renderTableActionEditAction()
    {
        $view = new ViewModel();
        return $view;
    }

    public function renderTableActionDeleteAction()
    {
        $view = new ViewModel();
        return $view;
    }

    public function deleteItemAction()
    {
        $id = $this->params()->fromQuery('id', null);
        $moduleTplTable = $this->getServiceLocator()->get('ModuleTplTable');
        $moduleTplTable->deleteById($id);

        return new JsonModel(array('success' => true));
    }
}