<?php 
namespace nf;


// Use Digital Ocean Spaces with Human Made S3 Uploads
// https://github.com/humanmade/S3-Uploads#custom-endpoints
add_filter( 's3_uploads_s3_client_params', function ( $params ) {

  if ( defined( 'S3_UPLOADS_ENDPOINT' ) ) {
    $params['endpoint'] = S3_UPLOADS_ENDPOINT;
    $params['use_path_style_endpoint'] = true;
    $params['debug'] = false; // Set to true if uploads are failing.
  }
  return $params;

}, 5, 1 );

// https://github.com/humanmade/S3-Uploads/issues/373#issuecomment-602016258
// add_filter( 'upload_dir', function( $uploads ) {
//   $uniqid = uniqid();
//   $uploads['path'] .= '/' . $uniqid;
//   $uploads['url'] .= '/' . $uniqid;
//   return $uploads;
// } );


// add_filter( 'wp_get_attachment_metadata', function($data, $attachment_id) {
//   var_dump($data);
//   return $attachment_id;
//   // $data['filesize'] = 0;
//   // return $data;
// });
