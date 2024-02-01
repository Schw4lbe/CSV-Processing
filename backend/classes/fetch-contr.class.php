<?php

class FetchContr extends Fetch
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