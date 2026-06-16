<?php
interface DatabaseInterface {
    public function getConnection(): PDO;
}
?>
