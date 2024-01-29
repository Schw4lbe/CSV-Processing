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

        $tableName = $this->getUniqueTableName(); // generate unique talbeName      
        $pdo = parent::connect(); // define php data object

        $sql = "CREATE TABLE $tableName (";
        foreach ($headers as $header) {
            $columnName = preg_replace("/[^A-Za-z0-9_]/", "", $header); // remove special chars and spaces from header for col name
            $maxColumnNameLength = 64; // define max columnName length due to sql standard of 64 chars

            // Check if columnName is a reserved keyword or empty or to long
            if (
                empty($columnName)
                || in_array(strtoupper($columnName), $this->getSQLReservedKeywords())
                || strlen($columnName) > $maxColumnNameLength
            ) {
                file_put_contents("debug.log", "Invalid or reserved keyword for column name: $columnName\n", FILE_APPEND);
                return false;
            }

            $sql .= "$columnName VARCHAR(255), ";
        }
        $sql = rtrim($sql, ", ") . ");"; // Remove the last comma and add closing parenthesis
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            file_put_contents("debug.log", "SQL execution failed.\n", FILE_APPEND);
            return false;
        }

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
        $headers = $this->replaceGermanUmlaut($headers); // replace Umlaute Ä, Ö, Ü

        return $headers;
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

    private function getUniqueTableName()
    {
        $time = time();
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charsLength = strlen($chars); // length of the characters string
        $strLength = 10; // desired length of the random string
        $rndStr = '';

        for ($i = 0; $i < $strLength; $i++) {
            $rndStr .= $chars[random_int(0, $charsLength - 1)];
        }

        return $time . $rndStr;
    }

    private function getSQLReservedKeywords()
    {
        return [
            'ACCESSIBLE',
            'ADD',
            'ALL',
            'ALTER',
            'ANALYZE',
            'AND',
            'AS',
            'ASC',
            'ASENSITIVE',
            'BEFORE',
            'BETWEEN',
            'BIGINT',
            'BINARY',
            'BLOB',
            'BOTH',
            'BY',
            'CALL',
            'CASCADE',
            'CASE',
            'CHANGE',
            'CHAR',
            'CHARACTER',
            'CHECK',
            'COLLATE',
            'COLUMN',
            'CONDITION',
            'CONSTRAINT',
            'CONTINUE',
            'CONVERT',
            'CREATE',
            'CROSS',
            'CUBE',
            'CUME_DIST',
            'CURRENT_DATE',
            'CURRENT_TIME',
            'CURRENT_TIMESTAMP',
            'CURRENT_USER',
            'CURSOR',
            'DATABASE',
            'DATABASES',
            'DAY_HOUR',
            'DAY_MICROSECOND',
            'DAY_MINUTE',
            'DAY_SECOND',
            'DEC',
            'DECIMAL',
            'DECLARE',
            'DEFAULT',
            'DELAYED',
            'DELETE',
            'DENSE_RANK',
            'DESC',
            'DESCRIBE',
            'DETERMINISTIC',
            'DISTINCT',
            'DISTINCTROW',
            'DIV',
            'DOUBLE',
            'DROP',
            'DUAL',
            'EACH',
            'ELSE',
            'ELSEIF',
            'EMPTY',
            'ENCLOSED',
            'ESCAPED',
            'EXCEPT',
            'EXISTS',
            'EXIT',
            'EXPLAIN',
            'FALSE',
            'FETCH',
            'FIRST_VALUE',
            'FLOAT',
            'FLOAT4',
            'FLOAT8',
            'FOR',
            'FORCE',
            'FOREIGN',
            'FROM',
            'FULLTEXT',
            'FUNCTION',
            'GENERATED',
            'GET',
            'GRANT',
            'GROUP',
            'GROUPING',
            'GROUPS',
            'HAVING',
            'HIGH_PRIORITY',
            'HOUR_MICROSECOND',
            'HOUR_MINUTE',
            'HOUR_SECOND',
            'IF',
            'IGNORE',
            'IN',
            'INDEX',
            'INFILE',
            'INNER',
            'INOUT',
            'INSENSITIVE',
            'INSERT',
            'INT',
            'INT1',
            'INT2',
            'INT3',
            'INT4',
            'INT8',
            'INTEGER',
            'INTERVAL',
            'INTO',
            'IO_AFTER_GTIDS',
            'IO_BEFORE_GTIDS',
            'IS',
            'ITERATE',
            'JOIN',
            'JSON_TABLE',
            'KEY',
            'KEYS',
            'KILL',
            'LAG',
            'LAST_VALUE',
            'LATERAL',
            'LEAD',
            'LEADING',
            'LEAVE',
            'LEFT',
            'LIKE',
            'LIMIT',
            'LINEAR',
            'LINES',
            'LOAD',
            'LOCALTIME',
            'LOCALTIMESTAMP',
            'LOCK',
            'LONG',
            'LONGBLOB',
            'LONGTEXT',
            'LOOP',
            'LOW_PRIORITY',
            'MASTER_BIND',
            'MASTER_SSL_VERIFY_SERVER_CERT',
            'MATCH',
            'MAXVALUE',
            'MEDIUMBLOB',
            'MEDIUMINT',
            'MEDIUMTEXT',
            'MEMBER',
            'MERGE',
            'MESSAGE_TEXT',
            'MICROSECOND',
            'MIDDLEINT',
            'MINUTE_MICROSECOND',
            'MINUTE_SECOND',
            'MOD',
            'MODIFIES',
            'NATURAL',
            'NOT',
            'NO_WRITE_TO_BINLOG',
            'NTH_VALUE',
            'NTILE',
            'NULL',
            'NUMERIC',
            'OF',
            'ON',
            'OPTIMIZE',
            'OPTIMIZER_COSTS',
            'OPTION',
            'OPTIONALLY',
            'OR',
            'ORDER',
            'OUT',
            'OUTER',
            'OUTFILE',
            'OVER',
            'PARTITION',
            'PERCENT_RANK',
            'PERSIST',
            'PERSIST_ONLY',
            'PRECISION',
            'PRIMARY',
            'PROCEDURE',
            'PURGE',
            'RANGE',
            'RANK',
            'READ',
            'READS',
            'READ_WRITE',
            'REAL',
            'RECURSIVE',
            'REFERENCES',
            'REGEXP',
            'RELEASE',
            'RENAME',
            'REPEAT',
            'REPLACE',
            'REQUIRE',
            'RESIGNAL',
            'RESTRICT',
            'RETURN',
            'REVOKE',
            'RIGHT',
            'RLIKE',
            'ROW',
            'ROWS',
            'ROW_NUMBER',
            'SCHEMA',
            'SCHEMAS',
            'SECOND_MICROSECOND',
            'SELECT',
            'SENSITIVE',
            'SEPARATOR',
            'SET',
            'SHOW',
            'SIGNAL',
            'SMALLINT',
            'SPATIAL',
            'SPECIFIC',
            'SQL',
            'SQLEXCEPTION',
            'SQLSTATE',
            'SQLWARNING',
            'SQL_BIG_RESULT',
            'SQL_CALC_FOUND_ROWS',
            'SQL_SMALL_RESULT',
            'SSL',
            'STARTING',
            'STORED',
            'STRAIGHT_JOIN',
            'SYSTEM',
            'TABLE',
            'TERMINATED',
            'THEN',
            'TINYBLOB',
            'TINYINT',
            'TINYTEXT',
            'TO',
            'TRAILING',
            'TRIGGER',
            'TRUE',
            'UNDO',
            'UNION',
            'UNIQUE',
            'UNLOCK',
            'UNSIGNED',
            'UPDATE',
            'USAGE',
            'USE',
            'USING',
            'UTC_DATE',
            'UTC_TIME',
            'UTC_TIMESTAMP',
            'VALUES',
            'VARBINARY',
            'VARCHAR',
            'VARCHARACTER',
            'VARYING',
            'VIRTUAL',
            'WHEN',
            'WHERE',
            'WHILE',
            'WINDOW',
            'WITH',
            'WRITE',
            'XOR',
            'YEAR_MONTH',
            'ZEROFILL',
            // Additional MariaDB-specific keywords
            'ARRAY',
            'ASYMMETRIC',
            'BINLOG',
            'CUME_DIST',
            'DENSE_RANK',
            'EMPTY',
            'EXCEPT',
            'FIRST_VALUE',
            'GEOMCOLLECTION',
            'GEOMETRY',
            'GEOMETRYCOLLECTION',
            'GROUPS',
            'JSON_TABLE',
            'LAG',
            'LAST_VALUE',
            'LEAD',
            'LINESTRING',
            'MULTILINESTRING',
            'MULTIPOINT',
            'MULTIPOLYGON',
            'NTH_VALUE',
            'NTILE',
            'OVER',
            'PERCENT_RANK',
            'POINT',
            'POLYGON',
            'RECURSIVE',
            'ROW_NUMBER',
            'SRID',
            'SYMMETRIC',
            'SYSTEM',
            'THREAD_PRIORITY',
            'TIES',
            'WINDOW'
        ];
    }
}