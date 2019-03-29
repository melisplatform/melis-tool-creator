<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisToolCreator\Service\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use MelisToolCreator\Service\MelisToolCreatorService;

class MelisToolCreatorServiceFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{ 
		$melisToolCreatorService = new MelisToolCreatorService();
        $melisToolCreatorService->setServiceLocator($sl);
		return $melisToolCreatorService;
	}
}