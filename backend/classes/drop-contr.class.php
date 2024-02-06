<?php

class DropContr extends Drop
{
    private $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function dropTable()
    {
        $tableNameValid = $this->validateTableName($this->tableName);
        if (!$tableNameValid) {
            error_log("tablename invalid: $this->tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        $result = parent::queryTableDrop($this->tableName);
        return $result;
    }

    private function validateTableName($tableName)
    {
        if (preg_match('/^[a-z0-9]+$/', $tableName)) {
            return true;
        } else {
            error_log("Invalid Table Name: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }
}