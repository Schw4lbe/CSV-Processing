<?php
class Dbh
{
    protected function connect()
    {
        try {
            $username = "root";
            $password = "test123!";
            // Dynamically set the host based on an environment variable or other condition
            $host = getenv('DOCKER_ENV') ? 'db' : 'localhost';
            $dbh = new PDO("mysql:host={$host};dbname=csv-processing;charset=utf8mb4", $username, $password);
            return $dbh;
        } catch (PDOException $e) {
            // $e parameter not in output for security reasons
            // should be logged into a separate file on separate directory

            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database connection error']);
            die();
        }
    }
}