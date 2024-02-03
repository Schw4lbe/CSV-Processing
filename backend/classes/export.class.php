<?php

class Export extends Dbh
{
    public function getTableHeadersExclID($tableName)
    {
        $pdo = parent::connect();
        $sql = "SHOW COLUMNS FROM {$tableName};";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            error_log("statement execution failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
            return ["success" => false];
        }

        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($columns)) {
            return [];
        }

        $columns = array_slice($columns, 1);
        return $columns;
    }

    public function queryExportData($tableName, $tableHeaders)
    {
        if (empty($tableHeaders)) {
            return ["success" => false, "message" => "No headers provided"];
        }

        $pdo = parent::connect();
        // Convert the array of table headers into a comma-separated string
        $columns = implode(", ", $tableHeaders);
        $sql = "SELECT $columns FROM $tableName;";

        try {
            $stmt = $pdo->prepare($sql);

            if (!$stmt->execute()) {
                error_log("statement execution failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
                return ["success" => false];
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["success" => true, "data" => $data];
        } catch (PDOException $e) {
            error_log("Error in queryExportData: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return ["success" => false];
        }
    }
}