<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace ModuleTpl\Model\Tables;

use MelisCore\Model\Tables\MelisGenericTable;
use Zend\Db\TableGateway\TableGateway;

class ModuleTplTable extends MelisGenericTable
{
    protected $tableGateway;
    #TCPVARRIMARYKEY
    
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
        #TCPRIMARYKEYCOLUMN
    }
}