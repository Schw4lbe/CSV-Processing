<?php
class Dbh
{
    protected function connect()
    {
        try {
            $username = "root";
            $password = "";
            $dbh = new PDO("mysql:host=localhost;dbname=csv-processing", $username, $password);
            return $dbh;
        } catch (PDOException $e) {
            // $e parameter not in output for security reasons
            // should be logged into a separate file

            // Send a JSON response with the error message
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database connection error']);
            die();
        }
    }
}