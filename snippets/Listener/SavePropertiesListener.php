<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use MelisCore\Listener\MelisCoreGeneralListener;
use Zend\Session\Container;
use Zend\Stdlib\ArrayUtils;

class SavePropertiesListener extends MelisCoreGeneralListener implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents      = $events->getSharedManager();

        $callBackHandler = $sharedEvents->attach(
            'ModuleTpl',
            'moduletpl_properties_save_start',
            function($e){

                $savePropertiesResult = $this->dispatchPlugin(
                    $e,
                    'ModuleTpl\Controller\Properties',
                    array(
                        'action' => 'saveProperties'
                    )
                );

                $saveLanguageResult = $this->dispatchPlugin(
                    $e,
                    'ModuleTpl\Controller\Language',
                    array(
                        'action' => 'saveLanguage',
                        'success' => $savePropertiesResult['success'],
                        'id' => $savePropertiesResult['data']['id'],
                    )
                );

                $result = ArrayUtils::merge($savePropertiesResult, $saveLanguageResult);

                $container = new Container('moduletpl');
                $container['moduletpl-save-action'] = $result;

            });

        $this->listeners[] = $callBackHandler;
    }

    private function dispatchPlugin($e, $ctrl, $vars)
    {
        $resultForward = $e->getTarget()->forward()->dispatch($ctrl, $vars);

        return $resultForward->getVariables();
    }
}