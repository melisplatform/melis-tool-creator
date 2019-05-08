<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisToolCreator\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\JsonModel;

/**
 * This service manage the creation of the new tool
 */
class MelisToolCreatorService  implements  ServiceLocatorAwareInterface
{
	protected $serviceLocator;
    private $moduleTplDir;
    private $tcSteps;

    public function __construct()
    {
        $this->moduleTplDir = __DIR__ .'/../../snippets';

        // Tool creator session container
        $container = new Container('melistoolcreator');
        $this->tcSteps = $container['melis-toolcreator'];
    }
	
	public function setServiceLocator(ServiceLocatorInterface $sl)
	{
		$this->serviceLocator = $sl;
		return $this;
	}
	
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}

    /**
     * This method triggered from AJAX request to finalize
     * the tool creation
     *
     * @return JsonModel
     */
    public function createTool()
    {
        $moduleName = $this->tcSteps['step1']['tcf-name'];

        /**
         * Module directories
         * this directories based on ZF2 framework
         */
        $moduleDirs = [
            'config' => null,
            'language' => null,
            'public' => [
                'js' => null
            ],
            'src' => [
                $moduleName => [
                    'Controller' => null,
                    'Listener' => null,
                    'Model' => [
                        'Tables' => [
                            'Factory' => null
                        ]
                    ]
                ]
            ],
            'view' => []
        ];

        $moduleDir = $_SERVER['DOCUMENT_ROOT'].'/../module/'.$moduleName;

        $this->generateModuleFile($moduleDir);

        // Generating sub dir and files of the module
        $this->generateModuleSubDirsAndFiles($moduleDirs, $moduleDir);

        return new JsonModel(['success' => true]);
    }


    /**
     * This method generate Module.php of the Module
     * @param $moduleName
     */
    private function generateModuleFile($moduleDir)
    {
        // Create module
        mkdir($moduleDir, 0777);
        $moduleFile = $this->fgc('/Module/Module.php');
        $this->generateFile('Module.php', null, $moduleDir, $moduleFile);
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

            // Generating directory
            mkdir($tempTargetDir, 0777);

            switch ($dir){
                case 'config':
                    $this->generateModuleConfigs($tempTargetDir);
                    break;
                case 'Controller':
                    $this->generateModuleController($tempTargetDir);
                    break;
                case 'Listener':
                    $this->generateModuleListeners($tempTargetDir);
                    break;
                case 'view':
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
    private function generateModuleConfigs($targetDir)
    {
        $moduleConfigFiles = $this->moduleTplDir.'/Config';
        foreach (scandir($moduleConfigFiles) As $file){
            if (is_file($moduleConfigFiles.'/'.$file)){
                if ($file == 'app.toolstree.php'){
                    $this->generateModuleInterfaceConfig($targetDir);
                }elseif ($file == 'app.tools.php'){
                    $this->generateModuleToolConfig($targetDir);
                }elseif ($file == 'module.config.php'){
                    $this->generateModuleConfig($targetDir);
                }elseif ($file == 'app.interface.php'){
                    $interfaceContent = $this->fgc('/Config/app.interface.php');
                    $this->generateFile('app.interface.php', null, $targetDir, $interfaceContent);
                }
            }
        }
    }

    /**
     * This method generates the tool interface config
     * @param $targetDir
     */
    private function generateModuleInterfaceConfig($targetDir)
    {
        // Application interfaces config
        $toolsTreeContent = $this->fgc('/Config/app.toolstree.php');

        $toolInterface = $this->fgc('/Code/'.$this->getToolType().'-interface');

        if ($this->hasLanguage())
            $toolInterface = $this->sp('#TCTOOLINTERFACE', $this->fgc('/Code/'.$this->getToolType().'-lang-interface'), $toolInterface);

        $toolsTreeContent = $this->sp('#TCTOOLINTERFACE', $toolInterface, $toolsTreeContent);
        $this->generateFile('app.toolstree.php', null, $targetDir, $toolsTreeContent);
    }

    /**
     * This method generate the Module Tool config
     * that contains the form config of the tool
     *
     * @param $targetDir
     */
    private function generateModuleToolConfig($targetDir)
    {
        // Table primary key details
        $pk = $this->hasPrimaryKey();

        // Table columns
        $strCol = [];
        // Table searchable columns
        $strSearchableCols = [];

        $tblCols = $this->fgc('/Code/tbl-cols');

        // Dividing length of table to several columns
        $colWidth = number_format(100/count($this->tcSteps['step4']['tcf-db-table-cols']), 0);
        foreach ($this->tcSteps['step4']['tcf-db-table-cols'] As $key => $col){

            // Primary column use to update and delete raw entry
            $priCol = ($col == $pk['Field']) ? 'DT_RowId' : $col;

            $strColTmp = $this->sp('#TCKEYCOL', $priCol, $tblCols);
            $strColTmp = $this->sp('#TCKEY', $col, $strColTmp);
            $strCol[] = $this->sp('#TCTBLKEY', $colWidth, $strColTmp);

            /**
             * This will avoid duplication of columns in
             * searchable keys and to avoid sql issue
             */
            if (!is_bool(strpos($col, 'tclangtblcol_')))
                $col = $this->sp('tclangtblcol_', '', $col);

            if (!isset($strSearchableCols[$col]))
                $strSearchableCols[$col] = $col;
            else
                $strSearchableCols[$col] = $this->tcSteps['step3']['tcf-db-table'].'.'.$col;
        }

        // Format array to string
        foreach ($strSearchableCols As $key => $col)
            $strSearchableCols[$key] = "\t\t\t\t\t\t\t".'\''.$col.'\'';

        $tblCols = implode(','."\n", $strCol);
        $tblSearchCols = implode(','."\n", $strSearchableCols);

        $fileContent = $this->fgc('/Config/app.tools.php');
        $fileContent = $this->sp('#TCTABLECOLUMNS', $tblCols, $fileContent);
        $fileContent = $this->sp('#TCTABLESEARCHCOLUMNS', $tblSearchCols, $fileContent);

        $langTablePK = null;
        if ($this->hasLanguage()){
            $langTable = $this->getTablePK($this->tcSteps['step3']['tcf-db-table-language-tbl']);
            if (!empty($langTable))
                if (($langTable['Extra'] == 'auto_increment'))
                    $langTablePK = $langTable['Field'];
        }

        /**
         * Checking if the database table selected ha a primary key,
         * else the update and delete feature would not be available to the
         * generated tool
         */
        $tblActionButtons = '';
        if (!empty($pk))
            $tblActionButtons = $this->fgc('/Form/edit-delete-tool-config');

        $formInputs = [];
        $formInputFilters = [];

        $langInputs = [];
        $langInputFilters = [];

        $formHiddenInputTplContent = $this->fgc('/Form/input-hidden');

        foreach ($this->tcSteps['step5']['tcf-db-table-col-editable'] As $key => $col){

            $inptType = $this->tcSteps['step5']['tcf-db-table-col-type'][$key];

            switch ($inptType){
                case 'Switch':
                    $formInputsTplContent = $this->fgc('/Form/switch-input');
                    break;
                case 'File':
                    $formInputsTplContent = $this->fgc('/Form/file-input');
                    break;
                default:
                    $formInputsTplContent = $this->fgc('/Form/input');
                    break;
            }

            $formInputsTplContent = $this->sp('#TCKEY', $col, $formInputsTplContent);
            $formInputsTplContent = $this->sp('#TCINPUTTYPE', $inptType, $formInputsTplContent);



            $skipValidator = false;
            if (!empty($pk)){
                // If a column is AUTO_INCREMENT
                // This column will skip for having validator
                if (($pk['Extra'] == 'auto_increment') && $pk['Field'] == $col){
                    $skipValidator = true;
                }
            }

            $disabledInput = '';
            if ($skipValidator)
                $disabledInput = '\'disabled\' => \'disabled\'';
            elseif (!is_null($langTablePK)){

                if (!is_bool(strpos($col, 'tclangtblcol_'))){
                    if ($langTablePK == $this->sp('tclangtblcol_', '', $col))
                        $skipValidator = true;
                }
            }

            $formInputsTplContent = $this->sp('#TCINPTAI', $disabledInput, $formInputsTplContent);

            // Generating input validators and filters
            $inputIsRequired = 'false';
            if (!$skipValidator){
                $formInputFilterTplContent = $this->fgc('/Form/input-filter');
                $formInputFilterTplContent = $this->sp('#TCKEY', $col, $formInputFilterTplContent);

                $formInputNotEmptyTplTplContent = '';
                if (in_array($col, $this->tcSteps['step5']['tcf-db-table-col-required'])){
                    $inputIsRequired = 'true';
                    $formInputNotEmptyTplTplContent = $this->fgc('/Form/not-empty-filter');
                }
                $formInputFilterTplContent = $this->sp('#TCVALIDATORS', $formInputNotEmptyTplTplContent, $formInputFilterTplContent);
                $formInputFilterTplContent = $this->sp('#TCISREQUIRED', $inputIsRequired, $formInputFilterTplContent);

                if (is_bool(strpos($col, 'tclangtblcol_')))
                    array_push($formInputFilters, $formInputFilterTplContent);
                else
                    if (!in_array($this->sp('tclangtblcol_', '', $col), $this->geLanguageFk()))
                        array_push($langInputFilters, $formInputFilterTplContent);
            }

            // input required attribute
            $formInputsTplContent = $this->sp('#TCINPUTREQUIRED', $inputIsRequired, $formInputsTplContent);
            if (is_bool(strpos($col, 'tclangtblcol_')))
                array_push($formInputs, $formInputsTplContent);
            else{
                if (in_array($this->sp('tclangtblcol_', '', $col), $this->geLanguageFk()) || $langTablePK == $this->sp('tclangtblcol_', '', $col))
                    $formInputsTplContent = $this->sp('#TCKEY', $col, $formHiddenInputTplContent);

                array_push($langInputs, $formInputsTplContent);
            }
        }

        $moduleFormContent = $this->fgc('/Form/form');
        $moduleForm = $this->sp('#FORMINPUTS', implode(','."\n", $formInputs), $moduleFormContent);
        $moduleForm = $this->sp('#FORMINPUTFILTERS', implode(','."\n", $formInputFilters), $moduleForm);

        $langForm = '';
        if ($this->hasLanguage()){
            $formLang = $this->fgc('/Form/form-language');
            $langForm = $this->sp('#FORMINPUTS', implode(','."\n", $langInputs), $formLang);
            $langForm = $this->sp('#FORMINPUTFILTERS', implode(','."\n", $langInputFilters), $langForm);
        }
        $moduleForm = $this->sp('#FORMLANGUAGE', $langForm, $moduleForm);

        $fileContent = $this->sp('#TCFORMELEMENTS', $moduleForm, $fileContent);
        $fileContent = $this->sp('#TCTABLEACTIONBUTTONS', $tblActionButtons, $fileContent);

        $this->generateFile('app.tools.php', null, $targetDir, $fileContent);
    }

    /**
     * This method generate the Module config
     * @param $targetDir
     */
    private function generateModuleConfig($targetDir)
    {
        // Application interfaces config
        $fileContent = $this->fgc('/Config/module.config.php');

        $controllers = $this->fgc('/Code/controllers');
        $services = $this->fgc('/Code/services');

        if ($this->hasLanguage()){
            $controllers = $this->fgc('/Code/lang-controllers');
            $services = $this->fgc('/Code/lang-services');
        }

        $fileContent = $this->sp('#TCSERVICES', $services, $fileContent);
        $fileContent = $this->sp('#TCCONTROLLERS', $controllers, $fileContent);

        $this->generateFile('module.config.php', null, $targetDir, $fileContent);
    }

    /**
     * This method generate the Module controller
     * @param $targetDir
     */
    private function generateModuleController($targetDir)
    {
        $pk = $this->hasPrimaryKey();
        if (!empty($pk)){

            $modulePropCtrlFile = $this->fgc('/Controller/PropertiesController.php');
            $modulePropCtrlFile = $this->sp('#TCPROPACTIONS', $this->fgc('/Code/'.$this->getToolType().'-prop-actions'), $modulePropCtrlFile);

            // Checking if there is one file input
            $fileParams = '';
            $fileData = '';
            $fileFilter = '';
            if (in_array('File', $this->tcSteps['step5']['tcf-db-table-col-type'])){
                $fileParams = $this->fgc('/Code/file-input-params');
                $fileData = $this->fgc('/Code/file-input-data');
                $fileFilter = $this->fgc('/Code/file-input-filter');
            }

            $modulePropCtrlFile = $this->sp('#TCFILEINPTPARAMS', $fileParams, $modulePropCtrlFile);
            $modulePropCtrlFile = $this->sp('#TCFILEINPTDATA', $fileData, $modulePropCtrlFile);
            $modulePropCtrlFile = $this->sp('#TCFILEINPTFILTER', $fileFilter, $modulePropCtrlFile);

            $modulePropCtrlFile = $this->sp('#TCKEY', $pk['Field'], $modulePropCtrlFile);
            $this->generateFile('PropertiesController.php', null, $targetDir, $modulePropCtrlFile);

            if ($this->hasLanguage()){

                $langCtrl = $this->fgc('/Controller/LanguageController.php');

                $langCtrlContent = $this->sp('#TCKEYLANGID', $this->tcSteps['step3']['tcf-db-table-language-lang-fk'], $langCtrl);
                $langCtrlContent = $this->sp('#TCKEYPRIID', $this->tcSteps['step3']['tcf-db-table-language-pri-fk'], $langCtrlContent);

                // Checking if there is one file input
                if (in_array('File', $this->tcSteps['step5']['tcf-db-table-col-type'])){
                    $fileParams = $this->fgc('/Code/file-input-params-lang');
                    $fileData = $this->fgc('/Code/file-input-data-lang');
                }

                $langCtrlContent = $this->sp('#TCFILEINPTPARAMS', $fileParams, $langCtrlContent);
                $langCtrlContent = $this->sp('#TCFILEINPTDATA', $fileData, $langCtrlContent);
                $langCtrlContent = $this->sp('#TCFILEINPTFILTER', $fileFilter, $langCtrlContent);

                $langTblPK = $this->getTablePK($this->tcSteps['step3']['tcf-db-table-language-tbl']);
                $langCtrlContent = $this->sp('#TCFKEYID', $langTblPK['Field'], $langCtrlContent);

                $this->generateFile('LanguageController.php', null, $targetDir, $langCtrlContent);
            }
        }

        $listCtrlFile = $this->fgc('/Controller/ListController.php');
        $modalAction = ($this->getToolType() == 'modal') ? $this->fgc('/Code/modal-action') : '';
        $listCtrlFile = $this->sp('#TCMODALVIEWMODEL', $modalAction, $listCtrlFile);
        $this->generateFile('ListController.php', null, $targetDir, $listCtrlFile);
    }

    private function generateModuleListeners($targetDir)
    {
        // Save listener
        $saveListener = $this->fgc('/Listener/SavePropertiesListener');
        $saveLangDispatch = '';
        if ($this->hasLanguage())
            $saveLangDispatch = $this->fgc('/Code/save-lang-dispatch');
        $saveListener = $this->sp('#TCSAVELISTENER', $saveLangDispatch, $saveListener);
        $this->generateFile('SavePropertiesListener.php', null, $targetDir, $saveListener);

        // Delete Listener
        $deleteListener = $this->fgc('/Listener/DeleteListener');
        $deleteLangDispatch = '';
        if ($this->hasLanguage())
            $deleteLangDispatch = $this->fgc('/Code/delete-lang-dispatch');
        $deleteListener = $this->sp('#TCDELETELISTENER', $deleteLangDispatch, $deleteListener);
        $this->generateFile('DeleteListener.php', null, $targetDir, $deleteListener);
    }


    /**
     * This method generate the Module views
     * @param $targetDir
     */
    private function generateModuleViews($targetDir)
    {
        // Generating view module directory
        mkdir($targetDir.'/'.$this->moduleNameToViewName($this->tcSteps['step1']['tcf-name']), 0777);

        // Common view file for tool
        $listViews = [
            'table-filter-limit',
            'table-filter-refresh',
            'table-filter-search',
            'table-action-edit',
            'table-action-delete',
            'tool-content',
            'tool-header',
            'tool',
        ];

        if ($this->getToolType() == 'modal'){
            $listViews[] = 'modal-form';
            $propViews[] = 'properties-form';
        }else{
            $propViews = [
                'prop-tool-main-content',
                'prop-tool-content',
                'prop-tool-header',
                'prop-tool'
            ];
        }

        $toolViews = [
            'list' => $listViews,
            'properties' => $propViews
        ];

        if ($this->hasLanguage()){
            $toolViews['language'] = [
                'language-form'
            ];
        }

        // Generating Views to the target module directory
        foreach ($toolViews As $ctrl => $files){

            $viewDir = $targetDir.'/'.$this->moduleNameToViewName($this->tcSteps['step1']['tcf-name']).'/'.$ctrl;
            mkdir($viewDir, 0777);

            foreach ($files As $file){
                $fileName = 'render-'.$file.'.phtml';

                if (is_file($this->moduleTplDir.'/View/'.$fileName)){
                    $fileContent = $this->fgc('/View/'.$fileName);
                    // Renaming view file for tool properties
                    $fileName = str_replace('prop-', '', $fileName);
                    $this->generateFile($fileName, null, $viewDir, $fileContent);
                }
            }
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
        $addBtnScript = $this->fgc('/Asset/'.$this->getToolType().'-add-btn');
        $saveScript = $this->fgc('/Asset/'.$this->getToolType().'-save');
        $ediBtnScript = $this->fgc('/Asset/'.$this->getToolType().'-edit-btn');
        $closeTabDelete = ($this->getToolType() == 'tab') ? $this->fgc('/Asset/tab-delete') : '';
        $langSave = ($this->hasLanguage()) ? $this->fgc('/Asset/'.$this->getToolType().'-lang-save'): '';

        $saveScript = $this->sp('#TCSAVELANG', $langSave, $saveScript);

        $pk = $this->hasPrimaryKey();
        if (!empty($pk))
            $saveScript = $this->sp('#TCPKEY', $pk['Field'], $saveScript);

        $fileContent = $this->fgc('/Asset/tool.js');
        $fileContent = $this->sp('#TCADDBTN', $addBtnScript, $fileContent);
        $fileContent = $this->sp('#TCSAVE', $saveScript, $fileContent);
        $fileContent = $this->sp('#TCEDIT', $ediBtnScript, $fileContent);
        $fileContent = $this->sp('#TCCLOSETABDELETE', $closeTabDelete, $fileContent);
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
        $moduleName = $this->tcSteps['step1']['tcf-name'];

        $cmsLang = $this->getServiceLocator()->get('MelisEngineTableCmsLang');
        $languages = $cmsLang->fetchAll()->toArray();

        $translationsSrv = $this->getServiceLocator()->get('MelisCoreTranslation');
        $commonTransTpl = require $this->moduleTplDir.'/Language/languages.php';

        // Common translation
        $commonTranslations = [];
        foreach ($languages As $lang){
            foreach ($commonTransTpl As $cKey => $cText)
                $commonTranslations[$lang['lang_cms_locale']][$cKey] = $translationsSrv->getMessage($cText, $lang['lang_cms_locale']);

            if (!empty($this->tcSteps['step6'][$lang['lang_cms_locale']])){
                $commonTranslations[$lang['lang_cms_locale']] = array_merge($commonTranslations[$lang['lang_cms_locale']], $this->tcSteps['step6'][$lang['lang_cms_locale']]['pri_tbl']);

                if (!empty($this->tcSteps['step6'][$lang['lang_cms_locale']]['lang_tbl']))
                    $commonTranslations[$lang['lang_cms_locale']] = array_merge($commonTranslations[$lang['lang_cms_locale']], $this->tcSteps['step6'][$lang['lang_cms_locale']]['lang_tbl']);
            }
        }

        // Merging texts from steps forms
        $stepTexts = array_merge_recursive($this->tcSteps['step2'], $commonTranslations);

        $translations = [];
        $textFields = [];

        // Default value setter
        foreach ($languages As $lang){
            $translations[$lang['lang_cms_locale']] = [];
            if (!empty($stepTexts[$lang['lang_cms_locale']])){
                foreach($stepTexts[$lang['lang_cms_locale']]  As $key => $text){

                    if (!in_array($key, ['tcf-lang-local', 'tcf-tbl-type'])){
                        // Input description
                        if (strpos($key, 'tcinputdesc')){  
                            if (empty($text))
                                $text = $stepTexts[$lang['lang_cms_locale']][$key];

                            $key = $this->sp('tcinputdesc', 'tooltip', $key);
                            $key = $this->sp('tclangtblcol_', '', $key);
                        }

                        $translations[$lang['lang_cms_locale']][$key] = $text;
                    }else
                        $text = '';

                    // Getting fields that has a value
                    // this will be use as default value if a field doesn't have value
                    if (!empty($text))
                        $textFields[$key] = $text;
                }
            }
        }

        // Assigning values to the fields that doesn't have value(s)
        foreach ($translations As $local => $texts)
            foreach ($textFields As $key => $text)
                if (empty($texts[$key]))
                    $translations[$local][$key] = $text;

        foreach ($translations As $locale => $texts){
            $strTranslations = '';
            foreach ($texts As $key => $text){
                $text = $this->sp("'", "\'", $text);
                $key = $this->sp('-', '_', $key);
                $key = $this->sp('tcf_', '', $key);

                $strTranslations .= "\t\t".'\'tr_'.strtolower($moduleName).'_'.$key.'\' => \''.$text.'\','."\n";
            }

            $fileContent = $this->fgc('/Language/language-tpl.php');
            $fileContent = $this->sp('#TCTRANSLATIONS', $strTranslations, $fileContent);

            $this->generateFile($locale.'.interface.php', null, $targetDir, $fileContent);
        }
    }

    /**
     * This method generate the Module Model
     * @param $targetDir
     */
    private function generateModuleModel($targetDir)
    {
        // Tool creator session container
        $moduleName = $this->tcSteps['step1']['tcf-name'];

        $moduleModelFiles = '/Model/ModuleTpl';
        $this->generateFile($moduleName.'.php', $moduleModelFiles, $targetDir);
    }

    /**
     * This method generate the Module Module tables
     * @param $targetDir
     */
    private function generateModuleModelTables($targetDir)
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $moduleName = $this->tcSteps['step1']['tcf-name'];

        $fileContent = $this->fgc('/Model/Tables/ModuleTplTable');

        $tcfDbTbl = $this->tcSteps['step3']['tcf-db-table'];
        $sql = 'DESCRIBE '.$tcfDbTbl;
        $table = $adapter->query($sql, [5]);
        $selectedTbl = $table->toArray();

        $primayTblPK = null;
        foreach ($selectedTbl As $key => $cols){
            // Checking if Selected table has a primary
            // else the target module will not have a update feature
            if ($cols['Key'] == 'PRI'){
                $primayTblPK = $cols['Field'];
                $fileContent = $this->sp('#TCPRIMARYKEYCOLUMN', '$this->idField = \''.$cols['Field'].'\';', $fileContent);
                break;
            }
        }

        $JoinSyntx = '';
        if ($this->hasLanguage()){
            // Join sql syntax
            $JoinSyntx = $this->fgc('/Code/lang-join-sql');
            $JoinSyntx = $this->sp('#TCPLANGTABLE', $this->tcSteps['step3']['tcf-db-table-language-tbl'], $JoinSyntx);
            $JoinSyntx = $this->sp('#TCPLANGTBLFK', $this->tcSteps['step3']['tcf-db-table-language-pri-fk'], $JoinSyntx);
            $JoinSyntx = $this->sp('#TCPLANGTBLLANGFK', $this->tcSteps['step3']['tcf-db-table-language-lang-fk'], $JoinSyntx);
            $JoinSyntx = $this->sp('#TCPPRIMARYTABLE', $this->tcSteps['step3']['tcf-db-table'], $JoinSyntx);
            $JoinSyntx = $this->sp('#TCPPRIMARYTBLPK', $primayTblPK, $JoinSyntx);
        }

        $fileContent = $this->sp('#TCPJOINSYNTX', $JoinSyntx, $fileContent);
        $fileContent = $this->sp('#TCPPRIMARYTABLE', $this->tcSteps['step3']['tcf-db-table'], $fileContent);
        $this->generateFile($moduleName.'Table.php', null, $targetDir, $fileContent);

        if ($this->hasLanguage()){

            $fileContent = $this->fgc('/Model/Tables/ModuleTplLangTable');

            $tcfDbTbl = $this->tcSteps['step3']['tcf-db-table-language-tbl'];
            $sql = 'DESCRIBE '.$tcfDbTbl;
            $table = $adapter->query($sql, [5]);
            $selectedTbl = $table->toArray();

            foreach ($selectedTbl As $key => $cols){
                // Checking if Selected table has a primary
                // else the target module will not have a update feature
                if ($cols['Key'] == 'PRI'){
                    $fileContent = $this->sp('#TCPRIMARYKEYCOLUMN', '$this->idField = \''.$cols['Field'].'\';', $fileContent);
                    break;
                }
            }

            $fileContent = $this->sp('#TCPFKEY', $this->tcSteps['step3']['tcf-db-table-language-pri-fk'], $fileContent);
            $fileContent = $this->sp('#TCPLANGFKEY', $this->tcSteps['step3']['tcf-db-table-language-lang-fk'], $fileContent);

            $this->generateFile($moduleName.'LangTable.php', null, $targetDir, $fileContent);
        }
    }

    /**
     * This method generate the Module Model table factory
     * @param $targetDir
     */
    private function generateModuleModelTablesFactory($targetDir)
    {
        // Tool creator session container
        $moduleName = $this->tcSteps['step1']['tcf-name'];
        $tcfDbTbl = $this->tcSteps['step3']['tcf-db-table'];

        $fileContent = $this->fgc('/Model/Tables/Factory/ModuleTplTableFactory');
        $fileContent = str_replace('#TCDATABASETABLE', $tcfDbTbl, $fileContent);

        $this->generateFile($moduleName.'TableFactory.php', null, $targetDir, $fileContent);

        if ($this->hasLanguage()){
            $fileContent = $this->fgc('/Model/Tables/Factory/ModuleTplLangTableFactory');
            $fileContent = str_replace('#TCDATABASETABLE', $this->tcSteps['step3']['tcf-db-table-language-tbl'], $fileContent);
            $this->generateFile($moduleName.'LangTableFactory.php', null, $targetDir, $fileContent);
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

        if (is_null($fileContent))
            $fileContent = $this->fgc($sourceDir);

        $fileContent = str_replace('ModuleTpl', $moduleName, $fileContent);
        $fileContent = str_replace('moduleTpl', lcfirst($moduleName), $fileContent);
        $fileContent = str_replace('moduletpl', strtolower($moduleName), $fileContent);

        if ($this->hasLanguage())
            $fileContent = $this->sp('tclangtblcol_', '', $fileContent);

        $targetFile = $targetDir.'/'.$fileName;
        if (!file_exists($targetFile)){
            $targetFile = fopen($targetFile, 'x+');
            fwrite($targetFile, $fileContent);
            fclose($targetFile);
        }
    }

    private function getToolType()
    {
        return $this->tcSteps['step1']['tcf-tool-type'];
    }

    private function hasLanguage()
    {
        return !empty($this->tcSteps['step3']['tcf-db-table-has-language']) ? true : false;
    }

    private function geLanguageFk()
    {
        return [$this->tcSteps['step3']['tcf-db-table-language-pri-fk'], $this->tcSteps['step3']['tcf-db-table-language-lang-fk']];
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
    public function hasPrimaryKey()
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
        $table = $adapter->query($sql, [5]);
        $selectedTbl = $table->toArray();

        $primaryKey = [];
        foreach ($selectedTbl As $col){
            if ($col['Key'] == 'PRI'){
                $primaryKey = $col;
                break;
            }
        }

        return $primaryKey;
    }

    private function getTablePK($table)
    {
        $sql = 'DESCRIBE '.$table;
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $tableAdapter = $adapter->query($sql, [5]);
        $selectedTbl = $tableAdapter->toArray();

        $primaryKey = [];
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

    private function fgc($path)
    {
        return file_get_contents($this->moduleTplDir.$path);
    }

    private function sp($srch, $replce, $subjct)
    {
        return str_replace($srch, $replce, $subjct);
    }
}