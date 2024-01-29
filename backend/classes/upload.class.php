<?php

class Upload extends Dbh
{
    public function createTable($file)
    {
        $delimiter = ";";
        $fileContent = file_get_contents($file["tmp_name"]);
        $normalizedContent = str_replace(["\r\n", "\r"], "\n", $fileContent);

        $headers = $this->getTableHeaders($delimiter, $normalizedContent);
        if ($headers === false) {
            return false;
        }

        // create rnd table name later on with current timestamp + rnd string concat
        $tableName = "testTable";
        $sql = "CREATE TABLE $tableName (";
        foreach ($headers as $header) {
            // remove special chars and spaces from header for col name
            $columnName = preg_replace("/[^A-Za-z0-9_]/", "", $header);
            $sql .= "$columnName VARCHAR(255), ";
        }
        $sql = rtrim($sql, ", ") . ");"; // Remove the last comma and add closing parenthesis



        file_put_contents("debug.log", "SQL Statement: " . $sql . "\n", FILE_APPEND);
        return true;
    }

    private function getTableHeaders($delimiter, $normalizedContent)
    {
        $temp = tmpfile();
        fwrite($temp, $normalizedContent);
        fseek($temp, 0);

        $headers = fgetcsv($temp, 0, $delimiter);
        if ($headers === false) {
            fclose($temp);
            return false;
        }

        fclose($temp);
        file_put_contents("debug.log", "Table Headers: " . implode(", ", $headers) . "\n", FILE_APPEND);
        return $headers;
    }
}