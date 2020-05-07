<?php
// Remove .com from domain name
$domain = str_replace('.com', '', $this->domain);
// Create the url dynamically using the correct wordpress path and domain name
$url = sprintf('https://dash.os.network-n.com/dist/%s.min.js', $domain);
?>

<script async="async" src="<?php echo $url; ?>"></script>
