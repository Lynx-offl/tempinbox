<?php
/*
 * Function to generate pronounceable word 
 */
function generateRandomWord() {
    $c  = 'bcdfghjklmnprstvwz'; //consonants except hard to speak ones
    $v  = 'aeiou';              //vowels
    $a  = $c.$v;                //both
    $pw = '';
    for($j=0;$j < 2; $j++){
        $pw .= $c[rand(0, strlen($c)-1)];
        $pw .= $v[rand(0, strlen($v)-1)];
        $pw .= $a[rand(0, strlen($a)-1)];
    }
    return $pw;
}
/*
 * Return a single random domain from the list of the domain defined in config.php file
 */
function generateRandomDomain($domainList) {
    $count = count($domainList);
    $selectedDomain = rand(1,$count) - 1;
    return $domainList[$selectedDomain];
}
?>