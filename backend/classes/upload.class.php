<?php

class Upload extends Dbh
{
    public function createTable($headers)
    {
        $tableName = $this->getUniqueTableName(); // generate unique talbeName      
        $pdo = parent::connect(); // define php data object

        $sql = "CREATE TABLE {$tableName} (";
        // adding primary key and unique identifier for frontend exchange
        $sql .= "id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, ";
        foreach ($headers as $header) {
            $columnName = preg_replace("/[^A-Za-z0-9_]/", "", $header); // remove special chars and spaces from header for col name
            $maxColumnNameLength = 64; // define max columnName length due to sql standard of 64 chars

            // Check if columnName is a reserved keyword or empty or to long
            if (empty($columnName) || in_array(strtoupper($columnName), $this->getSQLReservedKeywords()) || strlen($columnName) > $maxColumnNameLength) {
                return false;
            }

            $sql .= "{$columnName} VARCHAR(255), ";
        }
        $sql = rtrim($sql, ", ") . ");"; // Remove the last comma and add closing parenthesis
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            return false;
        }
        return ["success" => true, "tableName" => $tableName];
    }

    public function insertData($tableName, $headers, $contentRows)
    {
        $pdo = parent::connect();

        foreach ($contentRows as $row) {
            // Building the SQL query
            $columns = implode(', ', array_map(function ($header) {
                return "`" . preg_replace("/[^A-Za-z0-9_]/", "", $header) . "`";
            }, $headers));

            $placeholders = implode(', ', array_fill(0, count($headers), '?'));
            $sql = "INSERT INTO `{$tableName}` ({$columns}) VALUES ({$placeholders})";

            // Prepare and execute SQL statement
            $stmt = $pdo->prepare($sql);

            if (!$stmt->execute($row)) {
                return false; // Handle error appropriately
            }
        }
        return ["success" => true];
    }

    private function getUniqueTableName()
    {
        $time = time(); // get unix time stamp
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