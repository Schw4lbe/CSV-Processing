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

        $tableHeaders = parent::getTableHeadersExclID($this->tableName);
        if (!$tableHeaders) {
            return false;
        }

        $exportData = parent::queryExportData($this->tableName, $tableHeaders);
        if (!$exportData["success"]) {
            error_log("data export failed: $this->tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;

        } else if ($exportData["success"] && $exportData["data"]) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
            $output = fopen('php://output', 'w');

            if (!empty($exportData["data"])) {
                fputcsv($output, array_keys($exportData["data"][0]), ";");
            }

            foreach ($exportData["data"] as $row) {
                fputcsv($output, $row, ";");
            }
            fclose($output);
            exit();
        }
        return false;
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