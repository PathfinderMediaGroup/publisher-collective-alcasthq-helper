<?php
// Remove .com from domain name
$domain = str_replace('.com', '', $this->domain);
// Create the url dynamically using the correct wordpress path and domain name
$url = sprintf('https://os1.network-n.com/dist/%s.min.js', $domain);

if (class_exists('Patreon_Frontend') && method_exists('Patreon_Frontend', 'hide_ad_for_patrons')) {
	$patreon = Patreon_Frontend::hide_ad_for_patrons(1, '<script async="async" src="'.$url.'"></script>');
} else {
	$patreon = '<script async="async" src="'.$url.'"></script>';
}

if (!empty($patreon)) {
	echo $patreon;
}
