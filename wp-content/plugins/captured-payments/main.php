<?php
/*
Plugin Name: Captured Payments
Plugin URI: Plugins Url
Description: Approved/Void/Cancel paypal payments 
Version: 1.0
Author: Jino Lacson
Author URI: Author's Website
License:GPL2
*/


//ipn paypal access tokens for api request
$clientId= 'ASiEyKPeqeWsXsTKsnUO336cYcCT-XEnK-TeCLJbE2P56nH31_MS6L-74IqOR7IYX4h0BAN65V4fcuLe';
$secret = 'EAzy7qHo2Ig-r8mFPeha6XFLLSo1QoutPyOE000vjRgBRU2HI3p_Xk64VpOFak9Ih_rii4URapfxd2cP';  

//pdt identity
$identity = 'QXLl9lTqZG9UMFUhHI7rQvrAIpOsPVzH0rJU33jDwtqeyDOoAZu6hqU5Oyq';
$sandbox = 'https://www.sandbox.paypal.com/cgi-bin/webscr';


function reservation_host()
{  
    global $title, $wpdb;
    
    $user_ID = get_current_user_id();
  
    $lists =$wpdb->get_results("
            SELECT *
            
            FROM
                jd_reservations a
            
            LEFT JOIN
            
                 jd_cg_captured_payments b
            ON 
                 a.id = b.room_id
            WHERE
                 b.host_id = $user_ID
            AND
                 b.approved = 0
            AND
                a.approve NOT IN('yes','no')
            
            GROUP BY 
            
                b.txn_id;
    ");
    
    if( $wpdb->num_rows > 0 )
    {
        echo '<form name="post" method="post" id="post">';
        echo '<table class="gridtable"><th>ID</th><th>ARRIVAL</th><th>DEPARTURE</th><th>USER</th><th>NAME</th><th>EMAIL</th><th>COUNTRY</th><th>APPROVE</th>
              <th>ROOM</th><th>ROOMNUMBER</th><th>NUMBER</th><th>CHILDS</th><th>PRICE</th><th>RESERVATED</th><th colspan=4>ACTION</th>';
    }
    else
    {
        echo 'No Reservations found.';
    }
    
        
    foreach ($lists as $list) {
        
        //jd_cg_captured_payments
        $idt = $list->tid;
        $txn_id = $list->txn_id;
        $payer_email = $list->payer_email;
        $item_name = $list->item_name;
        $mc_currency = $list->mc_currency;
        $mc_gross = $list->mc_gross;
        $approved = $list->approved;
        
        //jd_reservations
        $idr = $list->id;
        $arrival = $list->arrival;
        $departure = $list->departure;
        $user = $list->user;
        $name = $list->name;
        $email = $list->email;
        $country = $list->country;
        $approve = $list->approve = "del" ? "Cancelled" : $list->approve ;
        $room = $list->room;
        $roomnumber = $list->roomnumber;
        $number = $list->number;
        $childs = $list->childs;
        $price = $list->price;
        $reservated = $list->reservated;
        
        
        $get_post_ids =$wpdb->get_var("SELECT post_id FROM jd_postmeta WHERE meta_value ='".$room."'");
        $authors =$wpdb->get_var("SELECT post_author FROM jd_posts WHERE ID ='".$get_post_ids."'");
        
        //get author by roomid for messaging
        $user_info = get_userdata( $authors );
	    
        echo "<tr>
                <td>".$idr."</td>
                <td>".$arrival."</td>
                <td>".$departure."</td>
                <td>".$user."</td>
                <td>".$name."</td>
                <td>".$email."</td>
                <td>".$country."</td>
                <td>".$approve."</td>
                <td>".$room."</td>
                <td>".$roomnumber."</td>
                <td>".$number."</td>
                <td>".$childs."</td>
                <td>".$price."</td>
                <td>".$reservated."</td>
                <td><a href='".site_url()."/confirmation-approve/?idr=".$idr."&idt=".$idt."&txn=".$txn_id."'>Approve</a></td>
                 <td><a href='".site_url()."/confirmation-disapproved/?idr=".$idr."&idt=".$idt."'>Disapprove</a></td>
                 <td><a href='".site_url()."/members/judan/messages/compose/?unames=".$user_info->user_login."'>Send Message</a></td>
              </tr>";
    }
    echo "</tr></table>";
      
    return;
}

function reservation_guest()
{
    global $title, $wpdb;
    
    $user_ID = get_current_user_id();
  
    $lists =$wpdb->get_results("
            SELECT *
            
            FROM
                jd_reservations a
            
            LEFT JOIN
            
                 jd_cg_captured_payments b
            ON 
                 a.id = b.room_id
            WHERE
                 b.host_id = $user_ID
            AND
                 b.approved = 0
            AND
                a.approve NOT IN('yes','no')
            
            GROUP BY 
            
                b.txn_id;
    ");
    
    if( $wpdb->num_rows > 0 )
    {
        echo '<form name="post" method="post" id="post">';
        echo '<table class="gridtable"><th>ID</th><th>ARRIVAL</th><th>DEPARTURE</th><th>USER</th><th>NAME</th><th>EMAIL</th><th>COUNTRY</th><th>APPROVE</th>
              <th>ROOM</th><th>ROOMNUMBER</th><th>NUMBER</th><th>CHILDS</th><th>PRICE</th><th>RESERVATED</th><th colspan=4>ACTION</th>';
    }
    else
    {
        echo 'No Reservations found.';
    }
    
    foreach ($lists as $list) {
        
        //jd_cg_captured_payments
        $idt = $list->tid;
        $txn_id = $list->txn_id;
        $payer_email = $list->payer_email;
        $item_name = $list->item_name;
        $mc_currency = $list->mc_currency;
        $mc_gross = $list->mc_gross;
        $approved = $list->approved;
        
        //jd_reservations
        $idr = $list->id;
        $arrival = $list->arrival;
        $departure = $list->departure;
        $user = $list->user;
        $name = $list->name;
        $email = $list->email;
        $country = $list->country;
        $approve = $list->approve = "del" ? "Cancelled" : $list->approve ;
        $room = $list->room;
        $roomnumber = $list->roomnumber;
        $number = $list->number;
        $childs = $list->childs;
        $price = $list->price;
        $reservated = $list->reservated;
        
        
        $get_post_ids =$wpdb->get_var("SELECT post_id FROM jd_postmeta WHERE meta_value ='".$room."'");
        $authors =$wpdb->get_var("SELECT post_author FROM jd_posts WHERE ID ='".$get_post_ids."'");
        
        //get author by roomid for messaging
        $user_info = get_userdata( $authors );
	    
	
        echo "<tr>
                <td>".$idr."</td>
                <td>".$arrival."</td>
                <td>".$departure."</td>
                <td>".$user."</td>
                <td>".$name."</td>
                <td>".$email."</td>
                <td>".$country."</td>
                <td>".$approve."</td>
                <td>".$room."</td>
                <td>".$roomnumber."</td>
                <td>".$number."</td>
                <td>".$childs."</td>
                <td>".$price."</td>
                <td>".$reservated."</td>
                <td><a href='".site_url()."/cancel-confirm-reservation/?idr=".$idr."&idt=".$idt."&txn=".$txn_id."'>Cancel</a></td>
                <td><a href='".site_url()."/confirmation-disapproved/?idr=".$idr."&idt=".$idt."'>Disapprove</a></td>
                <td><a href='".site_url()."/members/judan/messages/compose/?unames=".$user_info->user_login."'>Send Message</a></td>
              </tr>";
    }
    echo "</tr></table>";
      
    return;
}

function confirmation_host_disapproved()
{
    
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    
    echo 'Are you sure you want to disapproved this reservation?<br><br>';
    echo "<a href='".site_url()."/host-disapproved/?idr=".$idr."&idt=".$idt."'>Yes</a> | <a href='".site_url()."/list-reservation-host/'>No</a>";
 
    return ;
}
function hosts_disapproved()
{
    
    global $wpdb;
    
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    
    
    $lists =$wpdb->get_results("
        SELECT * FROM jd_reservations WHERE id = $idr
    ");

    foreach ($lists as $list) {
        
        if($list->approve == "del") //cancelled already
        {
            echo "Failed to disapprove reservation. reservation might be cancelled.  <a href='".site_url()."/list-reservation-host/'>Return</a>";
        }
        else
        {
            echo "Reservation was disapproved <a href='".site_url()."/list-reservation-host/'>return</a>";
            
            $arr = getpaypalamounts($idt);
            global $clientId,$secret;
            $results = void_payment(create_access_token($clientId,$secret),$arr['txn']);
            
                $wpdb->update( 
                	'jd_reservations', 
                	array( 
                		'approve' => 'no',	
                	), 
                	array( 'id' => $idr ), 
                	array( 
                		'%s',
                	), 
                	array( '%d' ) 
                );
                
                $wpdb->update( 
                	'jd_cg_captured_payments', 
                	array( 
                		'approved' => '2',	
                	), 
                	array( 'tid' => $idt ), 
                	array( 
                		'%s',
                	), 
                	array( '%d' ) 
                );
        }
    }
    
    return ;
}

function confirmation_host_approved()
{
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    $txn = isset($_GET['txn']) ? $_GET['txn'] : "" ;
    
    echo 'Are you sure you want to approved this reservation?<br><br>';
    echo "<a href='".site_url()."/host-approved/?idr=".$idr."&idt=".$idt."&txn=".$txn."'>Yes</a> | <a href='".site_url()."/list-reservation-host/'>No</a>";
}

function hosts_approved()
{
    global $wpdb;
     
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    $txn = isset($_GET['txn']) ? $_GET['txn'] : "" ;
    
    $lists =$wpdb->get_results("
        SELECT * FROM jd_reservations WHERE id = $idr
    ");

    foreach ($lists as $list) {
        
        if($list->approve == "del" || $list->approve == "no" ) //cancelled and rejected/disapproved are disallowed
        {
            $reason = $list->approve = "del" ? "Cancelled" : "Disapproved"; 
            
            echo "Opps Unable to approve this reservation the reaservation was : <b>".$reason."</b> <a href='".site_url()."/list-reservation-host/'> return</a>";
        }
        else
        {
            //process reservations and paypal
            $arr = getpaypalamounts($idt);

            global $clientId,$secret,$txn;
            
            $results = update_captures(create_access_token($clientId,$secret),$arr['txn'],'{
                "amount": {
                  "currency": "'.$arr['currency'].'",
                  "total": "'.$arr['total'].'"
                },
                "is_final_capture": true
              }');
            
            echo "Reservation was successfully approved <a href='".site_url()."/list-reservation-host/'>return</a>";
           
            $wpdb->update( 'jd_reservations', 
                	array( 
                	'approve' => 'yes',	
                	), 
                	array( 'id' => $idr ), 
                	array( 
                		'%s',
                	), 
                	array( '%d' ) 
                );
                
                $wpdb->update( 
                	'jd_cg_captured_payments', 
                	array( 
                		'approved' => '1',	
                	), 
                	array( 'tid' => $idt ), 
                	array( 
                		'%s',
                	), 
                	array( '%d' ) 
                );
        }
    }
    return;
}
function cancels_confirm_reservations()
{
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    $txn = isset($_GET['txn']) ? $_GET['txn'] : "" ;
 
    echo 'Are you sure you want to cancel this reservation?<br><br>';
    echo "<a href='".site_url()."/cancel-reservation/?idr=".$idr."&idt=".$idt."&txn=".$txn."'>Yes</a> | <a href='".site_url()."/reservations-for-guest/'>No</a>";
 
}
function cancel_reservations()
{
    global $wpdb;
    
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    
    
    $lists =$wpdb->get_results("
        SELECT * FROM jd_reservations WHERE id = $idr
    ");

    foreach ($lists as $list) {
        
        if($list->approve == "del") //cancelled already
        {
            echo "Reservation already cancelled.  <a href='".site_url()."/reservations-for-guest/'>Return</a>";
        }
        else
        {
            echo "Reservation was cancelled <a href='".site_url()."/reservations-for-guest'>return</a>";
            
            $arr = getpaypalamounts($idt);
            global $clientId,$secret;
            $results = void_payment(create_access_token($clientId,$secret),$arr['txn']);
            
                $wpdb->update( 
                	'jd_reservations', 
                	array( 
                		'approve' => 'del',	
                	), 
                	array( 'id' => $idr ), 
                	array( 
                		'%s',
                	), 
                	array( '%d' ) 
                );
                /*
                $wpdb->update( 
                	'jd_cg_captured_payments', 
                	array( 
                		'approved' => '2',	
                	), 
                	array( 'tid' => $idt ), 
                	array( 
                		'%s',
                	), 
                	array( '%d' ) 
                );
                */
        }
    }
}

function successreservation_reservations()
{
    global $wpdb;
    
    $user = isset($_GET['cm']) ? $_GET['cm'] : ""; 

    $txn = isset($_GET['tx']) ? $_GET['tx'] : "";

    $response = get_pdt_response($user,$txn);
    
     foreach ($response as $key => $value) {
                
              switch ($key) {
                  
                    case 'invoice':
                        $room_id = $value;
                    break;
                      
                    case 'item_number' :
                        $host_id = $value;
                    break;
                    
                    case 'payer_email' :
                        $payer_email = $value;
                    break;
                    
                    case 'item_name' :
                        $item_name = $value;
                    break;
                    
                    case 'mc_currency' :
                        $mc_currency = $value;
                    break;
                    
                    case 'mc_gross' :
                        $mc_gross = $value;
                    break;           
                   
              }
     }
     
      $wpdb->insert(
			 	
				 'jd_cg_captured_payments',
				 	
				 	 array(
				 	 	   "host_id" =>   $host_id ,
				 	 	   "room_id" =>  $room_id,
				 	 	   "payer_email" => $payer_email , 
				 	 	   "txn_id"=>  $txn,
				 	 	   "item_name" =>  $item_name, 
				 	 	   "mc_currency" =>  $mc_currency,
				 	 	   "mc_gross" =>  $mc_gross ),
				 	 	    
				 	 array("%s" ,"%s", "%s", "%s", "%s", "%s", "%s" )
			 );
    
    return;
}

function rhost($content)
{
    add_shortcode(  'rhost' , 'reservation_host' );
    return $content;
}
function rguest($content)
{
    add_shortcode(  'rguest' , 'reservation_guest' );
    return $content;
}
function confhostdisapproved($content)
{
    add_shortcode(  'confhostdisapproved' , 'confirmation_host_disapproved' );
    return $content;
}
function confhostapproved($content)
{
    add_shortcode(  'confhostapproved' , 'confirmation_host_approved' );
    return $content;
}
function host_disapproved($content)
{
    add_shortcode(  'host_disapproved' , 'hosts_disapproved' );
    return $content;
}
function host_approved($content)
{
    add_shortcode(  'host_approved' , 'hosts_approved' );
    return $content;
}
function cancel_reservation($content)
{
    add_shortcode(  'cancel_reservation' , 'cancel_reservations' );
    return $content;
}

function cancel_confirm_reservation($content)
{
    add_shortcode(  'cancel_confirm_reservation' , 'cancels_confirm_reservations' );
    return $content;
}

function successreservation($content)
{
    add_shortcode(  'successreservation' , 'successreservation_reservations' );
    return $content;
}

add_action( 'the_content', 'rhost');
add_action( 'the_content', 'rguest');
add_action( 'the_content', 'confhostdisapproved');
add_action( 'the_content', 'confhostapproved');
add_action( 'the_content', 'host_disapproved');
add_action( 'the_content', 'host_approved');
add_action( 'the_content', 'cancel_confirm_reservation');
add_action( 'the_content', 'cancel_reservation');
add_action( 'the_content', 'successreservation');
add_action( 'wp_head' , 'cascade' );


//get the informations from jd_cg_captured_payments for submition
function getpaypalamounts($idt)
{
     global $wpdb;

    $lists =$wpdb->get_results("
       SELECT * FROM `jd_cg_captured_payments` WHERE tid = $idt
    ");
    
     foreach ($lists as $list) {
         
         return array(
         'txn' => $list->txn_id,
         'currency' => $list->mc_currency,
         'total' => $list->mc_gross
        );
     }
}

//generate acces tokens for paypal transactions
function create_access_token()
{
    global $clientId,$secret;
    
    $ch = curl_init();                                                        
	curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

	$result = curl_exec($ch);

	if(empty($result))die("Error: No response.");

	else
	{
	    $json = json_decode($result);
	    //print_r($json);
		$access_tokens = $json->access_token;
	}

	return $access_tokens;

}

//update payment in paypal
function update_captures($token,$authorizations,$postdata)
{
	
    $curl = curl_init('https://api.sandbox.paypal.com/v1/payments/authorization/'.$authorizations.'/capture'); 
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    			'Authorization: Bearer '.$token,
    			'Accept: application/json',
    			'Content-Type: application/json'
    			));
    
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata); 
    #curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
    $response = curl_exec( $curl );
    
    
    if(empty($response))
    {
    	 die(curl_error($curl));
    	 curl_close($curl); 
    }
    else
    {
    	//$info = curl_getinfo($curl);
    }
    
    $jsonResponse = json_decode($response, TRUE);
    return $jsonResponse;

}

//void payment in paypal
function void_payment($token,$authorizations)
{
	
    $curl = curl_init('https://api.sandbox.paypal.com/v1/payments/orders/'.$authorizations.'/do-void '); 
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    			'Authorization: Bearer '.$token,
    			'Accept: application/json',
    			'Content-Type: application/json'
    			));
    
  
    $response = curl_exec( $curl );
    
    
    if(empty($response))
    {
    	 die(curl_error($curl));
    	 curl_close($curl); 
    }
    else
    {
    	//$info = curl_getinfo($curl);
    }
    
    $jsonResponse = json_decode($response, TRUE);
    return $jsonResponse;

}
//pdt return success
function get_pdt_response($user,$tx)
{
         global $identity,$sandbox;
         
         $ch = curl_init(); 
         
          curl_setopt_array($ch, array ( CURLOPT_URL => $sandbox,
          CURLOPT_POST => TRUE,
          CURLOPT_POSTFIELDS => http_build_query(array
            (
              'cmd' => '_notify-synch',
              'tx' => $tx,
              'at' => $identity,
            )),
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_HEADER => FALSE,
        ));
        
        ksort($response);
        
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if($status == 200 AND strpos($response, 'SUCCESS') === 0)
        {
            parse_str(str_replace(PHP_EOL,'&',$response),$response);
            
            return $response;
           
        }
}
//table css
function cascade() 
{

echo "<style>
     table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		color:#333333;
		border-width: 1px;
		border-color: #3A3A3A;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #3A3A3A;
		background-color: #B3B3B3;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #3A3A3A;
		background-color: #ffffff;
	}
    

    </style>";
}


?>