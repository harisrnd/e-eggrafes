<?php
$schsearch = "ΛΑΠΠΑ   test";
$words = preg_split('/[\s]+/', $schsearch);
//$words = explode(" ", $schsearch);
//echo $schsearch;
echo $words[0];
echo $words[1];
echo sizeof($words);
foreach ($words as $word)
  echo $word;
