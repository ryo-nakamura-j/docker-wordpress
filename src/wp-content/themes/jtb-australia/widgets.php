<?php
// Register and load the widget
function tourplan_load_widgets() {
	register_widget( 'Tourplan_Facebook_Widget' );
	register_widget( 'Tourplan_Phone_Widget' );
	register_widget( 'Tourplan_Address_Widget' );
	register_widget( 'Tourplan_Hours_Widget' );
}
add_action( 'widgets_init', 'tourplan_load_widgets' );

class Tourplan_Facebook_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'tp_fb_widget', 
			__('Tourplan Facebook Link', 'tp_fb_domain'), 
			array( 'description' => __( 'Link to Facebook URL in Tourplan Plugin', 'tp_fb_domain' ), ) 
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		echo '<div id="social" class="round-social-grey">';
		echo "<a href=\"" . get_option('tp_facebook_url') . "\" class=\"sprite facebook fa fa-facebook\" title=\"Visit our Facebook page\"></a>";
		echo '</div>';
		echo $args['after_widget'];
	}
}

class Tourplan_Phone_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'tp_phone_widget', 
			__('Tourplan Phone Number Link', 'tp_phone_domain'), 
			array( 'description' => __( 'Link to specified phone number', 'tp_phone_domain' ), ) 
		);
	}

	public function widget( $args, $instance ) {
		$iconClass = $instance['iconClass'];
		$phone = $instance['phone'];

		echo $args['before_widget'];
		echo '<span class="' . $iconClass . '"></span>';
		echo '<a href="tel:' . $phone . '">' . $phone . '</a>';
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$phone = $instance['phone'];
		$iconClass = isset($instance['iconClass']) ? $instance['iconClass'] : "glyphicon glyphicon-earphone";
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'iconClass' ); ?>">Icon Class</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'iconClass' ); ?>" name="<?php echo $this->get_field_name( 'iconClass' ); ?>" type="text" value="<?php echo esc_attr( $iconClass ); ?>" />
			<label for="<?php echo $this->get_field_id( 'phone' ); ?>">Phone Number</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>" />
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}

class Tourplan_Address_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'tp_address_widget', 
			__('Tourplan Address Display', 'tp_address_domain'), 
			array( 'description' => __( 'Address Display', 'tp_address_domain' ), ) 
		);
	}

	public function widget( $args, $instance ) {
		$iconClass = $instance['iconClass'];
		$address = $instance['address'];

		echo $args['before_widget'];
		echo '<span class="' . $iconClass . '"></span>';
		echo $address;
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$address = $instance['address'];
		$iconClass = isset($instance['iconClass']) ? $instance['iconClass'] : "glyphicon glyphicon-map-marker";
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'iconClass' ); ?>">Icon Class</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'iconClass' ); ?>" name="<?php echo $this->get_field_name( 'iconClass' ); ?>" type="text" value="<?php echo esc_attr( $iconClass ); ?>" />
			<label for="<?php echo $this->get_field_id( 'address' ); ?>">Address</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" type="text" value="<?php echo esc_attr( $address ); ?>" />
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}

class Tourplan_Hours_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'tp_hours_widget', 
			__('Tourplan Hours Display', 'tp_hours_domain'), 
			array( 'description' => __( 'Hours Display', 'tp_hours_domain' ), ) 
		);
	}

	public function widget( $args, $instance ) {
		$iconClass = $instance['iconClass'];
		$hours = $instance['hours'];

		echo $args['before_widget'];
		echo '<span class="' . $iconClass . '"></span>';
		echo $hours;
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$hours = $instance['hours'];
		$iconClass = isset($instance['iconClass']) ? $instance['iconClass'] : "glyphicon glyphicon-time";
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'iconClass' ); ?>">Icon Class</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'iconClass' ); ?>" name="<?php echo $this->get_field_name( 'iconClass' ); ?>" type="text" value="<?php echo esc_attr( $iconClass ); ?>" />
			<label for="<?php echo $this->get_field_id( 'hours' ); ?>">Hours</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'hours' ); ?>" name="<?php echo $this->get_field_name( 'hours' ); ?>" type="text" value="<?php echo esc_attr( $hours ); ?>" />
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}