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

function langs()
{
    switch (ICL_LANGUAGE_CODE) {
        case 'ja':
              $ext = "/ja";
            break;
        
         case 'en':
              $ext = "";
            break;
        
        
        default:
             $ext = "";
            break;
    }
    
    return $ext;
}
function reservation_host()
{  
  
    if ( check_prev() )
    {
        echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/reservations-for-guests/'>".__('View Guest Reservations','easyReservations')."</a>";
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
                echo '<table class="gridtable">
                        <thead>
                            <tr>
                                <th width="7%">'.__('ROOM','easyReservations').'</th>
                                <th>'.__('ARRIVAL','easyReservations').'</th>
                                <th>'.__('DEPARTURE','easyReservations').'</th>
                                <th>'.__('NAME','easyReservations').'</th>
                                <th>'.__('EMAIL','easyReservations').'</th>
                                <th>'.__('APPROVE','easyReservations').'</th>
                                <th width="7%">'.__('ADULTS','easyReservations').'</th>
                                <th width="7%">'.__('CHILDS','easyReservations').'</th>
                                <th>'.__('PRICE','easyReservations').'</th>
                                <th>'.__('RESERVATED','easyReservations').'</th>
                                <th>'.__('ACTION','easyReservations').'</th>
                            </tr>
                        </thead>';
            }
            else
            {
                echo _e("You haven't received any reservations yet","easyReservations");
            }
            
                
            foreach ($lists as $list) {
                $x="";
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
        	    
        	    $lnks = $approve != "Cancelled" ? " <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/confirmation-approve/?idr=".$idr."&idt=".$idt."&txn=".$txn_id."'>".__('Approve','easyReservations')."</a>
                        
                                                    <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/confirmation-disapproved/?idr=".$idr."&idt=".$idt."'>".__('Disapprove','easyReservations')."</a>" : "" ;
                                                    
                                                    
                 //get current usernicename
        	     global $current_user;
     
                get_currentuserinfo();
                
                
                //if($current_user->user_nicename===$user_info->user_login)
                if($user === $authors){
                    $x="disabled";
                    $xclass="";
                }else{
                    $xclass="wpb_btn-primary";
                }
                
                
                echo "<tr>
                 <td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small'  href=".get_permalink($pid).">".__('View','easyReservations')."</a></td>
                        <td>".date('F d, Y h:i A', strtotime($arrival) )."</td>
                        <td>".date('F d, Y h:i A', strtotime($departure) )."</td>
                        <td>".$name."</td>
                        <td>".$email."</td>
                        <td>".$approve."</td>
                        <td>".$number."</td>
                        <td>".$childs."</td>
                        <td>".$curr['symbol'].''.$curr['converted']."</td>
                        <td>".date('F d, Y h:i A', strtotime($reservated) )."</td>
                        <td>
                        
                         $lnks 
                         <a style='color: #fff;font-weight: 300;font-size: 18px;text-decoration:none' href='".site_url().''.langs()."/members/".$current_user->user_nicename."/messages/compose/?unames=".$name."'><button  ".$x." class='lnk wpb_button ".$xclass." wpb_btn-small' >".__('Send Message','easyReservations')."</button></a></td>
                      </tr>";
            }
            echo "</tr></table></div>";
            }
            else
            {
                echo _e("Please login<a href='#' class='simplemodal-login'> here</a>","easyReservations");
            }
    
    }
    
     return;
  
}

function reservation_guest()
{
   
    if ( !check_prev() )
    {
        echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/list-reservation-host/'>".__('View Host Reservations','easyReservations')."</a>";
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
                echo '<table class="gridtable">
                        <thead>
                            <tr>
                                <th width="7%">'.__('ROOM','easyReservations').'</th>
                                <th>'.__('ARRIVAL','easyReservations').'</th>
                                <th>'.__('DEPARTURE','easyReservations').'</th>
                                <th>'.__('NAME','easyReservations').'</th>
                                <th>'.__('EMAIL','easyReservations').'</th>
                                <th>'.__('APPROVE','easyReservations').'</th>
                                <th width="7%">'.__('ADULTS','easyReservations').'</th>
                                <th width="7%">'.__('CHILDS','easyReservations').'</th>
                                <th>'.__('PRICE','easyReservations').'</th>
                                <th>'.__('RESERVATED','easyReservations').'</th>
                                <th colspan=4>'.__('ACTION','easyReservations').'</th>
                            </tr>
                        </thead>';
            }
            else
            {
                echo _e('You haven\'t  done any reservations yet','easyReservations');
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
        	    $edit_check = $list->approve == "del" ? "----" : "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/reservation-editing-confirmation/?resource_id=".$room."&idr=".$idr."&idt=".$idt."'>".__('Edit','easyReservations')."</a>";
        	    
        	    //create link cancel if not cancelled yet
        	    $cancel_check = $list->approve == "del" ? "----" : "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/cancel-confirm-reservation/?idr=".$idr."&idt=".$idt."&txn=".$txn_id."'>".__('Cancel','easyReservations')."</a>";
        	    
        	    //get original post_id from post_meta
        	    foreach($wpdb->get_results("SELECT post_id FROM jd_postmeta WHERE meta_key = 'vh_resource_id' AND meta_value='$room'") as $pids )
        	    {
        	        $pid = $pids->post_id;
        	    }
        	     
        	    $curr = exchangeRate( $mc_gross, $mc_currency , $_COOKIE['C_CURRENCY']);
        	     
        	     //get current usernicename
        	     global $current_user;
               
                get_currentuserinfo();
                
                 if($user === $authors){
                    $x="disabled";
                    $xclass="";
                }else{
                    $xclass="wpb_btn-primary";
                }
                
                echo "<tr>
                        <td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href=".get_permalink($pid).">".__('View','easyReservations')."</a></td>
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
                       <td> <a style='color: #fff;font-weight: 300;font-size: 18px;text-decoration:none' href='".site_url().''.langs()."/members/".$current_user->user_nicename."/messages/compose/?unames=".$user_info->user_login."'><button  ".$x." class='lnk wpb_button ".$xclass." wpb_btn-small' >".__('Send Message','easyReservations')."</button></a></td>
                      </tr>";
            }
            echo "</tr></table></div>";
            }
            else
            {
               echo _e("Please login<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='#' class='simplemodal-login'>here</a>","easyReservations");
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
    
    echo '<b>Note:</b> You are about editing your previous transaction, any unfinished transaction will be permanently removed. click yes to proceed on editing or no to return list reservation. <br><br>';
    echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/book-now/?resource_id=".$room."&editing=reservation_editing_mode&idr=".$idr."&idt=".$idt."'>Yes</a>  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/reservations-for-guests/'>No</a>";
    
    return;
}

function confirmation_host_disapproved()
{
    
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    
    echo _e('Are you sure you want to disapproved this reservation?<br><br>','easyReservations');
    echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/host-disapproved/?idr=".$idr."&idt=".$idt."'>".__('Yes','easyReservations')."</a> <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/list-reservation-host/'>".__('No','easyReservations')."</a>";
 
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
            echo "Failed to disapprove reservation. reservation might be cancelled.  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/list-reservation-host/'>return</a>";
        }
        else
        {
            echo __('Reservation was disapproved','easyReservations')." <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/list-reservation-host/'>".__('return','easyReservations')."</a>";
            
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
            
            //daryl updates for email 6/6
            $post_title=$wpdb->get_row("SELECT jd_posts.post_title FROM jd_reservations INNER JOIN jd_postmeta ON jd_postmeta.meta_value=jd_reservations.room INNER JOIN jd_posts ON jd_posts.ID=jd_postmeta.post_id WHERE jd_reservations.id=$list->id");
                $guest_email=$wpdb->get_row("SELECT * FROM jd_users WHERE ID=$list->user");
                
            $from_name="JudoBNB";
		    $from_email="info@judobnb.creativegeek.jp";
		    
		    $headers  = "MIME-Version: 1.0 \n" ;
            $headers .= "From: " .
                   "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
                   "<".$from_email."> \n";
            $headers .= "Reply-To: " .
                   "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
                   "<".$from_email."> \n";
            $headers .= 'Content-type: text/html; charset=ISO-2022-JP' . "\r\n";
                   
                   
                $guest_name=$guest_email->display_name;
                $email_to=$guest_email->user_email;
                $query="SELECT value FROM `jd_bp_xprofile_data` WHERE field_id='635' AND user_id=".$list->user."";
                    //echo $query
                    $email_lang_diss=$wpdb->get_row($query);
                
                if($email_lang_diss->value == 'English'){
                     $body = file_get_contents('wp-includes/custom-emails/host-disapproved.html');
                      $subject = mb_convert_encoding("JudoBnB Disapproval Email", "ISO-2022-JP","AUTO");
                }
                if($email_lang_diss->value == 'Japanese'){
                     $body = file_get_contents('wp-includes/custom-emails/host-disapproved-ja.html');
                      $subject = mb_convert_encoding("JudoBnB不承認メール", "ISO-2022-JP","AUTO");
                }
               
                $message = str_ireplace('[guest_display_name]',$guest_name, $body);
                $message = str_ireplace('[post_title]',$post_title->post_title, $message);
                
                $email_body = mb_convert_encoding($message, "ISO-2022-JP","AUTO");
                mb_language("ja");
               
                $subject = mb_encode_mimeheader($subject);
                
                $stat=wp_mail($email_to,$subject, $email_body,$headers);
                
            //
        }
    }
    
    
    
    return ;
}

function confirmation_host_approved()
{
    $idr = isset($_GET['idr']) ? $_GET['idr'] : "" ;
    $idt = isset($_GET['idt']) ? $_GET['idt'] : "" ;
    $txn = isset($_GET['txn']) ? $_GET['txn'] : "" ;
    
    echo __('Are you sure you want to approved this reservation?','easyReservations')."<br><br>";
    echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/host-approved/?idr=".$idr."&idt=".$idt."&txn=".$txn."'>".__('Yes','easyReservations')."</a>  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/list-reservation-host/'>".__('No','easyReservations')."</a>";
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
            
            echo "Unable to approve this reservation the reaservation was : <b>".$reason."</b> by the guest. <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/list-reservation-host/'>return</a>";
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
             
              
              //daryl update for email 6/6
                    $post_title=$wpdb->get_row("SELECT jd_posts.post_title,jd_reservations.user FROM jd_reservations INNER JOIN jd_postmeta ON jd_postmeta.meta_value=jd_reservations.room INNER JOIN jd_posts ON jd_posts.ID=jd_postmeta.post_id WHERE jd_reservations.id=$idr");
                    $guest_email=$wpdb->get_row("SELECT * FROM jd_users WHERE ID=$post_title->user");
                    
                    $from_name="JudoBNB";
        		    $from_email="info@judobnb.creativegeek.jp";
        		    
        		    $headers  = "MIME-Version: 1.0 \n" ;
                    $headers .= "From: " .
                           "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
                           "<".$from_email."> \n";
                    $headers .= "Reply-To: " .
                           "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
                           "<".$from_email."> \n";
                    $headers .= 'Content-type: text/html; charset=ISO-2022-JP' . "\r\n";
            
                    $guest_name=$guest_email->display_name;
                   $email_to=$guest_email->user_email;
                    //$query="SELECT * FROM jd_cg_email_language WHERE email='".$email_to."'";
                    $query="SELECT value FROM `jd_bp_xprofile_data` WHERE field_id='635' AND user_id=".$post_title->user."";
                    //echo $query
                    $email_lang=$wpdb->get_row($query);
                    
                    if($email_lang->value == 'English'){
                        
                         $body = file_get_contents('wp-includes/custom-emails/host-approved.html');
                         $subject = mb_convert_encoding("JudoBnB Approval Email", "ISO-2022-JP","AUTO");
                         
                    }
                    if($email_lang->value == 'Japanese'){
                          $body = file_get_contents('wp-includes/custom-emails/host-approved-ja.html');
                          $subject = mb_convert_encoding("JudoBnB承認メール", "ISO-2022-JP","AUTO");
                          
                    }
                    
                       
                    $message = str_ireplace('[guest_display_name]',$guest_name, $body);
                    $message = str_ireplace('[post_title]',$post_title->post_title, $message);
                    
                    $email_body = mb_convert_encoding($message, "ISO-2022-JP","AUTO");
                    mb_language("ja");
                    
                    $subject = mb_encode_mimeheader($subject);
                
                    
                    $stat=wp_mail($email_to,$subject,$email_body,$headers);
                    
               
                    
              //end
            
            echo __('Reservation was successfully approved','easyReservations')." <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/list-reservation-host/'>".__('return','easyReservations')."</a>";
           
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
 
    echo _e('Are you sure you want to cancel this reservation?<br><br>','easyReservations');
    echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/cancel-reservation/?idr=".$idr."&idt=".$idt."&txn=".$txn."'>".__('Yes','easyReservations')."</a>  <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/reservations-for-guest/'>".__('No','easyReservations')."</a>";
 
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
            echo __('Reservation already cancelled.','easyReservations')." <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/reservations-for-guest/'>".__('return','easyReservations')."</a>";
        }
        else
        {
            echo __("Reservation was cancelled","easyReservations")." <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/reservations-for-guest'>".__('return','easyReservations')."</a>";
            
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
			  
        //daryl updates for email 06/06
        
    		$author_id=$wpdb->get_row("SELECT jd_posts.post_author,jd_posts.post_title FROM jd_reservations INNER JOIN jd_postmeta ON jd_postmeta.meta_value=jd_reservations.room INNER JOIN jd_posts ON jd_posts.ID=jd_postmeta.post_id WHERE jd_reservations.id=$room_id");
    		$author=$author_id->post_author;
    		$post_title=$author_id->post_title;
    		$guest_email=$wpdb->get_row("SELECT user_email,display_name FROM jd_users WHERE ID=$host_id"); 
    		$host_email=$wpdb->get_row("SELECT user_email,display_name FROM jd_users WHERE ID=$author");
    		  
		    $from_name="JudoBNB";
		    $from_email="info@judobnb.creativegeek.jp";
		    
		    $headers  = "MIME-Version: 1.0 \n" ;
            $headers .= "From: " .
                   "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
                   "<".$from_email."> \n";
            $headers .= "Reply-To: " .
                   "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
                   "<".$from_email."> \n";
            
                
            $headers .= "Content-Type: text/plain;charset=ISO-2022-JP \n";
		
		    $guestemail_to = $guest_email->user_email;
		   $query_guest="SELECT value FROM `jd_bp_xprofile_data` WHERE field_id='635' AND user_id=".$host_id."";
		    $guest_language=$wpdb->get_row($query_guest);
            if($guest_language->value == 'English'){
                $guestemail_body = file_get_contents('wp-includes/custom-emails/guest-reservation.html');
                $guest_subject = mb_convert_encoding("JudoBnB Reservation Email", "ISO-2022-JP","AUTO");
            }
            if($guest_language->value == 'Japanese'){
                $guestemail_body = file_get_contents('wp-includes/custom-emails/guest-reservation-ja.html');
                $guest_subject = mb_convert_encoding("JudoBnB予約メール", "ISO-2022-JP","AUTO");
            }
            $guest_email_message = str_ireplace('[guest_display_name]',$guest_email->display_name, $guestemail_body);
            $guest_email_message = str_ireplace('[post_title]',$author_id->post_title, $guest_email_message);
            
            $guest_body = mb_convert_encoding($guest_email_message, "ISO-2022-JP","AUTO");
            mb_language("ja");
            
            $guest_subject = mb_encode_mimeheader($guest_subject);
            
            
            $hostemail_to=$host_email->user_email;
           $query_host="SELECT value FROM `jd_bp_xprofile_data` WHERE field_id='635' AND user_id=".$author."";
		    $host_language=$wpdb->get_row($query_host);
            if($host_language->value == 'English'){
                $hostemail_body= file_get_contents('wp-includes/custom-emails/host-reservation.html');
                $hostemail_subject = mb_convert_encoding("JudoBnB Reservation Email", "ISO-2022-JP","AUTO");
            }
            if($host_language->value == 'Japanese'){
                 $hostemail_body= file_get_contents('wp-includes/custom-emails/host-reservation-ja.html');
                 $hostemail_subject = mb_convert_encoding("JudoBnB予約メール", "ISO-2022-JP","AUTO");
            }
            $host_email_message = str_ireplace('[guest_display_name]',$guest_email->display_name, $hostemail_body);
            $host_email_message = str_ireplace('[post_title]',$author_id->post_title, $host_email_message);
            $host_email_message=str_ireplace('[host_display_name]',$host_email->display_name, $host_email_message);
            
            $host_body = mb_convert_encoding($host_email_message, "ISO-2022-JP","AUTO");
            
            $guest_status = wp_mail($guestemail_to, $guest_subject, $guest_body,$headers);
            $host_status = wp_mail($hostemail_to, $hostemail_subject, $host_body,$headers);
            
           
        //end

   if ( !check_prev() ){
        echo __('Your reservation was successfully reserved.','easyReservations')."<br><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/list-reservation-host/'>".__('View Reservations','easyReservations')."</a>";
    }else{
        echo __('Your reservation was successfully reserved.','easyReservations')."<br><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/reservations-for-guests'>".__('View Reservations','easyReservations')."</a>";
    }
    return;
}

function listings_message_confirmation()
{
    global $post;
        $pid = isset($_GET['pid_del'] ) ? $_GET['pid_del'] : "";
        if($_GET['trashed'] == 1){
             echo __("Listing was successfully deleted.","easyReservations"). "<a href='" .site_url().''.langs(). "/manage-listing/'>".__('return','easyReservations')."</a>";
        }else{
            echo __("Are you sure you want to delete this listing?<br><br>","easyReservations");
            	echo "<a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='" . get_delete_post_link( $pid ) . "'>".__('Yes','easyReservations')."</a> <a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='" .site_url().''.langs(). "/manage-listing/'>".__('No','easyReservations')."</a>";
        }
    return;
}

function listings_list()
{
    global $wpdb;
    
    $id = get_current_user_id();
    
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    
    $limit = 20; 
    $offset = ( $pagenum - 1 ) * $limit;
    $total = $wpdb->get_var( "SELECT COUNT(`ID`) FROM `jd_posts` WHERE post_status ='publish' AND post_type='gd_place'  " );
    $num_of_pages = ceil( $total / $limit );

    $myrows = $wpdb->get_results( "SELECT * FROM `jd_posts` WHERE post_author = '$id' AND post_status ='publish' AND post_type='gd_place' ORDER BY ID DESC LIMIT $offset, $limit" );

    if ($myrows) 
    {
         echo '<table class="gridtable">
                        <thead>
                            <tr>
                                <th width="15%">'.__('TITLE','easyReservations').'</th>
                                <th  width="80%">'.__('DESCRIPTION','easyReservations').'</th>
                                <th colspan=4>'.__('ACTION','easyReservations').'</th>
                            </tr>
                        </thead>';
                        
        foreach ($myrows as $value) {
            $pid = $value->ID;
            $pid_del = $value->ID;
            $title = $value->post_title;
            $content = $value->post_content;
            
            echo '<tr>';
            echo "<td>".$title."</td>";
            echo "<td>".$content."</td>";
            echo "<td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".get_permalink($pid)."'>".__('View','easyReservations')."</a></td>";
            echo "<td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/add-listing/?listing_type=gd_place&pid=".$pid."'>".__('Edit','easyReservations')."</a></td>";
            echo "<td><a class='lnk wpb_button wpb_btn-primary wpb_btn-small' href='".site_url().''.langs()."/delete-listing-confirmation/?pid_del=".$pid."'>".__('Delete','easyReservations')."</a></td>";
            echo '</tr>';
            
        }
        echo '</table>';
        
        $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ) );
        if ( $page_links ) {
            echo '<div class="tablenav"> <div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div> </div>';
        }
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

function listings($content)
{
    add_shortcode(  'listings' , 'listings_list' );
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
add_action( 'the_content', 'listings');
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

add_action( 'init', 'my_initial' );

function my_initial() {
    if($_COOKIE['C_CURRENCY']==''){
        setcookie('C_CURRENCY', 'JPY' , time()+3600 * 24 * 365, COOKIEPATH, COOKIE_DOMAIN );
    }

    
    setcookie('LANG', ICL_LANGUAGE_CODE , time()+3600 * 24 * 365, COOKIEPATH, COOKIE_DOMAIN );
    
}
/*
function get_emails($user)
{

   global $wpdb;
   
     if (is_user_logged_in()  )
     {
         foreach($wpdb->get_results("SELECT value FROM `jd_bp_xprofile_data` WHERE field_id='330' AND user_id = $user") as $res1)
         {
             $emails = $res1->value;//email
         }
        
         foreach($wpdb->get_results("SELECT value FROM `jd_bp_xprofile_data` WHERE field_id='635' AND user_id =  $user") as $res2)
         {
             $langs = $res2->value;//language
         }
       	
        return array(
            'language' => $langs,
            'email' => $emails
        );
    
     }
 }
*/
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

    if($from == $to){
        $converted = $amount;
    }else{
        	$data = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$from&to=$to");
        	preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);
        	$converted = preg_replace("/[^0-9.]/", "", $converted[1]);
    }
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
	
	table.gridtable tr { 
	    border: 1px solid #ccc; 
	    margin-bottom: 5px;
	}
	
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
	table.gridtable td:nth-of-type(1):before { content: '".__('ROOM','easyReservations')."'; }
	table.gridtable td:nth-of-type(2):before { content: '".__('ARRIVAL','easyReservations')."'; }
	table.gridtable td:nth-of-type(3):before { content: '".__('DEPARTURE','easyReservations')."'; }
	table.gridtable td:nth-of-type(4):before { content: '".__('NAME','easyReservations')."'; }
	table.gridtable td:nth-of-type(5):before { content: '".__('EMAIL','easyReservations')."'; }
	table.gridtable td:nth-of-type(6):before { content: '".__('APPROVE','easyReservations')."'; }
	table.gridtable td:nth-of-type(7):before { content: '".__('ADULTS','easyReservations')."'; }
	table.gridtable td:nth-of-type(8):before { content: '".__('CHILDS','easyReservations')."'; }
	table.gridtable td:nth-of-type(9):before { content: '".__('PRICE','easyReservations')."'; }
	table.gridtable td:nth-of-type(10):before { content: '".__('RESERVATED','easyReservations')."'; }
	table.gridtable td:nth-of-type(11):before { content: '".__('ACTION(S)','easyReservations')."'; }
	
	/*
	Label the data in all reservations
	*/
	
	.page-id-2806 table.gridtable td:nth-of-type(1):before { content: 'ROOM'; }
	.page-id-2806 table.gridtable td:nth-of-type(2):before { content: 'ARRIVAL'; }
    .page-id-2806 table.gridtable td:nth-of-type(3):before { content: 'DEPARTURE'; }
	.page-id-2806 table.gridtable td:nth-of-type(4):before { content: 'NAME'; }
	.page-id-2806 table.gridtable td:nth-of-type(5):before { content: 'EMAIL'; }
	.page-id-2806 table.gridtable td:nth-of-type(6):before { content: 'COUNTRY'; }
	.page-id-2806 table.gridtable td:nth-of-type(7):before { content: 'ROOMNUMBER'; }
	.page-id-2806 table.gridtable td:nth-of-type(8):before { content: 'NUMBER'; }
	.page-id-2806 table.gridtable td:nth-of-type(9):before { content: 'CHILDS'; }
	.page-id-2806 table.gridtable td:nth-of-type(10):before { content: 'PRICE'; }
	.page-id-2806 table.gridtable td:nth-of-type(11):before { content: 'RESERVATED'; }
	.page-id-2806 table.gridtable td:nth-of-type(12):before { content: 'ACTION(S)'; }
	
}



    </style>";
}


/*country codes*/

function country_codes($value)
{
            $countrycodes = array (
          'AF' => 'Afghanistan',
          'AX' => 'Åland Islands',
          'AL' => 'Albania',
          'DZ' => 'Algeria',
          'AS' => 'American Samoa',
          'AD' => 'Andorra',
          'AO' => 'Angola',
          'AI' => 'Anguilla',
          'AQ' => 'Antarctica',
          'AG' => 'Antigua and Barbuda',
          'AR' => 'Argentina',
          'AU' => 'Australia',
          'AT' => 'Austria',
          'AZ' => 'Azerbaijan',
          'BS' => 'Bahamas',
          'BH' => 'Bahrain',
          'BD' => 'Bangladesh',
          'BB' => 'Barbados',
          'BY' => 'Belarus',
          'BE' => 'Belgium',
          'BZ' => 'Belize',
          'BJ' => 'Benin',
          'BM' => 'Bermuda',
          'BT' => 'Bhutan',
          'BO' => 'Bolivia',
          'BA' => 'Bosnia and Herzegovina',
          'BW' => 'Botswana',
          'BV' => 'Bouvet Island',
          'BR' => 'Brazil',
          'IO' => 'British Indian Ocean Territory',
          'BN' => 'Brunei Darussalam',
          'BG' => 'Bulgaria',
          'BF' => 'Burkina Faso',
          'BI' => 'Burundi',
          'KH' => 'Cambodia',
          'CM' => 'Cameroon',
          'CA' => 'Canada',
          'CV' => 'Cape Verde',
          'KY' => 'Cayman Islands',
          'CF' => 'Central African Republic',
          'TD' => 'Chad',
          'CL' => 'Chile',
          'CN' => 'China',
          'CX' => 'Christmas Island',
          'CC' => 'Cocos (Keeling) Islands',
          'CO' => 'Colombia',
          'KM' => 'Comoros',
          'CG' => 'Congo',
          'CD' => 'Zaire',
          'CK' => 'Cook Islands',
          'CR' => 'Costa Rica',
          'CI' => 'Côte D\'Ivoire',
          'HR' => 'Croatia',
          'CU' => 'Cuba',
          'CY' => 'Cyprus',
          'CZ' => 'Czech Republic',
          'DK' => 'Denmark',
          'DJ' => 'Djibouti',
          'DM' => 'Dominica',
          'DO' => 'Dominican Republic',
          'EC' => 'Ecuador',
          'EG' => 'Egypt',
          'SV' => 'El Salvador',
          'GQ' => 'Equatorial Guinea',
          'ER' => 'Eritrea',
          'EE' => 'Estonia',
          'ET' => 'Ethiopia',
          'FK' => 'Falkland Islands (Malvinas)',
          'FO' => 'Faroe Islands',
          'FJ' => 'Fiji',
          'FI' => 'Finland',
          'FR' => 'France',
          'GF' => 'French Guiana',
          'PF' => 'French Polynesia',
          'TF' => 'French Southern Territories',
          'GA' => 'Gabon',
          'GM' => 'Gambia',
          'GE' => 'Georgia',
          'DE' => 'Germany',
          'GH' => 'Ghana',
          'GI' => 'Gibraltar',
          'GR' => 'Greece',
          'GL' => 'Greenland',
          'GD' => 'Grenada',
          'GP' => 'Guadeloupe',
          'GU' => 'Guam',
          'GT' => 'Guatemala',
          'GG' => 'Guernsey',
          'GN' => 'Guinea',
          'GW' => 'Guinea-Bissau',
          'GY' => 'Guyana',
          'HT' => 'Haiti',
          'HM' => 'Heard Island and Mcdonald Islands',
          'VA' => 'Vatican City State',
          'HN' => 'Honduras',
          'HK' => 'Hong Kong',
          'HU' => 'Hungary',
          'IS' => 'Iceland',
          'IN' => 'India',
          'ID' => 'Indonesia',
          'IR' => 'Iran, Islamic Republic of',
          'IQ' => 'Iraq',
          'IE' => 'Ireland',
          'IM' => 'Isle of Man',
          'IL' => 'Israel',
          'IT' => 'Italy',
          'JM' => 'Jamaica',
          'JP' => 'Japan',
          'JE' => 'Jersey',
          'JO' => 'Jordan',
          'KZ' => 'Kazakhstan',
          'KE' => 'KENYA',
          'KI' => 'Kiribati',
          'KP' => 'Korea, Democratic People\'s Republic of',
          'KR' => 'Korea, Republic of',
          'KW' => 'Kuwait',
          'KG' => 'Kyrgyzstan',
          'LA' => 'Lao People\'s Democratic Republic',
          'LV' => 'Latvia',
          'LB' => 'Lebanon',
          'LS' => 'Lesotho',
          'LR' => 'Liberia',
          'LY' => 'Libyan Arab Jamahiriya',
          'LI' => 'Liechtenstein',
          'LT' => 'Lithuania',
          'LU' => 'Luxembourg',
          'MO' => 'Macao',
          'MK' => 'Macedonia, the Former Yugoslav Republic of',
          'MG' => 'Madagascar',
          'MW' => 'Malawi',
          'MY' => 'Malaysia',
          'MV' => 'Maldives',
          'ML' => 'Mali',
          'MT' => 'Malta',
          'MH' => 'Marshall Islands',
          'MQ' => 'Martinique',
          'MR' => 'Mauritania',
          'MU' => 'Mauritius',
          'YT' => 'Mayotte',
          'MX' => 'Mexico',
          'FM' => 'Micronesia, Federated States of',
          'MD' => 'Moldova, Republic of',
          'MC' => 'Monaco',
          'MN' => 'Mongolia',
          'ME' => 'Montenegro',
          'MS' => 'Montserrat',
          'MA' => 'Morocco',
          'MZ' => 'Mozambique',
          'MM' => 'Myanmar',
          'NA' => 'Namibia',
          'NR' => 'Nauru',
          'NP' => 'Nepal',
          'NL' => 'Netherlands',
          'AN' => 'Netherlands Antilles',
          'NC' => 'New Caledonia',
          'NZ' => 'New Zealand',
          'NI' => 'Nicaragua',
          'NE' => 'Niger',
          'NG' => 'Nigeria',
          'NU' => 'Niue',
          'NF' => 'Norfolk Island',
          'MP' => 'Northern Mariana Islands',
          'NO' => 'Norway',
          'OM' => 'Oman',
          'PK' => 'Pakistan',
          'PW' => 'Palau',
          'PS' => 'Palestinian Territory, Occupied',
          'PA' => 'Panama',
          'PG' => 'Papua New Guinea',
          'PY' => 'Paraguay',
          'PE' => 'Peru',
          'PH' => 'Philippines',
          'PN' => 'Pitcairn',
          'PL' => 'Poland',
          'PT' => 'Portugal',
          'PR' => 'Puerto Rico',
          'QA' => 'Qatar',
          'RE' => 'Réunion',
          'RO' => 'Romania',
          'RU' => 'Russian Federation',
          'RW' => 'Rwanda',
          'SH' => 'Saint Helena',
          'KN' => 'Saint Kitts and Nevis',
          'LC' => 'Saint Lucia',
          'PM' => 'Saint Pierre and Miquelon',
          'VC' => 'Saint Vincent and the Grenadines',
          'WS' => 'Samoa',
          'SM' => 'San Marino',
          'ST' => 'Sao Tome and Principe',
          'SA' => 'Saudi Arabia',
          'SN' => 'Senegal',
          'RS' => 'Serbia',
          'SC' => 'Seychelles',
          'SL' => 'Sierra Leone',
          'SG' => 'Singapore',
          'SK' => 'Slovakia',
          'SI' => 'Slovenia',
          'SB' => 'Solomon Islands',
          'SO' => 'Somalia',
          'ZA' => 'South Africa',
          'GS' => 'South Georgia and the South Sandwich Islands',
          'ES' => 'Spain',
          'LK' => 'Sri Lanka',
          'SD' => 'Sudan',
          'SR' => 'Suriname',
          'SJ' => 'Svalbard and Jan Mayen',
          'SZ' => 'Swaziland',
          'SE' => 'Sweden',
          'CH' => 'Switzerland',
          'SY' => 'Syrian Arab Republic',
          'TW' => 'Taiwan, Province of China',
          'TJ' => 'Tajikistan',
          'TZ' => 'Tanzania, United Republic of',
          'TH' => 'Thailand',
          'TL' => 'Timor-Leste',
          'TG' => 'Togo',
          'TK' => 'Tokelau',
          'TO' => 'Tonga',
          'TT' => 'Trinidad and Tobago',
          'TN' => 'Tunisia',
          'TR' => 'Turkey',
          'TM' => 'Turkmenistan',
          'TC' => 'Turks and Caicos Islands',
          'TV' => 'Tuvalu',
          'UG' => 'Uganda',
          'UA' => 'Ukraine',
          'AE' => 'United Arab Emirates',
          'GB' => 'United Kingdom',
          'US' => 'United States',
          'UM' => 'United States Minor Outlying Islands',
          'UY' => 'Uruguay',
          'UZ' => 'Uzbekistan',
          'VU' => 'Vanuatu',
          'VE' => 'Venezuela',
          'VN' => 'Viet Nam',
          'VG' => 'Virgin Islands, British',
          'VI' => 'Virgin Islands, U.S.',
          'WF' => 'Wallis and Futuna',
          'EH' => 'Western Sahara',
          'YE' => 'Yemen',
          'ZM' => 'Zambia',
          'ZW' => 'Zimbabwe',
        );

return array_search($value, $countrycodes);

}



?>