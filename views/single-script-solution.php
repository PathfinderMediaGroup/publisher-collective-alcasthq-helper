<?php
// Remove .com from domain name
$domain = str_replace('.com', '', $this->domain);
// Create the url dynamically using the correct wordpress path and domain name
$url = sprintf(plugin_dir_url(dirname(__FILE__)) . 'js/%s.min.js', $domain);
?>
<script async="async" src="<?php echo $url; ?>"></script>
