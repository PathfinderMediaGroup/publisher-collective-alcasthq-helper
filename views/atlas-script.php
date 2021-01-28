<?php
// Remove .com from domain name
$domain = str_replace('.com', '', $this->domain);

// Create the url dynamically using the correct wordpress path and domain name
$patreon = '<script async src="https://kumo.network-n.com/dist/app.js" site="'.$domain.'"></script>';
if (class_exists('Patreon_Frontend') && method_exists('Patreon_Frontend', 'hide_ad_for_patrons')) {
	$patreon = Patreon_Frontend::hide_ad_for_patrons(1, $patreon);
}

if (!empty($patreon)) {
	echo $patreon;
}
