<?php

class Fetch extends Dbh
{
    public function queryData($tableName, $fetchStart, $itemsPerPage, $sortBy)
    {
        $pdo = parent::connect();

        // extract assoc values from sortBy
        $sortByKey = $sortBy['key'];
        $sortByOrder = $sortBy['order'];

        $sql = "SELECT * FROM {$tableName} ORDER BY {$sortByKey} {$sortByOrder} LIMIT :itemsPerPage OFFSET :fetchStart;";
        $stmt = $pdo->prepare($sql);

        // bind INT type to named placeholders due to error in sql syntax -> string detected
        $stmt->bindValue(":itemsPerPage", (int) $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(":fetchStart", (int) $fetchStart, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            error_log("Data query statement failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
            exit();
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getItemCount($tableName)
    {
        $pdo = parent::connect();
        $sql = "SELECT COUNT(*) FROM {$tableName};";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            error_log("Item count statement failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
            exit();
        }

        $result = $stmt->fetchColumn();
        return $result;
    }
}