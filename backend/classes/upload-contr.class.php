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
        // file validations
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
        $tableName = null;

        // final validation on file format and content extraction
        $validationResult = $this->validateFileFormat($this->file);
        if (!$validationResult["success"]) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "errorCode" => "BEE04"]);
            exit();
        } else {
            // store headers and content rows
            $headers = $validationResult["headers"];
            $contentRows = $validationResult["rows"];
        }

        // table creation and data import
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
        // check File size
        if ($file["size"] > $maxSize) {
            return false;
        }
        return true;
    }

    private function validateFileName($file)
    {
        // Regex for file name validation
        $invalCharsRegex = '/[\/:*?"<>|\\\\]/';
        $dirTraversalRegex = '/\.\./';
        $fileName = $file["name"];

        // check for invalid Chars in file name
        if (preg_match($invalCharsRegex, $fileName) || preg_match($dirTraversalRegex, $fileName)) {
            return false;
        }
        return true;
    }

    private function validateFileType($file)
    {
        // MIME type validation
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file["tmp_name"]);
        if ($mimeType !== "text/csv" && $mimeType !== "text/plain") {
            return false;
        }
        return true;
    }

    private function validateFileFormat($file)
    {
        $delimiter = ";"; // define delimiter for value separation (in this case I assume it is ";")
        $fileContent = file_get_contents($file["tmp_name"]); // read file content
        $normalizedContent = str_replace(["\r\n", "\r"], "\n", $fileContent); // normalize line endings

        $temp = tmpfile(); // creating temp file handle for fgetcsv method
        fwrite($temp, $normalizedContent); // write normalized content to temp file
        rewind($temp); // reset file pointer from end to start

        // read headers (assuming first row are headers due to convention)
        $headers = fgetcsv($temp, 0, $delimiter);
        if ($headers === false) {
            fclose($temp);
            return false; // unable to read headers
        }

        // validate each row
        $rows = [];
        while (($row = fgetcsv($temp, 0, $delimiter)) !== FALSE) {

            if (count($row) != count($headers)) {
                fclose($temp);
                return false; // row does not match header count
            }
            $rows[] = $row;
        }

        fclose($temp);
        $headers = $this->replaceGermanUmlaut($headers);
        // return success indicator, headers and content rows for later processing
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