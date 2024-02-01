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
            return ["data" => $data, "total" => $totalItems];
        } else {
            // TODO: validate echo -> visible in frontend and clear for user?
            echo json_encode(["success" => false, "message" => "SQL statement validation failed!"]);
            exit();
        }
    }

    private function validateParams($tableName, $sortBy)
    {
        // validate tableName with Regex
        if (!preg_match('/^[a-zA-Z0-9]+$/', $tableName)) {
            // TODO: need propper error handling here.
            return false;
        }

        // extract assoc values from sortBy
        $sortByKey = $sortBy['key'];
        $sortByOrder = $sortBy['order'];

        // validate key value to prevent sql injection
        if (!in_array($sortByKey, ["id"])) {
            // TODO: need propper error handling here.
            return false;
        }

        // validate order value to prevent sql injection
        if (!in_array(strtolower($sortByOrder), ["asc", "desc"])) {
            // TODO: need propper error handling here.
            return false;
        }
        return true;
    }
}