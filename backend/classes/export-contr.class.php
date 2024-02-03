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

        // get table headers without ID column to have same structure in export as on import
        $tableHeaders = parent::getTableHeadersExclID($this->tableName);
        $exportData = parent::queryExportData($this->tableName, $tableHeaders);

        if (!$exportData["success"]) {
            error_log("data export failed: $this->tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;

        } else if ($exportData["success"] && $exportData["data"]) {
            // set headers for file creation
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
            // open file stream to be able to put content to
            $output = fopen('php://output', 'w');

            // Output column headers
            if (!empty($exportData["data"])) {
                fputcsv($output, array_keys($exportData["data"][0]));
            }

            // Output data rows
            foreach ($exportData["data"] as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
            exit();
        }
        return false; // Only reached if export is not successful
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