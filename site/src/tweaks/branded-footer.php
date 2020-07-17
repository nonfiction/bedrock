<?php
namespace nf;

$agency_url = "https://www.nonfiction.ca";
$agency_name = "nonfiction studios";

$footer  = "<span id='footer-thankyou'>handcrafted by ";
$footer .=   "<a href='$agency_url' target='_blank'>";
$footer .=     $agency_name;
$footer .=   "</a>";
$footer .= "</span>";

add_intervention( 'update-label-footer', $footer );
