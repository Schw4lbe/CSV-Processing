<?php

class UpdateContr extends Update
{
    private $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function fetchTableData()
    {
        $data = parent::queryData($this->tableName);
        return $data;
    }
}