<?php

class CrudContr
{
    private $tableName;
    private $item;

    public function __construct($tableName, $item)
    {
        $this->tableName = $tableName;
        $this->item = $item;
    }

    public function updateItemData()
    {
        $itemIdValid = $this->validateItemId($this->item["id"]);
        $tableNameValid = $this->validateTableName($this->tableName);
        if ($itemIdValid["validId"] && $tableNameValid) {
            // update data
        }
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
        // validate tablename with regex here
    }
}