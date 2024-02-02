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
}