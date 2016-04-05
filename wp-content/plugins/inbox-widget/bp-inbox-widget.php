<?php

/* Create the widget. */

class BP_Core_Inbox_Widget extends WP_Widget {
	
	function BP_Core_Inbox_Widget() {
		parent::WP_Widget( false, 'Inbox' );
	}


	function widget($args, $instance) {
		global $bp;
	 
	 // Show the widget only to logged-in users on the homepage
	  If ( !is_user_logged_in() || ( bp_is_user_messages() ) )
		return;
		
	
	    extract( $args );

		echo $before_widget;
		echo $before_title
		   . $widget_name
		   . $after_title; ?>

	
		
	
	<?php if ( bp_has_message_threads('per_page=3') ) : ?>

 	<ul id="message-threads">
 
 		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>

    	<li<?php if ( bp_message_thread_has_unread() ) : ?> class="unread"<?php else: ?> class="read"<?php endif; ?>>
      
     		<div class="message-subject"> 
     			<?php bp_message_thread_avatar() ?>
      			<?php bp_message_thread_subject() ?>
    		 </div>
    
    		<div class="message-body">
     			<?php bp_message_thread_excerpt() ?>
     		</div>
     	
     		<div class="message-meta">
     			<p><a class="button view" title="View Message" href="<?php bp_message_thread_view_link() ?>">View Message</a> <a class="button view" title="Send Reply" href="<?php bp_message_thread_view_link() ?>#send-reply">Reply</a></p>
   			</div>
     
    	</li>
    
  		<?php endwhile; ?>
  
  	</ul>

	<?php else: ?>

 	<div id="message" class="info">
   
    <p>There are no messages to display.</p>
  
  	</div>	

	<?php endif;?>

		<?php echo $after_widget; ?>
	<?php
	}

}


function bpinbox_register_widgets() {
	register_widget( 'BP_Core_Inbox_Widget' );
}

add_action( 'widgets_init', 'bpinbox_register_widgets' );


/* Add basic css. */

function bp_inbox_widget_add_css() { ?>

	<style type="text/css">
	 
	
		.widget #message-threads li {
		
			padding: 10px; 
			border-bottom: 1px solid #EEEEEE;
			margin-bottom: 14px;  
			
		}
		
		.widget #message-threads li.unread {
		
			background:#FFF9DB none repeat scroll 0 0;
			border-bottom:1px solid #FFE8C4;
			border-top:1px solid #FFE8C4;
			
		}

		.widget #message-threads li.read {
		
			opacity: .6;
			
		}
		
		.widget #message-threads img.avatar {

			height:20px;
			width:20px;
			margin-right: 5px;
		}

		.widget .message-subject, .widget .message-body {

			width: 100%; 
			float: left; 
			margin-bottom: 8px; 	

		}

		.widget .message-subject p {

			font-size: 16px; 
			font-weight: bold; 
			margin: 2px 0 0 30px;
			
		}
		
	

	</style>
<?php	
}
add_action( 'wp_footer', 'bp_inbox_widget_add_css' );


?>