<?php
// Remove .com from domain name
$domain = str_replace('.com', '', $this->domain);

// Create the url dynamically using the correct wordpress path and domain name
echo '<script async src="https://kumo.network-n.com/dist/app.js" site="'.$domain.'"></script>';
