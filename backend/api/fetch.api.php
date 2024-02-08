<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

// included classes
include_once "../classes/dbh.class.php";
include_once "../classes/fetch.class.php";
include_once "../classes/fetch-contr.class.php";

// Handle preflight request for OPTIONS method from browser
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if ($_SERVER["PATH_INFO"] === "/fetch") {
        $tableName = isset($_GET["tableName"]) ? $_GET["tableName"] : null;
        $page = isset($_GET["page"]) ? $_GET["page"] : null;
        $itemsPerPage = isset($_GET["itemsPerPage"]) ? $_GET["itemsPerPage"] : null;

        $sortByString = isset($_GET["sortBy"]) ? $_GET["sortBy"] : null;
        $sortBy = json_decode($sortByString, true);

        $newFetch = new FetchContr($tableName, $page, $itemsPerPage, $sortBy, null, null);
        $tableData = $newFetch->fetchTableData();
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "tableData" => $tableData["data"], "total" => $tableData["total"]]);
    }

    if ($_SERVER["PATH_INFO"] === "/search") {
        $tableName = isset($_GET["tableName"]) ? $_GET["tableName"] : null;
        $page = isset($_GET["page"]) ? $_GET["page"] : null;
        $itemsPerPage = isset($_GET["itemsPerPage"]) ? $_GET["itemsPerPage"] : null;

        $sortByString = isset($_GET["sortBy"]) ? $_GET["sortBy"] : null;
        $sortBy = json_decode($sortByString, true);

        $searchCategory = isset($_GET["searchCategory"]) ? $_GET["searchCategory"] : null;
        $searchQuery = isset($_GET["searchQuery"]) ? $_GET["searchQuery"] : null;

        $newSearch = new FetchContr($tableName, $page, $itemsPerPage, $sortBy, $searchCategory, $searchQuery);
        $tableData = $newSearch->fetchSearchData();
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "tableData" => $tableData["data"], "total" => $tableData["total"]]);
    }
}