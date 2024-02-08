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
        $paramsValid = $this->validateTableParams($this->tableName, $this->sortBy);

        if ($paramsValid) {
            $totalItems = parent::getRowCount($this->tableName);
            $fetchStart = ($this->page - 1) * $this->itemsPerPage;

            $data = parent::queryData($this->tableName, $fetchStart, $this->itemsPerPage, $this->sortBy);
            return ["success" => true, "data" => $data, "total" => $totalItems];

        } else {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "SQL statement validation failed!"]);
        }
        exit();
    }

    public function fetchSearchData()
    {
        $tableParamsValid = $this->validateTableParams($this->tableName, $this->sortBy);
        $searchParamsValid = $this->validateSearchParams($this->searchCategory, $this->searchQuery);

        if ($tableParamsValid && $searchParamsValid) {
            $totalItems = parent::getSearchCount($this->tableName, $this->searchCategory, $this->searchQuery);
            $fetchStart = ($this->page - 1) * $this->itemsPerPage;

            $data = parent::querySearch($this->tableName, $fetchStart, $this->itemsPerPage, $this->sortBy, $this->searchCategory, $this->searchQuery);
            return ["success" => true, "data" => $data, "total" => $totalItems];

        } else {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "SQL statement validation failed!"]);
        }
        exit();
    }


    private function validateTableParams($tableName, $sortBy)
    {
        // validate tableName with Regex
        if (!preg_match('/^[a-zA-Z0-9]+$/', $tableName)) {
            error_log("Invalid tableName: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        // extract assoc values from sortBy
        $sortByKey = $sortBy['key'];
        $sortByOrder = $sortBy['order'];

        // validate key value to prevent sql injection
        if (!in_array($sortByKey, ["id"])) {
            error_log("Invalid sortBy key: $sortByKey" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        // validate order value to prevent sql injection
        if (!in_array(strtolower($sortByOrder), ["asc", "desc"])) {
            error_log("Invalid sortBy order: $sortByOrder" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }

    private function validateSearchParams($searchCategory, $searchQuery)
    {
        //validate Category
        if (!preg_match('/^[a-zA-Z0-9]+$/', $searchCategory)) {
            error_log("Invalid searchCategory: $searchCategory" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        // Regex for search term validation
        $invalCharsRegex = '/[\/:*?"<>|\\\\]/';
        $dirTraversalRegex = '/\.\./';

        if (preg_match($invalCharsRegex, $searchQuery) || preg_match($dirTraversalRegex, $searchQuery)) {
            error_log("Invalid searchQuery: $searchQuery" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }
}