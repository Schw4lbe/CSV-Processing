<?php

class CrudContr extends Crud
{
    private $tableName;
    private $item;
    private $itemId;

    public function __construct($tableName, $item, $itemId)
    {
        $this->tableName = $tableName;
        $this->item = $item;
        $this->itemId = $itemId;
    }

    public function updateItemData()
    {
        $itemData = $this->headerValueSeparated($this->item);
        $itemHeadersValid = $this->validateItemHeaders($itemData["headers"]);
        $itemIdValid = $this->validateItemId($this->itemId);
        $tableNameValid = $this->validateTableName($this->tableName);

        if ($itemHeadersValid && $itemIdValid["validId"] && $tableNameValid) {
            $updateResult = parent::commitItemUpdate($this->tableName, $itemIdValid["id"], $itemData["headers"], $itemData["values"]);
            return $updateResult;
        } else {
            exit();
        }
    }

    public function addNewItem()
    {
        $itemData = $this->headerValueSeparated($this->item);
        $itemHeadersValid = $this->validateItemHeaders($itemData["headers"]);
        $tableNameValid = $this->validateTableName($this->tableName);

        if ($itemHeadersValid && $tableNameValid) {
            $createItemResult = parent::createNewItem($this->tableName, $itemData["headers"], $itemData["values"]);
            return $createItemResult;
        } else {
            exit();
        }
    }

    public function deleteItem()
    {
        $itemIdValid = $this->validateItemId($this->itemId);
        $tableNameValid = $this->validateTableName($this->tableName);

        if ($itemIdValid["validId"] && $tableNameValid) {
            $deleteItemResult = parent::executeDeletion($this->tableName, $itemIdValid["id"]);
            return $deleteItemResult;
        } else {
            exit();
        }
    }

    private function headerValueSeparated($item)
    {
        $headers = [];
        $values = [];
        foreach ($item as $header => $value) {
            $headers[] = $header;
            $values[] = $value;
        }
        return (["headers" => $headers, "values" => $values]);
    }

    private function validateItemHeaders($headers)
    {
        foreach ($headers as $header) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $header)) {
                error_log("Invalid Header: $header" . PHP_EOL, 3, "../logs/app-error.log");
                return false;
            }
        }
        return true;
    }

    private function validateItemId($itemId)
    {
        if (preg_match('/^[0-9]+$/', $itemId)) {
            $itemId = (int) $itemId;
            return (["validId" => true, "id" => $itemId]);

        } else {
            error_log("Invalid itemId: $itemId" . PHP_EOL, 3, "../logs/app-error.log");
            return (["validId" => false]);
        }
    }

    private function validateTableName($tableName)
    {
        if (preg_match('/^[a-z0-9]+$/', $tableName)) {
            return true;
        } else {
            error_log("Invalid Table Name: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }
}