<?php

class Crud extends Dbh
{
    public function commitItemUpdate($tableName, $itemId, $itemData)
    {
        $pdo = parent::connect();

        // Unpack headers and values from the itemData array
        $columnNames = $itemData["headers"];
        $columnValues = $itemData["values"];

        // Dynamically construct the SET part of the SQL statement
        $updateAssignments = [];
        foreach ($columnNames as $columnName) {
            // For each column name, create an assignment expression "columnName = ?"
            $updateAssignments[] = "{$columnName} = ?";
        }
        // update Assignments joined together separated with "," to have valid SQL syntax
        $updateClause = implode(', ', $updateAssignments);

        // Prepare the full SQL statement
        $sql = "UPDATE {$tableName} SET {$updateClause} WHERE id = ?";

        $stmt = $pdo->prepare($sql);

        // Append $itemId to the array of values for the WHERE clause
        $parametersForPreparedStmt = array_merge($columnValues, [$itemId]);

        // Execute the prepared statement with the array of values
        if ($stmt->execute($parametersForPreparedStmt)) {
            return true;
        } else {
            error_log("Item Update failed: $tableName, $itemId, $itemData" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }

    public function createNewItem($tableName, $itemData)
    {
        $pdo = parent::connect();

        // Unpack headers and values from the itemData array
        $columnNames = $itemData["headers"];
        $columnValues = $itemData["values"];

        // Construct placeholders string for the VALUES clause
        $placeholders = rtrim(str_repeat("?,", count($columnValues)), ",");

        // Construct the column names part of the SQL statement
        $columns = implode(', ', $columnNames);

        // Prepare the full SQL statement
        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$placeholders})";

        $stmt = $pdo->prepare($sql);

        // Execute the prepared statement with the array of values
        if ($stmt->execute($columnValues)) {
            return true;
        } else {
            error_log("Item creation failed in table: {$tableName}, Data: " . print_r($itemData, true) . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }
}