<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Controller;

use Laminas\View\Model\ViewModel;
use MelisCore\Controller\MelisAbstractActionController;

class IndexController extends MelisAbstractActionController
{
    public function renderIframeAction()
    {
        $view = new ViewModel();

        $view->url = '#TCIFRAMEURL';
        return $view;
    }
}