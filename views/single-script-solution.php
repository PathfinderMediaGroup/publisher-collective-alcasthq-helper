<?php
// Remove .com from domain name
$domain = str_replace('.com', '', $this->domain);
// Create the url dynamically using the correct wordpress path and domain name
$url = sprintf('https://os1.network-n.com/dist/%s.min.js', $domain);

echo '<script async="async" src="'.$url.'"></script>';
