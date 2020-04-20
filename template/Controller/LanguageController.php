<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Controller;

use Laminas\Stdlib\ArrayUtils;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use MelisCore\Controller\AbstractActionController;

class LanguageController extends AbstractActionController
{
    public function renderLanguageFormAction()
    {
        $langTable = $this->getServiceManager()->get('ModuleTplLangTable');
        $cmsLang = $this->getServiceManager()->get('MelisEngineTableCmsLang');
        $languages = $cmsLang->fetchAll()->toArray();

        $view = new ViewModel();
        $id = $this->params()->fromQuery('id', 'add');
        $view->id = $id;

        $langForm = [];
        foreach ($languages As $key => $lang){
            $form = $this->getForm();

            $form->setAttribute('class', $id. '_' . $form->getAttribute('name'));
            $form->get('#TCKEYLANGID')->setValue($lang['lang_cms_id']);

            $data = $langTable->getLangByFKID($id, $lang['lang_cms_id'])->current();
            if (!empty($data))
                $form->bind($data);

            $langForm[$lang['lang_cms_locale']] = $form;

            // Language label
            $languages[$key]['lang_label'] = $this->langLabel($lang['lang_cms_locale'], $lang['lang_cms_name']);
        }

        $view->languages = $languages;
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

        foreach ($postData['language'] As $key => $formData){

            // Foreign key from Param
            $fkId = $this->params()->fromRoute('id');
            $fkSuccess = $this->params()->fromRoute('success');

            $langForm = $this->getForm();

#TCFILEINPTPARAMS

            $langForm->setData($formData);

            if ($langForm->isValid()){

                if (!empty($fkId) && $fkSuccess){
                    $id = null;
                    $formData = $langForm->getData();

                    if (!empty($formData['#TCFKEYID']))
                        $id = $formData['#TCFKEYID'];
                    else
                        unset($formData['#TCFKEYID']);

#TCFILEINPTDATA

#TCDATEINPTDATA

                    foreach ($formData As $input => $val)
                        if (empty($val) && !is_numeric($val))
                            $formData[$input] = null;

                    // Assign foreign key value
                    $formData['#TCKEYPRIID'] = $fkId;

                    $langTable = $this->getServiceManager()->get('ModuleTplService');
                    $langTable->saveLang($formData, $id);
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

#TCFILEINPTFILTER

    public function deleteAction()
    {
        $request = $this->getRequest();
        $queryData = $request->getQuery()->toArray();

        if (!empty($queryData['id'])){
            $myToolTabLangTable = $this->getServiceManager()->get('ModuleTplService');
            $myToolTabLangTable->deleteLang($queryData['id']);
        }
    }

    private function getForm()
    {
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('moduletpl/tools/moduletpl_tools/forms/moduletpl_language_form', 'moduletpl_language_form');

        // Factoring ModuleTpl event and pass to view
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);

        return $form;
    }

    /**
     * This method return the language name with flag image
     * if exist
     *
     * @param $locale
     * @param $langName
     * @return string
     */
    private function langLabel($locale, $langName)
    {
        $langLabel = '<span>'. $langName .'</span>';

        $moduleSvc = $this->getServiceManager()->get('ModulesService');
        if (file_exists($moduleSvc->getModulePath('MelisCms').'/public/images/lang-flags/'.$locale.'.png')){
            $langLabel .= '<span class="pull-right"><img src="/MelisCms/images/lang-flags/'.$locale.'.png"></span>';
        }

        return $langLabel;
    }
}