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

### crud.api.php

> Die CRUD API kümmert sich um Anfragen aus dem Frontend bezüglich Neuanlagen, Anpassungen und Löschungen von Datensätzen. Sie empfängt zwei Parameter **tableName** und **item**. Der **tableName** wird immer **_strtolower_** umgewandelt. Die Separierung findet mittels **REQUEST_METHOD** und ggf. **PATH_INFO** statt.

```php
// Filter mit "PUT" für Anpassungen
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    // Umwandlung in Kleinbuchstaben, da in SQL Data Table names keine Caps zu finden sind
    $tableName = strtolower($data["tableName"]);
    $item = $data["item"];
    // ...

// Filter mit "POST" für Anlage und Löschung
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Filter mit "/add" zur Anlage
    if ($_SERVER["PATH_INFO"] === "/add") {
    // tableName und item Deklaration
        // ...

    // Filter mit "/delete" zur Löschung
    } else if ($_SERVER["PATH_INFO"] === "/delete") {
    // tableName und item Deklaration
        // ...
    }
    // ...
}
```

> Alle Operationen instanzieren die gleiche Klasse, **CrudContrl** mittels Constructor Method und übergeben direkt die Parameter. Danach wird die jeweilige Methode der Klasse aufgerufen. In allen Fällen wird ein Bool an das Frontend kommuniziert.

```php
// Anpassung
$newUpdate = new CrudContr($tableName, $item);
$response = $newUpdate->updateItemData();

// Return Value
if (!$response) {
    echo json_encode(["success" => false]);
}
echo json_encode(["success" => true]);

// ...
// Anlage
$newItem = new CrudContr($tableName, $item);
$response = $newItem->addNewItem();
// Gleiches Verhalten bei Return Value

// ...
// Löschung
$newDelete = new CrudContr($tableName, $item);
$response = $newDelete->deleteItem();
// Gleiches Verhalten bei Return Value
```
