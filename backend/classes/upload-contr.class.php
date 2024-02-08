<?php

class UploadContr extends Upload
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function csvUpload()
    {
        if (!$this->validateFileSize($this->file)) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "errorCode" => "BEE01"]);
            exit();
        }

        if (!$this->validateFileName($this->file)) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "errorCode" => "BEE02"]);
            exit();
        }

        if (!$this->validateFileType($this->file)) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "errorCode" => "BEE03"]);
            exit();
        }

        $headers = null;
        $contentRows = null;

        $validationResult = $this->validateFileFormat($this->file);
        if (!$validationResult["success"]) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "errorCode" => "BEE04"]);
            exit();
        } else {
            $headers = $validationResult["headers"];
            $contentRows = $validationResult["rows"];
        }

        $tableName = null;

        if ($headers) {
            $createTableResult = parent::createTable($headers);
            if ($createTableResult["success"]) {
                $tableName = $createTableResult["tableName"];
                $insertDataResult = parent::insertData($tableName, $headers, $contentRows);

                if ($insertDataResult["success"]) {
                    return ["success" => true, "tableName" => $tableName];
                } else {
                    return ["success" => false, "errorCode" => "BEE06"];
                }
            } else {
                return ["success" => false, "errorCode" => "BEE05"];
            }
        }
    }

    private function validateFileSize($file)
    {
        $maxSize = 5 * 1024 * 1024;
        if ($file["size"] > $maxSize) {
            return false;
        }
        return true;
    }

    private function validateFileName($file)
    {
        $invalCharsRegex = '/[\/:*?"<>|\\\\]/';
        $dirTraversalRegex = '/\.\./';
        $fileName = $file["name"];

        if (preg_match($invalCharsRegex, $fileName) || preg_match($dirTraversalRegex, $fileName)) {
            return false;
        }
        return true;
    }

    private function validateFileType($file)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file["tmp_name"]);
        if ($mimeType !== "text/csv" && $mimeType !== "text/plain") {
            return false;
        }
        return true;
    }

    private function validateFileFormat($file)
    {
        $delimiter = ";";
        $fileContent = file_get_contents($file["tmp_name"]);
        $normalizedContent = str_replace(["\r\n", "\r"], "\n", $fileContent);

        $temp = tmpfile();
        fwrite($temp, $normalizedContent);
        rewind($temp);

        $headers = fgetcsv($temp, 0, $delimiter);
        if ($headers === false) {
            fclose($temp);
            return false;
        }

        $rows = [];
        while (($row = fgetcsv($temp, 0, $delimiter)) !== FALSE) {

            if (count($row) != count($headers)) {
                fclose($temp);
                return false;
            }
            $rows[] = $row;
        }

        fclose($temp);
        $headers = $this->replaceGermanUmlaut($headers);
        return ["success" => true, "headers" => $headers, "rows" => $rows];
    }

    private function replaceGermanUmlaut($headers)
    {
        $search = array('Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß');
        $replace = array('Ae', 'Oe', 'Ue', 'ae', 'oe', 'ue', 'ss');

        foreach ($headers as &$header) {
            $header = str_replace($search, $replace, $header);
        }
        return $headers;
    }
}