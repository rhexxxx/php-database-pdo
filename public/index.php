<?php 

require_once '../App/core/DB.php';

define("DB_HOST",'localhost');
define('DB_USER','root'); 
define("DB_PASS", 'root');
define('DB_NAME', 'crud_dasar');
$db = new DB();

$results = [];

$inputfile = 'input.json';
$inputData = json_decode(file_get_contents($inputfile),true);

$outputfile = "output.json";
$outputlog = [];

if(isset($inputData["create"])){
    $table = $inputData['create']['table'];
    $data = $inputData['create']['data'];
    $createResult = $db->create($table, $data);
    
    $outputlog['create'] = $createResult ? "Create operation succesful!" :"Create operationn failed!";
}
if(isset($inputData['update'])){
    $table = $inputData['read']['table'];
    $conditions = $inputData['read']['conditions'];
    $readResult = $db->read($table, $conditions);

    $outputlog['read'] = !empty($readResult) ? $readResult :"no record found.";
}
if (isset($inputData['update'])) {
    $table = $inputData['update']['table'];
    $data = $inputData['update']['data'];
    $conditions = $inputData['update']['conditions'];
    $updateResult = $db->update($table, $data, $conditions);
    
    $outputLog['update'] = $updateResult ? "Update operation successful!" : "Update operation failed.";
}
if (isset($inputData['delete'])) {
    $table = $inputData['delete']['table'];
    $conditions = $inputData['delete']['conditions'];
    $deleteResult = $db->delete($table, $conditions);
    
    $outputLog['delete'] = $deleteResult ? "Delete operation successful!" : "Delete operation failed.";
}

file_put_contents($outputfile, json_encode($outputLog, JSON_PRETTY_PRINT));

echo "CRUD operations completed.";