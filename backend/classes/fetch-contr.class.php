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
        $stmtParamsValid = $this->validateParams($this->tableName, $this->sortBy);

        if ($stmtParamsValid) {
            $fetchStart = ($this->page - 1) * $this->itemsPerPage;
            $data = parent::queryData($this->tableName, $fetchStart, $this->itemsPerPage, $this->sortBy);
            $totalItems = parent::getItemCount($this->tableName);
            return ["success" => true, "data" => $data, "total" => $totalItems];
        } else {
            echo json_encode(["success" => false, "message" => "SQL statement validation failed!"]);
        }
        exit(); // Exit AFTER sending the response
    }

    private function validateParams($tableName, $sortBy)
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
}