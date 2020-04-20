<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Controller;

use Laminas\View\Model\ViewModel;
use MelisCore\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function renderIframeAction()
    {
        $view = new ViewModel();

        $view->url = '#TCIFRAMEURL';
        return $view;
    }
}