<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class LanguageController extends AbstractActionController
{
    public function renderLanguageFormAction()
    {
        $myToolTabLangTable = $this->getServiceLocator()->get('ModuleTplLangTable');
        $cmsLang = $this->getServiceLocator()->get('MelisEngineTableCmsLang');
        $languages = $cmsLang->fetchAll()->toArray();

        $view = new ViewModel();
        $id = $this->params()->fromQuery('id', 'add');
        $view->id = $id;
        $view->languages = $languages;

        $langForm = [];
        foreach ($languages As $lang){
            $form = $this->getForm();

            $form->setAttribute('class', $id. '_' . $form->getAttribute('name'));
            $form->get('#TCKEYLANGID')->setValue($lang['lang_cms_id']);

            $data = $myToolTabLangTable->getLangByFKID($id, $lang['lang_cms_id'])->current();
            if (!empty($data))
                $form->bind($data);

            $langForm[$lang['lang_cms_locale']] = $form;
        }

        $view->langForm = $langForm;

        return $view;
    }

    public function saveLanguageAction()
    {
        $entryTitle = null;
        $success = 0;
        $errors = [];

        $request = $this->getRequest();
        $postData = $request->getPost()->toArray();

        foreach ($postData['language'] As $form){

            // Foreign key from Param
            $fkId = $this->params()->fromRoute('id');
            $fkSuccess = $this->params()->fromRoute('success');

            $langForm = $this->getForm();
            $langForm->setData($form);

            if ($langForm->isValid()){

                if (!empty($fkId) && $fkSuccess){
                    $id = null;
                    if (!empty($form['#TCFKEYID']))
                        $id = $form['#TCFKEYID'];

                    $formData = $langForm->getData();

                    // Assign foreign key value
                    $formData['#TCKEYPRIID'] = $fkId;

                    $myToolTabLangTable = $this->getServiceLocator()->get('ModuleTplLangTable');
                    $myToolTabLangTable->save($formData, $id);
                }

                $success = 1;
            }else{
                $errors = ArrayUtils::merge($errors, $langForm->getMessages());
            }
        }

        if ($success)
            $errors = [];

        $result = [
            'success' => $success,
            'errors' => $errors
        ];

        return new JsonModel($result);
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $queryData = $request->getQuery()->toArray();

        if (!empty($queryData['id'])){
            $myToolTabLangTable = $this->getServiceLocator()->get('ModuleTplLangTable');
            $myToolTabLangTable->deleteByFkId($queryData['id']);
        }
    }

    private function getForm()
    {
        $melisCoreConfig = $this->serviceLocator->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('moduletpl/tools/moduletpl_tools/forms/moduletpl_language_form', 'moduletpl_language_form');

        // Factoring ModuleTpl event and pass to view
        $factory = new \Zend\Form\Factory();
        $formElements = $this->serviceLocator->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        return $form;
    }
}