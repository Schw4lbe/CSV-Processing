<?php

class FetchContr extends Fetch
{
    private $tableName;
    private $page;
    private $itemsPerPage;
    private $sortBy;
    private $searchCategory;
    private $searchQuery;

    public function __construct($tableName, $page, $itemsPerPage, $sortBy, $searchCategory, $searchQuery)
    {
        $this->tableName = strtolower($tableName);
        $this->page = $page;
        $this->itemsPerPage = (int) $itemsPerPage;
        $this->sortBy = $sortBy;
        $this->searchCategory = $searchCategory;
        $this->searchQuery = $searchQuery;
    }

    public function fetchTableData()
    {
        $paramsValid = $this->validateTableParams($this->tableName, $this->page, $this->itemsPerPage, $this->sortBy);
        if (!$paramsValid) {
            error_log("invalid table params in fetchTableData: $this->tableName, $this->page, $this->itemsPerPage, $this->sortBy" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        $totalItems = parent::getRowCount($this->tableName);
        if (!$totalItems) {
            return false;
        }

        $fetchStart = ($this->page - 1) * $this->itemsPerPage;
        $data = parent::queryData($this->tableName, $fetchStart, $this->itemsPerPage, $this->sortBy);
        if (!$data) {
            return false;
        }

        return ["success" => true, "data" => $data, "total" => $totalItems];
    }

    public function fetchSearchData()
    {
        $tableParamsValid = $this->validateTableParams($this->tableName, $this->page, $this->itemsPerPage, $this->sortBy);
        if (!$tableParamsValid) {
            error_log("invalid table params in fetchSearchData: $this->tableName, $this->page, $this->itemsPerPage, $this->sortBy" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        $searchParamsValid = $this->validateSearchParams($this->searchCategory, $this->searchQuery);
        if (!$searchParamsValid) {
            error_log("invalid search params in fetchSearchData: $this->searchCategory, $this->searchQuery" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        if ($tableParamsValid && $searchParamsValid) {
            $totalItems = parent::getSearchCount($this->tableName, $this->searchCategory, $this->searchQuery);
            $fetchStart = ($this->page - 1) * $this->itemsPerPage;
            $data = parent::querySearch($this->tableName, $fetchStart, $this->itemsPerPage, $this->sortBy, $this->searchCategory, $this->searchQuery);
            return ["success" => true, "data" => $data, "total" => $totalItems];
        }
        error_log("check failed! . PHP_EOL, 3, ../logs/app-error.log");
        return false;
    }


    private function validateTableParams($tableName, $page, $itemsPerPage, $sortBy)
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $tableName)) {
            error_log("Invalid tableName: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        if (!preg_match('/^[0-9]+$/', $page)) {
            error_log("Invalid page: $page" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        if (!preg_match('/^[0-9]+$/', $itemsPerPage)) {
            error_log("Invalid page: $itemsPerPage" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        $sortByKey = $sortBy['key'];
        $sortByOrder = $sortBy['order'];

        if (!in_array($sortByKey, ["id"])) {
            error_log("Invalid sortBy key: $sortByKey" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        if (!in_array(strtolower($sortByOrder), ["asc", "desc"])) {
            error_log("Invalid sortBy order: $sortByOrder" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }

    private function validateSearchParams($searchCategory, $searchQuery)
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $searchCategory)) {
            error_log("Invalid searchCategory: $searchCategory" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        $invalCharsRegex = '/[\/:*?"<>|\\\\]/';
        $dirTraversalRegex = '/\.\./';

        if (preg_match($invalCharsRegex, $searchQuery) || preg_match($dirTraversalRegex, $searchQuery)) {
            error_log("Invalid searchQuery: $searchQuery" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }
}