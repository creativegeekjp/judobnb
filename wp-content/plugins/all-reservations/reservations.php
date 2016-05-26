<?php
/*
Plugin Name: All Reservations
Plugin URI: Plugins Url
Description: List all reservations for admin
Version: 1.0
Author: Daryl joyce lopez
Author URI: Author's Website
License:GPL2
*/

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
	        
	       
	    }
	}
	
function check_role(){
    global $wpdb;
    
    $user_ID = get_current_user_id();
     
    $capabilities=$wpdb->get_results("
    SELECT meta_value FROM jd_usermeta WHERE user_id=$user_ID AND meta_key='jd_capabilities'");
    
    $capability=unserialize($capabilities[0]->meta_value);
    
    return $capability;
}

function load_reservation(){
    
    $role=check_role();
    if($role['administrator']){
        load_all_reservations();
    }else{
        echo 'Access Denied';
       
    }
     return;
}


function load_all_reservations(){
    
    global $wpdb;
    
    $reservations = $wpdb->get_results("
    SELECT jd_reservations.*,jd_cg_captured_payments.host_id,jd_cg_captured_payments.tid FROM jd_reservations INNER JOIN jd_cg_captured_payments ON jd_cg_captured_payments.room_id=jd_reservations.id WHERE jd_reservations.approve='yes' AND jd_reservations.paid=0  GROUP BY jd_cg_captured_payments.txn_id;");


    if( $wpdb->num_rows > 0 )
    {
        
        echo '<form name="post" method="post" id="post">';
        echo '<table class="gridtable">
                <thead>
                    <tr>
                        <th>ROOM</th>
                        <th>ARRIVAL</th>
                        <th>DEPARTURE</th>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>COUNTRY</th>
                        <th>ROOMNUMBER</th>
                        <th>NUMBER</th>
                        <th>CHILDS</th>
                        <th>PRICE</th>
                        <th>RESERVATED</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>';
                
           
             
        foreach($reservations as $reserve){
            
             $get_post_ids =$wpdb->get_var("SELECT post_id FROM jd_postmeta WHERE meta_value ='".$reserve->room."'");
            $authors =$wpdb->get_var("SELECT post_author FROM jd_posts WHERE ID ='".$get_post_ids."'");
            
            //get author by roomid for messaging
            $user_info = get_userdata( $authors );
    	    
    	    
    	     //get original post_id from post_meta
    	    foreach($wpdb->get_results("SELECT post_id FROM jd_postmeta WHERE meta_key = 'vh_resource_id' AND meta_value='$reserve->room'") as $pids )
    	    {
    	        $pid = $pids->post_id;
    	    }
	    
                echo "<tr>
                    <td><a href=".get_permalink($pid).">View</a></td>
                    <td>".date('F d, Y h:i A',strtotime($reserve->arrival) )."</td>
                    <td>".date('F d, Y h:i A',strtotime($reserve->departure) )."</td>
                    <td>".$reserve->name."</td>
                    <td>".$reserve->email."</td>
                    <td>".$reserve->country."</td>
                    <td>".$reserve->roomnumber."</td>
                    <td>".$reserve->number."</td>
                    <td>".$reserve->childs."</td>
                    <td>".$reserve->price."</td>
                    <td>".date('F d, Y h:i A', strtotime($reserve->reservated) )."</td>
                    <td><a href='".site_url()."/payment-confirmation/?host=".$reserve->host_id."&tid=".$reserve->tid."&id=".$reserve->id."&status=payout'><button type='button' name='payoutButton' class='btn-success' >PAY HOST</button></a></td>
                </tr>";
        }
        echo "</tbody></table></form>";
             
              return;
    }else{
        echo 'No Reservations Found';
    }
}

//register the js file
function register_reservation_js(){
    
    
    $role=check_role();
    $registered=wp_register_script('all-reservations_js.js',site_url().'/wp-content/plugins/all-reservations/js/all-reservations.js',array( 'jquery' ),true);
    if($registered){
        
        if($role['administrator']){
            wp_enqueue_script('all-reservations_js.js');
            wp_localize_script( 'all-reservations_js.js', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }
            
    }
        
}

function get_departures(){
    
    global $wpdb;
    $today=date('Y-m-d');
    $payReserve=new stdClass();
    $responses=[];
    
    $ctr=0;
    
    $role=check_role();
    
    if($role['administrator']){
        
         $reservations = $wpdb->get_results("
            SELECT jd_reservations.*,jd_cg_captured_payments.host_id,jd_cg_captured_payments.tid FROM jd_reservations INNER JOIN jd_cg_captured_payments ON jd_cg_captured_payments.room_id=jd_reservations.id WHERE jd_reservations.approve='yes' AND jd_reservations.paid=0  GROUP BY jd_cg_captured_payments.txn_id;");


        if( $wpdb->num_rows > 0 )
        {
            $data=[];
            foreach($reservations as $reserve){
                
                $get_post_ids =$wpdb->get_var("SELECT post_id FROM jd_postmeta WHERE meta_value ='".$reserve->room."'");
                $authors =$wpdb->get_var("SELECT post_author FROM jd_posts WHERE ID ='".$get_post_ids."'");
                
                //get author by roomid for messaging
                $user_info = get_userdata( $authors );
        	   
        	    
        	    $host=$authors;
        	    $tid=$reserve->tid;
        	    $id=$reserve->id;
        	    date_default_timezone_set("Asia/Manila");
        	    
        	    
        	      
        	              $time=new DateTime($reserve->departure);
        	              $time->add(new DateInterval("PT24H"));
        	              $endTime=$time->getTimestamp();
        	              $diff=time()-$endTime;
        	              
        	              if($diff > 0 && $diff < 3601){
        	                 
        	                 $arr=getamounts($tid);
        	                 
        	                
            
                            $data=array(
                                "USER"          => "daryljoycepalis-facilitator_api1.ymail.com",
                                "PWD"           => "ZGRMZGJE33BTU8RF",
                                "SIGNATURE"     => "AFcWxV21C7fd0v3bYYYRCpSSRl31AlMbxUVuS39eWS2Q.dHO7D6oXab7",
                                "METHOD"        => "MassPay",
                                "VERSION"       => "99",
                                "RECEIVERTYPE"  =>"EMailAddress",
                                "CURRENCYCODE"  =>$arr['currency'],
                                "L_EMAIL0"      =>"daryljoycepalis-facilitator-1@ymail.com",
                                "L_AMT0"        =>'30.00'
                                );
                            
                              $result=call_pay_api($data);
        	              
        	                   if(isset($result) && $result=='ACK=Success'){
                        
                                     $query=$wpdb->query("UPDATE jd_reservations SET paid=1 WHERE id=$id");
                                    
                                };
                                
                                $data=array(
                                    'res_id'=>$id,
                                    'result' => $result
                                    );
                             
        	                  array_push($responses,$data);
        	              }else{
        	                  $ctr++;
        	                 
        	              }
        	        
            }
            if(sizeof($reservations) == $ctr && sizeof($responses) == 0){
                $data=array(
                    'res_id' => 0,
                    'result' => 'false'
                    );
                 array_push($responses,$data);
            }
                
                
            
        
            foreach($responses as $v){
             
             
             if($v['res_id'] != 0)
                echo '<span id="curl_responses">reservation:'.$v['res_id'].'='.$v['result'].'</span>';
            else
                echo '<span id="curl_responses">reservation:'.$v['result'].'</span>';
            }
                
           
        
    }else{
         echo '<span id="curl_responses">reservation:No Hosts to payout</span>';
    }
    
   
    
}else{
        echo 'Access denied';
    }

}
function confirm_payout(){
    global $wpdb,$clientId,$secret;
    
    if(isset($_GET['status']) && $_GET['status']=='payout'){
        $host = isset($_GET['host']) ? $_GET['host'] : "" ;
        $res_id = isset($_GET['id']) ? $_GET['id'] : "" ;
        $tid = isset($_GET['tid']) ? $_GET['tid'] : "" ;
    
    
        $hostname=$wpdb->get_results("SELECT user_login FROM jd_users WHERE id=$host LIMIT 1");
        $hostname=$hostname[0]->user_login;
        
        
       // checkPaymentMessage(false,$hostname);
        $arr=getamounts($tid);
        
        $data=array(
            "USER"          => "daryljoycepalis-facilitator_api1.ymail.com",
            "PWD"           => "ZGRMZGJE33BTU8RF",
            "SIGNATURE"     => "AFcWxV21C7fd0v3bYYYRCpSSRl31AlMbxUVuS39eWS2Q.dHO7D6oXab7",
            "METHOD"        => "MassPay",
            "VERSION"       => "99",
            "RECEIVERTYPE"  =>"EMailAddress",
            "CURRENCYCODE"  =>$arr['currency'],
            "L_EMAIL0"      =>"daryljoycepalis-facilitator-1@ymail.com",
            "L_AMT0"        =>'30.00'
            );
        
          $result=call_pay_api($data);
          

                if(isset($result) && $result=='ACK=Success'){
                    
                     $query=$wpdb->query("UPDATE jd_reservations SET paid=1 WHERE id=$res_id");
                    checkPaymentMessage(true,$hostname,$arr['total']);
                    
                }else{
                    checkPaymentMessage(false,$hostname);
                }
                
                
               
        
    }

    
}

function call_pay_api($data){
        $curl = curl_init('https://api-3t.sandbox.paypal.com/nvp');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); 
        
        
       
        $response = curl_exec( $curl );
        
        $res=explode('&',$response);
        return $res[2];
}
    

function checkPaymentMessage($stat,$hostName,$total){
    if($stat)
        echo 'Transaction Complete.Payment sent to '.$hostName.'. Total:'.$total;
    else
        echo 'Transaction Failed.Payment not sent to '.$hostName.'.';
        
        echo '<br/><a href="'.site_url().'/all-reservations">Return</a>';
    return;
}

function create_access_token_for_host($id,$pass)
{
   
    
    $ch = curl_init();                                                        
	curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_USERPWD, $id.":".$pass);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

	$result = curl_exec($ch);

	
	if(empty($result))die("Error: No response.");

	else
	{
	    $json = json_decode($result);
	    
		$access_tokens = $json->access_token;
	}

	return $access_tokens;

}


function getamounts($idt)
{
     global $wpdb;

    $lists =$wpdb->get_results("
       SELECT * FROM `jd_cg_captured_payments` WHERE tid = $idt
    ");
    
     foreach ($lists as $list) {
         
         return array(
         'currency' => $list->mc_currency,
         'total' => $list->mc_gross
        );
     }
}


function allreservations($content)
{
    add_shortcode(  'allreservations' , 'load_reservation' );
    return $content;
}

function departures($content)
{
    add_shortcode(  'departures' , 'get_departures' );
    return $content;
}

function confirmPayout($content){
    
    add_shortcode('confirmPayout','confirm_payout');
    return $content;
}

function pay($token,$data){
        
    
        $curl = curl_init('https://api.sandbox.paypal.com/v1/payments/payment');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        			'Authorization: Bearer '.$token,
        			'Accept: application/json',
        			'Content-Type: application/json'
        			));
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
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
        
        $jsonResponse = json_decode($response);
        foreach($jsonResponse->links as $w){
                    if($w->rel=='approval_url')
                        $approval_url=$w->href;
                }
               
        return $approval_url;
            
        
}

add_action( 'the_content', 'allreservations');
add_action( 'the_content', 'confirmPayout');
add_action( 'the_content', 'departures');