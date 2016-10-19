<?php
//var_dump(Database::import(realpath('export.sql')));
//
// new Query('');
// ImportDB(Query::$pdo);

// function ImportDB($dbh) {
//   $sql = 'source '.realpath('export.sql');
//   try {
//     $stmt = $dbh->prepare($sql);
//     var_dump($stmt->execute());
//     var_dump($stmt->errorInfo());
//   }
//   catch (PDOException $e) {
//     print $e->getMessage();
//   }
// }
var_dump(Database::export(APP_TMP.'/export.php'));
// var_dump(include APP_TMP.'/export.php');