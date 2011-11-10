<?php

class KickoffLabs_Widget extends WP_Widget {

	/*	Widget setup	*/
	function KickoffLabs_Widget() {
		//	widget settings
		$widget_ops = array( 'classname' => 'kickofflabs', 'description' => 'A widget for displaying the KickoffLabs subscription form.');
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'kickofflabs-widget' );

		//	Create the widget
		$this->WP_Widget( 'kickofflabs-widget', 'KickoffLabs Subscription Widget', $widget_ops, $control_ops );
	}

	/*	How to display the widget on the screen.	*/
	function widget( $args, $instance ) {

		/* Before widget (defined by themes). */
		echo $before_widget;
		
		//	display widget html
		?>
		<div id="<?php echo $this->id; ?>" class="KOL_widget" style="margin-top:20px">
		<?php
		if ($_POST[$this->id]==1) { 
			//	grab email and subscribe
			require_once('KickoffLabsAPI.php');
			$send_ar = isset( $instance['send_ar'] ) ? $instance['send_ar'] : false;
			$kol = new KickoffLabsAPI($instance['landing_page_id'],$send_ar);
			$email = $_POST['email'];
			//	validate email address
			if($this->ValidateEmail($email)) {
				if(!$social_url = $kol->Subscribe($email)) {
					//	error!
					echo "There was an error subscribing the email address ".$email;	//.$kol->error;
					//	display subscription form
					$this->DisplayForm($instance);
				}
				else {
					?>
					<p class="KOL_thank_you_msg"><?php echo $instance['thank_you_msg']; ?></p>
					<p class="KOL_social_url"><a href="<?php echo $social_url; ?>"><?php echo $social_url; ?></a></p>
					<?php
					$show_share = isset( $instance['show_share'] ) ? $instance['show_share'] : false;
					if($show_share) {
			?>
					<div class="KOL_share_buttons" style="float:left; margin:3px 0 0 7px;">
						<a class="KOL_share_twitter" title="Tweet this!" href="#" onclick="window.open('http://twitter.com/home?status=<?php echo urlencode($social_url);?>', 'newWindow', 'width=815, height=436'); return false;"><img src="<?php  echo plugins_url('/imgs/icn-twitter.png', __FILE__); ?>"/></a>&nbsp;
						<a class="KOL_share_fb" title="Share on Facebook" href="#" onclick="window.open('http://www.facebook.com/sharer.php?u=<?php echo urlencode($social_url);?>', 'newWindow', 'width=816, height=523'); return false;"><img src="<?php  echo plugins_url('/imgs/icn-facebook.png', __FILE__); ?>"/></a>
					</div>
					<?php
					}
				}
			}
			else{
				//	invalid email address
				echo "The email address entered is invalid.";
				$this->DisplayForm($instance);
			}
		}
		else {
			//	display subscription form
			$this->DisplayForm($instance);
		}
		?>
		</div>
		<?php
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	function DisplayForm($instance) {
		$email = $_POST['email'];
		?>
			<form id="signup_form" class="KOL_signup_form" method="post">
				<div class="KOL_title"><?php echo $instance['title']; ?></div>
				<div class="KOL_input">
					<label class="KOL_email_label" for="email">Email</label>
					<input type="text" size="30" name="email" id="email" class="KOL_email_input" placeholder="Enter Your Email" value="<?php echo $email; ?>" />
					<input id="<?php echo $this->id; ?>" name="<?php echo $this->id; ?>"  type="hidden" value="1" />
					<input type="submit" id="submit_email" class="KOL_submit_button" value="Notify Me"/>
				</div>
			</form>
		<?php		
	}
	
	function ValidateEmail($email) {
		// First, we check that there's one @ symbol, 
		// and that the lengths are right.
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters 
			// in one section or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
			?'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
			$local_array[$i])) {
				return false;
			}
		}
		// Check if domain is IP. If not, 
		// it should be valid domain name
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if
				(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
				?([A-Za-z0-9]+))$",
				$domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	}
	
	/*	Update the widget settings.	*/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags($new_instance['title'] );
		$instance['landing_page_id'] = strip_tags($new_instance['landing_page_id'] );
		$instance['thank_you_msg'] = strip_tags($new_instance['thank_you_msg'] );
		$instance['show_share'] = ( isset( $new_instance['show_share'] ) ? true : false );  
		$instance['send_ar'] = ( isset( $new_instance['send_ar'] ) ? true : false );  
		return $instance;
	}

	/*	Displays the widget settings controls on the widget panel.	 */
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Subscribe', 
			'thank_you_msg'=>'Thank you for signing up. Please share this link with your friends!' ,
			'show_share' => true,
			'send_ar' =>true
			);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title_fid = $this->get_field_id( 'title' );
		$title_name = $this->get_field_name( 'title' );
		$landing_fid = $this->get_field_id( 'landing_page_id' );
		$landing_name = $this->get_field_name( 'landing_page_id' );
		$thanks_fid = $this->get_field_id( 'thank_you_msg' );
		$thanks_name = $this->get_field_name( 'thank_you_msg' );
		$share_fid = $this->get_field_id( 'show_share' );
		$share_name = $this->get_field_name( 'show_share' );
		$send_fid = $this->get_field_id( 'send_ar' );
		$send_name = $this->get_field_name( 'send_ar' );
?>
		<p>
			<label for="<?php echo $title_fid; ?>">Title</label>
			<input id="<?php echo $title_fid; ?>" name="<?php echo $title_name; ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $landing_fid; ?>">Landing Page ID</label>
			<input id="<?php echo $landing_fid; ?>" name="<?php echo $landing_name; ?>" value="<?php echo $instance['landing_page_id']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $thanks_fid; ?>">Thank You Text</label>
			<input id="<?php echo $thanks_fid; ?>" name="<?php echo $thanks_name; ?>" value="<?php echo $instance['thank_you_msg']; ?>" style="width:100%;" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_share'], true ); ?> id="<?php echo $share_fid; ?>" name="<?php echo $share_name; ?>" /> 
			<label for="<?php echo $share_fid; ?>">Show Facebook/Twitter Share Buttons?</label>
		</p>		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['send_ar'], true ); ?> id="<?php echo $send_fid; ?>" name="<?php echo $send_name; ?>" /> 
			<label for="<?php echo $send_fid; ?>">Send KickoffLabs Auto Response</label>
		</p>
<?php
	}
}
?>
