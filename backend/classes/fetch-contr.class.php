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
        $fetchStart = ($this->page - 1) * $this->itemsPerPage;
        $data = parent::queryData($this->tableName, $fetchStart, $this->itemsPerPage, $this->sortBy);
        $totalItems = parent::getItemCount($this->tableName);
        return ["data" => $data, "totalItems" => $totalItems];
    }
}