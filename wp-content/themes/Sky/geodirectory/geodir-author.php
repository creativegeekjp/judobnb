<?php 
// get header
get_header(); 

###### WRAPPER OPEN ######
// this adds the opening html tags to the primary div, this required the closing tag below :: ($type='',$id='',$class='')
do_action( 'geodir_wrapper_open', 'author-page', 'geodir-wrapper','');

	###### TOP CONTENT ######
	// action called before the main content and the page specific content
	do_action('geodir_top_content', 'author-page');
	// template specific, this can add the sidebar top section and breadcrums
	do_action('geodir_author_before_main_content');
	// action called before the main content
	do_action('geodir_before_main_content', 'author-page');

	// action, author page title
	do_action( 'geodir_author_page_title');
	// action, author page description
	do_action( 'geodir_author_page_description');
				
				
			###### SIDEBAR ######
			do_action('geodir_author_sidebar_left');
				
			###### MAIN CONTENT WRAPPERS OPEN ######
			// this adds the opening html tags to the content div, this required the closing tag below :: ($type='',$id='',$class='')
			do_action( 'geodir_wrapper_content_open', 'author-page', 'geodir-wrapper-content','');
			
			
			
					###### MAIN CONTENT ######
					// this call the main page content
        			// do_action('geodir_author_content');

					$author_id = get_query_var( 'author' );
					$skype_link = get_the_author_meta( 'pe_skype', $author_id );
					$twitter_link = get_the_author_meta( 'pe_twitter', $author_id );
					$yahoo_link = get_the_author_meta( 'pe_yahoo', $author_id );
					$aim_link = get_the_author_meta( 'pe_aim', $author_id );
					$author_description = get_the_author_meta( 'description', $author_id );

					if ( $skype_link != '' || $twitter_link != '' || $yahoo_link != '' || $aim_link != '' ) {
						$main_span = ' vc_col-sm-9';
						$social_enabled = true;
					} else {
						$main_span = ' no-social vc_col-sm-12';
						$social_enabled = false;
					}

					if ( $author_description != '' ) {
						$description_enabled = true;
					} else {
						$description_enabled = false;
					}

					if ( $description_enabled ) {
						echo '<div class="author-description'.$main_span.'">';
						echo '<h2>'.__('About', 'vh').' '.get_the_author_meta( 'display_name', $author_id ).'</h2>';

						echo vh_limit_text(get_the_author_meta( 'description', $author_id ), get_option('geodir_author_desc_word_limit', 50))
						.'</div>';
					}

					if ( $social_enabled ) {
						echo '
						<div class="author-social-links vc_col-sm-3">
							<h2>'.__('Contact', 'vh').' '.get_the_author_meta( 'display_name', $author_id ).'</h2>
							<span class="lower-title">'.__('via social networks', 'vh').'</span>';
							if ( $skype_link != '' ) {
								echo '<a href="skype:'.$skype_link.'?call" class="author-link author-skype icon-skype"></a>';
							}
							if ( $twitter_link != '' ) {
								echo '<a href="'.$twitter_link.'" class="author-link author-twitter icon-twitter"></a>';
							}
							if ( $yahoo_link != '' ) {
								echo '<a href="'.$yahoo_link.'" class="author-link author-yahoo icon-yahoo"></a>';
							}
							if ( $aim_link != '' ) {
								echo '<a href="'.$aim_link.'" class="author-link author-aim icon-aim"></a>';
							}
						echo '
						</div>';
					}

					echo '<div class="clearfix"></div>';

					echo do_shortcode( '[vh_featured_properties module_type="author-dash"]' );
					
					echo '<div class="clearfix"></div>';

	    	###### MAIN CONTENT WRAPPERS CLOSE ######
			// this adds the closing html tags to the wrapper_content div :: ($type='')
			do_action( 'geodir_wrapper_content_close', 'author-page');
			
			###### SIDEBAR ######
			do_action('geodir_author_sidebar_right');
			
	###### BOTTOM SECTION WIDGET AREA ######
	// adds the details bottom section widget area, you can add more classes via ''
	do_action( 'geodir_sidebar_author_bottom_section');

###### WRAPPER CLOSE ######	
// this adds the closing html tags to the wrapper div :: ($type='')
do_action( 'geodir_wrapper_close', 'author-page');
get_footer();  