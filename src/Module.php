<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisToolCreator;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Session\Container;
use Zend\Stdlib\ArrayUtils;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->createTranslations($e);
    }

    public function getConfig()
    {
        $config = array();
        $configFiles = array(
            include __DIR__ . '/../config/module.config.php',
            include __DIR__ . '/../config/app.interface.php',
            include __DIR__ . '/../config/app.toolstree.php',
        );

        // Existing frameworks
        $frameworks = [];
        $thirdPartyDir = require __DIR__.'/../config/frameworks.php';
        foreach ($thirdPartyDir['frameworks'] As $pf){
            // Checking if the module exist in the vendor directory
            $pfDir = __DIR__ .'/../../melis-platform-framework-'.$pf;
            if (is_dir($pfDir))
                $frameworks[$pf] = ucfirst($pf);
        }

        $toolsConfig = include __DIR__ . '/../config/app.tools.php';

        if (!empty($frameworks)){

            // Frameworks that available to generate tool
            $thirdPartyDir['form-elements']['elements'][1]['spec']['options']['value_options'] = $frameworks;
            // Getting the first data to be the default selected item
            foreach ($frameworks As $key => $fw){
                $thirdPartyDir['form-elements']['elements'][1]['spec']['attributes']['value'] = $key;
                break;
            }

            // Adding to the form config
            // Form elements
            foreach ($thirdPartyDir['form-elements']['elements'] As $spcs)
                $toolsConfig['plugins']['melistoolcreator']['forms']['melistoolcreator_step1_form']['elements'][] = $spcs;
            // Form elements filters
            foreach ($thirdPartyDir['form-elements']['input_filter'] As $spcs)
                $toolsConfig['plugins']['melistoolcreator']['forms']['melistoolcreator_step1_form']['input_filter'][] = $spcs;
        }

        // Adding to final config
        $configFiles[] = $toolsConfig;

        foreach ($configFiles as $file)
            $config = ArrayUtils::merge($config, $file);

        return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function createTranslations($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('translator');

        // Get the locale used from meliscore session
        $container = new Container('meliscore');
        $locale = $container['melis-lang-locale'];

        // Load files
        if (!empty($locale))
        {
            $translationType = array(
                'interface',
            );

            $translationList = array();
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/../module/MelisModuleConfig/config/translation.list.php')){
                $translationList = include 'module/MelisModuleConfig/config/translation.list.php';
            }

            foreach($translationType as $type){

                $transPath = '';
                $moduleTrans = __NAMESPACE__."/$locale.$type.php";

                if(in_array($moduleTrans, $translationList)){
                    $transPath = "module/MelisModuleConfig/languages/".$moduleTrans;
                }

                if(empty($transPath)){

                    // if translation is not found, use melis default translations
                    $defaultLocale = (file_exists(__DIR__ . "/../language/$locale.$type.php"))? $locale : "en_EN";
                    $transPath = __DIR__ . "/../language/$defaultLocale.$type.php";
                }

                $translator->addTranslationFile('phparray', $transPath);
            }
        }
    }

}
