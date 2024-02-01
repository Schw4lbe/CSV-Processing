<?php

class FetchContr extends Fetch
{
    private $tableName;
    private $page;
    private $itemsPerPage;
    private $sortBy;

    public function __construct($tableName, $page, $itemsPerPage, $sortBy)
    {
        $this->tableName = $tableName;
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
        $this->sortBy = $sortBy;
    }

    public function fetchTableData()
    {
        $data = parent::queryData($this->tableName);
        return $data;
    }
}