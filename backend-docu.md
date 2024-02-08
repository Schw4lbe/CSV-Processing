# BACKEND

> Das Backend ist nach dem MVC (Model View Controller) Prinzip konstruiert und richtet sich nach den SCP (Single Concern Principle) Vorgaben. Aus Gründen der Übersichtlichkeit, wird nachfolgend jede API und direkt anschließend die zugehörige Controller Klasse und Klasse erläutert.

---

## AUFBAU

> Der Grundaufbau ist in APIs, Klassen und Logs untergliedert.

- **api directory** = Enthält alle Endpunkte zur Kommunikation mit dem Frontend
- **classes directory** = Enthält alle Klassen und Controller
- **logs directory** = Enthält Log Dateien

> Jede API enthält die gleiche Nomenklatur. **name.api.php**

> Die Klassen untergliedern sich wie folgt und haben ebenfalls eine einheitliche Nomenklatur. Das MVC Prinzip besteht aus der Klasse (Model), der Controller Klasse (Controller) und dem View, welcher über das Frontend dargestellt wird und somit nicht im Backend zu finden ist.

- **name-contr.class.php** = Controller Klasse zur Steuerung der gleichnamigen Klasse
- **name.class.php** = Klasse zur Interaktion mit der Datenbank
- **dbh.class.php** = Sonderklasse zur Bereitstellung der Datenbank Konnektivität (dbh = Database Handler)

> Die **index.php** Datei erfüllt keinen dedizierten Zweck und dient hier lediglich als Einstiegspunkt für den Apache Server in XAMPP.

> Für den vollständigen Workflow werden die folgenden Dateien eingebunden. Dies geschieht in jeder API einheitlich nach den folgenden Vorgaben.

```php
// Import der benötigten Klassen, in dieser Reichenfolge
include_once "../classes/dbh.class.php";
include_once "../classes/name.class.php";
include_once "../classes/name-contr.class.php";
```

---

## DBH

> Die **dbh** Klasse wird in jeder API als Schnittpunkt zur Datenbank verwendet. Die Konfiguraton entspricht der Entwicklungsumgebung und ist im Falle der Produktivsetzung anzupassen. (Gleiches gilt für die Header Konfiguration der API's).

```php
<?php
class Dbh
{
    // connect Methode für die Verbindung mit dem SQL Server
    protected function connect()
    {
        try {
            // User Default Credentials für XAMPP Setup
            $username = "root";
            $password = "";
            // Instanzierung eines PHP Data Objects
            $dbh = new PDO("mysql:host=localhost;dbname=csv-processing", $username, $password);
            return $dbh;
        // Abfangen von Fehlermeldungen
        } catch (PDOException $e) {
            // $e Parameter nicht in Ausgabe aus Sicherheitsgründen
            // Log für saubere Auswertung ausstehend

            // JSON response mit error message
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database connection error']);
            die();
        }
    }
}
```

---

## API's

# crud.api.php

> Die CRUD API kümmert sich um Anfragen aus dem Frontend bezüglich Neuanlagen, Anpassungen und Löschungen von Datensätzen. Sie empfängt zwei Parameter **tableName** und **item**. Der **tableName** wird immer **_strtolower_** umgewandelt. Die Separierung findet mittels **REQUEST_METHOD** und ggf. **PATH_INFO** statt.

> **BESONDERHEIT:** Bei Neuanlage wird keine ID übergeben, daher ist der Wert für ID im Constructor **null**. Bei der Löschung wird nur die ID übertragen. Daher ist der Wert für Item im Constructor auf **null** gesetzt.

```php
// PUT FILTER ANPASSUNG
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    // Umwandlung in Kleinbuchstaben, da in SQL Data Table names keine Caps zu finden sind
    $tableName = strtolower($data["tableName"]);
    $item = $data["item"];
    $itemId = $item["id"];
    // ...


// POST FILTER FÜR NEUANLAGE & LÖSCHUNG
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Daten werden vor der "if" condition erzeugt um code zu sparen
    $data = json_decode(file_get_contents("php://input"), true);


    // FILTER VIA /ADD FÜR NEUANLAGE
    if ($_SERVER["PATH_INFO"] === "/add") {
    $tableName = strtolower($data["tableName"]);
    $item = $data["item"];
    // ID fällt hier weg da neuer Artikel
    // ...


    // FILTER VIA /DELETE FÜR LÖSCHUNG
    } else if ($_SERVER["PATH_INFO"] === "/delete") {
    $tableName = strtolower($data["tableName"]);
    // item hier nicht vorhanden da nur ID übertragen wird
    $itemId = $data['itemId'];
    // ...
    }
    // ...
}
```

> Alle Operationen instanzieren die gleiche Klasse, **CrudContrl** mittels Constructor Method und übergeben direkt die Parameter. Danach wird die jeweilige Methode der Klasse aufgerufen. In allen Fällen wird ein Bool an das Frontend kommuniziert.

```php
// Anpassung
$newUpdate = new CrudContr($tableName, $item, $itemId);
$response = $newUpdate->updateItemData();

// Return Value für alle Fälle in dieser API gleich
if (!$response) {
    echo json_encode(["success" => false]);
}
echo json_encode(["success" => true]);

// ...
// Anlage
$newItem = new CrudContr($tableName, $item, null);
$response = $newItem->addNewItem();
// Gleiches Verhalten bei Return Value

// ...
// Löschung
$newDelete = new CrudContr($tableName, null, $itemId);
$response = $newDelete->deleteItem();
// Gleiches Verhalten bei Return Value
```

---

# crud-contr.class.php

> **CrudContr** ist der Controller für CRUD Operationen. Es sind 2 private Properties für den Constructor definiert. **class CrudContr extends Crud**

```php
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
// ...
}
```

> **updateItemData** Method zur Bearbeitung bestehender Items.

```php
class CrudContr extends Crud
{
    // ...
    public function updateItemData()
    {
        // Trennung der Header und zugehörigen Werte, da alle Werte in Item konsolidiert sind
        $itemData = $this->headerValueSeparated($this->item);
        // Validierung Header, ID und tableName mittels Regex
        $itemHeadersValid = $this->validateItemHeaders($itemData["headers"]);
        $itemIdValid = $this->validateItemId($this->item["id"]);
        $tableNameValid = $this->validateTableName($this->tableName);

        // Weitergabe der Parameter nach Überprüfung an Model
        if ($itemHeadersValid && $itemIdValid["validId"] && $tableNameValid) {
            $updateResult = parent::commitItemUpdate($this->tableName, $itemIdValid["id"], $itemData);
            return $updateResult;
        } else {
            exit();
        }
    }
    // ...
}
```

> **addNewItem** Method zur Neuanlage eines Artikels. Da die ID erst im Table generiert wird, kann diese auch nicht überprüft werden. Ansonsten ist alles gleich wie bei updateItemData.

> **deleteItem** Method löscht einen bestehenden Artikel anhand der ID. Es werden keine Item Daten übertragen und müssen somit auch nicht überprüft werden.

> **headerValueSeparated** Method trennt wie der Name sagt Überschriften von Werten und gibt diese in zwei separaten assoziativen Arrays zurück.

>

```php
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
```

> Die Regex Validierungsmethoden sind straight forward und brauchen keine gesonderten Erklärung. Diese und ähnliche Validierungen sind im gesamten Backend als 2. Sicherheitsebene zu finden.

```php
// Angabe des Regex je Validierung
// Headers
('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
// ID
('/^[0-9]+$/')
// tableName
('/^[a-z0-9]+$/')
```

---

# crud.class.php

> Die **Crud** Klasse extends zu **Dbh**.

> **commitItemUpdate** Method nimmt 4 Parameter auft. **tableName**, **itemId**, **columnNames** und **columnValues**. Eine pdo Variable wird als Verbindung zur Datenbank definiert. Anschließend wird ein dynamisches SQL Statement erzeugt.

```php
class Crud extends Dbh
{
    public function commitItemUpdate($tableName, $itemId, $columnNames, $columnValues)
    {
        $pdo = parent::connect();
        // ...
    }
}
```

> Zum Aufbau des Statements, wird über die **columnNames** gelooped und mit string concat eine assignment expression generiert (columnName = ?). Das Ergebnis wird im **updateAssignment** Array gespeichert. Im Anschluss wird in der Variable **updateClause** via implode jeder Array Value mit einem separator zu einem validen SQL String umgewandelt.

```php
public function commitItemUpdate($tableName, $itemId, $columnNames, $columnValues)
    {
        $pdo = parent::connect();

        $updateAssignments = [];
        foreach ($columnNames as $columnName) {
            // Anlage assignment expression für jeden Wert "columnName = ?"
            $updateAssignments[] = "{$columnName} = ?";
        }
        // update Assignments werden mit , zu einem validen SQL String zusammen gefasst
        $updateClause = implode(', ', $updateAssignments);
        // ...
    }

```

> Volles SQL Statement wird vorbereitet.

```php
public function commitItemUpdate($tableName, $itemId, $columnNames, $columnValues)
    {
        // ...
        $sql = "UPDATE {$tableName} SET {$updateClause} WHERE id = ?;";
        $stmt = $pdo->prepare($sql);
        // ...
    }
```

> Da Prepared Statements immer in correcter Reihenfolge angegeben werden müssen und ID das letzte Element im Statement darstellt, wird ID ans Ende des Arrays angehangen. Das Statement wird anschließend ausgeführt und potenzielle Fehler werden in einem Log File abgefangen. Der Datensatz wird geändert und gibt einen Bool an das Frontend zurück.

```php
    public function commitItemUpdate($tableName, $itemId, $columnNames, $columnValues)
    {
        // ...
        // Hängt die ID ans Ende des Arrays für Zuweisung prepared Statements
        $prepStmtValues = array_merge($columnValues, [$itemId]);

        // Statement wird ausgeführt; bei Fehler Log in app-error.log File
        if (!$stmt->execute($prepStmtValues)) {
            error_log("Item Update failed: $tableName, $itemId, $columnNames, $columnValues" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }
```

> **createNewItem** Method nimmt 3 Parameter auf, **tableName**, **columnNames**, **columnValues** und legt einen neuen Datensatz an. Zur Anlage der prepared Statements wird eine **placeholder** Variable deklariert. Die columns werden als String Komma separiert umgewandelt.

```php
    public function createNewItem($tableName, $columnNames, $columnValues)
    {
        $pdo = parent::connect();
        // erzeugt placeholder String für prepared Statement
        $placeholders = rtrim(str_repeat("?,", count($columnValues)), ",");
        // erzeugt einen String tür die Tabellen Header
        $columns = implode(', ', $columnNames);
        // SQL statement prepared
        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$placeholders});";
        $stmt = $pdo->prepare($sql);

        // Ausführen des Statements mit prepared Values
        if (!$stmt->execute($columnValues)) {
            error_log("Item creation failed in table: {$tableName}, Data: " . $columnNames, $columnValues . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }
```

> **executeDeletion** Method nimmt 2 Parameter auf, **tableName**, **itemId** und löscht den entsprechenden Datensatz. Die Methode ist straight forward mit prepared Statement.

```php
    public function executeDeletion($tableName, $itemId)
    {
        $pdo = parent::connect();
        $sql = "DELETE FROM {$tableName} WHERE id = ?;";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$itemId])) {
            return true;
        } else {
            error_log("Item deletion failed: $tableName, $itemId" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
    }
```

---

# drop.api.php

> Die drop API löscht den Data Table nach Verlassen der Anwendung. Hiefür wird lediglich der **tableName** als Parameter übergeben.

```php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $tableName = strtolower($data);

    // Instanzierung der DropContr Klasse
    $newDrop = new DropContr($tableName);
    $result = $newDrop->dropTable();

    if (!$result) {
        error_log("error on dropTable method: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
        echo json_encode(["success" => false]);
        exit();
    }
    echo json_encode(["success" => true]);
}
```

---

# drop-contr.class.php

> **DropContr** Klasse verwendet für den Constructor den Parameter **tableName**. **dropTable** Method validiert **tableName** und gibt bei Erfolg den Parameter an die **Drop** Klasse weiter. **DropContr extends Drop**

```php
    public function dropTable()
    {
        $tableNameValid = $this->validateTableName($this->tableName);
        if (!$tableNameValid) {
            error_log("tablename invalid: $this->tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        $result = parent::queryTableDrop($this->tableName);
        return $result;
    }
```

> validateTableName validiert mittels Regex.

```php
    private function validateTableName($tableName)
    {
        if (!preg_match('/^[a-z0-9]+$/', $tableName)) {
            error_log("Invalid Table Name: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }
```

---

# drop.class.php

> **Drop extends Dbh** Class. **queryTableDrop** löscht anschließend den Data Table aus der Datenbank und gibt einen Bool zurück.

```php
    public function queryTableDrop($tableName)
    {
        $pdo = parent::connect();
        $sql = "DROP TABLE {$tableName};";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            error_log("query table drop failed: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }
```

---

# export.api.php

> Die **export** API stellt mittels **tableName** eine CSV Datei zum Download bereit. In Summe werden alle Daten aus dem Table ausgelesen und in einem File Stream zum Download bereit gestellt. Zu Beginn wird die **ExportContr** Klasse instanziert. Da ein File Stream Erzeugt wird ist kein Return Value notwendig.

```php
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $tableName = isset($_GET['tableName']) ? $_GET['tableName'] : null;
    file_put_contents('debug.log', $tableName . PHP_EOL, FILE_APPEND);

    $newExport = new ExportContr($tableName);
    $response = $newExport->exportData();

    if (!$response) {
        error_log("exportData call failed with tableName: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
        header('Content-Type: application/json');
        echo json_encode(["success" => false]);
        exit();
    }
}
```

---

# export-contr.class.php

> **ExportContr extends Export**. Der Constructor von **ExportContr** nimmt einen Parameter an. **tableName** wird mittels Regex validiert. Im File Import wurde keine ID übermittelt, diese wurde nur im Rahmen der Bearbeitung als unique identifier für Front- und Backend Kommunikation verwendet. Da die Zielsetzung vorschreibt, das Import Format beim Export bei zu behalten, wird ID aus dem Export ausgenommen. **tableHeaders** wird anhand des **tableName** ohne ID angefragt. **tableName** und **tableHeaders** werden an **queryExportData** Method übergeben.

```php
    public function exportData()
    {
        // Validierung von tableName
        $validTableName = $this->validateTableName($this->tableName);

        if (!$validTableName) {
            error_log("tableName invalid: $this->tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }

        // tableHeader werden in Export Klasse ohne ID angefragt
        $tableHeaders = parent::getTableHeadersExclID($this->tableName);
        // exportData fängt vollständigen Datenbestand exkl. ID ab
        $exportData = parent::queryExportData($this->tableName, $tableHeaders);

        if (!$exportData["success"]) {
        error_log("data export failed: $this->tableName" . PHP_EOL, 3, "../logs/app-error.log");
        return false;
        // ...
    }
```

> Ist der Datenexport erfolgreich, werden zuerst die Header gesetzt. In der Variable **output** wird mittels **fopen** Methode ein File Stream geöffnet. Wenn **exportData** nicht leer ist, wird Index 0 mit **fputcsv** Methode ausgelesen und als Header der CSV Datei gesetzt. Als Delimiter wird ";" verwendet. Ein Loop schreibt danach alle übrigen Zeilen in den File Stream. **fclose** schließt den File Stream und **exit**() beendet den Prozess. (Der File Stream wird von der Frontend API exportService.js ausgelesen.)

```php
    public function exportData()
    {
        // ...
        } else if ($exportData["success"] && $exportData["data"]) {
            // Header für File Stream setzen
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
            // File Stream öffnen
            $output = fopen('php://output', 'w');

            // Header für CSV in File Stream schreiben
            if (!empty($exportData["data"])) {
                fputcsv($output, array_keys($exportData["data"][0]), ";");
            }

            // restliche Daten in File Stream schreiben
            foreach ($exportData["data"] as $row) {
                fputcsv($output, $row, ";");
            }

        	// File Stream schließen und Vorgang beenden.
            fclose($output);
            exit();
        }
        return false; // wird nur erreicht falls etwas fehlschlägt.
    }
```

> Der Vollständigkeit halber **validateTableName**.

```php
    private function validateTableName($tableName)
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $tableName)) {
            error_log("Invalid tableName: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
            return false;
        }
        return true;
    }
```

---

# export.class.php

> **Export extends Dbh**. **Export** Klasse hat 2 Methoden. **getTableHeadersExclID** empfängt alle Columns im Data Table. Ist der Return Value aus welchem Grund auch immer leer, wird ein leeres Array zurück gegeben. Andernfalls wird der 1. Eintrag ergo die ID entfernt und an **ExportContr** übergeben.

```php
    public function getTableHeadersExclID($tableName)
    {
        $pdo = parent::connect();
        // Abfrage COLUMNS
        $sql = "SHOW COLUMNS FROM {$tableName};";
        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute()) {
            error_log("statement execution failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
            return ["success" => false];
        }

        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($columns)) {
            return [];
        }
        // Entfernen von 1. Eintrag ergo ID
        $columns = array_slice($columns, 1);
        return $columns;
    }
```

> **queryExportData** fragt mittels **tableName** und **tableHeaders** alle Daten (Ausgenommen Column ID) in der Datenbank ab. Ist **tableHeaders** ein leeres Array, wird eine Fehlermeldung zurück gegeben. Die Spalten werden in der Variable **columns** mittels implode in einen String umgewandelt.

```php
    public function queryExportData($tableName, $tableHeaders)
    {
        if (empty($tableHeaders)) {
            return ["success" => false, "message" => "No headers provided"];
        }

        $pdo = parent::connect();
        // tableHeader Array wird in String umgewandelt
        $columns = implode(", ", $tableHeaders);
        $sql = "SELECT $columns FROM $tableName;";
        // ...
    }
```

> Ist die Abfrage erfolgreich werden die Daten zurück an die **ExportContr** Klasse gegeben und im Verlauf dessen in den File Stream geschrieben.

```php
    public function queryExportData($tableName, $tableHeaders)
    {
        // ...
        try {
            $stmt = $pdo->prepare($sql);

            if (!$stmt->execute()) {
                error_log("statement execution failed: $stmt" . PHP_EOL, 3, "../logs/app-error.log");
                return ["success" => false];
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["success" => true, "data" => $data];

        } catch (PDOException $e) {
            error_log("Error in queryExportData: " . $e->getMessage() . PHP_EOL, 3, "../logs/app-error.log");
            return ["success" => false];
        }
    }
```

---
