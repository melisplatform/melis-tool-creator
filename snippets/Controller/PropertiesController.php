<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;

class PropertiesController extends AbstractActionController
{
    public function renderToolAction()
    {
        $id = $this->params()->fromQuery('id', 'add');
        $view = new ViewModel();
        $view->id = $id;
        return $view;
    }

    public function renderToolHeaderAction()
    {
        $view = new ViewModel();
        $id = $this->params()->fromQuery('id', 'add');

        $translator = $this->getServiceLocator()->get('translator');
        $title = $translator->translate('tr_moduletpl_common_button_add');

        if (is_numeric($id)){
            $title = $translator->translate('tr_moduletpl_title').' / '.$id;
        }

        $view->id = $id;
        $view->title = $title;
        return $view;
    }

    public function renderToolContentAction()
    {
        $view = new ViewModel();
        $id = $this->params()->fromQuery('id', 'add');
        $view->id = $id;
        return $view;
    }

    public function renderToolMainContentAction()
    {
        $view = new ViewModel();
        $moduleTplForm = $this->getForm();

        $id = $this->params()->fromQuery('id', 'add');
        $view->id = $id;

        $moduleTplForm->setAttribute('id', $id. '_' . $moduleTplForm->getAttribute('id'));

        if ($id != 'add'){
            $moduleTplTable = $this->getServiceLocator()->get('ModuleTplTable');
            $data = $moduleTplTable->getEntryById($id)->current();
            $moduleTplForm->bind($data);
        }

        $view->moduleTplForm = $moduleTplForm;

        return $view;
    }

#TCSAVEACTION

    private function getForm()
    {
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('moduletpl/tools/moduletpl_tools/forms/moduletpl_property_form', 'moduletpl_property_form');

        // Factoring ModuleTpl event and pass to view
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        return $form;
    }
}