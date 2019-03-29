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

class ModuleTplLangTable extends MelisGenericTable
{
    protected $tableGateway;
    protected $idField;
    
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
        #TCPRIMARYKEYCOLUMN
    }

    public function getLangByFKID($fkId, $langId)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->where('#TCPFKEY ="'. $fkId .'"');
        $select->where('#TCPLANGFKEY  ='. $langId);

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function deleteByFkId($fkId)
    {
        return $this->tableGateway->delete(array('#TCPFKEY' => $fkId));
    }
}