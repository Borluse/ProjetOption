<?php
require 'Analyseur.php';

$test = new Analyseur("test.xls");

$test->retirerStructure();

$test->retirerDonnee();
?>