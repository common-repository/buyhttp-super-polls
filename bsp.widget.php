<?php
class PollWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'PollWidget', // Base ID
			__('BSP Polls', 'bsppolls'), // Name
			array( 'description' => __( 'A Poll Widget', 'bsppolls' ),'class'=>'bspwidget' ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $wpdb;
		$row = $wpdb->get_row("SELECT question,options FROM ".$wpdb->prefix."bsppolls WHERE poll_id=".$instance['question']);
		$options = json_decode($row->options,true);
		$title = apply_filters( 'widget_title', $instance['title'] );
		$settings = get_option( 'bsp_settings' );
		if(isset($settings['oneVotePerIp'])){
			$voted = $wpdb->get_var("SELECT COUNT(vote_id) FROM ".$wpdb->prefix."bsppollvotes WHERE ip='".$_SERVER['REMOTE_ADDR']."' AND poll_id=".$instance['question']);
			echo $voted;
		}

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo stripslashes($row->question);
		?>
		<form id="bspForm">
			<?php foreach($options as $option){?>
			<input type="radio" name="votefor" value="<?php echo $option['id']; ?>"> <?php echo stripslashes($option['name']); ?><br />
			<?php } ?>
			<?php if(isset($voted) && $voted > 0){ ?>
			<p><?php echo __('You have already voted in this poll.','bsppolls'); ?></p>
			<?php } else { ?>
			<p><input type="submit" value="<?php echo __('Save Vote','bsppolls'); ?>" class="button button-primary" id="bspSubmit"></p>
			<?php } ?>
			<?php wp_nonce_field( 'bspPollVoting' ); ?>
			<input type="hidden" name="poll_id" value="<?php echo $instance['question']; ?>">
		</form>
		<div id="bspSubmitFeedback"></div>
		
		<p><a href="<?php echo add_query_arg('poll_id',$instance['question'],get_permalink($settings['pageUrl'])); ?>">View Results</a></p>
		
		<?php
		if(isset($settings['linkOnWidget'])){
			echo '<p>Poll plugin by <a href="http://www.buyhttp.com" target="_blank">BuyHTTP</a></p>';
		}
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		global $wpdb;
		$questions = $wpdb->get_results("SELECT poll_id, question FROM ".$wpdb->prefix."bsppolls ORDER BY poll_id DESC");
		$widget_defaults = array(
		    'title'		=> 'Poll',
		    'question'	=> ''
		);
		$instance  = wp_parse_args( (array) $instance, $widget_defaults );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php __( 'Title:','bsppolls' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'question' ); ?>"><?php __( 'Question:','bsppolls' ); ?></label> 
		<select id="<?php echo $this->get_field_id( 'question' ); ?>" name="<?php echo $this->get_field_name( 'question' ); ?>">
			<option><?php echo __('- Select One-','bsppolls'); ?></option>
			<?php foreach($questions as $question){ ?>
			<option value="<?php echo $question->poll_id; ?>" <?php selected( $question->poll_id, $instance['question'], true ); ?>><?php echo stripslashes($question->question); ?></option>
			<?php } ?>
		</select>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
 
	    $instance['title'] = strip_tags($new_instance['title']);
	    $instance['question'] = $new_instance['question'];
	 
	    return $instance;
	}

} // class PollWidget


function registerPollWidget()
{
    register_widget( 'PollWidget' );
}
