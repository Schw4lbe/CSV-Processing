<?php

class ExportContr extends Export
{
    private $tableName;

    public function __construct($tableName)
    {
        $this->tableName = strtolower($tableName);
    }

    public function exportData()
    {
        $validTableName = $this->validateTableName($this->tableName);

        if (!$validTableName) {
            error_log("tableName invalid: $this->tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        $exportData = parent::queryExportData($this->tableName);
        return true;
    }

    private function validateTableName($tableName)
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $tableName)) {
            error_log("Invalid tableName: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }
}