<?php

class Export extends Dbh
{
    public function queryExportData($tableName)
    {
        $pdo = parent::connect();
        $sql = "SELECT * FROM {$tableName};";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            error_log("statement execution failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
            return (["success" => false]);
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return (["success" => true, "data" => $data]);
    }
}