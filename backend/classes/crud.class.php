<?php

class Crud extends Dbh
{
    public function commitItemUpdate($tableName, $itemId, $columnNames, $columnValues)
    {
        $pdo = parent::connect();
        $updateAssignments = [];
        foreach ($columnNames as $columnName) {
            $updateAssignments[] = "{$columnName} = ?";
        }
        $updateClause = implode(', ', $updateAssignments);
        $sql = "UPDATE {$tableName} SET {$updateClause} WHERE id = ?;";

        try {
            $stmt = $pdo->prepare($sql);
            $prepStmtValues = array_merge($columnValues, [$itemId]);

            if (!$stmt->execute($prepStmtValues)) {
                error_log("Item Update failed: $tableName, $itemId, $columnNames, $columnValues" . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }
            return true;

        } catch (PDOException $e) {
            error_log("Error in commitItemUpdate: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }

    public function createNewItem($tableName, $columnNames, $columnValues)
    {
        $pdo = parent::connect();
        $placeholders = rtrim(str_repeat("?,", count($columnValues)), ",");
        $columns = implode(', ', $columnNames);
        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$placeholders});";

        try {
            $stmt = $pdo->prepare($sql);

            if (!$stmt->execute($columnValues)) {
                error_log("Item creation failed in table: {$tableName}, Data: " . $columnNames, $columnValues . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }
            return true;

        } catch (PDOException $e) {
            error_log("Error in createNewItem: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }

    public function executeDeletion($tableName, $itemId)
    {
        $pdo = parent::connect();
        $sql = "DELETE FROM {$tableName} WHERE id = ?;";

        try {
            $stmt = $pdo->prepare($sql);

            if (!$stmt->execute([$itemId])) {
                error_log("Item deletion failed: $tableName, $itemId" . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }
            return true;

        } catch (PDOException $e) {
            error_log("Error in executeDeletion: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }
}