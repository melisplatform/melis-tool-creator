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

#TCPROPACTIONS

    public function saveAction()
    {
        $translator = $this->getServiceLocator()->get('translator');

        $success = 0;
        $textTitle = $translator->translate('tr_moduletpl_title');
        $textMessage = $translator->translate('tr_moduletpl_unable_to_save');
        $errors = [];

        $request = $this->getRequest();
        $id = null;
        $entryTitle = null;

        if ($request->isPost()){

            $this->getEventManager()->trigger('moduletpl_properties_save_start', $this, $request);

            // Result stored on session
            $container = new Container('moduletpl');
            $saveResult = $container['moduletpl-save-action'];

            if (!empty($saveResult['errors']))
                $errors = $saveResult['errors'];
            if (!empty($saveResult['data']))
                $data = $saveResult['data'];

            if (empty($errors)){
                $id = $data['id'];
                $success = 1;

                $entryTitle = $translator->translate('tr_moduletpl_common_entry_id').': '.$id;

                if ($request->getPost()['#TCKEY'] == 'add')
                    $textMessage = $translator->translate('tr_moduletpl_create_success');
                else
                    $textMessage = $translator->translate('tr_moduletpl_save_success');
            }

            // Unset temporary data on session
            unset($container['moduletpl-save-action']);
        }

        $response = [
            'success' => $success,
            'textTitle' => $textTitle,
            'textMessage' => $textMessage,
            'errors' => $errors
        ];

        if (!is_null($id)){
            $response['entryId'] = $id;
            $response['entryTitle'] = $entryTitle;
        }

        return new JsonModel($response);
    }

    public function savePropertiesAction()
    {
        $id = null;
        $entryTitle = null;
        $success = 0;
        $errors = [];

        $translator = $this->getServiceLocator()->get('translator');

        $request = $this->getRequest();
        $formData = $request->getPost()->toArray();

        $moduleTplForm = $this->getForm();

#TCFILEINPTPARAMS

        $moduleTplForm->setData($formData);

        if ($moduleTplForm->isValid()){

            $formData = $moduleTplForm->getData();

#TCFILEINPTDATA

#TCDATEINPTDATA

            foreach ($formData As $input => $val)
                if (empty($val) && !is_numeric($val))
                    $formData[$input] = null;

            if (is_numeric($formData['#TCKEY']))
                $id = $formData['#TCKEY'];
            else
                unset($formData['#TCKEY']);

            $moduleTplService = $this->getServiceLocator()->get('ModuleTplService');
            $res = $moduleTplService->saveItem($formData, $id);

            if (!is_null($res)){
                $id = $res;
                $success = 1;
            }
        }else{
            $errors = $moduleTplForm->getMessages();
            foreach ($errors as $keyError => $valueError){
                $errors[$keyError]['label'] = $translator->translate("tr_moduletpl_".$keyError);
            }
        }

        $result = [
            'success' => $success,
            'errors' => $errors,
            'data' => ['id' => $id],
        ];

        return new JsonModel($result);
    }

#TCFILEINPTFILTER

    public function deleteAction()
    {
        $request = $this->getRequest();
        $queryData = $request->getQuery()->toArray();

        if (!empty($queryData['id'])){
            $moduleTplService = $this->getServiceLocator()->get('ModuleTplService');
            $moduleTplService->deleteItem($queryData['id']);
        }
    }

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