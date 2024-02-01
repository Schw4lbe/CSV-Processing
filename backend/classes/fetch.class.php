<?php

class Fetch extends Dbh
{
    public function queryData($tableName, $fetchStart, $itemsPerPage, $sortBy)
    {
        $pdo = parent::connect();

        $sortByKey = $sortBy['key'];
        $sortByOrder = $sortBy['order'];

        $sql = "SELECT * FROM $tableName ORDER BY $sortByKey $sortByOrder LIMIT $itemsPerPage OFFSET $fetchStart;";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            exit();
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getItemCount($tableName)
    {
        $pdo = parent::connect();
        $sql = "SELECT COUNT(*) FROM $tableName;";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            exit();
        }

        $result = $stmt->fetchColumn();
        return $result;
    }
}