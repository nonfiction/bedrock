<?php
namespace nf;

$agency_url = "https://www.nonfiction.ca";
$agency_name = "nonfiction studios";

add_action( 'login_enqueue_scripts', function() {
  echo "<style type='text/css'>\n";
  echo "  #login h1 a, .login h1 a {\n";
  echo "    background-image: url(/app/site/assets/img/logo.jpg);\n";
  echo "    background-size: contain;\n";
  echo "    width: 150px;\n";
  echo "    height: 150px;\n";
  echo "    border-radius: 50%;\n";
  echo "  }\n";
  echo "</style>";
});

add_filter( 'login_headertext', function() use ($agency_name) { return $agency_name; });
add_filter( 'login_headerurl', function() use ($agency_url) { return $agency_url; });
