<?php

require 'sift-php/lib/Services_JSON-1.0.3/JSON.php';
require 'sift-php/lib/SiftRequest.php';
require 'sift-php/lib/SiftResponse.php';
require 'sift-php/lib/SiftClient.php';
require 'sift-php/lib/Sift.php';

$client = new SiftClient('55d57364e4b0d743f204436e');

// Sample $create_account event
$properties = array(
  // Required Fields
  '$user_id'    => 'billy_jones_301',

  // Supported Fields
  '$session_id'       => 'gigtleqddo84l8cm15qe4il',
  '$user_email'       => 'bill@gmail.com',
  '$name'             => 'Bill Jones',
  '$phone'            => '1-415-555-6040',
  '$referrer_user_id' => 'janejane101',
  '$payment_methods'  => array(
      array(
          '$payment_type'    => '$credit_card',
          '$card_bin'        => '542486',
          '$card_last4'      => '4444'
      )
  ),
  '$billing_address'  => array(
      '$name'         => 'Bill Jones',
      '$phone'        => '1-415-555-6040',
      '$address_1'    => '2100 Main Street',
      '$address_2'    => 'Apt 3B',
      '$city'         => 'New London',
      '$region'       => 'New Hampshire',
      '$country'      => 'US',
      '$zipcode'      => '03257'
  ),

  '$social_sign_on_type'   => '$twitter',

  // Suggested Custom Fields
  'twitter_handle'          => 'billyjones',
  'work_phone'              => '1-347-555-5921',
  'location'                => 'New London, NH',
  'referral_code'           => 'MIKEFRIENDS',
  'email_confirmed_status'  => "$pending",
  'phone_confirmed_status'  => "$pending"
);

$response = $client->track('$create_account', $properties);

header('Location: http://dev.carrothon.com/register.php');
exit;
