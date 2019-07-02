<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Model\Tables\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ObjectProperty;

use ModuleTpl\Model\ModuleTpl;
use ModuleTpl\Model\Tables\ModuleTplLangTable;

class ModuleTplLangTableFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $sl)
	{
    	$hydratingResultSet = new HydratingResultSet(new ObjectProperty(), new ModuleTpl());
    	$tableGateway = new TableGateway('#TCDATABASETABLE', $sl->get('Zend\Db\Adapter\Adapter'), null, $hydratingResultSet);
		
    	return new ModuleTplLangTable($tableGateway);
	}
}