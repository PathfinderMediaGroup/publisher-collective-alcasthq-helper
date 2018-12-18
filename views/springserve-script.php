<!-- Springserve -->
<script>
// SpringServe keys
var full_uri = window.location.pathname;
// Remove beginning and trailing slash
if (full_uri.charAt(0) == "/") full_uri = full_uri.substr(1);
if (full_uri.charAt(full_uri.length - 1) == "/") full_uri = full_uri.substr(0, full_uri.length - 1);
var ss_keys = '';
if( full_uri ){
  ss_keys = '';
  var eURI = encodeURIComponent( full_uri );
  ss_keys += '&ss_url=' + eURI;
  console.log( 'SpringServe: ss_url = '+full_uri );

  var url_parts = full_uri.split('/');
  for( var index in url_parts ){
      var part = url_parts[index].replace(/-/g, '_');
      ss_keys += '&ss_url_part_'+part+'=1';
      console.log( 'SpringServe: ss_url_part_'+part+' = 1' );
  }
} else {
  ss_keys = '&ss_url=/';
  console.log( 'SpringServe: ss_url = /' );
}

var w = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
var supplyID = (w > 800) ? 321126 : 321127; // Change supply tag based on browser width
var tagURL = "http://vid.springserve.com/vast/"+supplyID+"?w=$$WIDTH$$&h=$$HEIGHT$$&url=$$REFERER$$&cb=$$RANDOM$$" + ss_keys;
</script>
<!-- End Springserve -->
