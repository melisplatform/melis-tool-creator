<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisToolCreator\Controller;

use MelisCore\MelisModuleManager;
use MelisCore\Module;
use Zend\Form\Element\Text;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Db\Metadata\Metadata;

class ToolCreatorController extends AbstractActionController
{
    private $moduleTplDir;
    private $cacheKey = 'toolcreator_database_tables';
    private $cacheConfig = 'toolcreator_database';

    public function __construct()
    {
        $this->moduleTplDir = __DIR__ .'/../../../etc/ModuleTpl';
    }

    /**
     * Render tool creator
     * @return ViewModel
     */
    public function renderToolCreatorAction()
    {
        $view = new ViewModel();

        // Initializing the Tool creator session container
        $container = new Container('melistoolcreator');
        $container['melis-toolcreator'] = array();

        return $view;
    }

    /**
     * Render tool creator header
     * @return ViewModel
     */
    public function renderToolCreatorHeaderAction()
    {
        $view = new ViewModel();
        return $view;
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
        $filePermissionErr = array();
        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/../config/melis.module.load.php'))
            $filePermissionErr[] = 'tr_melistoolcreator_fp_config';

        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/../cache'))
            $filePermissionErr[] = 'tr_melistoolcreator_fp_cache';

        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/../module'))
            $filePermissionErr[] = 'tr_melistoolcreator_fp_module';

        if (!empty($filePermissionErr)){
            $view->fPErr = $filePermissionErr;
            return $view;
        }

        // Database table caching
        $this->getDBTablesCached();

        $config = $this->getServiceLocator()->get('config');

        // Retrieving steps form config
        $stepsConfig = $config['plugins']['melistoolcreator']['datas']['steps'];


        $view->stepsConfig = $stepsConfig;
        return $view;
    }

    /**
     * This methos render the steps of the tool
     * this will call dynamically the requested step ViewModel
     * @return ViewModel
     */
    public function renderToolCreatorStepsAction()
    {
        $view = new ViewModel();



        // The steps requested
        $curStep = $this->params()->fromPost('curStep', 1);
        $nxtStep = $this->params()->fromPost('nxtStep', 1);
        $validate = $this->params()->fromPost('validate', false);

        // Current viewModel
        $viewStp = new ViewModel();
        $viewStp->id = 'melistoolcreator_step'.$curStep;
        $viewStp->setTemplate('melis-tool-creator/step'.$curStep);

        $stpFunction = 'renderStep'.$curStep;

        /**
         * This will try to get the requested view
         * with the current view and the flag for validation
         */
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

        // Rendering the result view and attach to the container
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
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
        $melisMelisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step1_form', 'melistoolcreator_step1_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step1Form = $factory->createForm($appConfigForm);

        // Setting the form data stored in session
        if (!empty($container['melis-toolcreator']['step1'])){
            $step1Form->setData($container['melis-toolcreator']['step1']);
        }

        // Form validation
        $request = $this->getRequest();
        if ($validate){
            $formData = get_object_vars($request->getPost());

            $step1Form->setData($formData['step-form']);

            if(!$step1Form->isValid()){
                // adding a variable to viewmodel to flag an error
                $viewStp->hasError = true;
            }else{
                /**
                 * Validating the module entered if its already existing on the platform
                 */
                $modulesSvc = $this->getServiceLocator()->get('ModulesService');
                $existingModules = array_merge($modulesSvc->getModulePlugins(), \MelisCore\MelisModuleManager::getModules());

                if (in_array($formData['step-form']['tcf-name'], $existingModules)){
                    // adding a variable to viewmodel to flag an error
                    $viewStp->hasError = true;

                    // Adding error message to module input
                    $translator = $this->getServiceLocator()->get('translator');
                    $step1Form->get('tcf-name')->setMessages(array(
                        'ModuleExist' => sprintf($translator->translate('tr_melistoolcreator_err_module_exist'), $formData['step-form']['tcf-name'])
                    ));
                }else{
                    $container['melis-toolcreator']['step1'] = $step1Form->getData();
                }
            }
        }

        $config = $this->getServiceLocator()->get('MelisCoreConfig');

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
        $coreLang = $this->getServiceLocator()->get('MelisCoreTableLang');
        $languages = $coreLang->getLanguageInOrdered()->toArray();

        // Step form fields
        $melisMelisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step2_form', 'melistoolcreator_step2_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);

        $request = $this->getRequest();
        $formData = $request->getPost()->toArray();

        $step2Form = array();

        $hasErrorForm = false;
        $hasValidForm = false;

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
                    $hasErrorForm = true;
                }

                $container['melis-toolcreator']['step2'][$lang['lang_locale']] = $step2Formtmp->getData();
            }

            // Adding language form
            $step2Form[$lang['lang_locale']] = $step2Formtmp;

            // Language label
            $languages[$key]['lang_label'] = $this->langLabel($lang['lang_locale'], $lang['lang_name']);
        }

        // adding a variable to viewmodel to flag an error
        if ($hasErrorForm&&!$hasValidForm)
            $viewStp->hasError = true;

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

        $tcfDbTbl = null;
        if (!empty($container['melis-toolcreator']['step3'])){
            $tcfDbTbl = $container['melis-toolcreator']['step3']['tcf-db-table'];

            /**
             * If this step has selected table this will
             * get the view of the column details to show directly
             * to the step view
             */
            $tblColsHtml = $this->forward()->dispatch(
                'MelisToolCreator\Controller\ToolCreator',
                array(
                    'action' => 'renderStep3TableColumns',
                    'tableName' => $tcfDbTbl
                )
            )->getVariables();

            $viewStp->tblCols = $tblColsHtml['html'];
        }

        $dbTablesHtml = $this->forward()->dispatch(
            'MelisToolCreator\Controller\ToolCreator',
            array(
                'action' => 'renderStep3DbTables',
                'selectedTbl' => $tcfDbTbl,
                'validate' => $validate,
            )
        )->getVariables();

        if (isset($dbTablesHtml['hasError'])){
            $viewStp->hasError = true;
        }

        $viewStp->dbTables = $dbTablesHtml['html'];

        return $viewStp;
    }

    public function renderStep3DbTablesAction()
    {
        $results = array();

        // Tool creator session container
        $container = new Container('melistoolcreator');

        $view = new ViewModel();
        $view->setTemplate('melis-tool-creator/step3-db_tble');

        $tcfDbTbl = null;
        if (!empty($container['melis-toolcreator']['step3']))
            $tcfDbTbl = $container['melis-toolcreator']['step3']['tcf-db-table'];

        $validate = $this->params()->fromRoute('validate', false);
        $selectedTbl = $this->params()->fromRoute('selectedTbl', $this->params()->fromPost('selectedTbl', $tcfDbTbl));
        $reloadDbCache = $this->params()->fromPost('reloadDbTblCached', false);

        // Step form fields
        $melisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step3_form', 'melistoolcreator_step3_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step3Form = $factory->createForm($appConfigForm);

        if (!empty($container['melis-toolcreator']['step3'])) {
            $step3Form->setData($container['melis-toolcreator']['step3']);
        }elseif (!empty($selectedTbl)){
            $step3Form->get('tcf-db-table')->setValue($selectedTbl);
        }

        if ($validate){
            $request = $this->getRequest();
            $formData = $request->getPost()->toArray();
            $step3Form->setData($formData['step-form']);

            if(!$step3Form->isValid()){
                // adding a variable to viewmodel to flag an error
                $results['hasError'] = true;
            }else{
                $formData = $step3Form->getData();
                if (!empty($container['melis-toolcreator']['step3'])){
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

                $container['melis-toolcreator']['step3'] = $formData;
            }
        }

        // Re-caching database tables
        if ($reloadDbCache){
            $this->getDBTablesCached(true);
        }

        if ($selectedTbl){
            $view->selectedTbl = $selectedTbl;
        }

        $view->step3Form = $step3Form;
        $view->tables = $this->getDBTablesCached();

        // Rendering the ViewModel to return html string
        $viewRenderer = $this->getServiceLocator()->get('ViewRenderer');
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

        // Describe query to get the details of the Database table
        $tableName = $this->params()->fromPost('tableName',$this->params()->fromRoute('tableName'));
        $sql = 'DESCRIBE '.$tableName;
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $table = $adapter->query($sql, array(5));
        $view->table = $table->toArray();

        // Rendering the ViewModel to return html string
        $viewRenderer = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRenderer->render($view);

        return new JsonModel(array('html' => $html));
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
        $sql = 'DESCRIBE '.$tcfDbTbl;
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $table = $adapter->query($sql, array(5));
        $viewStp->table = $table->toArray();

        // Step form fields
        $melisMelisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step4_form', 'melistoolcreator_step4_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step4Form = $factory->createForm($appConfigForm);

        $viewStp->step4Form = $step4Form;

        $request = $this->getRequest();
        if ($validate)
        {
            unset($container['melis-toolcreator']['step4']);

            $formData = get_object_vars($request->getPost());

            if(empty($formData['step-form']))
            {
                // adding a variable to viewmodel to flag an error
                $viewStp->hasError = true;
            }
            else
            {
                $container['melis-toolcreator']['step4'] = $formData['step-form'];
            }
        }

        if (!empty($container['melis-toolcreator']['step4']))
        {
            if (!empty($container['melis-toolcreator']['step4']['tcf-db-table-cols']))
            {
                $viewStp->tblColumns = $container['melis-toolcreator']['step4']['tcf-db-table-cols'];
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
        $melisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step5_form', 'melistoolcreator_step5_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $step5Form = $factory->createForm($appConfigForm);
        $viewStp->step5Form = $step5Form;

        $request = $this->getRequest();
        if ($validate){
            unset($container['melis-toolcreator']['step5']);

            $formData = $request->getPost()->toArray();

            if(empty($formData['step-form'])){
                // adding a variable to viewmodel to flag an error
                $viewStp->hasError = true;
            }else{
                $container['melis-toolcreator']['step5'] = $formData['step-form'];
            }
        }

        if (!empty($container['melis-toolcreator']['step5'])){
            if (!empty($container['melis-toolcreator']['step5'])){
                $viewStp->tblCols = $container['melis-toolcreator']['step5'];
            }
        }

        // Describe query to get the details of the Database table
        $sql = 'DESCRIBE '.$tcfDbTbl;
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $table = $adapter->query($sql, array(5));
        $tableCols = $table->toArray();

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

            // input states
            $tableCols[$key]['editableIsChecked'] = false;
            $tableCols[$key]['requiredIsChecked'] = false;

            if ($val['Key'] == 'PRI' && $val['Extra'] == 'auto_increment')
            {
                $tableCols[$key]['editable'] = 'AUTO_INCREMENT';
                $tableCols[$key]['required'] = 'AUTO_INCREMENT';
                $tableCols[$key]['editableIsChecked'] = true;
                $tableCols[$key]['requiredIsChecked'] = true;
            }
            elseif ($val['Key'] == 'PRI' || $val['Null'] == 'NO')
            {
                $tableCols[$key]['editable'] = sprintf($iconTag, implode(' ', array($editableIcon, $checkedIcon)));
                $tableCols[$key]['required'] = sprintf($iconTag, implode(' ', array($requiredIcon, $checkedIcon)));
                $tableCols[$key]['editableIsChecked'] = true;
                $tableCols[$key]['requiredIsChecked'] = true;
            }
            else
            {
                $tableCols[$key]['editable'] = sprintf($iconTag, implode(' ', array($editableIcon, $unCheckedIcon, $activeCheckIcon)));
                $tableCols[$key]['required'] = sprintf($iconTag, implode(' ', array($requiredIcon, $unCheckedIcon, $activeCheckIcon)));
                $tableCols[$key]['isChecked'] = false;

                if (!empty($container['melis-toolcreator']['step5']))
                {
                    // Checking editable column checkbox
                    if ($container['melis-toolcreator']['step5']['tcf-db-table-col-editable'])
                    {
                        if (in_array($val['Field'], $container['melis-toolcreator']['step5']['tcf-db-table-col-editable']))
                        {
                            $tableCols[$key]['editable'] = sprintf($iconTag, implode(' ', array($editableIcon, $checkedIcon, $checkedColorIcon, $activeCheckIcon)));
                            $tableCols[$key]['editableIsChecked'] = true;
                        }
                    }

                    // Checking required column checkbox
                    if ($container['melis-toolcreator']['step5']['tcf-db-table-col-required'])
                    {
                        if (in_array($val['Field'], $container['melis-toolcreator']['step5']['tcf-db-table-col-required']))
                        {
                            $tableCols[$key]['required'] = sprintf($iconTag, implode(' ', array($requiredIcon, $checkedIcon, $checkedColorIcon, $activeCheckIcon)));
                            $tableCols[$key]['requiredIsChecked'] = true;
                        }
                    }
                }
            }

            $tableCols[$key]['fieldType'] = null;

            if (!empty($container['melis-toolcreator']['step5'])){

                if (!empty($container['melis-toolcreator']['step5']['tcf-db-table-col-editable'] &&
                    $container['melis-toolcreator']['step5']['tcf-db-table-col-type']))
                {
                    $fieldType = $container['melis-toolcreator']['step5']['tcf-db-table-col-type'];

                    foreach ($container['melis-toolcreator']['step5']['tcf-db-table-col-editable'] As $fKey => $fVal){
                        if ($fVal == $val['Field']){
                            $tableCols[$key]['fieldType'] = $fieldType[$fKey];
                        }
                    }
                }
            }
        }

        $viewStp->tableCols = $tableCols;

        return $viewStp;
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
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator'];

        $coreLang = $this->getServiceLocator()->get('MelisCoreTableLang');
        $languages = $coreLang->getLanguageInOrdered()->toArray();

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
        $tcfDbTbl = $container['melis-toolcreator']['step3']['tcf-db-table'];
        $sql = 'DESCRIBE '.$tcfDbTbl;
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $table = $adapter->query($sql, array(5));
        $tableCols = $table->toArray();

        // Preparing the column the will be added to the Form dynamically
        $selectedColumns = array();
        foreach($tableCols As $val){
            if (in_array($val['Field'], $stepsColumns)){
                array_push($selectedColumns, $val['Field']);
            }
        }

        // Step form fields
        $melisMelisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step6_form', 'melistoolcreator_step6_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);

        $hasValidForm = false;
        $hasErrorForm = false;

        $request = $this->getRequest();

        $step6Form = array();

        foreach ($languages As $key => $lang){
            $step6FormTmp = $factory->createForm($appConfigForm);

            $step6FormTmp->get('tcf-lang-local')->setValue($lang['lang_locale']);

            $inputFilter = new InputFilter();

            /**
             * Adding the selected Database table column
             * dynamically to the form for column translations
             */
            foreach ($selectedColumns As $col){
                $step6FormTmp->add(array(
                    'name' => $col,
                    'type' => 'MelisText',
                    'options' => array(
                        'label' => $col.' *'
                    ),
                    'attributes' => array(
                        'required' => 'required'
                    ),
                ));

                $input = new Input($col);
                $input->setRequired(true);

                $input->getValidatorChain()
                    ->attachByName(
                        'NotEmpty',
                            array('messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'tr_melistoolcreator_err_empty',
                            )
                        )
                    );


                $inputFilter->add($input);
                $step6FormTmp->setInputFilter($inputFilter);
            }

            if (!empty($container['melis-toolcreator']['step6'])){
                if (!empty($container['melis-toolcreator']['step6'][$lang['lang_locale']])){
                    $step6FormTmp->setData($container['melis-toolcreator']['step6'][$lang['lang_locale']]);
                }
            }

            if ($validate){
                $formData = $request->getPost()->toArray();

                foreach ($formData['step-form'] As $val){
                    if ($val['tcf-lang-local'] == $lang['lang_locale']){
                        $step6FormTmp->setData($val);
                    }
                }

                /**
                 * Just like in Step 2
                 * this will accept if of the of the form validated with no error(s)
                 */
                if($step6FormTmp->isValid()){
                    $hasValidForm = true;
                }else{
                    $hasErrorForm = true;
                }

                $container['melis-toolcreator']['step6'][$lang['lang_locale']] = $step6FormTmp->getData();
            }

            $step6Form[$lang['lang_locale']] = $step6FormTmp;

            // Language label
            $languages[$key]['lang_label'] = $this->langLabel($lang['lang_locale'], $lang['lang_name']);
        }

        // adding a variable to viewmodel to flag an error
        if ($hasErrorForm && !$hasValidForm)
            $viewStp->hasError = true;

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
        $config = $this->getServiceLocator()->get('config');
        $viewStp->steps = $config['plugins']['melistoolcreator']['datas']['steps'];

        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator'];
        $viewStp->datas = $tcfDbTbl;

        // Languages
        $coreLang = $this->getServiceLocator()->get('MelisCoreTableLang');
        $viewStp->languages = $coreLang->getLanguageInOrdered()->toArray();

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
        $melisCoreConfig = $this->getServiceLocator()->get('MelisCoreConfig');
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistoolcreator/forms/melistoolcreator_step8_form', 'melistoolcreator_step8_form');
        $factory = new \Zend\Form\Factory();
        $formElements = $this->getServiceLocator()->get('FormElementManager');
        $factory->setFormElementManager($formElements);

        $request = $this->getRequest();

        if ($validate){

            $validateModule = $request->getPost()->toArray();
            $activateModule = (!empty($validateModule['step-form']['tcf-activate-tool'])) ? true : false;

            $this->finalize();

            if ($activateModule){

                // Tool creator session container
                $container = new Container('melistoolcreator');
                $tcfDbTbl = $container['melis-toolcreator'];
                $moduleName = $tcfDbTbl['step1']['tcf-name'];

                // Activating module
                $moduleSvc = $this->getServiceLocator()->get('ModulesService');
                $moduleSvc->activateModule($moduleName);

                // Flag to reload the page in-order to run the new created tool
                $viewStp->restartRequired = true;
            }

            $viewStp->finalized = $validate;
        }

        $viewStp->form = $factory->createForm($appConfigForm);

        return $viewStp;
    }

    /**
     * This methos triggered from AJAX request to finalize
     * the tool creation
     *
     * @return JsonModel
     */
    private function finalize()
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator'];

        $moduleName = $tcfDbTbl['step1']['tcf-name'];

        /**
         * Moudle directories
         * this directories based on ZF2 framework
         */
        $moduleDirs = array(
            'config' => null,
            'language' => null,
            'public' => array(
                'js' => null
            ),
            'src' => array(
                $moduleName => array(
                    'Controller' => null,
                    'Model' => array(
                        'Tables' => array(
                            'Factory' => null
                        )
                    )
                )
            ),
            'view' => array(
                $this->moduleNameToViewName($moduleName) => array(
                    'index' => null
                )
            )
        );

        $moduleDir = $_SERVER['DOCUMENT_ROOT'].'/../module/'.$moduleName;

        // Create module
        $this->generateModule($moduleDir);

        // Generating sub dir and files of the module
        $this->generateModuleSubDirsAndFiles($moduleDirs, $moduleDir);

        return new JsonModel(array('success' => true));
    }

    /**
     * This method generate the module file of the Module
     * @param $tempTargetDir
     */
    private function generateModule($tempTargetDir)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator'];

        mkdir($tempTargetDir, 0777);

        $moduleName = $tcfDbTbl['step1']['tcf-name'];

        $moduleFile = $this->moduleTplDir.'/Module.php';

        $fileContent = file_get_contents($moduleFile);

        $fileContent = str_replace('ModuleTpl', $moduleName, $fileContent);
        $fileContent = str_replace('moduletpl', strtolower($moduleName), $fileContent);

        $fh = fopen($tempTargetDir.'/Module.php', 'x');
        fwrite($fh, $fileContent);
        fclose($fh);
    }

    /**
     * This method manage the creation of the module directories
     * and file need of target Module
     *
     * @param array $dirs - list of directories
     * @param string $targetDir - the directory where the module will created
     */
    private function generateModuleSubDirsAndFiles($dirs, $targetDir)
    {
        foreach ($dirs As $dir => $subDir){
            $tempTargetDir = $targetDir.'/'.$dir;

            mkdir($tempTargetDir, 0777);

            switch ($dir){
                case 'config':
                    $this->generateModuleConfig($tempTargetDir);
                    break;
                case 'Controller':
                    $this->generateModuleController($tempTargetDir);
                    break;
                case 'index':
                    $this->generateModuleViews($tempTargetDir);
                    break;
                case 'js':
                    $this->generateModuleJs($tempTargetDir);
                    break;
                case 'language':
                    $this->generateModuleLanguages($tempTargetDir);
                    break;
                case 'Model':
                    $this->generateModuleModel($tempTargetDir);
                    break;
                case 'Tables':
                    $this->generateModuleModelTables($tempTargetDir);
                    break;
                case 'Factory':
                    $this->generateModuleModelTablesFactory($tempTargetDir);
                    break;
            }

            if (is_array($subDir)){
                $this->generateModuleSubDirsAndFiles($subDir, $tempTargetDir);
            }
        }
    }

    /**
     * This method generates the Module config
     * @param $targetDir
     */
    private function generateModuleConfig($targetDir)
    {
        $moduleConfigFiles = $this->moduleTplDir.'/config';
        foreach (scandir($moduleConfigFiles) As $file){
            if (is_file($moduleConfigFiles.'/'.$file)){

                if ($file == 'app.tools.php'){
                    $this->generateModuleToolConfig($targetDir);
                }else{
                    $this->generateFile($file, $moduleConfigFiles.'/'.$file, $targetDir);
                }
            }
        }
    }

    /**
     * This method generate the Module controller
     * @param $targetDir
     */
    private function generateModuleController($targetDir)
    {
        $file = 'IndexController.php';
        $moduleCtrlFile = $this->moduleTplDir.'/src/ModuleTpl/Controller/IndexController.php';
        $fileContent = file_get_contents($moduleCtrlFile);

        $savingItemFile = $this->moduleTplDir.'/extra/CodeTemplates/saving-item-without-id.txt';
        $savingItemContent = file_get_contents($savingItemFile);

        $tableActions  = '';
        $tableActionColumn  = '';
        $formModalContent  = '';

        $pk = $this->hasPrimaryKey();
        if (!empty($pk)){
            $extraFile = $this->moduleTplDir.'/extra/AssetsTemplates/edit-delete-function.txt';
            $tableActions = file_get_contents($extraFile);

            $extraFile = $this->moduleTplDir.'/extra/AssetsTemplates/table-action-column.txt';
            $tableActionColumn = file_get_contents($extraFile);

            $fileContent = str_replace('\'key\' => true,', '\'key\' => \''.$pk['Field'].'\',', $fileContent);

            $savingItemFile = $this->moduleTplDir.'/extra/CodeTemplates/saving-item-with-id.txt';
            $savingItemContent = file_get_contents($savingItemFile);
            $savingItemContent = str_replace('#TCKEY', $pk['Field'], $savingItemContent);

            $formModalFile = $this->moduleTplDir.'/extra/CodeTemplates/form-with-id.txt';
            $formModalContent = file_get_contents($formModalFile);
            $formModalContent = str_replace('#TCKEY', $pk['Field'], $formModalContent);
        }

        $fileContent = str_replace('#TCEDITDELETEFUNCTIONACTION', $tableActions, $fileContent);
        $fileContent = str_replace('#TCTABLEACTIONCOLUMN', $tableActionColumn, $fileContent);
        $fileContent = str_replace('#TCSAVINGITEM', $savingItemContent, $fileContent);
        $fileContent = str_replace('#TCFORMMODAL', $formModalContent, $fileContent);

        $this->generateFile($file, null, $targetDir, $fileContent);
    }

    /**
     * This method generate the Module Tool config
     * that contains the form config of the tool
     *
     * @param $targetDir
     */
    private function generateModuleToolConfig($targetDir)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcSteps = $container['melis-toolcreator'];
        $moduleName = $tcSteps['step1']['tcf-name'];

        $strCol = array();
        $strSearchableCols = array();
        $colWidth = number_format(100/count($tcSteps['step4']['tcf-db-table-cols']), 0);
        foreach ($tcSteps['step4']['tcf-db-table-cols'] As $key => $col){
            $strCol[] = "\t\t\t\t\t\t\t".'\''.$col.'\' => array('.
                        "\n\t\t\t\t\t\t\t\t".'\'text\' => \'tr_'.strtolower($moduleName).'_'.$col.'\','.
                        "\n\t\t\t\t\t\t\t\t".'\'css\' => array(\'width\' => \''.$colWidth.'%\'),'.
                        "\n\t\t\t\t\t\t\t\t".'\'sortable\' => true'.
                        "\n\t\t\t\t\t\t\t".')';
            $strSearchableCols[] = "\t\t\t\t\t\t\t".'\''.$col.'\'';
        }

        $tblCols = implode(','."\n", $strCol);
        $tblSearchCols = implode(','."\n", $strSearchableCols);

        $moduleConfigFiles = $this->moduleTplDir.'/config/app.tools.php';
        $fileContent = file_get_contents($moduleConfigFiles);
        $fileContent = str_replace('#TCTABLECOLUMNS', $tblCols, $fileContent);
        $fileContent = str_replace('#TCTABLESEARCHCOLUMNS', $tblSearchCols, $fileContent);

        $pk = $this->hasPrimaryKey();

        /**
         * Checking if the database table selected ha a primary key,
         * else the update and delete feature would not be available to the
         * generated tool
         */
        $tblActionButtons = '';
        if (!empty($pk)){
            $moduleTblActionButtons = $this->moduleTplDir.'/extra/FormTemplates/edit-delete-tool-config.txt';
            $tblActionButtons = file_get_contents($moduleTblActionButtons);
        }

        $moduleForm = $this->moduleTplDir.'/extra/FormTemplates/form.txt';
        $moduleFormContent = file_get_contents($moduleForm);

        $formInputsTpl = $this->moduleTplDir.'/extra/FormTemplates/input.txt';
        $formInputs = array();
        $formInputFiltersTpl = $this->moduleTplDir.'/extra/FormTemplates/input-filter.txt';
        $formInputFilters = array();
        $formInputNotEmptyTplTpl = $this->moduleTplDir.'/extra/FormTemplates/not-empty-filter.txt';

        foreach ($tcSteps['step5']['tcf-db-table-col-editable'] As $key => $col){
            $formInputsTplContent = file_get_contents($formInputsTpl);
            $formInputsTplContent = str_replace('#TCKEY', $col, $formInputsTplContent);
            $formInputsTplContent = str_replace('#TCINPUTTYPE', $tcSteps['step5']['tcf-db-table-col-type'][$key], $formInputsTplContent);

            $skipValidator = false;
            if (!empty($pk)){
                // If a column is AUTO_INCREMENT
                // This column will skip for having validator
                if (($pk['Extra'] == 'auto_increment') && $pk['Field'] == $col){
                    $skipValidator = true;
                }
            }

            // Generating input validators and filters
            $inputIsRequired = 'false';
            if (!$skipValidator){
                $formInputFilterTplContent = file_get_contents($formInputFiltersTpl);
                $formInputFilterTplContent = str_replace('#TCKEY', $col, $formInputFilterTplContent);

                if (in_array($col, $tcSteps['step5']['tcf-db-table-col-required'])){
                    $inputIsRequired = 'true';
                    $formInputNotEmptyTplTplContent = file_get_contents($formInputNotEmptyTplTpl);
                    $formInputFilterTplContent = str_replace('#TCVALIDATORS', $formInputNotEmptyTplTplContent, $formInputFilterTplContent);
                }

                $formInputFilterTplContent = str_replace('#TCISREQUIRED', $inputIsRequired, $formInputFilterTplContent);
                array_push($formInputFilters, $formInputFilterTplContent);
            }

            // input required attribute
            $formInputsTplContent = str_replace('#TCINPUTREQUIRED', $inputIsRequired, $formInputsTplContent);
            array_push($formInputs, $formInputsTplContent);
        }

        $moduleForm = str_replace('#FORMINPUTS', implode(','."\n", $formInputs), $moduleFormContent);
        $moduleForm = str_replace('#FORMINPUTFILTERS', implode(','."\n", $formInputFilters), $moduleForm);

        $fileContent = str_replace('#TCFORMELEMENTS', $moduleForm, $fileContent);
        $fileContent = str_replace('#TCTABLEACTIONBUTTONS', $tblActionButtons, $fileContent);

        $this->generateFile('app.tools.php', null, $targetDir, $fileContent);
    }

    /**
     * This method generate the Module Model
     * @param $targetDir
     */
    private function generateModuleModel($targetDir)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcSteps = $container['melis-toolcreator'];
        $moduleName = $tcSteps['step1']['tcf-name'];

        $moduleModelFiles = $this->moduleTplDir.'/src/ModuleTpl/Model/ModuleTpl.php';
        $this->generateFile($moduleName.'.php', $moduleModelFiles, $targetDir);
    }

    /**
     * This method generate the Module Module tables
     * @param $targetDir
     */
    private function generateModuleModelTables($targetDir)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcSteps = $container['melis-toolcreator'];
        $moduleName = $tcSteps['step1']['tcf-name'];

        $moduleModelTableFiles = $this->moduleTplDir.'/src/ModuleTpl/Model/Tables/ModuleTplTable.php';

        $fileContent = file_get_contents($moduleModelTableFiles);

        $tcfDbTbl = $tcSteps['step3']['tcf-db-table'];
        $sql = 'DESCRIBE '.$tcfDbTbl;
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $table = $adapter->query($sql, array(5));
        $selectedTbl = $table->toArray();

        /**
         * Checking if the selected database table has a primary key
         * primary key used to update and delete entry from the tool table list
         */
        $varPrimaryKey = '';
        $primaryKey = '';
        foreach ($selectedTbl As $key => $cols){
            // Checking if Selected table has a primary
            // else the target module will not have a update feature
            if ($cols['Key'] == 'PRI'){
                $varPrimaryKey = 'protected $idField;';
                $primaryKey = '$this->idField = \''.$cols['Field'].'\';';
                break;
            }
        }

        $fileContent = str_replace('#TCPVARRIMARYKEY', $varPrimaryKey, $fileContent);
        $fileContent = str_replace('#TCPRIMARYKEYCOLUMN', $primaryKey, $fileContent);

        $this->generateFile($moduleName.'Table.php', null, $targetDir, $fileContent);
    }

    /**
     * This method generetae the Module Model table factory
     * @param $targetDir
     */
    private function generateModuleModelTablesFactory($targetDir)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfSteps = $container['melis-toolcreator'];
        $moduleName = $tcfSteps['step1']['tcf-name'];
        $tcfDbTbl = $tcfSteps['step3']['tcf-db-table'];

        $moduleModelTableFactoryFiles = $this->moduleTplDir.'/src/ModuleTpl/Model/Tables/Factory/ModuleTplTableFactory.php';

        $fileContent = file_get_contents($moduleModelTableFactoryFiles);
        $fileContent = str_replace('#TCDATABASETABLE', $tcfDbTbl, $fileContent);

        $this->generateFile($moduleName.'TableFactory.php', null, $targetDir, $fileContent);
    }

    /**
     * This method generate the Module views
     * @param $targetDir
     */
    private function generateModuleViews($targetDir)
    {
        $moduleViewFiles = $this->moduleTplDir.'/view/module-tpl/index';
        foreach (scandir($moduleViewFiles) As $file){
            if (is_file($moduleViewFiles.'/'.$file)){

                $fileContent = file_get_contents($moduleViewFiles.'/'.$file);

                $this->generateFile($file, null, $targetDir, $fileContent);
            }
        }

        /**
         * Checking if the database table selected ha a primary key,
         * else the update and delete feature would not be available to the
         * generated tool
         */
        if (!empty($this->hasPrimaryKey())){
            $extraFile = $this->moduleTplDir.'/extra/ViewTemplates/render-table-action-edit.phtml';
            $this->generateFile('render-table-action-edit.phtml', $extraFile, $targetDir);
            $extraFile = $this->moduleTplDir.'/extra/ViewTemplates/render-table-action-delete.phtml';
            $this->generateFile('render-table-action-delete.phtml', $extraFile, $targetDir);
        }
    }

    /**
     * This method generate the Module Js assets
     * used for add/update/delete of the entries of the tool
     *
     * @param $targetDir
     */
    private function generateModuleJs($targetDir)
    {
        $moduleJsFile = $this->moduleTplDir.'/public/js/tool.js';

        $fileContent = file_get_contents($moduleJsFile);

        $editDelScripts = '';
        if (!empty($this->hasPrimaryKey())){

            $extraFile = $this->moduleTplDir.'/extra/AssetsTemplates/edit-delete.txt';
            $editDelScripts = file_get_contents($extraFile);
        }

        $fileContent = str_replace('#TCEDITDELELTESCRIPTS', $editDelScripts, $fileContent);

        $this->generateFile('tool.js', null, $targetDir, $fileContent);
    }

    /**
     * This method generate the Module Languages
     * this will use as translations of the tool
     *
     * @param $targetDir
     */
    private function generateModuleLanguages($targetDir)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfSteps = $container['melis-toolcreator'];
        $moduleName = $tcfSteps['step1']['tcf-name'];

        $coreLang = $this->getServiceLocator()->get('MelisCoreTableLang');
        $languages = $coreLang->getLanguageInOrdered()->toArray();

        $translationsSrv = $this->getServiceLocator()->get('MelisCoreTranslation');
        $commonTransTpl = require $this->moduleTplDir.'/extra/Languages/languages.php';

        // Common translation
        $commonTranslations = array();
        foreach ($languages As $lang){
            foreach ($commonTransTpl As $cKey => $cText){
                $commonTranslations[$lang['lang_locale']][$cKey] = $translationsSrv->getMessage($cText, $lang['lang_locale']);
            }
        }

        // Merging texts from steps forms
        $stepTexts = array_merge_recursive($tcfSteps['step6'], $commonTranslations, $tcfSteps['step2']);

        $translations = array();
        $textFields = array();

        foreach ($languages As $lang){
            $translations[$lang['lang_locale']] = array();
            if (!empty($stepTexts[$lang['lang_locale']])){
                foreach($stepTexts[$lang['lang_locale']] As $key => $text){
                    if ($key != 'tcf-lang-local'){
                        $translations[$lang['lang_locale']][$key] = $text;

                        // Getting fields that has a value
                        // this will be use as default value if a field doesn't have value
                        if (!empty($text)){
                            $textFields[$key] = $text;
                        }
                    }
                }
            }
        }

        // Assigning values to the fields that doesn't have value(s)
        foreach ($translations As $local => $texts){
            foreach ($textFields As $key => $text){
                if (empty($texts[$key])){
                    $translations[$local][$key] = $text;
                }
            }
        }

        $moduleCtrlFiles = $this->moduleTplDir.'/language/language-tpl.php';

        foreach ($translations As $locale => $texts){
            $strTranslations = '';
            foreach ($texts As $key => $text){
                $text = str_replace("'", "\'", $text);
                $key = str_replace("-", "_", $key);
                $key = str_replace("tcf_", "", $key);
                $strTranslations .= "\t\t".'\'tr_'.strtolower($moduleName).'_'.$key.'\' => \''.$text.'\','."\n";
            }

            $fileContent = file_get_contents($moduleCtrlFiles);
            $fileContent = str_replace('#TCTRANSLATIONS', $strTranslations, $fileContent);

            $this->generateFile($locale.'.interface.php', null, $targetDir, $fileContent);
        }
    }

    /**
     * This method generate files to the directory
     *
     * @param string $fileName - file name
     * @param string $sourceDir - directory of the source
     * @param string $targetDir - the target directory where the file will created
     * @param string $fileContent - will be the content of the file created
     */
    private function generateFile($fileName, $sourceDir = null, $targetDir, $fileContent = null)
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcfDbTbl = $container['melis-toolcreator'];
        $moduleName = $tcfDbTbl['step1']['tcf-name'];

        if (is_null($fileContent)){
            $fileContent = file_get_contents($sourceDir);
        }

        $fileContent = str_replace('ModuleTpl', $moduleName, $fileContent);
        $fileContent = str_replace('moduleTpl', lcfirst($moduleName), $fileContent);
        $fileContent = str_replace('moduletpl', strtolower($moduleName), $fileContent);

        $targetFile = fopen($targetDir.'/'.$fileName, 'x');
        fwrite($targetFile, $fileContent);
        fclose($targetFile);
    }

    /**
     * This method the details of the Primary key
     * If the selected Database table has more that one primary key
     * this will only return the FIRST primary key found
     * and set a Primary Key for tool
     *
     * This set to the Model table and other queries (update, delete)
     * @return array
     */
    private function hasPrimaryKey()
    {
        // Tool creator session container
        $container = new Container('melistoolcreator');
        $tcSteps = $container['melis-toolcreator'];

        /**
         * Checking if the database table selected ha a primary key,
         * else the update and delete feature would not be available to the
         * generated tool
         */
        $tcfDbTbl = $tcSteps['step3']['tcf-db-table'];
        $sql = 'DESCRIBE '.$tcfDbTbl;
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $table = $adapter->query($sql, array(5));
        $selectedTbl = $table->toArray();

        $primaryKey = array();
        foreach ($selectedTbl As $col){
            if ($col['Key'] == 'PRI'){
                $primaryKey = $col;
                break;
            }
        }

        return $primaryKey;
    }

    /**
     * This method converting a Module name to a valid view name  directory
     * @param $string - Module name
     * @return string
     */
    private function moduleNameToViewName($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $string));
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
        $langLabel = $langName;

        $langLocale = explode('_', $locale)[0];
        $moduleSvc = $this->getServiceLocator()->get('ModulesService');
        if (file_exists($moduleSvc->getModulePath('MelisCore').'/public/assets/images/lang/'.$langLocale.'.png'))
            $langLabel = '<img src="/MelisCore/assets/images/lang/'.$langLocale.'.png"> '.$langName;

        return $langLabel;
    }

    /**
     * This method setting and retrieving database table cached file
     *
     * @param $reloadCached
     * @return \Zend\Db\Metadata\Object\TableObject[]
     */
    private function getDBTablesCached($reloadCached = false)
    {
        /**
         * Caching Database tables to file cache
         * to avoid slow request for step 2
         */
        $melisEngineCacheSystem = $this->serviceLocator->get('MelisEngineCacheSystem');
        $results = $melisEngineCacheSystem->getCacheByKey($this->cacheKey, $this->cacheConfig, true);
        if (!$results || $reloadCached){
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $metadata = new Metadata($adapter);
            $tables = $metadata->getTables();
            $melisEngineCacheSystem->setCacheByKey($this->cacheKey, $this->cacheConfig, $tables, true);
            $results = $tables;
        }

        return $results;
    }
}