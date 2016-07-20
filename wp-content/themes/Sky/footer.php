<?php
/**
 * The template for displaying the footer.
 */

global $vh_is_footer;
$vh_is_footer = true;
$retina_logo_class = '';
$logo_size_html = '';
$map_class = '';

// // Get theme footer logo
$footer_logo = get_option('vh_site_footer_logo');
if($footer_logo == false) {
	$footer_logo = get_template_directory_uri() . '/images/footer-logo.png';
	$logo_size_html = ' width="221" height="138"';
}

// // Get theme footer logo dimensions
$website_footer_logo_retina_ready = filter_var(get_option('vh_website_footer_logo_retina'), FILTER_VALIDATE_BOOLEAN);
if ((bool)$website_footer_logo_retina_ready != false) {
	$logo_size = getimagesize($footer_logo);
	$logo_size_html = ' style="width: ' . ($logo_size[0] / 2) . 'px; height: ' . ($logo_size[1] / 2) . 'px;" width="' . ($logo_size[0] / 2) . '" height="' . ($logo_size[1] / 2) . '"';
	$retina_logo_class = 'retina';
}

// Footer copyright
$copyrights = get_option('vh_footer_copyright') ? get_option('vh_footer_copyright') : '&copy; [year], Sky by <a href="http://cohhe.com" target="_blank">Cohhe</a>';
$copyrights = str_replace( '[year]', date('Y'), $copyrights);

// Scroll to top option
$scroll_to_top = filter_var(get_option('vh_scroll_to_top'), FILTER_VALIDATE_BOOLEAN);
?>
			</div><!--end of main-->
		</div><!--end of wrapper-->
		<div class="footer-wrapper">
			<div class="footer-container vc_row wpb_row vc_row-fluid <?php if ( !is_front_page() ) { echo 'not_front_page';}?>">
				<!--<a href="<?php echo home_url(); ?>" class="footer-logo-link"><img src="<?php echo esc_attr($footer_logo); ?>"<?php echo $logo_size_html ; ?> class="footer-logo <?php echo $retina_logo_class; ?>" alt="<?php esc_attr(bloginfo('name')); ?>" /></a>-->
				
				<div class="footer-content">
					<?php
						// How many footer columns to show?
						$footer_columns = get_option( 'vh_footer_columns' );
						if ( $footer_columns == false ) {
							$footer_columns = 4;
						}
					?>
					<div class="footer-links-container columns_count_<?php echo $footer_columns; ?>">
						<?php get_sidebar( 'footer' ); ?>
						<div class="clearfix"></div>
					
					</div><!--end of footer-links-container-->
				</div>
				<?php if ( (bool)$scroll_to_top != false ) { ?>
				<div class="scroll-to-top-container">
					<div class="scroll-to-top"><span><?php _e('To Top', 'vh'); ?></span></div>

				</div>
				<?php } ?>
				<div class="footer-inner">
					<div class="footer-lower">
						<?php dynamic_sidebar( 'sidebar-7' ); ?>
						<div class="clearfix"></div>
					</div>
					<div class="footer_info">
						<div class="copyright"><?php echo $copyrights; ?></div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
		<?php
			$fixed_menu    = filter_var(get_option('vh_fixed_menu'), FILTER_VALIDATE_BOOLEAN);
			$tracking_code = get_option( 'vh_tracking_code' ) ? get_option( 'vh_tracking_code' ) : '';
			if ( !empty( $tracking_code ) ) { ?>
				<!-- Tracking Code -->
				<?php
				echo '
					' . $tracking_code;
			}
		?>
		</div>
		<?php wp_footer(); ?>
		<?php if ( is_singular( 'gd_place' ) || bp_is_messages_conversation() ) : ?>
		<script type="text/javascript">
		<?php if ( is_singular( 'gd_place' ) ) :
			$v = '.comment-content .comment-text';
			$c = '.single-listing-main-content .content';
		else :
			$v = '.message-box .message-content';
		endif;
		
		$to = ICL_LANGUAGE_CODE;
		?>
		
		var target = jQuery('<?php echo $v; ?> p'),
			<?php if ( is_singular( 'gd_place' ) ) : ?>target2 = jQuery('<?php echo $c; ?> p'),<?php endif; ?>
			translateurl = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate?_=1461320337152&appId=E8DB680F742769E3F9B95BFDB55798C13FEB0E5C&to=<?php echo $to; ?>&text=';
		
		function translateTxt() {
		  target.each(function() {
		    var targetTxt = jQuery(this);
		    jQuery.get(translateurl + encodeURI(targetTxt.text()),function(data){
		      t = eval(data);
		      targetTxt.html(t);
		    });
		  });
		}
		
		<?php if ( is_singular( 'gd_place' ) ) : ?>
		function translateContent() {
		  target2.each(function() {
		    var targetTxt = jQuery(this);
		    jQuery.get(translateurl + encodeURI(targetTxt.text()),function(data){
		      t = eval(data);
		      targetTxt.html(t);
		    });
		  });
		}
		<?php endif; ?>
		
		</script>
		
		<?php endif; ?>
		
		<?php if ( function_exists('geodir_is_page') && geodir_is_page('preview') ) : ?>
		<script>
			jQuery(function ($) {
				$('#modal-info').modal();
				
				$('#modal-info .ok').click(function () {
					$.modal.close();
				});
			});
		</script>
		<?php endif; ?>
		
		
		
		
		<?php 
		
	   $user = wp_get_current_user();

		if(isset($user->data->ID)){
		  if(!in_array('host',$user->roles)){
		     $prev = true; //guest
		  }else{
		  	 $prev = false; //host
		  }
	
		}
		
		if($prev && ICL_LANGUAGE_CODE=='ja')
		{
			$label = "アカウントがゲストに切り替わりました。";
		    $redirect_button = "<button class='ok'>Ok</button>";
		}
		
		if($prev && ICL_LANGUAGE_CODE=='en')
		{
			$label ="You are now logged in as Guest";
			$redirect_button = "<button class='ok'>Ok</button>";
		}
		
		global $wpdb, $current_user; 
		
	    $uID = get_current_user_id();
	    
		$myrows = $wpdb->get_results( "SELECT * FROM `jd_bp_xprofile_data` WHERE user_id = $uID and field_id= 330" );
	
		
		if ($myrows) 
	    {
			if(!$prev && ICL_LANGUAGE_CODE=='ja')
			{
					$label = "アカウントがホストに切り替わりました。";
					$redirect_button = "<button class='ok'>Ok</button>";
			}
			
			if(!$prev && ICL_LANGUAGE_CODE=='en')
			{
					$label = "You are now logged in as Host";
					$redirect_button = "<button class='ok'>Ok</button>";
			}
				
	    }else{
	    	if(!$prev && ICL_LANGUAGE_CODE=='ja')
			{
					$label = "ホストになる場合は、Paypalアカウントが必須になります。プロファイル編集画面にて、ご編集くださいませ。";
					$redirect_button = "<a href=".site_url().''.langs()."/members/".$current_user->user_nicename."/profile/edit/group/1/"."><button class='ok'>Ok</button></a>";
			}
			
			if(!$prev && ICL_LANGUAGE_CODE=='en')
			{
					$label = "Before you become a host, please make sure that you set up your paypal email account.";
					$redirect_button = "<a href=".site_url().''.langs()."/members/".$current_user->user_nicename."/profile/edit/group/1/"."><button class='ok'>Ok</button></a>";
					$disableds = true;
			}
			?>
			 <script>
		        jQuery( document ).ready(function() {
	              if(jQuery( ".sub-menu" ).find('li.menu-item-2267').text() !=="" || jQuery( ".sub-menu" ).find('li.menu-item-2766').text() !==""  )
					{ 
					  jQuery( ".sub-menu" ).find('li.menu-item-2766').css("display","none");
					  jQuery( ".sub-menu" ).find('li.menu-item-2267').css("display","none"); 
					}
	
				});
	    	</script>
			<?php
		
	    }
		
		?>
		<div id="modal-info">
			<div class="title-bar">&nbsp;</div>
			<div class="content">
				<p><?php echo _e($label, 'geodirectory'); ?></p>
				<p><?php echo $redirect_button; ?></p>
			</div>
		</div>
	    <script>
	     		jQuery(function($){
				    var selections;
				    var docURL = document.URL;
					
					 if (jQuery.cookie("langss").split("/")[3] == "ja") {
					 	selections = "JPY";
					 }else{
					 	selections = "USD";
					 }
					
					 if(typeof(selections) != "undefined" && selections !== null) {
					  
					   if(selections != jQuery.cookie("C_CURRENCY"))
					   {  
								/*
							    if (confirm("Your current language did not match with your selected currency.") == true) {
							       
							    } else {
							       
							    }*/

					   }else{ 
					   		console.log("language tab match."); 
					   }
					
					 }
	     	});
	    	
	    </script>
	    <?php 
	    if($_COOKIE['switching_role']==1 && isset($user->data->ID) ){
	    ?>
		<script>
		    jQuery(function ($) {
				$('#modal-info').modal();
				$('#modal-info .ok').click(function () {
				    jQuery.cookie('switching_role', 0 , {path: '/'});
					$.modal.close();
				});
			});
		</script>
		 <?php 
	    }else{
	    }
	    ?>
	    <script>
	   
		     function ls(e)
		     {
				
					jQuery.cookie('switching_lang', 1 , {path: '/'});
					 
					var docURL = document.URL;
					
					if (docURL.indexOf("ja") == -1) {
						
						  jQuery.cookie('vh_selected_people', '大人1/子供なし', {path: '/'});
						  
						  jQuery.cookie('C_CURRENCY', 'JPY', {path: '/'});
						  
					}else{
						
						  jQuery.cookie('vh_selected_people', '1 Adult/No Children', {path: '/'});
						  
						  jQuery.cookie('C_CURRENCY', 'USD', {path: '/'});
					}
		     }
	    </script>
	    <?php 
			global $wpdb;
			
			$uid = get_current_user_id();
			
			if($wpdb->get_var("SELECT meta_value FROM jd_usermeta WHERE user_id =$uid AND meta_key= 'deuid'") > 0 )
			{
				
					$lang = $wpdb->get_var("SELECT value FROM `jd_bp_xprofile_data` WHERE field_id='635' AND user_id = $uid");
			
					if( $wpdb->num_rows > 0 ) 
		            {
		            	 switch($lang)
		            	 {
		            	 	case 'Japanese' :
		            	 		
					            	?>
					            	<script>
					            	  jQuery(document ).ready(function() {
					            	  	
						            	  	if(  typeof(jQuery.cookie('fb_users_href')) == "undefined" && jQuery.cookie('fb_users_href') == null ){
										   	
										   		 if( confirm("Click ok to switch to Japanese Language") == true)
												    {
												    	jQuery.cookie('fb_users_href', '1' , {path: '/'});
												    	
												    	 window.location.href = location.protocol + "//www.judobnb.com/ja/"; 
												    	 
												    }
										   }	 
					            	  });
					            	</script>
					            	<?php
					            	
		            	 		break;
		            	 		
		            	 	case '' :
		            	 			  //english already
		            	 		break;
		            	 }
		            	 
						
		            }else{
		            	
		            	global $current_user;
		            	
		            	$edit_profiles = site_url().''.langs()."/members/".$current_user->user_nicename."/profile/edit/group/1/";
		            	
		            	 ?>
							<script>
							
								   jQuery(document ).ready(function() {
								   	  
									   if(   typeof(jQuery.cookie('fb_users')) == "undefined" && jQuery.cookie('fb_users') == null  ){
									   	
									   		 if(confirm( "You are now logged in using Facebook Account. Click Ok to set your preferred language" ) == true)
											    {
											    	jQuery.cookie('fb_users', '1' , {path: '/'});
											    	
											    	window.location.href = '<?php echo $edit_profiles; ?>';
											    }
									   }
									  
								   });
								   
						    </script>
						    
						 <?php
		            }
			 
			}   
	    ?>
	    
			<script>
					/*
					jQuery(window).on('load', function(e){
						if (window.location.hash == '#_=_') {
							window.location.hash = ''; 
							history.pushState('', document.title, window.location.pathname); 
							window.location.href = location.protocol + "//www.judobnb.com/ja/"; 
						}
					})
					*/
			</script>
	  
	</body>
</html>