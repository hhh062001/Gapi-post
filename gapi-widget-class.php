<?php
// Creating the widget 
class gapi_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of widget
		'gapi_widget', 
		
		// Widget name will appear in UI
		__('Google Analytic Top Posts', 'gapi_widget_domain'), 
		
		// Widget description
		array( 'description' => __( 'Disply Google Analytic Top Posts', 'gapi_widget_domain' ), ) 
		);
	}
	
	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		
		// Display the output
		
		define('ga_email',GA_EMAIL);
		define('ga_password',GA_PASSWORD);
		define('ga_profile_id',GA_PROFILE_ID);
		
		require 'gapi.class.php';
		
		$ga = new gapi(ga_email,ga_password);
		
		$startIndex=1;
		$maxResults = GA_MAX_RECORD;
		
		$gapi .= '<style>
					ul.tabs {
					float:left;
					list-style:none;
					height:32px;
					width:100%;
					margin:0;
					padding:15px 0 0 !important;
					}
					 
					ul.tabs li {
					height:31px;
					line-height:31px;
					float:left;
					border:1px solid #999;
					overflow:hidden;
					position:relative;
					
					margin-right: 2px;
					padding: 0px !important;
					background: none !important;
					}
					 
					ul.tabs li a {
					
					text-decoration:none;
					color:#000;
					display:block;
					font-size:1.2em;
					border:1px solid #fff;
					outline:none;
					padding: 5px 8px !important;
					height:31px;
					line-height:31px;
					
					}
					 
					ul.tabs li a:hover {
					background:#ccc;
					}
					 
					html ul.tabs li.active,html ul.tabs li.active a:hover {
					background:#fff !important;
					border-bottom:1px solid #fff;
					}
					 
					.tabContainer {
					border:1px solid #999;
					overflow:hidden;
					clear:both;
					float:left;
					width:100%;
					background:#fff;
					}
					 
					.tabContent {
					font-size: 12px;
					padding:20px;
					}
					
					#sidebar ul.tabs li {
					padding: 0px !important;
					}
					</style>
					<script>
					$(document).ready(function() {
						//hiding tab content except first one
						$(".tabContent").not(":first").hide();
						// adding Active class to first selected tab and show
						$("ul.tabs li:first").addClass("active").show(); 
					 
						// Click event on tab
						$("ul.tabs li").click(function() {
							// Removing class of Active tab
							$("ul.tabs li.active").removeClass("active");
							// Adding Active class to Clicked tab
							$(this).addClass("active");
							// hiding all the tab contents
							$(".tabContent").hide();       
							// showing the clicked tabs content using fading effect
							$($("a",this).attr("href")).fadeIn("slow");
					 
							return false;
						});
					 
					});
					</script>';
		$gapi .= '<div class="tabbed-area">
				  <ul class="tabs">';
				  
				  if(GA_WEEKLY == 1)
				  {
					$gapi .= '<li><a href="#tab1">7 Days</a></li>';
				  }
				  if(GA_MONTHLY == 1)
				  {
					$gapi .= '<li><a href="#tab2">1 Month</a></li>';
				  }
				  if(GA_YEARLY == 1)
				  {
					$gapi .= '<li><a href="#tab3">1 Year</a></li>';
				  }
					
		$gapi .= ' </ul>
		  		  <div class="tabContainer">';
		
		$filter = 'country == United States && browser == Firefox || browser == Chrome';
		
		// For Weekly Tab
		if(GA_WEEKLY == 1)
		{
			 $startDate = date('Y-m-d', strtotime('-7 days'));
			 $endDate = date('Y-m-d');
			 
			 
			$ga->requestReportData(ga_profile_id,array('pageTitle','pagePath'),array('pageviews','visits'),'-pageviews',$filter,$startDate, $endDate, $startIndex, $maxResults);
			
			$gapi .= '<div id="tab1" class="tabContent"><ul>';
			
			foreach($ga->getResults() as $result){
				
				$rep_column = explode("||", $result);
				
				// check if title not available
				if($rep_column[0] != '')
				{
			
					$gapi .= '<li><a href="' . get_option('siteurl') . $rep_column[1] . '" target="_blank">' . $rep_column[0] . '</a></li>';
				}
			
			}
			
			$gapi .= '</ul></div>';
		}
		
		// For Monthly Tab
		if(GA_MONTHLY == 1)
		{
			 $startDate = date('Y-m-d', strtotime('-30 days'));
			 $endDate = date('Y-m-d');
			 
			 
			$ga->requestReportData(ga_profile_id,array('pageTitle','pagePath'),array('pageviews','visits'),'-pageviews',$filter,$startDate, $endDate, $startIndex, $maxResults);
			
			$gapi .= '<div id="tab2" class="tabContent"><ul>';
			
			foreach($ga->getResults() as $result){
				
				$rep_column = explode("||", $result);
				
				// check if title not available
				if($rep_column[0] != '')
				{
			
					$gapi .= '<li><a href="' . get_option('siteurl') . $rep_column[1] . '" target="_blank">' . $rep_column[0] . '</a></li>';
				}
			
			}
			
			$gapi .= '</ul></div>';
		}
		
		// For Yearly Tab
		if(GA_YEARLY == 1)
		{
			 $startDate = date('Y-m-d', strtotime('-365 days'));
			 $endDate = date('Y-m-d');
			 
			 
			$ga->requestReportData(ga_profile_id,array('pageTitle','pagePath'),array('pageviews','visits'),'-pageviews',$filter,$startDate, $endDate, $startIndex, $maxResults);
			
			$gapi .= '<div id="tab3" class="tabContent"><ul>';
			
			foreach($ga->getResults() as $result){
				
				$rep_column = explode("||", $result);
				
				// check if title not available
				if($rep_column[0] != '')
				{
			
					$gapi .= '<li><a href="' . get_option('siteurl') . $rep_column[1] . '" target="_blank">' . $rep_column[0] . '</a></li>';
				}
			
			}
			
			$gapi .= '</ul></div>';
		}
		
		$gapi .='</div></div>';
		
		echo __( $gapi , 'gapi_widget_domain' );
		//echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
		$title = $instance[ 'title' ];
		}
		else {
		$title = __( 'Top Articles', 'gapi_widget_domain' );
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // Class gapi_widget ends here
?>