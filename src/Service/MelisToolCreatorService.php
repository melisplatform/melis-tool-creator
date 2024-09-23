<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisToolCreator\Service;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Metadata\Metadata;
use Laminas\Db\Metadata\Object\ColumnObject;
use Laminas\Db\Metadata\Object\ConstraintObject;
use Laminas\Session\Container;
use Laminas\View\Model\JsonModel;
use MelisCore\Service\MelisGeneralService;
use MelisCore\Service\MelisServiceManager;
use Symfony\Component;

/**
 * This service manage the creation of the new tool
 */
class MelisToolCreatorService  extends MelisGeneralService
{
    private $moduleTplDir;
    private $tcSteps;

    public function __construct()
    {
        $this->moduleTplDir = __DIR__ .'/../../template';

        // Tool creator session container
        $container = new Container('melistoolcreator');
        $this->tcSteps = $container['melis-toolcreator'];
    }

    /**
     * This method triggered from AJAX request to finalize
     * the tool creation
     *
     * @return JsonModel
     */
    public function createTool()
    {
        // Send event
        $this->sendEvent('melis_tool_creator_generate_tool_start', []);

        $moduleName = $this->moduleName();

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
                        'Tables' => null
                    ],
                    'Service' => null
                ]
            ],
            'view' => []
        ];

        $moduleDir = $_SERVER['DOCUMENT_ROOT'].'/../module/'.$moduleName;

        $this->generateModuleFile($moduleDir);

        // Generating sub dir and files of the module
        $this->generateModuleSubDirsAndFiles($moduleDirs, $moduleDir);

        // Send event
        $this->sendEvent('melis_tool_creator_generate_tool_end', $this->tcSteps);

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

        $code = '';
        $config ='';
        if ($this->isDbTool() || $this->isBlankTool()){
            if (!$this->isBlankTool() && !$this->isFrameworkTool())
                $code = $this->fgc('/Code/module');

            $config = 'include __DIR__ . \'/config/app.interface.php\',';

            if (!$this->isFrameworkTool())
                $config .= PHP_EOL . "\t\t\t". 'include __DIR__ . \'/config/app.tools.php\',';

            if (!$this->isFrameworkTool() && !$this->isBlankTool())
                $config .= PHP_EOL . "\t\t\t".  'include __DIR__ . \'/config/app.microservice.php\',';

            if ($this->isFrameworkTool())
                $config .= PHP_EOL . "\t\t\t". 'include __DIR__ . \'/config/app.framework.php\',';
        }

        $moduleFile = $this->sp('#TCMODULE', $code, $moduleFile);
        $moduleFile = $this->sp('#TCCONFIG', $config, $moduleFile);

        $this->generateFile('Module.php', $moduleDir, $moduleFile);
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

            if (!$this->skipDir($dir)){

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
                    case 'Tables':
                        $this->generateModuleModelTables($tempTargetDir);
                        break;
                    case 'Service':
                        $this->generateModuleService($tempTargetDir);
                        break;
                }

                if (is_array($subDir)){
                    $this->generateModuleSubDirsAndFiles($subDir, $tempTargetDir);
                }
            }
        }
    }

    public function skipDir($dir)
    {
        if ($this->isIframeTool())
            if (in_array($dir, ['Listener', 'Model']))
                return true;

        if ($this->isBlankTool())
            if (in_array($dir, ['Listener', 'Model', 'Service']))
                return true;

        if ($this->isFrameworkTool())
            if (in_array($dir, ['Listener', 'Model', 'Service']))
                return true;

        return false;
    }

    /**
     * This method generates the Module config
     * @param $targetDir
     */
    private function generateModuleConfigs($targetDir)
    {
        $moduleConfigFiles = $this->moduleTplDir.'/Config';
        foreach (scandir($moduleConfigFiles) As $file) {

            if (is_file($moduleConfigFiles.'/'.$file)) {

                if ($file == 'app.toolstree.php') {

                    $this->generateModuleToolstreeConfig($targetDir);

                } elseif ($file == 'app.tools.php' && ($this->isDbTool() || $this->isBlankTool()) && !$this->isFrameworkTool()) {

                    $this->generateModuleToolConfig($targetDir);

                } elseif ($file == 'module.config.php') {

                    $this->generateModuleConfig($targetDir);

                } elseif ($file == 'app.interface.php' && ($this->isDbTool() || $this->isBlankTool())) {

                    $fileName = 'app.interface.php';

                    if ($this->isFrameworkTool())
                        $interfaceContent = $this->fgc('/Code/framework-'.strtolower($this->isFrameworkTool()).'-interface');
                    else
                        $interfaceContent = $this->fgc('/Config/'.$fileName);

                    $this->generateFile($fileName, $targetDir, $interfaceContent);

                } elseif ($file == 'app.microservice.php') {

                    if (!$this->isBlankTool() && !$this->isFrameworkTool()) {
                        $content = $this->fgc('/Config/app.microservice.php');
                        $this->generateFile('app.microservice.php', $targetDir, $content);
                    }

                } elseif ($file == 'app.framework.php' && $this->isFrameworkTool()) {

                    $configFile = $this->fgc('/Code/framework-'. $this->isFrameworkTool() .'-config');
                    $phpConfigFile = $this->sp('#TCFRAMEWORKCONFIG', $configFile, $this->fgc('/Config/'.$file));
                    $this->generateFile($file, $targetDir, $phpConfigFile);
                }
            }
        }
    }

    /**
     * This method generates the tool interface config
     * @param $targetDir
     */
    private function generateModuleToolstreeConfig($targetDir)
    {
        // Application interfaces config
        $toolsTreeContent = $this->fgc('/Config/app.toolstree.php');

        $toolInterface = $this->fgc('/Code/'.$this->getToolEditType().'-interface');

        if ($this->isDbTool() || $this->isBlankTool()){

            if (!$this->isFrameworkTool()) {


                $toolsTreeContent = $this->sp('#TCTOOLSTREE', $this->fgc('/Code/db-toolstree'), $toolsTreeContent);

                if ($this->isBlankTool())
                    $toolInterface = '';
                else
                    if ($this->hasLanguage())
                        $toolInterface = $this->sp('#TCTOOLINTERFACE', $this->fgc('/Code/' . $this->getToolEditType() . '-lang-interface'), $toolInterface);
            } else
                $toolsTreeContent = $this->sp('#TCTOOLSTREE', $this->fgc('/Code/framework-' . $this->getToolEditType(). '-toolstree'), $toolsTreeContent);

        }else{
            $toolsTreeContent = $this->sp('#TCTOOLSTREE', $this->fgc('/Code/iframe-toolstree'), $toolsTreeContent);
        }

        $toolsTreeContent = $this->sp('#TCTOOLINTERFACE', $toolInterface, $toolsTreeContent);
        $this->generateFile('app.toolstree.php', $targetDir, $toolsTreeContent);
    }

    /**
     * This method generate the Module Tool config
     * that contains the form config of the tool
     *
     * @param $targetDir
     */
    private function generateModuleToolConfig($targetDir)
    {
        $tblCols = '';
        $tblSearchCols = '';
        $moduleForm = '';
        $tblActionButtons = '';

        if (!$this->isBlankTool()){

            // Table primary key details
            $pk = $this->hasPrimaryKey();

            // Table columns
            $strCol = [];
            // Table searchable columns
            $strSearchableCols = [];

            $tblCols = $this->fgc('/Code/tbl-cols');


            $mainTableCols = $this->getTableColumns($this->tcSteps['step3']['tcf-db-table']);

            $langTableCols = [];
            if ($this->hasLanguage()) {
                $langTableCols = $this->getTableColumns($this->tcSteps['step3']['tcf-db-table-language-tbl']);
            }

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
                if (!is_bool(strpos($col, 'tclangtblcol_'))) {

                    $col = $this->sp('tclangtblcol_', '', $col);
                    if (in_array($col, $mainTableCols)) {
                        $col = $this->tcSteps['step3']['tcf-db-table-language-tbl'].'.'.$col;
                    }
                } else {
                    if ($this->hasLanguage()) {
                        if (in_array($col, $langTableCols)) {
                            $col = $this->tcSteps['step3']['tcf-db-table'].'.'.$col;
                        }
                    }
                }

                $strSearchableCols[$col] = $col;
            }

            // Format array to string
            foreach ($strSearchableCols As $key => $col)
                $strSearchableCols[$key] = "\t\t\t\t\t\t\t".'\''.$col.'\'';

            $tblCols = implode(','."\n", $strCol);
            $tblSearchCols = implode(','."\n", $strSearchableCols);

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

            $priTable = $this->describeTable($this->tcSteps['step3']['tcf-db-table']);
            $langTable = [];
            if ($this->hasLanguage()){
                $langTable = $this->describeTable($this->tcSteps['step3']['tcf-db-table-language-tbl']);
            }


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
                    case 'Select':
                        $formInputsTplContent = $this->fgc('/Form/select');

                        $selectVals = '';
                        if (is_bool(strpos($col, 'tclangtblcol_'))){
                            // Checking from primary table
                            foreach ($priTable As $dCol)
                                if ($dCol['Field'] == $col){
                                    preg_match('#\((.*?)\)#', $dCol['Type'], $match);
                                    if (!empty($match[1]))
                                        $selectVals = $match[1];
                                }
                        }else{
                            // Checking from language table
                            foreach ($langTable As $dCol)
                                if ($dCol['Field'] == str_replace('tclangtblcol_', '', $col)){
                                    preg_match('#\((.*?)\)#', $dCol['Type'], $match);
                                    if (!empty($match[1]))
                                        $selectVals = $match[1];
                                }
                        }

                        $formInputsTplContent = $this->sp('#TCSELECTVALUES', $selectVals, $formInputsTplContent);
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

                    $formInputValidator = [];
                    if (in_array($col, $this->tcSteps['step5']['tcf-db-table-col-required'])){
                        $inputIsRequired = 'true';
                        array_push($formInputValidator, $this->fgc('/Form/not-empty-validator'));
                    }

                    // Numeric validation
                    $colNumTypes = [
                        'int',
                        'smallint',
                        'mediumint',
                        'tinyint',
                        'bigint',
                        'decimal',
                        'float',
                        'double',
                        'real',
                        'bit',
                        'serial',
                    ];

                    $table = [];
                    if (is_bool(strpos($col, 'tclangtblcol_')))
                        $table = $priTable;
                    else
                        if ($this->hasLanguage())
                            $table = $langTable;

                    if (!empty($table))
                        foreach ($table As $ccol)
                            if ($ccol['Field'] == $col)
                                if (in_array(preg_replace("/\([^)]+\)/", '', $ccol['Type']), $colNumTypes))
                                    array_push($formInputValidator, $this->fgc('/Form/number-validator'));

                    $formInputFilterTplContent = $this->sp('#TCVALIDATORS', implode(','."\n", $formInputValidator), $formInputFilterTplContent);

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
        }

        $fileContent = $this->fgc('/Config/app.tools.php');
        $fileContent = $this->sp('#TCTABLECOLUMNS', $tblCols, $fileContent);
        $fileContent = $this->sp('#TCTABLESEARCHCOLUMNS', $tblSearchCols, $fileContent);
        $fileContent = $this->sp('#TCFORMELEMENTS', $moduleForm, $fileContent);
        $fileContent = $this->sp('#TCTABLEACTIONBUTTONS', $tblActionButtons, $fileContent);

        $this->generateFile('app.tools.php', $targetDir, $fileContent);
    }

    /**
     * This method generate the Module config
     * @param $targetDir
     */
    private function generateModuleConfig($targetDir)
    {
        // Application interfaces config
        $fileContent = $this->fgc('/Config/module.config.php');

        if ($this->isDbTool()){

            if (!$this->isFrameworkTool()){

                $controllers = $this->fgc('/Code/controllers');
                $services = $this->fgc('/Code/services');

                if ($this->hasLanguage()){
                    $controllers = $this->fgc('/Code/lang-controllers');
                    $services = $this->fgc('/Code/lang-services');
                }
            }else{
                $controllers = $this->fgc('/Code/framework-controller');
                $services = '';
            }
        }elseif ($this->isBlankTool()){
            if ($this->isFrameworkTool())
                $blankCtrl = '/Code/framework-blank-controller';
            else
                $blankCtrl = '/Code/blank-controller';

            $controllers = $this->fgc($blankCtrl);
            $services = '';
        }else{
            $controllers = $this->fgc('/Code/iframe-controller');
            $services = '';
        }

        $fileContent = $this->sp('#TCSERVICES', $services, $fileContent);
        $fileContent = $this->sp('#TCCONTROLLERS', $controllers, $fileContent);

        $this->generateFile('module.config.php', $targetDir, $fileContent);
    }

    /**
     * This method generate the Module controller
     * @param $targetDir
     */
    private function generateModuleController($targetDir)
    {
        if ($this->isIframeTool()) {
            $iframeCtrl = $this->fgc('/Controller/IndexController.php');
            $iframeCtrl = $this->sp('#TCIFRAMEURL', $this->tcSteps['step1']['tcf-tool-iframe-url'], $iframeCtrl);
            $this->generateFile('IndexController.php', $targetDir, $iframeCtrl);
            return;
        }

        if ($this->isFrameworkTool()) {
            $frameworkCtrl = $this->fgc('/Controller/Framework-IndexController.php');
            $frameworkCtrl = $this->sp('#TCFRAMEWORK', $this->isFrameworkTool(), $frameworkCtrl);

            $formContent = '';
            if ($this->getToolEditType() == 'tab') {
                $formContent = "\n\n".$this->fgc('/Code/framework-form-content');
            }
            $frameworkCtrl = $this->sp('#TCFORMCONTENT', $formContent, $frameworkCtrl);

            $this->generateFile('IndexController.php', $targetDir, $frameworkCtrl);
            return;
        }

        if ($this->isBlankTool()) {
            $blnkListCtrl = $this->fgc('/Controller/Blank-ListController.php');
            $this->generateFile('ListController.php', $targetDir, $blnkListCtrl);
            return;
        }

        $pk = $this->hasPrimaryKey();
        if (!empty($pk)){

            $modulePropCtrlFile = $this->fgc('/Controller/PropertiesController.php');
            $modulePropCtrlFile = $this->sp('#TCPROPACTIONS', $this->fgc('/Code/'.$this->getToolEditType().'-prop-actions'), $modulePropCtrlFile);

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

            // Date input field data filter
            $dateFields = [];
            foreach ($this->tcSteps['step5']['tcf-db-table-col-type'] As $key => $type)
                if (strpos($type, 'date') !== false && strpos($this->tcSteps['step5']['tcf-db-table-col-editable'][$key], 'tclangtblcol') === false)
                    $dateFields[] = '\''.$this->tcSteps['step5']['tcf-db-table-col-editable'][$key].'\'';



            $dateDataFilter = '';
            if (!empty($dateFields)){
                $dateDataFilter = $this->fgc('/Code/date-input-data');
                $dateDataFilter = $this->sp('#TCDATEINPUTNAME', implode(', ', $dateFields), $dateDataFilter);
            }
            $modulePropCtrlFile = $this->sp('#TCDATEINPTDATA', $dateDataFilter, $modulePropCtrlFile);

            $modulePropCtrlFile = $this->sp('#TCKEY', $pk['Field'], $modulePropCtrlFile);
            $this->generateFile('PropertiesController.php', $targetDir, $modulePropCtrlFile);

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

                // Date input field data filter
                $dateFields = [];
                foreach ($this->tcSteps['step5']['tcf-db-table-col-type'] As $key => $type)
                    if (strpos($type, 'Date') !== false && strpos($this->tcSteps['step5']['tcf-db-table-col-editable'][$key], 'tclangtblcol') !== false)
                        $dateFields[] = '\''.$this->tcSteps['step5']['tcf-db-table-col-editable'][$key].'\'';

                $dateDataFilter = '';
                if (!empty($dateFields)){
                    $dateDataFilter = $this->fgc('/Code/date-input-data');
                    $dateDataFilter = $this->sp('#TCDATEINPUTNAME', implode(', ', $dateFields), $dateDataFilter);
                }
                $langCtrlContent = $this->sp('#TCDATEINPTDATA', "\t\t".$dateDataFilter, $langCtrlContent);

                $langTblPK = $this->getTablePK($this->tcSteps['step3']['tcf-db-table-language-tbl']);
                $langCtrlContent = $this->sp('#TCFKEYID', $langTblPK['Field'], $langCtrlContent);

                $this->generateFile('LanguageController.php', $targetDir, $langCtrlContent);
            }
        }

        $listCtrlFile = $this->fgc('/Controller/ListController.php');
        $modalAction = ($this->getToolEditType() == 'modal') ? $this->fgc('/Code/modal-action') : '';
        $listCtrlFile = $this->sp('#TCMODALVIEWMODEL', $modalAction, $listCtrlFile);

        // Column display filter flag
        $hasColDisplayFilter = false;

        // Data empty filter
        $emptyDataFilter = '';
        if ($this->hasLanguage()){
            $requiredLangFields = [];

            foreach ($this->tcSteps['step4']['tcf-db-table-cols'] As $val)
                if (!is_bool(strpos($val, 'tclangtblcol_')))
                    array_push($requiredLangFields, '\''. $this->sp('tclangtblcol_', '', $val .'\''));

            foreach ($this->tcSteps['step5']['tcf-db-table-col-required'] As $val)
                if (!is_bool(strpos($val, 'tclangtblcol_'))) {
                    $col = '\''.$this->sp('tclangtblcol_', '', $val .'\'');
                    if (!in_array($col, $requiredLangFields))
                        array_push($requiredLangFields,  $col);
                }

            $emptyDataFilter = $this->sp('#TCREQUIRETBLFIELDS', implode(', ', $requiredLangFields), $this->fgc('/Code/empty-columns'));
            $emptyDataFilter = $this->sp('#TCPFKEY', $this->tcSteps['step3']['tcf-db-table-language-pri-fk'], $emptyDataFilter);
            $emptyDataFilter = $this->sp('#TCLANGFKEY', $this->tcSteps['step3']['tcf-db-table-language-lang-fk'], $emptyDataFilter);

            // Columns display
            $tblColDisplay = '';
            $tblColDisplayFilters = [];
            foreach ($this->tcSteps['step4']['tcf-db-table-cols'] As $key => $col){
                if (!is_bool(strpos($col, 'tclangtblcol_')) && $this->tcSteps['step4']['tcf-db-table-col-display'][$key] != 'raw_view'){
                    $tblColDisplayFilters[] = $this->sp(
                        ['#TCCOLUMN', '#TCCOLDISPLAY'],
                        [$this->sp('tclangtblcol_', '', $col), $this->tcSteps['step4']['tcf-db-table-col-display'][$key]],
                        $this->fgc('/Code/tbl-col-display-filter-lang')
                    );
                }
            }

            if (!empty($tblColDisplayFilters)){
                $hasColDisplayFilter = true;
                $tblColDisplay = implode(PHP_EOL, $tblColDisplayFilters);
            }

            $emptyDataFilter = $this->sp('#TCTABLECOLDISPLAYFILTER', $tblColDisplay, $emptyDataFilter);
        }
        $listCtrlFile = $this->sp('#TCDATAEMPTYFILTER', $emptyDataFilter, $listCtrlFile);


        // Blob input field data filter
        $blobFields = [];
        // Primary talbe
        foreach ($this->describeTable($this->tcSteps['step3']['tcf-db-table']) As $col)
            if (!is_bool(strpos($col['Type'], 'blob')))
                array_push($blobFields, $col['Field']);
        // language table
        if ($this->hasLanguage())
            foreach ($this->describeTable($this->tcSteps['step3']['tcf-db-table-language-tbl']) As $col)
                if (!is_bool(strpos($col['Type'], 'blob')))
                    array_push($blobFields, $col['Field']);

        // Blog type columns
        $blobFilter = '';
        if (!empty($blobFields)){
            $blobFilter = $this->fgc('/Code/blob-data-filter');
            $blobData = [];
            foreach ($blobFields As $col)
                array_push($blobData, "\t\t\t\t".'unset($tableData[$key][\''.$col.'\']);');

            $blobDataStr = implode("\n", $blobData);
            $blobFilter = $this->sp('#TCBLOBFIELD', $blobDataStr, $blobFilter);
        }
        $listCtrlFile = $this->sp('#TCBLOBDATAFILTER', $blobFilter, $listCtrlFile);

        // Columns display
        $tblColDisplay = '';
        $tblColDisplayFilters = [];
        foreach ($this->tcSteps['step4']['tcf-db-table-cols'] As $key => $col){
            if (is_bool(strpos($col, 'tclangtblcol_')) && $this->tcSteps['step4']['tcf-db-table-col-display'][$key] != 'raw_view'){
                $tblColDisplayFilters[] = $this->sp(
                    ['#TCCOLUMN', '#TCCOLDISPLAY'],
                    [$col, $this->tcSteps['step4']['tcf-db-table-col-display'][$key]],
                    $this->fgc('/Code/tbl-col-display-filter')
                );
            }
        }

        if (!empty($tblColDisplayFilters)){
            $hasColDisplayFilter = true;
            $tblColDisplay = $this->sp('#TCCOLFILTERS', implode(PHP_EOL, $tblColDisplayFilters) , $this->fgc('/Code/tbl-col-display'));
        }

        // Melis core event service
        $coreEventSrv = '';
        if ($hasColDisplayFilter)
            $coreEventSrv = '$coreSrv = $this->getServiceManager()->get(\'MelisGeneralService\');';

        $listCtrlFile = $this->sp('#TCCOREEVENTSERVICE', $coreEventSrv, $listCtrlFile);
        $listCtrlFile = $this->sp('#TCTABLECOLDISPLAYFILTER', $tblColDisplay, $listCtrlFile);



        $this->generateFile('ListController.php', $targetDir, $listCtrlFile);
    }

    private function generateModuleListeners($targetDir)
    {
        if ($this->isFrameworkTool())
            return;

        // Save listener
        $saveListener = $this->fgc('/Listener/SavePropertiesListener');
        $saveLangDispatch = '';
        if ($this->hasLanguage())
            $saveLangDispatch = $this->fgc('/Code/save-lang-dispatch');
        $saveListener = $this->sp('#TCSAVELISTENER', $saveLangDispatch, $saveListener);
        $this->generateFile('SavePropertiesListener.php', $targetDir, $saveListener);

        // Delete Listener
        $deleteListener = $this->fgc('/Listener/DeleteListener');
        $deleteLangDispatch = '';
        if ($this->hasLanguage())
            $deleteLangDispatch = $this->fgc('/Code/delete-lang-dispatch');
        $deleteListener = $this->sp('#TCDELETELISTENER', $deleteLangDispatch, $deleteListener);
        $this->generateFile('DeleteListener.php', $targetDir, $deleteListener);
    }


    /**
     * This method generate the Module views
     * @param $targetDir
     */
    private function generateModuleViews($targetDir)
    {

        // Generating view module directory
        mkdir($targetDir.'/'.$this->moduleViewName(), 0777);

        if ($this->isDbTool()){

            if (!$this->isFrameworkTool()){
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

                if ($this->getToolEditType() == 'modal'){
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
            }else{
                if ($this->getToolEditType() != 'tab'){
                    $toolViews = [
                        'index' => [
                            'framework-tool',
                        ]
                    ];
                } else {
                    $toolViews = [
                        'index' => [
                            'framework-tool',
                            'framework-tool-form'
                        ]
                    ];
                }
            }

        }elseif ($this->isIframeTool()){

            $toolViews = [
                'index' => [
                    'iframe'
                ]
            ];

        }elseif ($this->isBlankTool()){

            if (!$this->isFrameworkTool()) {
                $listViews = [
                    'table-filter-limit',
                    'table-filter-refresh',
                    'table-filter-search',
                    'blank-tool-content',
                    'blank-tool-header',
                    'tool',
                ];

                $toolViews = [
                    'list' => $listViews
                ];
            } else {
                // Framework blank tool
                $toolViews = [
                    'index' => [
                        'framework-tool',
                    ]
                ];

            }
        }

        // Generating Views to the target module directory
        foreach ($toolViews As $ctrl => $files){

            $viewDir = $targetDir.'/'.$this->moduleViewName().'/'.$ctrl;
            mkdir($viewDir, 0777);

            foreach ($files As $file){
                if (!is_bool(strpos($file, 'blank'))){
                    // Blank view template
                    $fileName = 'blank-render-'.$this->sp('blank-', '', $file).'.phtml';
                }elseif (!is_bool(strpos($file, 'framework'))){
                    // Framework view template
                    $fileName = 'framework-render-'.$this->sp('framework-', '', $file).'.phtml';
                }else{
                    $fileName = 'render-'.$file.'.phtml';
                }

                if (is_file($this->moduleTplDir.'/View/'.$fileName)){
                    $fileContent = $this->fgc('/View/'.$fileName);
                    // Renaming view file for tool properties
                    $fileName = str_replace('prop-', '', $fileName);
                    $fileName = str_replace('blank-', '', $fileName);
                    $fileName = str_replace('framework-', '', $fileName);
                    $this->generateFile($fileName, $viewDir, $fileContent);
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
        if ($this->isBlankTool()) {
            $fileContent = $this->fgc('/Asset/blank-tool.js');
            $this->generateFile('tool.js', $targetDir, $fileContent);
        }

        if (!$this->isDbTool())
            return;

        if (!$this->isFrameworkTool() && !$this->isBlankTool()) {
            $addBtnScript = $this->fgc('/Asset/'.$this->getToolEditType().'-add-btn');
            $saveScript = $this->fgc('/Asset/'.$this->getToolEditType().'-save');
            $ediBtnScript = $this->fgc('/Asset/'.$this->getToolEditType().'-edit-btn');
            $closeTabDelete = ($this->getToolEditType() == 'tab') ? $this->fgc('/Asset/tab-delete') : '';
            $langSave = ($this->hasLanguage()) ? $this->fgc('/Asset/'.$this->getToolEditType().'-lang-save'): '';

            $saveScript = $this->sp('#TCSAVELANG', $langSave, $saveScript);

            $pk = $this->hasPrimaryKey();
            if (!empty($pk))
                $saveScript = $this->sp('#TCPKEY', $pk['Field'], $saveScript);

            $fileContent = $this->fgc('/Asset/tool.js');
            $fileContent = $this->sp('#TCADDBTN', $addBtnScript, $fileContent);
            $fileContent = $this->sp('#TCSAVE', $saveScript, $fileContent);
            $fileContent = $this->sp('#TCEDIT', $ediBtnScript, $fileContent);
            $fileContent = $this->sp('#TCCLOSETABDELETE', $closeTabDelete, $fileContent);
            $this->generateFile('tool.js', $targetDir, $fileContent);
        }else{
            // @TODO Assets of third party tool
            return;
        }
    }

    /**
     * This method generate the Module Languages
     * this will use as translations of the tool
     *
     * @param $targetDir
     */
    private function generateModuleLanguages($targetDir)
    {
        $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
        $languages = $coreLang->fetchAll()->toArray();
        $translationsSrv = $this->getServiceManager()->get('MelisCoreTranslation');

        if ($this->isDbTool() && !$this->isFrameworkTool()) {

            $commonTransTpl = require $this->moduleTplDir.'/Language/languages.php';

            // Common translation
            $commonTranslations = [];
            foreach ($languages As $lang){
                foreach ($commonTransTpl As $cKey => $cText)
                    $commonTranslations[$lang['lang_locale']][$cKey] = $translationsSrv->getMessage($cText, $lang['lang_locale']);

                if (!empty($this->tcSteps['step6'][$lang['lang_locale']])){
                    $inputTrans = [];
                    foreach ($this->tcSteps['step6'][$lang['lang_locale']]['pri_tbl'] As $key => $trans)
                        $inputTrans['input_'.$key] = $trans;

                    $commonTranslations[$lang['lang_locale']] = array_merge($commonTranslations[$lang['lang_locale']], $inputTrans);

                    if (!empty($this->tcSteps['step6'][$lang['lang_locale']]['lang_tbl'])) {
                        $inputTrans = [];
                        foreach ($this->tcSteps['step6'][$lang['lang_locale']]['lang_tbl'] As $key => $trans)
                            $inputTrans['input_'.$key] = $trans;

                        $commonTranslations[$lang['lang_locale']] = array_merge($commonTranslations[$lang['lang_locale']], $inputTrans);
                    }
                }
            }

            // Merging texts from steps forms
            $stepTexts = array_merge_recursive($this->tcSteps['step2'], $commonTranslations);

            $translations = [];
            $textFields = [];

            // Default value setter
            foreach ($languages As $lang){
                $translations[$lang['lang_locale']] = [];
                if (!empty($stepTexts[$lang['lang_locale']])){
                    foreach($stepTexts[$lang['lang_locale']]  As $key => $text){

                        if (!in_array($key, ['tcf-lang-local', 'tcf-tbl-type'])){
                            // Input description
                            if (strpos($key, 'tcinputdesc')){
                                if (empty($text))
                                    $text = $stepTexts[$lang['lang_locale']][$key];

                                $key = $this->sp('tcinputdesc', 'tooltip', $key);
                                $key = $this->sp('tclangtblcol_', '', $key);
                            }

                            $translations[$lang['lang_locale']][$key] = $text;
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
        }elseif ($this->isIframeTool()){
            $translations = [];
            foreach ($languages As $lang){
                $translations[$lang['lang_locale']] = [
                    'title' => $this->moduleName(),
                ];
            }
        }elseif ($this->isBlankTool() || $this->isFrameworkTool()){
            $translations = [];

            $title = '';
            $desc = '';

            foreach ($languages As $lang){

                if (!empty($this->tcSteps['step2'][$lang['lang_locale']]['tcf-title']) && empty($title))
                    $title = $this->tcSteps['step2'][$lang['lang_locale']]['tcf-title'];

                if (!empty($this->tcSteps['step2'][$lang['lang_locale']]['tcf-desc']) && empty($desc))
                    $desc = $this->tcSteps['step2'][$lang['lang_locale']]['tcf-desc'];

                if (!empty($title) && !empty($desc))
                    break;
            }

            foreach ($languages As $lang){

                if (!empty($this->tcSteps['step2'][$lang['lang_locale']]['tcf-title']))
                    $title = $this->tcSteps['step2'][$lang['lang_locale']]['tcf-title'];

                if (!empty($this->tcSteps['step2'][$lang['lang_locale']]['tcf-desc']))
                    $desc = $this->tcSteps['step2'][$lang['lang_locale']]['tcf-desc'];

                $translations[$lang['lang_locale']] = [
                    'title' => $title,
                    'desc' => $desc,
                ];

                if ($this->isBlankTool() && !$this->isFrameworkTool()) {
                    $translations[$lang['lang_locale']]['header'] = $translationsSrv->getMessage('tr_melistoolcreator_common_header', $lang['lang_locale']);
                    $translations[$lang['lang_locale']]['content'] = $translationsSrv->getMessage('tr_melistoolcreator_common_content', $lang['lang_locale']);
                }
            }
        }

        foreach ($translations As $locale => $texts){
            $strTranslations = '';
            foreach ($texts As $key => $text){
                $text = $this->sp("'", "\'", $text);
                $key = $this->sp('-', '_', $key);
                $key = $this->sp('tcf_', '', $key);

                $strTranslations .= "\t\t".'\'tr_'.strtolower($this->moduleName()).'_'.$key.'\' => \''.$text.'\','."\n";
            }

            $fileContent = $this->fgc('/Language/language-tpl.php');
            $fileContent = $this->sp('#TCTRANSLATIONS', $strTranslations, $fileContent);

            $this->generateFile($locale.'.interface.php', $targetDir, $fileContent);
        }
    }

    /**
     * This method generate the Module Module tables
     * @param $targetDir
     */
    private function generateModuleModelTables($targetDir)
    {
        $adapter = $this->getServiceManager()->get('Laminas\Db\Adapter\Adapter');

        $moduleName = $this->moduleName();

        $fileContent = $this->fgc('/Model/Tables/ModuleTplTable');

        $tcfDbTbl = $this->tcSteps['step3']['tcf-db-table'];
        /**
         * @var MelisToolCreatorService $toolCreatorSrv
         */
        $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
        $selectedTbl = $toolCreatorSrv->describeTable($tcfDbTbl);

        // Table
        $fileContent = $this->sp('#TCPRIMARYTABLE', $tcfDbTbl, $fileContent);

        $primayTblPK = null;
        foreach ($selectedTbl As $key => $cols){
            // Getting the first primary ky of the table as module primary key
            if ($cols['Key'] == 'PRI'){
                $primayTblPK = $cols['Field'];
                // Primary Key
                $fileContent = $this->sp('#TCPRIMARYKEY', $primayTblPK, $fileContent);
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

        // Adding get-item-by-id function when the tool also has language enabled
        if ($this->hasLanguage()) {
            $tblJoinSyntax = $this->fgc('/Code/table-get-item-join-syntax');
            $tblJoinSyntax = $this->sp('#TCPLANGTABLE', $this->tcSteps['step3']['tcf-db-table-language-tbl'], $tblJoinSyntax);
            $tblJoinSyntax = $this->sp('#TCPLANGTBLFK', $this->tcSteps['step3']['tcf-db-table-language-pri-fk'], $tblJoinSyntax);
            $tblJoinSyntax = $this->sp('#TCPPRIMARYTABLE', $this->tcSteps['step3']['tcf-db-table'], $tblJoinSyntax);
            $tblJoinSyntax = $this->sp('#TCPPRIMARYTBLPK', $primayTblPK, $tblJoinSyntax);

            $getItemContents = $this->fgc('/Code/table-get-item-contents-with-lang');
            $getItemContents = $this->sp('#TCPJOINSYNTX', $tblJoinSyntax, $getItemContents);
            $getItemContents = $this->sp('#TCPPRIMARYTABLE', $this->tcSteps['step3']['tcf-db-table'], $getItemContents);
            $fileContent = $this->sp('#GETITEMBYID', $getItemContents, $fileContent);
        } else {
            $fileContent = $this->sp('#GETITEMBYID', '', $fileContent);
        }

        $this->generateFile($moduleName.'Table.php', $targetDir, $fileContent);

        if ($this->hasLanguage()){

            $fileContent = $this->fgc('/Model/Tables/ModuleTplLangTable');

            $tcfDbTbl = $this->tcSteps['step3']['tcf-db-table-language-tbl'];

            /**
             * @var MelisToolCreatorService $toolCreatorSrv
             */
            $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
            $selectedTbl = $toolCreatorSrv->describeTable($tcfDbTbl);

            // Table
            $fileContent = $this->sp('#TCLANGTABLE', $tcfDbTbl, $fileContent);

            foreach ($selectedTbl As $key => $cols){
                // Checking if Selected table has a primary
                // else the target module will not have a update feature
                if ($cols['Key'] == 'PRI'){
                    $primayTblPK = $cols['Field'];
                    // Primary Key
                    $fileContent = $this->sp('#TCLANGPRIMARYKEY', $primayTblPK, $fileContent);
                    break;
                }
            }

            $fileContent = $this->sp('#TCPFKEY', $this->tcSteps['step3']['tcf-db-table-language-pri-fk'], $fileContent);
            $fileContent = $this->sp('#TCPLANGFKEY', $this->tcSteps['step3']['tcf-db-table-language-lang-fk'], $fileContent);

            $this->generateFile($moduleName.'LangTable.php', $targetDir, $fileContent);
        }
    }

    /**
     * Generates service + service factory files
     * @param $targetDir
     */
    private function generateModuleService($targetDir)
    {
        if ($this->isFrameworkTool())
            return;

        // Service
        $servicePath = $targetDir;
        $fileContent = $this->fgc('/Service/service');

        if ($this->hasLanguage()) {
            $langSection = $this->fgc('/Code/service-lang-section');
            $getItemByIdContent = $this->fgc('/Code/service-get-item-contents-with-lang');
            $fileContent = str_replace('#SERVICESECTIONLANG', $langSection, $fileContent);
            $fileContent = str_replace('#SERVICEGETITEMBYIDCONTENT', $getItemByIdContent, $fileContent);
        } else {
            $getItemByIdContent = $this->fgc('/Code/service-get-item-contents');
            $fileContent = str_replace('#SERVICEGETITEMBYIDCONTENT', $getItemByIdContent, $fileContent);
            $fileContent = str_replace('#SERVICESECTIONLANG', '', $fileContent);
        }

        $this->generateFile($this->moduleName().'Service.php', $servicePath, $fileContent);
    }

    /**
     * This method generate files to the directory
     *
     * @param string $fileName - file name
     * @param string $targetDir - the target directory where the file will created
     * @param string $fileContent - will be the content of the file created
     */
    private function generateFile($fileName, $targetDir, $fileContent = null)
    {
        // Tool creator session container
        $moduleName = $this->moduleName();

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

    public function moduleName()
    {
        return $this->generateModuleNameCase($this->tcSteps['step1']['tcf-name']);
    }

    public function moduleViewName()
    {
        return $this->moduleNameToViewName($this->generateModuleNameCase($this->tcSteps['step1']['tcf-name']));
    }

    private function isDbTool()
    {
        return $this->tcSteps['step1']['tcf-tool-type'] == 'db' ? true : false;
    }

    private function isIframeTool()
    {
        return $this->tcSteps['step1']['tcf-tool-type'] == 'iframe' ? true : false;
    }

    private function isBlankTool()
    {
        return $this->tcSteps['step1']['tcf-tool-type'] == 'blank' ? true : false;
    }

    private function getToolEditType()
    {
        return $this->tcSteps['step1']['tcf-tool-edit-type'];
    }

    public function isFrameworkTool()
    {
        return (!empty($this->tcSteps['step1']['tcf-create-framework-tool'])) ? $this->tcSteps['step1']['tcf-tool-framework'] : false;
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
        $tcfDbTbl = $tcSteps['step3']['tcf-db-table'];
        return $this->getTablePK($tcfDbTbl);
    }

    public function getTablePK($table)
    {
        $selectedTbl = $this->describeTable($table);

        $primaryKey = [];
        foreach ($selectedTbl As $col){
            if ($col['Key'] == 'PRI' && $col['Extra'] == 'auto_increment'){
                $primaryKey = $col;
                break;
            }
        }

        return $primaryKey;
    }

    public function getTableColumns($table)
    {
        $selectedTbl = $this->describeTable($table);

        $tblCols = [];
        foreach ($selectedTbl As $col)
            $tblCols[] = $col['Field'];

        return $tblCols;
    }

    public function describeTable($table)
    {
        $adapter = $this->getServiceManager()->get('Laminas\Db\Adapter\Adapter');
        $tableAdapter = $adapter->query('DESCRIBE '.$table, Adapter::QUERY_MODE_EXECUTE);

        return $tableAdapter->toArray();
    }

    /**
     * This will modified a string to valid zf2 module name
     * @param string $str
     * @return string
     */
    public function generateModuleNameCase($str) {
        $str = preg_replace('/([a-z])([A-Z])/', "$1$2", $str);
        $str = str_replace(['-', '_'], '', ucwords(strtolower($str)));
        $str = ucfirst($str);
        $str = $this->cleanString($str);
        return $str;
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
     * Clean strings from special characters
     *
     * @param string $str
     * @return string
     */
    public function cleanString($str)
    {
        $str = preg_replace("/[áàâãªä]/u", "a", $str);
        $str = preg_replace("/[ÁÀÂÃÄ]/u", "A", $str);
        $str = preg_replace("/[ÍÌÎÏ]/u", "I", $str);
        $str = preg_replace("/[íìîï]/u", "i", $str);
        $str = preg_replace("/[éèêë]/u", "e", $str);
        $str = preg_replace("/[ÉÈÊË]/u", "E", $str);
        $str = preg_replace("/[óòôõºö]/u", "o", $str);
        $str = preg_replace("/[ÓÒÔÕÖ]/u", "O", $str);
        $str = preg_replace("/[úùûü]/u", "u", $str);
        $str = preg_replace("/[ÚÙÛÜ]/u", "U", $str);
        $str = preg_replace("/[’‘‹›‚]/u", "'", $str);
        $str = preg_replace("/[“”«»„]/u", '"', $str);
        $str = str_replace("–", "-", $str);
        $str = str_replace(" ", " ", $str);
        $str = str_replace("ç", "c", $str);
        $str = str_replace("Ç", "C", $str);
        $str = str_replace("ñ", "n", $str);
        $str = str_replace("Ñ", "N", $str);

        return ($str);
    }

    private function fgc($path)
    {
        return file_get_contents($this->moduleTplDir.$path);
    }

    private function sp($srch, $replce, $subjct)
    {
        return str_replace($srch, $replce, $subjct);
    }

    private function hasMicroServicesAccess()
    {
        return !empty($this->tcSteps['step1']['tcf-create-microservice']) ? true : false;
    }
}