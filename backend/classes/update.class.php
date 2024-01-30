<?php

class Update extends Dbh
{
    public function queryData($tableName)
    {
        $pdo = parent::connect();
        $sql = "SELECT * FROM $tableName";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            exit();
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}