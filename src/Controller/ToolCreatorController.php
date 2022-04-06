<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisToolCreator\Controller;

use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Session\Container;
use Laminas\Stdlib\ArrayUtils;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Metadata\Metadata;
use MelisCore\Controller\MelisAbstractActionController;
use MelisCore\Service\MelisGeneralService;
use MelisToolCreator\Service\MelisToolCreatorService;

class ToolCreatorController extends MelisAbstractActionController
{

    private $cacheKey = 'toolcreator_database_tables';
    private $cacheConfig = 'toolcreator_database';

    /**
     * Render tool creator
     * @return ViewModel
     */
    public function renderToolCreatorAction()
    {
        $view = new ViewModel();

        // Initializing the Tool creator session container
        $container = new Container('melistoolcreator');
        $container['melis-toolcreator'] = [];

        return $view;
    }

    /**
     * Render tool creator header
     * @return ViewModel
     */
    public function renderToolCreatorHeaderAction()
    {
        return new ViewModel();
    }

    /**
     * Render tool creator content
     * @return ViewModel
     */
    public function renderToolCreatorContentAction()
    {    
        $view = new ViewModel();

        /**
         * Checking file permission to file and directories needed to create and activate the tool
         */
        $filePermissionErr = [];
        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/../config/melis.module.load.php'))
            $filePermissionErr[] = 'tr_melistoolcreator_fp_config';

        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/../cache'))
            $filePermissionErr[] = 'tr_melistoolcreator_fp_cache';

        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/../module'))
            $filePermissionErr[] = 'tr_melistoolcreator_fp_module';
        
        // Database table caching
        $this->getDBTablesCached();

        $melisEngineCacheSystem = $this->getServiceManager()->get('MelisEngineCacheSystem');
        $dbCached = $melisEngineCacheSystem->getCacheByKey($this->cacheKey, $this->cacheConfig, true);

        if (!$dbCached || is_null($dbCached))
            $filePermissionErr[] = 'tr_melistoolcreator_fp_db_cached_empty';

        if (!empty($filePermissionErr)){
            $view->fPErr = $filePermissionErr;
            return $view;
        }

        $config = $this->getServiceManager()->get('config');

        // Retrieving steps form config
        $stepsConfig = $config['plugins']['melistoolcreator']['datas']['steps'];


        $view->stepsConfig = $stepsConfig;
        return $view;
    }

    /**
     * This method render the steps of the tool
     * this will call dynamically the requested step ViewModel
     * @return ViewModel
     */
    public function renderToolCreatorStepsAction()
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator'];

        $view = new ViewModel();

        // The steps requested
        $curStep = $this->params()->fromPost('curStep', 1);
        $nxtStep = $this->params()->fromPost('nxtStep', 1);
        $validate = $this->params()->fromPost('validate', false);

        // Current viewModel
        $viewStp = new ViewModel();
        $viewStp->id = 'melistoolcreator_step'.$curStep;
        $viewStp->setTemplate('melis-tool-creator/step'.$curStep);

        if ($validate){
            $request = $this->getRequest();
            $post = $request->getPost();
            if (!empty($post['step-form']['tcf-tool-type'])){
                if ($post['step-form']['tcf-tool-type'] == 'iframe'){
                    $nxtStep = 7;
                }
            }else{
                if ($tcfDbTbl['step1']['tcf-tool-type'] == 'blank' && $nxtStep == 3){
                    $nxtStep = 7;
                }
            }
        }

        if ($curStep == 7 && $nxtStep == 6){
            if (!empty($tcfDbTbl['step1']['tcf-tool-type']))
                if ($tcfDbTbl['step1']['tcf-tool-type'] == 'iframe')
                    $nxtStep = 1;
                elseif ($tcfDbTbl['step1']['tcf-tool-type'] == 'blank')
                    $nxtStep = 2;
        }

        /**
         * This will try to get the requested view
         * with the current view and the flag for validation
         */
        $stpFunction = 'renderStep'.$curStep;

        $viewVars = $this->$stpFunction($viewStp, $validate);
        /**
         * Checking if the view returns with error this view will display to show the errors message(s)
         * else this will call the next step and return to render
         */
        if (!isset($viewVars->hasError)){
            $viewStp->id = 'melistoolcreator_step'.$nxtStep;
            $viewStp->setTemplate('melis-tool-creator/step'.$nxtStep);
            $stpFunction = 'renderStep'.$nxtStep;

            $viewStp = $this->$stpFunction($viewStp);
        }

        $viewRender = $this->getServiceManager()->get('ViewRenderer');

        if ($validate || ( $curStep > $nxtStep)){

            // Retrieving steps form config
            $config = $this->getServiceManager()->get('config');
            $stepsConfig = $config['plugins']['melistoolcreator']['datas']['steps'];

            // translating error modal title
            $translator = $this->getServiceManager()->get('translator');

            $results = [
                'textTitle' => $translator->translate($stepsConfig['melistoolcreator_step'.$curStep]['name']),
                'html' => $viewRender->render($viewStp) // Sending the step view without container
            ];

            if (isset($viewVars->hasError) && !isset($viewVars->skipErrModal) && !isset($viewVars->finalized)){
                $results['errors'] = $translator->translate($stepsConfig['melistoolcreator_step'.$nxtStep]['name']);

                // Translating error input name
                $stepErrors = [];
                foreach ($viewVars->hasError As $key => $val){
                    $stepErrors[$key] = $val;
                    $colLabel = ($translator->translate('tr_melistoolcreator_'.$key) != 'tr_melistoolcreator_'.$key) ? $translator->translate('tr_melistoolcreator_'.$key) : $key;
                    // Removing prefix for language table columns
                    $stepErrors[$key]['label'] = str_replace('tclangtblcol_', '', $colLabel);
                }

                $results['errors'] = $stepErrors;
                $results['textMessage'] = $translator->translate('tr_melistoolcreator_err_message');
            }

            return new JsonModel($results);
        }

        // Rendering the result view and attach to the container
        $view->step = $viewRender->render($viewStp);

        return $view;
    }

    /**
     * This method manage the first step of the tool creator
     * This steps manage the name of the Tool/module to be created
     *
     * @param ViewModel $viewStp - viewmodel of the step
     * @param bool $validate - flag for validation
     * @return ViewModel
     */
    private function renderStep1($viewStp, $validate = false)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');

        // Step form fields
        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step1_form', 'melistoolcreator_step1_form');
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step1Form = $factory->createForm($appConfigForm);

        // Setting the form data stored in session
        if (!empty($container['melis-toolcreator']['step1'])){
            $step1Form->setData($container['melis-toolcreator']['step1']);
        }

        // Form validation
        $request = $this->getRequest();
        if ($validate){
            $formData = $request->getPost()->toArray();

            $step1Form->setData($formData['step-form']);

            if ($formData['step-form']['tcf-tool-type'] != 'iframe'){
                $step1Form->getInputFilter()->remove('tcf-tool-iframe-url');
            }

            if(!$step1Form->isValid()){
                // adding a variable to viewmodel to flag an error
                $viewStp->hasError = $step1Form->getMessages();
            }else{
                /**
                 * Validating the module entered if its already existing on the platform
                 */
                $modulesSvc = $this->getServiceManager()->get('ModulesService');
                $existingModules = array_merge($modulesSvc->getModulePlugins(), \MelisCore\MelisModuleManager::getModules());

                $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
                $targetModuleName = $toolCreatorSrv->generateModuleNameCase($formData['step-form']['tcf-name']);

                if (in_array($targetModuleName, $existingModules)){

                    // Adding error message to module input
                    $translator = $this->getServiceManager()->get('translator');
                    $step1Form->get('tcf-name')->setMessages([
                        'ModuleExist' => sprintf($translator->translate('tr_melistoolcreator_err_module_exist'), $formData['step-form']['tcf-name'])
                    ]);

                    // adding a variable to viewmodel to flag an error
                    $viewStp->hasError = $step1Form->getMessages();
                }else{
                    $container['melis-toolcreator']['step1'] = $step1Form->getData();
                }
            }
        }

        $config = $this->getServiceManager()->get('MelisCoreConfig');

        $leftMenuConfig = $config->getItem('meliscore_leftmenu');

        $viewStp->leftMenuConfig = $leftMenuConfig;

        $viewStp->step1Form = $step1Form;

        return $viewStp;
    }

    /**
     * This method manage the Second step of the tool creator
     * This methos manage the text translation of the title and description
     * of the Tool to be created
     *
     * @param ViewModel $viewStp - viewmodel of the step
     * @param bool $validate - flag for validation
     * @return ViewModel
     */
    private function renderStep2($viewStp, $validate = false)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');

        // Meliscore languages
        $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
        $languages = $coreLang->fetchAll()->toArray();

        // Step form fields
        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step2_form', 'melistoolcreator_step2_form');
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);

        $request = $this->getRequest();
        $formData = $request->getPost()->toArray();

        $step2Form = [];

        $hasErrorForm = [];
        $hasValidForm = false;
        $inputHasValue = [];

        foreach ($languages As $key => $lang){
            // Generating form for each language
            $step2Formtmp = $factory->createForm($appConfigForm);

            if (!empty($container['melis-toolcreator']['step2'])){
                if (!empty($container['melis-toolcreator']['step2'][$lang['lang_locale']])){
                    $step2Formtmp->setData($container['melis-toolcreator']['step2'][$lang['lang_locale']]);
                }
            }

            $step2Formtmp->get('tcf-lang-local')->setValue($lang['lang_locale']);

            if ($validate){
                foreach ($formData['step-form'] As $val){
                    if ($val['tcf-lang-local'] == $lang['lang_locale']){
                        $step2Formtmp->setData($val);
                    }
                }

                /**
                 * If one of the languages form validated with no errors this will return true
                 * else all language form has a errors and return
                 */
                if($step2Formtmp->isValid()){
                    $hasValidForm = true;

                }else{
                    if (empty($step2Formtmp))
                        $hasErrorForm = $step2Formtmp->getMessages();
                    else
                        $hasErrorForm = ArrayUtils::merge($hasErrorForm, $step2Formtmp->getMessages());
                }

                // Getting input with data for Error preparation
                foreach ($step2Formtmp->getData() As $key => $kVal){
                    if (!empty($kVal) && !in_array($key, $inputHasValue))
                        array_push($inputHasValue, $key);
                }

                $container['melis-toolcreator']['step2'][$lang['lang_locale']] = $step2Formtmp->getData();
            }

            // Adding language form
            $step2Form[$lang['lang_locale']] = $step2Formtmp;

            // Language label
            $languages[$key]['lang_label'] = $this->langLabel($lang['lang_locale'], $lang['lang_name']);
        }

        // Removing input with data on any Form fieldset
        if (!empty($inputHasValue)){
            foreach ($inputHasValue As $key => $val)
                if (isset($hasErrorForm[$val]))
                    unset($hasErrorForm[$val]);
        }

        // adding a variable to viewmodel to flag an error
        if (!empty($hasErrorForm)&&!$hasValidForm)
            $viewStp->hasError = $hasErrorForm;

        $viewStp->step2Form = $step2Form;
        $viewStp->languages = $languages;
        return $viewStp;
    }

    /**
     * This method manage the Third step of the tool creator
     * This method manage the selection of Database table that will
     * use to the Tool to be craeted
     *
     * @param ViewModel $viewStp - viewmodel of the step
     * @param bool $validate - flag for validation
     * @return ViewModel
     */
    private function renderStep3($viewStp, $validate = false)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');

        $toolType = $container['melis-toolcreator']['step1']['tcf-tool-edit-type'];
        $viewStp->toolType = $toolType;

        /**
         * ========================================================
         * ============= Primary database table ===================
         * ========================================================
         */
        $dbTablesHtml = $this->forward()->dispatch(
            'MelisToolCreator\Controller\ToolCreator',
            [
                'action' => 'renderStep3PrimaryDbTables',
                'validate' => $validate,
            ]
        )->getVariables();

        if (isset($dbTablesHtml['hasError'])){
            $viewStp->hasError = $dbTablesHtml['hasError'];
            $viewStp->skipErrModal = true;
        }

        if (!empty($dbTablesHtml['selectedTbl'])) {
            /**
             * If this step has selected table this will
             * get the view of the column details to show directly
             * to the step view
             */
            $tblColsHtml = $this->forward()->dispatch(
                'MelisToolCreator\Controller\ToolCreator',
                [
                    'action' => 'renderStep3TableColumns',
                    'tableName' => $dbTablesHtml['selectedTbl']
                ]
            )->getVariables();

            $viewStp->tblCols = $tblColsHtml['html'];
        }

        $viewStp->dbTables = $dbTablesHtml['html'];

        /**
         * ========================================================
         * ============= Language database table ==================
         * ========================================================
         */
        $dbLangTablesHtml = $this->forward()->dispatch(
            'MelisToolCreator\Controller\ToolCreator',
            [
                'action' => 'renderStep3LanguageDbTables',
                'priTableName' => $dbTablesHtml['selectedTbl'],
                'validate' => $validate,
            ]
        )->getVariables();

        if ($dbLangTablesHtml['hasLanguage']){

            // Merging error messages
            if (isset($dbLangTablesHtml['hasError'])){
                if (!empty($dbTablesHtml['hasError'])){
                    $viewStp->hasError = ArrayUtils::merge($dbTablesHtml['hasError'], $dbLangTablesHtml['hasError']);
                }else{
                    $viewStp->hasError = $dbLangTablesHtml['hasError'];
                }
                $viewStp->skipErrModal = true;
            }

            if (!empty($dbLangTablesHtml['selectedTbl'])){
                /**
                 * If this step has selected table this will
                 * get the view of the column details to show directly
                 * to the step view
                 */
                $tblColsHtml = $this->forward()->dispatch(
                    'MelisToolCreator\Controller\ToolCreator',
                    [
                        'action' => 'renderStep3TableColumns',
                        'type' => 'language-db',
                        'tableName' => $dbLangTablesHtml['selectedTbl'],
                        'ptfk' => $dbLangTablesHtml['ptfk'],
                        'ltfk' => $dbLangTablesHtml['ltfk'],
                    ]
                )->getVariables();

                $viewStp->langTblCols = $tblColsHtml['html'];
            }

        }

        $viewStp->hasLanguage = $dbLangTablesHtml['hasLanguage'];
        $viewStp->dbLangTables = $dbLangTablesHtml['html'];

        return $viewStp;
    }

    /**
     * This methos render the Database tables list
     * @return JsonModel
     */
    public function renderStep3PrimaryDbTablesAction()
    {
        $results = [];

        // Tool creator session container
        $container = new Container('melistoolcreator');

        $view = new ViewModel();
        $view->setTemplate('melis-tool-creator/step3-db_tble');
        $view->type = 'primary-db';

        $selectedTbl = null;
        if (!empty($container['melis-toolcreator']['step3']['tcf-db-table']))
            $selectedTbl = $container['melis-toolcreator']['step3']['tcf-db-table'];

        $validate = $this->params()->fromRoute('validate', false);
        $reloadDbCache = $this->params()->fromPost('reloadDbTblCached', false);

        // Step form fields
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step3_form/melistoolcreator_step3_primary_tbl', 'melistoolcreator_step3_primary_tbl');
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step3Form = $factory->createForm($appConfigForm);

        if (!empty($container['melis-toolcreator']['step3']['tcf-db-table'])) {
            $step3Form->setData($container['melis-toolcreator']['step3']);
        }elseif (!empty($selectedTbl)){
            $step3Form->get('tcf-db-table')->setValue($selectedTbl);
        }

        if ($validate){

            $request = $this->getRequest();
            $formData = $request->getPost()->toArray();

            // if Module type is Tabulation
            // the form submitted with language db details also
            if (empty($formData['step-form'][0])){
                $step3Form->setData($formData['step-form']);
            }else{
                $step3Form->setData($formData['step-form'][0]);
            }

            if(!$step3Form->isValid()){
                // adding a variable to ViewModel to flag an error
                $results['hasError'] = $step3Form->getMessages();
            }else{
                $formData = $step3Form->getData();

                // Describe query to get the details of the Database table
                /**
                 * @var MelisToolCreatorService $toolCreatorSrv
                 */
                $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
                $table = $toolCreatorSrv->describeTable($formData['tcf-db-table']);

                $hasPrimaryKey = false;
                $hasPrimaryKeyIdAI = false;
                $notNullableBlobType = [];
                foreach ($table As $tbl){

                    if ($tbl['Key'] == 'PRI'){
                        $hasPrimaryKey = true;
                        if ($tbl['Extra'] == 'auto_increment')
                            $hasPrimaryKeyIdAI = true;
                    }

                    if (!is_bool(strpos($tbl['Type'], 'blob')))
                        if ($tbl['Null'] == 'NO'){
                            array_push($notNullableBlobType, '<b>'. $tbl['Field'] .'</b>');
                        }
                }

                if ($hasPrimaryKey && $hasPrimaryKeyIdAI && empty($notNullableBlobType)){
                    if (!empty($container['melis-toolcreator']['step3']['tcf-db-table'])){
                        /**
                         * If the selected table is changed,
                         * this will reset the related data store in session
                         */
                        if ($formData['tcf-db-table'] != $container['melis-toolcreator']['step3']['tcf-db-table']){
                            // Reset step 4, 5 and 6
                            unset($container['melis-toolcreator']['step4']);
                            unset($container['melis-toolcreator']['step5']);
                            unset($container['melis-toolcreator']['step5']);
                            unset($container['melis-toolcreator']['step6']);
                        }
                    }

                    unset($container['melis-toolcreator']['step3']);
                    $container['melis-toolcreator']['step3']['tcf-db-table'] = $formData['tcf-db-table'];
                }else{
                    $translator = $this->getServiceManager()->get('translator');

                    // adding a variable to ViewModel to flag an error
                    if (!$hasPrimaryKey)
                        $results['hasError']['tcf-db-table'] = [
                            'priTblNoPrimaryKey' => $translator->translate('tr_melistoolcreator_err_no_primary_key')
                        ];

                    if (!$hasPrimaryKeyIdAI)
                        $results['hasError']['tcf-db-table']['priTblNoPrimaryKeyNotAI'] = $translator->translate('tr_melistoolcreator_err_primary_key_not_ai');

                    if (!empty($notNullableBlobType)){
                        if (count($notNullableBlobType) > 1)
                            $errMsg = 's '.implode(', ', $notNullableBlobType);
                        else
                            $errMsg = ' '.implode(',', $notNullableBlobType);

                        $results['hasError']['tcf-db-table']['notNullableBlobType'] = sprintf($translator->translate('tr_melistoolcreator_err_blob_type_found_pri_tbl'), $errMsg);
                    }
                }
            }

            if (!empty($formData['tcf-db-table']))
                $selectedTbl = $formData['tcf-db-table'];
        }

        // Re-caching database tables
        if ($reloadDbCache){
            $this->getDBTablesCached(true);
        }

        if ($selectedTbl){
            $view->selectedTbl = $selectedTbl;
        }

        $results['selectedTbl'] = $selectedTbl;

        $view->step3Form = $step3Form;
        $view->tables = $this->getDBTablesCached();

        // Rendering the ViewModel to return html string
        $viewRenderer = $this->getServiceManager()->get('ViewRenderer');
        $html = $viewRenderer->render($view);

        $results['html'] = $html;
        return new JsonModel($results);
    }

    public function renderStep3LanguageDbTablesAction()
    {
        $results = [];

        // Tool creator session container
        $container = new Container('melistoolcreator');
        $stepCont = !empty( $container['melis-toolcreator']['step3']) ?  $container['melis-toolcreator']['step3'] : [];

        $view = new ViewModel();
        $view->setTemplate('melis-tool-creator/step3-db_tble');
        $view->type = 'language-db';

        // Step form fields
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step3_form/melistoolcreator_step3_language_tbl', 'melistoolcreator_step3_language_tbl');
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step3Form = $factory->createForm($appConfigForm);

        if (!empty($stepCont))
            $step3Form->setData($stepCont);

        $view->selectedPrimaryTbl = $this->params()->fromRoute('priTableName', null);

        $selectedTbl = null;
        if (!empty($stepCont['tcf-db-table-language-tbl']))
            $selectedTbl = $stepCont['tcf-db-table-language-tbl'];

        $ptfk = null;
        $ltfk = null;
        if (!empty($stepCont['tcf-db-table-language-pri-fk']))
            $ptfk = $stepCont['tcf-db-table-language-pri-fk'];

        if (!empty($stepCont['tcf-db-table-language-lang-fk']))
            $ltfk = $stepCont['tcf-db-table-language-lang-fk'];

        $validate = $this->params()->fromRoute('validate', false);

        $request = $this->getRequest();
        $formData = $request->getPost()->toArray();

        if ($validate){

            $step3Form->setData($formData['step-form'][1]);


            if(!$step3Form->isValid()){

                if ($formData['step-form'][1]['tcf-db-table-has-language']){

                    // adding a variable to viewModel to flag an error
                    $formErr = $step3Form->getMessages();

                    // Removing FK errors if the language table has no value
                    // To show first the error message of language table
                    if (isset($formErr['tcf-db-table-language-tbl'])){
                        $temp['tcf-db-table-language-tbl'] = $formErr['tcf-db-table-language-tbl'];
                        $formErr = $temp;
                    }

                    $results['hasError'] = $formErr;
                }
            }

            $formData = $step3Form->getData();

            if ($formData['tcf-db-table-has-language'] && !empty($formData['tcf-db-table-language-tbl'])){

                // Describe query to get the details of the Database table
                /**
                 * @var MelisToolCreatorService $toolCreatorSrv
                 */
                $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
                $table = $toolCreatorSrv->describeTable($formData['tcf-db-table-language-tbl']);

                $hasPrimaryKeyIdAI = false;
                $hasPrimaryKey = false;
                $notNullableBlobType = [];
                foreach ($table As $tbl){
                    if ($tbl['Key'] == 'PRI'){
                        $hasPrimaryKey = true;
                        if ($tbl['Extra'] == 'auto_increment')
                            $hasPrimaryKeyIdAI = true;
                    }

                    if (!is_bool(strpos($tbl['Type'], 'blob')))
                        if ($tbl['Null'] == 'NO'){
                            array_push($notNullableBlobType, '<b>'. $tbl['Field'] .'</b>');
                        }
                }

                if (!$hasPrimaryKey || !$hasPrimaryKeyIdAI || !empty($notNullableBlobType)){

                    if (empty($results['hasError']))
                        $results['hasError'] = [];

                    $translator = $this->getServiceManager()->get('translator');
                    // adding a variable to ViewModel to flag an error

                    if (!$hasPrimaryKey)
                        $results['hasError']['tcf-db-table']['langTblNoPrimaryKey'] = $translator->translate('tr_melistoolcreator_err_lang_no_primary_key');

                    if (!$hasPrimaryKeyIdAI)
                        $results['hasError']['tcf-db-table']['langTblNoPrimaryKeyNotAI'] = $translator->translate('tr_melistoolcreator_err_lang_primary_key_not_ai');

                    if (!empty($notNullableBlobType)){
                        if (count($notNullableBlobType) > 1)
                            $errMsg = 's '.implode(', ', $notNullableBlobType);
                        else
                            $errMsg = ' '.implode(',', $notNullableBlobType);

                        $results['hasError']['tcf-db-table']['notNullableBlobType'] = sprintf($translator->translate('tr_melistoolcreator_err_blob_type_found_lang_tbl'), $errMsg);
                    }
                }
            }

            if (!empty($stepCont))
                $container['melis-toolcreator']['step3'] = ArrayUtils::merge($stepCont, $step3Form->getData());
            else
                $container['melis-toolcreator']['step3'] = $step3Form->getData();

            $ptfk = $formData['tcf-db-table-language-pri-fk'];
            $ltfk = $formData['tcf-db-table-language-lang-fk'];

            $selectedTbl = $formData['tcf-db-table-language-tbl'];
        }

        $results['selectedTbl'] = $selectedTbl;
        $results['ptfk'] = $ptfk;
        $results['ltfk'] = $ltfk;

        $view->step3Form = $step3Form;
        $view->tables = $this->getDBTablesCached();

        $results['hasLanguage'] = $step3Form->get('tcf-db-table-has-language')->getValue();

        if (!empty($container['melis-toolcreator']['step3']['tcf-db-table-language-tbl'])){
            $view->selectedTbl = $container['melis-toolcreator']['step3']['tcf-db-table-language-tbl'];
        }

        // Rendering the ViewModel to return html string
        $viewRenderer = $this->getServiceManager()->get('ViewRenderer');
        $html = $viewRenderer->render($view);

        $results['html'] = $html;
        return new JsonModel($results);
    }

    /**
     * This method will render the Database table column details
     * for Third step, this will show after selection of the database table
     *
     * @return JsonModel
     */
    public function renderStep3TableColumnsAction()
    {
        $view = new ViewModel();
        $view->setTemplate('melis-tool-creator/step3-tbl-col');

        $view->tblType = $this->params()->fromPost('type', $this->params()->fromRoute('type'));
        $view->ptfk = $this->params()->fromPost('ptfk', $this->params()->fromRoute('ptfk'));
        $view->ltfk = $this->params()->fromPost('ltfk', $this->params()->fromRoute('ltfk'));

        // Describe query to get the details of the Database table
        $tableName = $this->params()->fromPost('tableName', $this->params()->fromRoute('tableName'));
        if ($tableName){
            /**
             * @var MelisToolCreatorService $toolCreatorSrv
             */
            $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
            $tables = $toolCreatorSrv->describeTable($tableName);

            $hasPrimaryKey = false;
            $hasBlobType = false;
            foreach ($tables As $tbl){
                if ($tbl['Key'] == 'PRI')
                    $hasPrimaryKey = true;

                if (!is_bool(strpos($tbl['Type'], 'blob')))
                    $hasBlobType = true;
            }

            $view->table = $tables;
            $view->hasPrimaryKey = $hasPrimaryKey;
            $view->hasBlobType = $hasBlobType;
        }


        // Rendering the ViewModel to return html string
        $viewRenderer = $this->getServiceManager()->get('ViewRenderer');
        $html = $viewRenderer->render($view);

        return new JsonModel(['html' => $html]);
    }

    /**
     * This method manage the Fourth step of the tool creator
     * This method manage the Database table column that will display on the
     * Table list of the Tool
     *
     * @param ViewModel $viewStp - viewmodel of the step
     * @param bool $validate - flag for validation
     * @return ViewModel
     */
    public function renderStep4($viewStp, $validate = false)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator']['step3']['tcf-db-table'];

        // Describe query to get the details of the Database table
        /**
         * @var MelisToolCreatorService $toolCreatorSrv
         */
        $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
        $viewStp->priTblCols = $toolCreatorSrv->describeTable($tcfDbTbl);

        if (!empty($container['melis-toolcreator']['step3']['tcf-db-table-has-language'])){

            /**
             * Describe query to get the details of the Database table
             *
             * @var MelisToolCreatorService $toolCreatorSrv
             */
            $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
            $viewStp->langTblCols = $toolCreatorSrv->describeTable($container['melis-toolcreator']['step3']['tcf-db-table-language-tbl']);

            // Adding prefix "lang_" for language columns
            $viewStp->fkCols = [
                'tclangtblcol_'.$container['melis-toolcreator']['step3']['tcf-db-table-language-lang-fk']
            ];

            $viewStp->fkMainTbl = $container['melis-toolcreator']['step3']['tcf-db-table-language-pri-fk'];
        }

        // Step form fields
        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step4_form', 'melistoolcreator_step4_form');
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step4Form = $factory->createForm($appConfigForm);

        /**
         * Triggering an event for the Select input option values dynamic values
         * @var MelisGeneralService $coreSrv
         */
        $coreSrv = $this->getServiceManager()->get('MelisGeneralService');
        $colDisplayOptions = $step4Form->get('tcf-db-table-col-display')->getValueOptions();
        $result = $coreSrv->sendEvent('melis_toolcreator_col_display_options', ['valueOptions' => $colDisplayOptions]);
        $step4Form->get('tcf-db-table-col-display')->setValueOptions($result['valueOptions']);

        $viewStp->step4Form = $step4Form;

        $request = $this->getRequest();
        if ($validate){
            unset($container['melis-toolcreator']['step4']);

            $formData = $request->getPost()->toArray();

            if(empty($formData['step-form'])){
                // adding a variable to ViewModel to flag an error
                $viewStp->hasError = true;
                $viewStp->skipErrModal = true;
            }else{
                $container['melis-toolcreator']['step4'] = $formData['step-form'];
            }
        }

        if (!empty($container['melis-toolcreator']['step4'])){
            if (!empty($container['melis-toolcreator']['step4']['tcf-db-table-cols'])){
                $viewStp->tblColumns = $container['melis-toolcreator']['step4']['tcf-db-table-cols'];
                $viewStp->tblColumnsDisplay = $container['melis-toolcreator']['step4']['tcf-db-table-col-display'];
            }
        }

        return $viewStp;
    }

    /**
     * This method manage the Fifth step of the tool creator
     * This method manage the Tool fields for adding and updating
     *
     * @param ViewModel $viewStp - viewmodel of the step
     * @param bool $validate - flag for validation
     * @return ViewModel
     */
    public function renderStep5($viewStp, $validate = false)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator']['step3']['tcf-db-table'];

        // Step form fields
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step5_form', 'melistoolcreator_step5_form');
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step5Form = $factory->createForm($appConfigForm);

        /**
         * Triggering an event for the Select input option values dynamic values
         */
        $coreSrv = $this->getServiceManager()->get('MelisGeneralService');
        $editTypeOptions = $step5Form->get('tcf-db-table-col-type')->getValueOptions();
        $result = $coreSrv->sendEvent('melis_toolcreator_input_edition_type_options', ['valueOptions' => $editTypeOptions]);
        $step5Form->get('tcf-db-table-col-type')->setValueOptions($result['valueOptions']);

        $viewStp->step5Form = $step5Form;

        $request = $this->getRequest();
        if ($validate){
            unset($container['melis-toolcreator']['step5']);

            $formData = $request->getPost()->toArray();

            if(empty($formData['step-form'])){
                // adding a variable to viewmodel to flag an error
                $viewStp->hasError = true;
                $viewStp->skipErrModal = true;
            }else{
                $container['melis-toolcreator']['step5'] = $formData['step-form'];
            }
        }

        if (!empty($container['melis-toolcreator']['step5'])){
            if (!empty($container['melis-toolcreator']['step5'])){
                $viewStp->tblCols = $container['melis-toolcreator']['step5'];
            }
        }

        /**
         * Describe query to get the details of the Database table
         *
         * @var MelisToolCreatorService $toolCreatorSrv
         */
        $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
        $tableCols = $toolCreatorSrv->describeTable($tcfDbTbl);

        $viewStp->tableCols = $this->tblColsFields($tableCols);

        if (!empty($container['melis-toolcreator']['step3']['tcf-db-table-has-language'])){

            /**
             * Describe query to get the details of the Database table
             *
             * @var MelisToolCreatorService $toolCreatorSrv
             */
            $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
            $angTblCols = $toolCreatorSrv->describeTable($container['melis-toolcreator']['step3']['tcf-db-table-language-tbl']);

            $viewStp->langTblCols = $this->tblColsFields($angTblCols, true);
        }

        // Input Types
        $config = $this->getServiceManager()->get('config');
        $viewStp->inputTypes = $config['plugins']['melistoolcreator']['datas']['input_types'];

        return $viewStp;
    }

    private function tblColsFields($tableCols, $langTbl = false)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        /**
         * This part is assigning to a input field data
         * base of the database table column details
         *
         * This manage the editable and mandatory of the field
         * and also the type of the field if it s editable
         */
        foreach($tableCols As $key => $val){



            $editableIcon = 'fa fa-lg tcf-fa-checkbox-editable';
            $requiredIcon = 'fa fa-lg tcf-fa-checkbox-required';
            $iconTag = '<i class="%s"></i>';
            $activeCheckIcon = 'tcf-fa-checkbox';
            $unCheckedIcon = 'fa-square-o';
            $checkedIcon = 'fa-check-square-o';
            $checkedColorIcon = 'text-success';

            $colField = $val['Field'];
            if ($langTbl){
                // Adding prefix "lang_" for language columns
                $colField = 'tclangtblcol_'.$val['Field'];
            }

            // input states
            $tableCols[$key]['editableIsChecked'] = false;
            $tableCols[$key]['requiredIsChecked'] = false;

            if ($val['Key'] == 'PRI' && $val['Extra'] == 'auto_increment'){
                $tableCols[$key]['editable'] = 'AUTO_INCREMENT';//sprintf($iconTag, implode(' ', [$editableIcon, $checkedIcon]));
                $tableCols[$key]['required'] = 'AUTO_INCREMENT';//sprintf($iconTag, implode(' ', [$requiredIcon, $checkedIcon]));
                $tableCols[$key]['isAutoIncrement'] = true;
                $tableCols[$key]['editableIsChecked'] = true;
                $tableCols[$key]['requiredIsChecked'] = true;
            }elseif ($val['Key'] == 'PRI' || $val['Null'] == 'NO'){
                $tableCols[$key]['editable'] = sprintf($iconTag, implode(' ', [$editableIcon, $checkedIcon]));
                $tableCols[$key]['required'] = sprintf($iconTag, implode(' ', [$requiredIcon, $checkedIcon]));
                $tableCols[$key]['editableIsChecked'] = true;
                $tableCols[$key]['requiredIsChecked'] = true;
            }else{
                $tableCols[$key]['editable'] = sprintf($iconTag, implode(' ', [$editableIcon, $unCheckedIcon, $activeCheckIcon]));
                $tableCols[$key]['required'] = sprintf($iconTag, implode(' ', [$requiredIcon, $unCheckedIcon, $activeCheckIcon]));
                $tableCols[$key]['isChecked'] = false;

                if (!empty($container['melis-toolcreator']['step5'])){
                    // Checking editable column checkbox
                    if ($container['melis-toolcreator']['step5']['tcf-db-table-col-editable']){
                        if (in_array($colField, $container['melis-toolcreator']['step5']['tcf-db-table-col-editable'])){
                            $tableCols[$key]['editable'] = sprintf($iconTag, implode(' ', [$editableIcon, $checkedIcon, $checkedColorIcon, $activeCheckIcon]));
                            $tableCols[$key]['editableIsChecked'] = true;
                        }
                    }

                    // Checking required column checkbox
                    if ($container['melis-toolcreator']['step5']['tcf-db-table-col-required']){
                        if (in_array($colField, $container['melis-toolcreator']['step5']['tcf-db-table-col-required'])){
                            $tableCols[$key]['required'] = sprintf($iconTag, implode(' ', [$requiredIcon, $checkedIcon, $checkedColorIcon, $activeCheckIcon]));
                            $tableCols[$key]['requiredIsChecked'] = true;
                        }
                    }
                }
            }

            // Language Foreign keys
            if (!empty($container['melis-toolcreator']['step3']['tcf-db-table-has-language']) && $val['Key'] != 'PRI'){
                if (in_array($val['Field'], [$container['melis-toolcreator']['step3']['tcf-db-table-language-pri-fk'], $container['melis-toolcreator']['step3']['tcf-db-table-language-lang-fk']])){
                    $tableCols[$key]['editable'] = 'FK';
                    $tableCols[$key]['required'] = 'FK';
                    $tableCols[$key]['editableIsChecked'] = true;
                    $tableCols[$key]['requiredIsChecked'] = true;
                }
            }

            $tableCols[$key]['fieldType'] = null;

            if (!empty($container['melis-toolcreator']['step5'])){

                if (!empty($container['melis-toolcreator']['step5']['tcf-db-table-col-editable'] &&
                    $container['melis-toolcreator']['step5']['tcf-db-table-col-type']))
                {
                    $fieldType = $container['melis-toolcreator']['step5']['tcf-db-table-col-type'];

                    foreach ($container['melis-toolcreator']['step5']['tcf-db-table-col-editable'] As $fKey => $fVal){
                        if ($fVal == $colField){
                            $tableCols[$key]['fieldType'] = $fieldType[$fKey];
                        }
                    }
                }
            }
        }

        return $tableCols;
    }

    /**
     * This method manage the Sixth step of the tool creator
     * This method manage the translation of the Database table columns
     * that are selected to display to the Tool
     *
     * @param ViewModel $viewStp - viewmodel of the step
     * @param bool $validate - flag for validation
     * @return ViewModel
     */
    public function renderStep6($viewStp, $validate = false)
    {
        $translator = $this->getServiceManager()->get('translator');

        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator'];

        $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
        $languages = $coreLang->fetchAll()->toArray();

        /**
         * Merging the columns the selected on step 4, 5 and 6
         * this will avoid the duplication of appearance in the form
         */
        $stepsColumns = array_merge(
            $tcfDbTbl['step4']['tcf-db-table-cols'],
            $tcfDbTbl['step5']['tcf-db-table-col-editable'],
            $tcfDbTbl['step5']['tcf-db-table-col-required']
        );

        // Making sure the column order is the same with the database table structure
        /**
         * Describe query to get the details of the Database table
         *
         * @var MelisToolCreatorService $toolCreatorSrv
         */
        $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
        $tableCols = $toolCreatorSrv->describeTable($tcfDbTbl['step3']['tcf-db-table']);

        // Preparing the column the will be added to the Form dynamically
        $selectedColumns = [];
        foreach($tableCols As $val){
            if (in_array($val['Field'], $stepsColumns)){
                $selectedColumns['pri_tbl'][] = $val['Field'];
            }
        }

        if (!empty($tcfDbTbl['step3']['tcf-db-table-has-language'])){

            /**
             * Skiping Foreign keys
             */
            $skipFkCols = [
                $tcfDbTbl['step3']['tcf-db-table-language-pri-fk']
            ];

            // If lanugage FK not exist in table col this will not include also in form texts
            if (!in_array('tclangtblcol_'.$tcfDbTbl['step3']['tcf-db-table-language-lang-fk'], $tcfDbTbl['step4']['tcf-db-table-cols'])){
                $skipFkCols[] = $tcfDbTbl['step3']['tcf-db-table-language-lang-fk'];
            }

            /**
             * Describe query to get the details of the Database table
             *
             * @var MelisToolCreatorService $toolCreatorSrv
             */
            $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
            $table = $toolCreatorSrv->describeTable($tcfDbTbl['step3']['tcf-db-table-language-tbl']);

            $langTblCols = [];
            foreach($table As $val){
                // Adding prefix "tclangtblcol_" for language columns
                $tblCol = 'tclangtblcol_'.$val['Field'];
                if (in_array($tblCol, $stepsColumns) && !in_array($val['Field'], $skipFkCols)){
                    array_push($langTblCols, $tblCol);
                }
            }

            $temp = $selectedColumns;
            $selectedColumns = [];
            $selectedColumns['pri_tbl'] = $temp['pri_tbl'];
            $selectedColumns['lang_tbl'] = $langTblCols;
        }

        // Step form fields
        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step6_form', 'melistoolcreator_step6_form');
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);

        $hasErrorForm = [];
        $inputHasValue = [];
        $step6Form = [];

        $request = $this->getRequest();

        $formDatas = [];

        foreach ($languages As $key => $lang){

            foreach ($selectedColumns As $tblType => $tblCols){

                $step6FormTmp = $factory->createForm($appConfigForm);
                $inputFilter = new InputFilter();

                /**
                 * Adding the selected Database table column
                 * dynamically to the form for column translations
                 */
                foreach ($tblCols As $col){

                    // Skip main talbe foriegn keys

                    // Column name
                    $step6FormTmp->add([
                        'name' => $col,
                        'type' => 'MelisText',
                        'options' => [
                            'label' => $col.' *',
                            'col-name' => true,
                        ],
                        'attributes' => [
                            'required' => 'required',
                            'placeholder' => $translator->translate('tr_melistoolcreator_inpt_name')
                        ]
                    ]);

                    // Column validator nad filter
                    $input = new Input($col);
                    $input->setRequired(true);
                    $input->getValidatorChain()
                        ->attachByName(
                            'NotEmpty',
                            [
                                'messages' => [
                                    \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                                ]
                            ]
                        );
                    $input->getFilterChain()
                        ->attachByName(
                            'StripTags',
                            'StringTrim'
                        );
                    $inputFilter->add($input);

                    // Column name tooltip description
                    $step6FormTmp->add([
                        'name' => $col.'_tcinputdesc',
                        'type' => 'MelisText',
                        'options' => [
                            'col-desc' => true,
                        ],
                        'attributes' => [
                            'required' => 'required',
                            'placeholder' => $translator->translate('tr_melistoolcreator_inpt_name tooltip'),
                        ]
                    ]);

                    // Column name tooltip description validator and filters
                    $inputDesc = new Input($col.'_tcinputdesc');
                    $inputDesc->setRequired(false);
                    $inputDesc->getFilterChain()
                        ->attachByName(
                            'StripTags',
                            'StringTrim'
                        );
                    $inputFilter->add($inputDesc);

                    // Setup input filters to Form
                    $step6FormTmp->setInputFilter($inputFilter);
                }

                if (!$validate){
                    if (!empty($container['melis-toolcreator']['step6'])){
                        if (!empty($container['melis-toolcreator']['step6'][$lang['lang_locale']][$tblType])){
                            $step6FormTmp->setData($container['melis-toolcreator']['step6'][$lang['lang_locale']][$tblType]);
                        }
                    }

                    $step6FormTmp->get('tcf-lang-local')->setValue($lang['lang_locale']);
                    $step6FormTmp->get('tcf-tbl-type')->setValue($tblType);
                }

                if ($validate){
                    $formData = $request->getPost()->toArray();

                    foreach ($formData['step-form'] As $val){
                        if ($val['tcf-lang-local'] == $lang['lang_locale'] && $val['tcf-tbl-type'] == $tblType){
                            $step6FormTmp->setData($val);
                        }
                    }

                    /**
                     * Just like in Step 2
                     * this will accept if of the of the form validated with no error(s)
                     */
                    if(!$step6FormTmp->isValid()){
                        if (empty($step6FormTmp))
                            $hasErrorForm = $step6FormTmp->getMessages();
                        else
                            $hasErrorForm = ArrayUtils::merge($hasErrorForm, $step6FormTmp->getMessages());
                    }

                    // Getting input with data for Error preparation
                    foreach ($step6FormTmp->getData() As $ckey => $kVal){
                        if (!empty($kVal) && !in_array($ckey, $inputHasValue)){
                            array_push($inputHasValue, $ckey);
                        }
                    }

                    $formDatas[$lang['lang_locale']][$tblType] = $step6FormTmp->getData();
                }

                $step6Form[$lang['lang_locale']][$tblType] = $step6FormTmp;
            }

            // Language label
            $languages[$key]['lang_label'] = $this->langLabel($lang['lang_locale'], $lang['lang_name']);
        }

        // Removing input with data on any Form Fieldset
        if (!empty($inputHasValue)) {
            foreach ($inputHasValue As $key => $val)
                if (isset($hasErrorForm[$val]))
                    unset($hasErrorForm[$val]);
        }

        // adding a variable to ViewModel to flag an error
        if ($hasErrorForm){
            foreach ($hasErrorForm As $key => $errs){
                foreach ($errs As $eKey => $txt)
                    $hasErrorForm[$key][$eKey] = $translator->translate($txt);
            }
            $viewStp->hasError = $hasErrorForm;
        }

        if (empty($hasErrorForm) && $validate){
            $container['melis-toolcreator']['step6'] = $formDatas;
        }

        $viewStp->columns = $selectedColumns;
        $viewStp->languages = $languages;
        $viewStp->step6Form = $step6Form;

        return $viewStp;
    }

    /**
     * This method manage the Seventh step of the tool creator
     * This will display all the result from step 1 to 6
     * for summary
     *
     * @param ViewModel $viewStp - viewmodel of the step
     * @param bool $validate - flag for validation
     * @return ViewModel
     */
    public function renderStep7($viewStp, $validate = false)
    {
        // Config
        $config = $this->getServiceManager()->get('config');
        $viewStp->steps = $config['plugins']['melistoolcreator']['datas']['steps'];

        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator'];
        $viewStp->datas = $tcfDbTbl;

        $viewStp->toolType = $tcfDbTbl['step1']['tcf-tool-type'];

        // Languages
        $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
        $viewStp->languages = $coreLang->fetchAll()->toArray();

        if ($tcfDbTbl['step1']['tcf-tool-type'] == 'db'){
            $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
            $priCol = $toolCreatorSrv->hasPrimaryKey();

            if (!empty($priCol)){
                $viewStp->priCol = $priCol['Field'];
            }
        }

        return $viewStp;
    }

    /**
     * This method manage the Last step of the tool creator
     * This will finalize the creation of the tool
     *
     * @param ViewModel $viewStp - viewmodel of the step
     * @param bool $validate - flag for validation
     * @return ViewModel
     */
    public function renderStep8($viewStp, $validate = false)
    {
        // Step form fields
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step8_form', 'melistoolcreator_step8_form');
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);

        $request = $this->getRequest();

        $viewStp->frameworkSetupUrl = false;

        if ($validate){

            $validateModule = $request->getPost()->toArray();
            $activateModule = (!empty($validateModule['step-form']['tcf-activate-tool'])) ? true : false;

            $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
            $toolCreatorSrv->createTool();

            if ($activateModule){

                // Activating module
                $moduleSvc = $this->getServiceManager()->get('ModulesService');
                $moduleSvc->activateModule($toolCreatorSrv->moduleName());

                // Reloading module paths
                unlink($_SERVER['DOCUMENT_ROOT'].'/../config/melis.modules.path.php');

                // Flag to reload the page in-order to run the new created tool
                $viewStp->restartRequired = true;
            }

            $isFrameworkTool = $toolCreatorSrv->isFrameworkTool();
            if ($isFrameworkTool)
                $viewStp->frameworkSetupUrl = 'melis/' .$isFrameworkTool. '-module-create';

            $viewStp->finalized = $validate;
            $viewStp->hasError = [];
        }

//        $viewStp->finalized = true;
//        $viewStp->frameworkSetupUrl = 'melis/laravel-module-create';
//        $viewStp->restartRequired = true;

        $viewStp->form = $factory->createForm($appConfigForm);

        return $viewStp;
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

        $lang = explode('_', $locale);
        if (!empty($lang[0])) {

            $moduleSvc = $this->getServiceManager()->get('ModulesService');
            $imgPath = $moduleSvc->getModulePath('MelisCore').'/public/assets/images/lang/'.$lang[0].'.png';

            if (file_exists($imgPath)) 
                $langLabel .= '<span class="pull-right"><img src="/MelisCore/assets/images/lang/'.$lang[0].'.png"></span>';
            else
                $langLabel .= '<span style="border: 1px solid #fff;padding: 4px 4px;line-height: 10px;float: right;margin: 5px;">'. strtoupper($lang[0]) .'</span>';
        }

        return $langLabel;
    }

    /**
     * This method setting and retrieving database table cached file
     *
     * @param $reloadCached
     * @return \Laminas\Db\Metadata\Object\TableObject[]
     */
    private function getDBTablesCached($reloadCached = false)
    {
        //added to fix timeout issue when Tool Creator is opened for the first time
        session_write_close();
        ini_set('max_execution_time', 0);
        set_time_limit(0);
       
        /**
         * Caching Database tables to file cache
         * to avoid slow request for step 2
         */
        $melisEngineCacheSystem = $this->getServiceManager()->get('MelisEngineCacheSystem');
        $results = $melisEngineCacheSystem->getCacheByKey($this->cacheKey, $this->cacheConfig, true);
        if (!$results || $reloadCached){

            $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');

            $adapter = $this->getServiceManager()->get(\Laminas\Db\Adapter\Adapter::class);
            $metadata = new Metadata($adapter);
            $tables = $metadata->getTables();

            // Only table that has Primary key and auto increment
            $results = [];
            foreach ($tables As $tbl)
                if (!empty($toolCreatorSrv->getTablePK($tbl->getName())))
                    $results[] = $tbl;

            $melisEngineCacheSystem->setCacheByKey($this->cacheKey, $this->cacheConfig, $results, true);
        }

        return $results;
    }

    /**
     * For checking the Tool creator steps data stored
     * in the session container
     */
    public function sessAction()
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcSteps = $container['melis-toolcreator'];
        print_r($tcSteps);
        die();
    }

    public function chAction()
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');

        $container['melis-toolcreator']['step1']['tcf-name'] = 'Newstooltab';
//        $container['melis-toolcreator']['step1']['tcf-tool-edit-type'] = 'tab';
        exit;
    }

    public function strAction()
    {
//        $toolCreatorSrv = $this->$this->getServiceManager()->get('MelisToolCreatorService');
//        echo 'meLisModuLe: '.$toolCreatorSrv->generateModuleNameCase('MelisModuleJeJejE');

        $text = 'ignore everything except this ("1", "2")';
        preg_match('#\((.*?)\)#', $text, $match);
        print_r($match);

        exit;
    }

    public function testAction()
    {
        $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
        $res = $toolCreatorSrv->createTool();
        dd($res->getVariables());
    }

    public function desAction()
    {
        $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
        $res = $toolCreatorSrv->describeTable('aaa');
        print_r($res);
        die();
    }

    public function formAction()
    {
        $srv = $this->getServiceManager()->get('FormElementManager');
        $element = $srv->get('MelisCmsTemplateSelect');

        print_r($element);

        $element->setName('test_test');


        $viewHelper = $this->getServiceManager()->get('ViewHelperManager');
        $fielRow = $viewHelper->get('MelisFieldRow');

        echo $fielRow->render($element);

        exit;
    }
}