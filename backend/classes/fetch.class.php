<?php

class Fetch extends Dbh
{
    public function getRowCount($tableName)
    {
        $pdo = parent::connect();
        $sql = "SELECT COUNT(*) FROM {$tableName};";

        try {
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute()) {
                error_log("Item count statement failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }

            $result = $stmt->fetchColumn();
            return $result;

        } catch (PDOException $e) {
            error_log("Error in getRowCount: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }

    public function queryData($tableName, $fetchStart, $itemsPerPage, $sortBy)
    {
        $pdo = parent::connect();
        $sortByKey = $sortBy['key'];
        $sortByOrder = $sortBy['order'];
        $sql = "SELECT * FROM {$tableName} ORDER BY {$sortByKey} {$sortByOrder} LIMIT :itemsPerPage OFFSET :fetchStart;";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":itemsPerPage", $itemsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(":fetchStart", (int) $fetchStart, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                error_log("Data query statement failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            error_log("Error in queryData: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }

    public function getSearchCount($tableName, $searchCategory, $searchQuery)
    {
        $pdo = parent::connect();
        $searchQuery = '%' . $searchQuery . '%';
        $sql = "SELECT COUNT(*) FROM {$tableName} WHERE {$searchCategory} LIKE :searchQuery;";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":searchQuery", $searchQuery, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                error_log("Item count statement failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }

            $result = $stmt->fetchColumn();
            return $result;

        } catch (PDOException $e) {
            error_log("Error in getSearchCount: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }

    public function querySearch($tableName, $fetchStart, $itemsPerPage, $sortBy, $searchCategory, $searchQuery)
    {
        $pdo = parent::connect();
        $sortByKey = $sortBy['key'];
        $sortByOrder = $sortBy['order'];
        $searchQuery = '%' . $searchQuery . '%';
        $sql = "SELECT * FROM {$tableName} WHERE {$searchCategory} LIKE :searchQuery ORDER BY {$sortByKey} {$sortByOrder} LIMIT :itemsPerPage OFFSET :fetchStart;";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":searchQuery", $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(":itemsPerPage", $itemsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(":fetchStart", (int) $fetchStart, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                error_log("Data query statement failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            error_log("Error in querySearch: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }
}