<?php
/*
Plugin Name: Captured Payments
Plugin URI: Plugins Url
Description: Approved/Void/Cancel paypal payments 
Version: 1.0
Author: Jino Lacson
*/

/*detect host name*/
if(isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" )
{
    $url = 'https://'.$_SERVER['HTTP_HOST'] ;
}
else
{
   $url = 'http://'.$_SERVER['HTTP_HOST'] ; 
}

/*Instantiate first the needed keys for paypal*/
global $wpdb;

	foreach ($wpdb->get_results("SELECT * FROM jd_cg_paypal_keys") as $credentials) {
	    switch ($credentials->name) {
	        case 'clientId':
	           $clientId=$credentials->value;
	            break;
	         case 'secret':
	             $secret=$credentials->value;
	            break;
	         case 'admin_identity':
	             $admin_identity=$credentials->value;
	            break;
	         case 'mode':
	             $sandbox=$credentials->value;
	            break;
	         case 'admin_clientID':
	         	 $adminClientID=$credentials->value;
	         	 break;
	         case 'admin_secret':
	         	 $adminSecret=$credentials->value;
	         	 break;
	         case 'mode':
	             $sandbox=$credentials->value;
	             break;
	        
	       
	    }
	}
function check_prev()
{
	$user = wp_get_current_user();

	if(isset($user->data->ID)){
	  if(!in_array('host',$user->roles)){
	     return true; //guest
	  }else{
	  	  return false; //host
	  }

	}

}
 
function reservation_host()
{  
   
	
    if ( check_prev() )
    {
        echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/reservations-for-guests/'>View Guest Reservations</a>";
    }
    else
    {
            if (is_user_logged_in()  )
            {
                  global $title, $wpdb;
            
            $user_ID = get_current_user_id();
          
            /*$lists =$wpdb->get_results("
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
                        a.approve IN('','del')
                    
                    GROUP BY 
                    
                        b.txn_id;
            ");*/
            
            $lists=$wpdb->get_results("select a.*,b.*,d.post_author FROM jd_reservations a INNER JOIN jd_cg_captured_payments b ON a.id=b.room_id INNER JOIN jd_postmeta c ON c.meta_value=a.room INNER JOIN jd_posts d ON d.ID=c.post_id WHERE d.post_author=$user_ID AND a.approve IN('','del') GROUP BY b.txn_id");
            
            if( $wpdb->num_rows > 0 )
            {
                 echo '<div id="page-wrap">';
                echo '<table class="gridtable"><thead><tr><th>ROOM</th><th>ARRIVAL</th><th>DEPARTURE</th><th>NAME</th><th>EMAIL</th><th>APPROVE</th>
                      <th>ADULTS</th><th>CHILDS</th><th>PRICE</th><th>RESERVATED</th><th colspan=4>ACTION</th></tr></thead>';
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
                $approve = $list->approve == "del" ? "Cancelled" : $list->approve ;
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
        	    
        	    $curr = exchangeRate( $mc_gross, $mc_currency , $_COOKIE['C_CURRENCY']);
        	     
        	     //get original post_id from post_meta
        	    foreach($wpdb->get_results("SELECT post_id FROM jd_postmeta WHERE meta_key = 'vh_resource_id' AND meta_value='$room'") as $pids )
        	    {
        	        $pid = $pids->post_id;
        	    }
        	   
                echo "<tr>
                 <td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small'  href=".get_permalink($pid).">View</a></td>
                        <td>".date('F d, Y h:i A', strtotime($arrival) )."</td>
                        <td>".date('F d, Y h:i A', strtotime($departure) )."</td>
                        <td>".$name."</td>
                        <td>".$email."</td>
                        <td>".$approve."</td>
                        <td>".$number."</td>
                        <td>".$childs."</td>
                        <td>".$curr['symbol'].''.$curr['converted']."</td>
                        <td>".date('F d, Y h:i A', strtotime($reservated) )."</td>
                        <td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/confirmation-approve/?idr=".$idr."&idt=".$idt."&txn=".$txn_id."'>Approve</a></td>
                         <td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/confirmation-disapproved/?idr=".$idr."&idt=".$idt."'>Disapprove</a></td>
                         <td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/members/judan/messages/compose/?unames=".$user_info->user_login."'>SendMessage</a></td>
                      </tr>";
            }
            echo "</tr></table></div>";
            }
            else
            {
                echo "Please login<a href='#' class='simplemodal-login'> here</a>";
            }
    
    }
    
     return;
  
}

function reservation_guest()
{
    if ( !check_prev() )
    {
        echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/list-reservation-host/'>View Host Reservations</a>";
    }
    else
    {
            if ( is_user_logged_in()  )
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
                        a.approve IN('','del')
                    
                    GROUP BY 
                    
                        b.txn_id;
            ");
            
            if( $wpdb->num_rows > 0 )
            {
                echo '<div id="page-wrap">';
                echo '<table class="gridtable"><thead><tr><th>ROOM</th><th>ARRIVAL</th><th>DEPARTURE</th><th>NAME</th><th>EMAIL</th><th>APPROVE</th>
                      <th>ADULTS</th><th>CHILDS</th><th>PRICE</th><th>RESERVATED</th><th colspan=4>ACTION</th></tr></thead>';
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
                $approve = $list->approve == "del" ? "Cancelled" : $list->approve ;
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
        	    
        	    //create link edit if not cancelled yet
        	    $edit_check = $list->approve == "del" ? "----" : "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/reservation-editing-confirmation/?resource_id=".$room."&idr=".$idr."&idt=".$idt."'>Edit</a>";
        	    
        	    //create link cancel if not cancelled yet
        	    $cancel_check = $list->approve == "del" ? "----" : "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/cancel-confirm-reservation/?idr=".$idr."&idt=".$idt."&txn=".$txn_id."'>Cancel</a>";
        	    
        	    //get original post_id from post_meta
        	    foreach($wpdb->get_results("SELECT post_id FROM jd_postmeta WHERE meta_key = 'vh_resource_id' AND meta_value='$room'") as $pids )
        	    {
        	        $pid = $pids->post_id;
        	    }
        	     
        	    $curr = exchangeRate( $mc_gross, $mc_currency , $_COOKIE['C_CURRENCY']);
        	     
                echo "<tr>
                        <td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href=".get_permalink($pid).">View</a></td>
                        <td>".date('F d, Y h:i A', strtotime($arrival) )."</td>
                        <td>".date('F d, Y h:i A', strtotime($departure) )."</td>
                        <td>".$name."</td>
                        <td>".$email."</td>
                        <td>".$approve."</td>
                        <td>".$number."</td>
                        <td>".$childs."</td>
                        <td>".$curr['symbol'].''.$curr['converted']."</td>
                        <td>".date('F d, Y h:i A', strtotime($reservated) )."</td>
                         <td>".$edit_check."</td>
                        <td>".$cancel_check."</td>
                        <td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/members/judan/messages/compose/?unames=".$user_info->user_login."'>SendMessage</a></td>
                      </tr>";
            }
            echo "</tr></table></div>";
            }
            else
            {
               echo "Please login<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='#' class='simplemodal-login'>here</a>";
            }
   }
      return;
   
}

//ask if guest want to edit if yes then void his previous transaction to paypal
function reservation_editing_confirmation()
{
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;//using this id we need to void previous payment and retreive access token from our database 
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $room = isset($_GET['resource_id']) ? $_GET['resource_id'] : "" ;
    
    echo 'Youre about editing your previous transaction. proceed? <br><br>';
    echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/book-now/?resource_id=".$room."&editing=reservation_editing_mode&idr=".$idr."&idt=".$idt."'>Yes</a>  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/reservations-for-guests/'>No</a>";
    
    return;
}

function confirmation_host_disapproved()
{
    
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    
    echo 'Are you sure you want to disapproved this reservation?<br><br>';
    echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/host-disapproved/?idr=".$idr."&idt=".$idt."'>Yes</a> <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/list-reservation-host/'>No</a>";
 
    return ;
}
function hosts_disapproved()
{
    
    global $wpdb;
    
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    
    
    $lists =$wpdb->get_results("SELECT * FROM jd_reservations WHERE id = $idr");

    foreach ($lists as $list) {
        
        if($list->approve == "del") //cancelled already
        {
            echo "Failed to disapprove reservation. reservation might be cancelled.  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/list-reservation-host/'>return</a>";
        }
        else
        {
            echo "Reservation was disapproved <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/list-reservation-host/'>return</a>";
            
            $arr = getpaypalamounts($idt);
            global $clientId,$secret,$adminClientID,$adminSecret;
            $results = void_payment(create_access_token($adminClientID,$adminSecret),$arr['txn']);
            
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
    echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/host-approved/?idr=".$idr."&idt=".$idt."&txn=".$txn."'>Yes</a>  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/list-reservation-host/'>No</a>";
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
            $reason = $list->approve == "del" ? "Cancelled" : "Disapproved"; 
            
            echo "Unable to approve this reservation the reaservation was : <b>".$reason."</b> by the guest. <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/list-reservation-host/'>return</a>";
        }
        else
        {
            //process reservations and paypal
            $arr = getpaypalamounts($idt);

            global $clientId,$secret,$adminClientID,$adminSecret,$txn;
            
            
            $results = update_captures(create_access_token(),$arr['txn'],'{
                "amount": {
                  "currency": "'.$arr['currency'].'",
                  "total": "'.$arr['total'].'"
                },
                "is_final_capture": true
              }');
            
            echo "Reservation was successfully approved <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/list-reservation-host/'>return</a>";
           
            $query=$wpdb->query("UPDATE jd_reservations SET approve='yes' WHERE id=$idr");
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
    echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/cancel-reservation/?idr=".$idr."&idt=".$idt."&txn=".$txn."'>Yes</a>  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/reservations-for-guest/'>No</a>";
 
}
function cancel_reservations()
{
    global $wpdb,$adminClientID,$adminSecret;
    
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    
    
    $lists =$wpdb->get_results("SELECT * FROM jd_reservations WHERE id = $idr");

    foreach ($lists as $list) {
        
        if($list->approve == "del") //cancelled already
        {
            echo "Reservation already cancelled.  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/reservations-for-guest/'>return</a>";
        }
        else
        {
            echo "Reservation was cancelled <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/reservations-for-guest'>return</a>";
            
            $arr = getpaypalamounts($idt);
            global $clientId,$secret;
            $results = void_payment(create_access_token($adminClientID,$adminSecret),$arr['txn']);
            
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
		 
   if ( !check_prev() ){
        echo "Your reservation was successfully reserved.<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/list-reservation-host/'>View Reservations</a>";
    }else{
        echo "Your reservation was successfully reserved. <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url()."/reservations-for-guests'>View Reservations</a>";
    }
    return;
}

function listings_message_confirmation()
{
        $pid = isset($_GET['pid_del'] ) ? $_GET['pid_del'] : "";
        if($_GET['trashed'] == 1){
             echo "Listing was successfully deleted.  <a href='" . site_url() . "/manage-listing/'> return </a>";
        }else{
            echo "Are you sure you want to delete this listing?<br><br>";
            	echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='" . get_delete_post_link( $pid ) . "'>Yes</a> <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='" . site_url() . "/manage-listing/'> No </a>";
        }
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
function res_editing_confirmation($content)
{
    add_shortcode(  'res_editing_confirmation' , 'reservation_editing_confirmation' );
    return $content;
}
function listing_message_confirmation($content)
{
    add_shortcode(  'listing_message_confirmation' , 'listings_message_confirmation' );
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
add_action( 'the_content', 'res_editing_confirmation');
add_action( 'the_content', 'listing_message_confirmation');
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
    global $adminClientID,$adminSecret;
    
    $ch = curl_init();                                                        
	curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_USERPWD, $adminClientID.":".$adminSecret);
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
         global $admin_identity,$sandbox;
         
         $ch = curl_init(); 
         
          curl_setopt_array($ch, array ( CURLOPT_URL => $sandbox,
          CURLOPT_POST => TRUE,
          CURLOPT_POSTFIELDS => http_build_query(array
            (
              'cmd' => '_notify-synch',
              'tx' => $tx,
              'at' => $admin_identity,
            )),
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_HEADER => FALSE,
        ));
        
        ksort($response);
        
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //var_dump($response);
        curl_close($ch);
        
        
        if($status == 200 AND strpos($response, 'SUCCESS') === 0)
        {
            parse_str(str_replace(PHP_EOL,'&',$response),$response);
            
            return $response;
           
        }
}
############set currency#########333
add_action( 'init', 'my_setcookie' );

function my_setcookie() {
    if($_COOKIE['C_CURRENCY']==''){
        setcookie('C_CURRENCY', 'JPY' , time()+3600 * 24 * 365, COOKIEPATH, COOKIE_DOMAIN );
    }
}

function dynamic_convert($postid, $currency_format, $previous_money , $page )
{
   
   $accnt = getoriginalcurrency($postid,$page); 
   
   if($currency_format == $accnt['currency'] )
   {
        $money = $previous_money;
        $sign = $accnt['sign'];
        $currency = $accnt['currency']; 
        $code = $accnt['code'];
        
   }
   else if($_COOKIE['C_CURRENCY']=='')
   {
        $money = $previous_money;
        $sign = $accnt['sign'];
        $currency = $accnt['currency'];
        $code = $accnt['code'];
        
   }else{
        switch ($currency_format) {
            
            case 'USD':
                $money = convertCurrency($previous_money, 'JPY' , "USD");
                $sign = '$';
                $code = '#36';
                $currency = 'USD';
                break;
            
           case 'JPY':
                $money = convertCurrency($previous_money, 'USD' , "JPY");
                $sign = '¥';
                $code = '#165';
                $currency = 'JPY';
                break;
           
        }
   }        
        
    return array('sign' => $sign, 
                'money' => $money, 
                'currency' => $currency, 
                'code' => $code 
            );
}
function getoriginalcurrency($pids,$page)
{

    global $wpdb;
  
            foreach($wpdb->get_results(" SELECT post_id FROM jd_postmeta WHERE meta_key ='vh_resource_id' AND meta_value = $pids") as $ls):
                $get_pids = $ls->post_id;
            endforeach;
 
    foreach($wpdb->get_results(" SELECT meta_value FROM jd_postmeta WHERE meta_key ='jd_cg_currency' AND post_id = $get_pids") as $currencies)
    {
        $currency =  $currencies->meta_value;
    }
    foreach($wpdb->get_results(" SELECT meta_value FROM jd_postmeta WHERE meta_key ='jd_cg_sign' AND post_id = $get_pids") as $signs)
    {
        $sign = $signs->meta_value;
    }
    foreach($wpdb->get_results(" SELECT meta_value FROM jd_postmeta WHERE meta_key ='jd_cg_code' AND post_id = $get_pids") as $codes)
    {
        $code = $codes->meta_value;
    }
    return array('sign' => $sign, 'currency' => $currency, 'code' => $code );
}

//no post id dependent convert by selected currency
function independent_convert($currency_format,$previous_money)
{
   if($currency_format=='')
   {
        $formats = $previous_money;
        $sign = '¥';
        
   }else{
        switch ($currency_format) {
            
            case 'USD':
                $formats = convertCurrency($previous_money, "JPY" ,  strtoupper($currency_format)  );
                $sign = '$';
                break;
            
           case 'JPY':
                $formats = convertCurrency($previous_money, "USD",  strtoupper($currency_format) );
              $sign = '¥';
                break;
           
        }
   }        
        
    return array('sign' => $sign, 'money' => $formats );
}

function convertCurrency($amount, $from, $to)
{
	$data = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$from&to=$to");
	preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);
	$converted = preg_replace("/[^0-9.]/", "", $converted[1]);
	return number_format($converted,0, '', '');
}

function signage($currency_format)
{
    return array_search( $currency_format,array('#36'=>'USD', '#165' => 'JPY') ); //decimal value
}

function add_listing_price_holder()//add listing price place holder geodir_listing_price
{
     	if(!isset($_COOKIE['C_CURRENCY']) || empty($_COOKIE['C_CURRENCY']))//default jpy if empty
     	{
     		 $site_title = "Listing Price (JPY)";
     	}else{
     	     $site_title = "Listing Price (". $_COOKIE['C_CURRENCY'].")";
     	}
     	
     return $site_title;
}
//converted ko
function exchangerate($amount, $from, $to)
{
	
    switch ($from) {
        case "JPY":
            $from_f = "JPY";
			$sign_f = "#165";
			$symbol = '¥';
            break;
        case "USD":
             $from_f = "USD";
			 $sign_f = "#36";
			 $symbol = '$';
            break;
    }

    switch ($to) {
        case "JPY":
            $to_t = "JPY";
			$sign_t = "#165";
			$symbol = '¥';
            break;
        case "USD":
            $to_t  = "USD";
			$sign_t = "#36";
			$symbol = '$';
            break;
    }

  	$data = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$from&to=$to");
	preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);
	$converted = preg_replace("/[^0-9.]/", "", $converted[1]);
	
	return array(
				'converted' => number_format($converted, 0, '.', ''),
				 'currency' => $to_t,
				 'sign' => $sign_t,
				 'symbol' => $symbol
				);
}
//places preview
function preview_symbol()//add listing price preview
{
      switch ($_COOKIE['C_CURRENCY']) {
        case "JPY":
			$symbol = '¥';
            break;
        case "USD":
			$symbol = '$';
            break;
        default:
            $symbol = '¥';
            break;
    }
     	
     return $symbol;
}

//table css
function cascade() 
{

echo "<style>
table.gridtable { font-size: 12px; font-family: 'Helvetica'; }
a.lnk{ text-decoration: none; font-size: 12px; font-family: 'Helvetica'; padding: 8px; }

@media only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {
	/* Force table to not be like tables anymore */

	table.gridtable, .gridtable thead, .gridtable tbody, .gridtable th, .gridtable td, .gridtable tr { 
		display: block; 
	}
	
	/* Hide table headers (but not display: none;, for accessibility) */
	table.gridtable thead tr { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
	
	table.gridtable tr { border: 1px solid #ccc; }
	
	table.gridtable td { 
		/* Behave  like a 'row' */
		border: none;
		border-bottom: 1px solid #eee; 
		position: relative;
		padding-left: 50%; 
	}
	
	table.gridtable td:before { 
		/* Now like a table header */
		position: absolute;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%; 
		padding-right: 10px; 
		white-space: nowrap;
	}
	
	/*
	Label the data
	*/
	table.gridtable td:nth-of-type(1):before { content: 'ROOM'; }
	table.gridtable td:nth-of-type(2):before { content: 'ARRIVAL'; }
	table.gridtable td:nth-of-type(3):before { content: 'DEPARTURE'; }
	table.gridtable td:nth-of-type(4):before { content: 'NAME'; }
	table.gridtable td:nth-of-type(5):before { content: 'EMAIL'; }
	table.gridtable td:nth-of-type(6):before { content: 'COUNTRY'; }
	table.gridtable td:nth-of-type(7):before { content: 'APPROVE'; }
	table.gridtable td:nth-of-type(8):before { content: 'NUMBER'; }
	table.gridtable td:nth-of-type(9):before { content: 'CHILDS'; }
	table.gridtable td:nth-of-type(10):before { content: 'PRICE'; }
	table.gridtable td:nth-of-type(11):before { content: 'RESERVATED'; }
	table.gridtable td:nth-of-type(12):before { content: 'ACTION(S)'; }
}



    </style>";
}


?>