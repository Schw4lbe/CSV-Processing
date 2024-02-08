<?php

class Drop extends Dbh
{
    public function queryTableDrop($tableName)
    {
        $pdo = parent::connect();
        $sql = "DROP TABLE {$tableName};";

        try {
            $stmt = $pdo->prepare($sql);

            if (!$stmt->execute()) {
                error_log("query table drop failed: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }
            return true;

        } catch (PDOException $e) {
            error_log("Error in queryTableDrop: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }
}