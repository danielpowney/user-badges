<?php

/**
 * Settings class
*
* @author dpowney
*/
class BROO_Settings {
	
	public $actions_enabled = null;
	public $general_settings = array();
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'default_settings' ) );
		}
		
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			add_action('admin_init', array( &$this, 'register_settings' ) );
		}
	}
	
	/**
	 * Reisters settings
	 */
	function register_settings() {

		$this->register_general_settings();
		
	}
	
	function default_settings() {
		
		$this->actions_enabled = (array) get_option( 'broo_actions_enabled' );
		$this->general_settings = (array) get_option( 'broo_general_settings' );
		
		$this->actions_enabled = apply_filters( 'broo_default_actions_enabled', $this->actions_enabled );
		
		$assignment_moderator_notification_email_template = __( "Hello,\r\n\r\n"
				. "A new assignment #{assignment_id} for \"{display_name}\" requires moderation.\r\n\r\n"
				. "{assignment_details}\r\nDate: {date}\r\n\r\n{assignment_moderation_link}\r\n\r\n"
				. "Thank you.", 'badgearoo' );
		
		$this->general_settings = array_merge( array(
				'broo_assignment_auto_approve'					=> true,
				'broo_assignment_notify_moderators'				=> false,
				'broo_assignment_moderators'						=> get_option( 'admin_email' ),
				'broo_assignment_moderation_notification_from'	=> get_option( 'blogname' ),
				'broo_assignment_moderation_notification_email'	=> get_option( 'admin_email' ),
				'broo_assignment_moderation_notification_subject' => __( 'New Assignment', 'badgearoo' ),
				'broo_assignment_moderation_notification_heading'	=> __( 'New Assignment', 'badgearoo' ),
				'broo_assignment_moderation_notification_template' => $assignment_moderator_notification_email_template,
				'broo_show_user_assignment_modal'					=> true,
				'broo_enable_badge_permalink'						=> true,
				'broo_badge_theme'								=> 'light'
		), $this->general_settings );
		
		update_option( 'broo_actions_enabled', $this->actions_enabled );
		update_option( 'broo_general_settings', $this->general_settings);
	}
	
	/**
	 * Register general settings
	 */
	function register_general_settings() {
	
		register_setting( 'broo_general_settings', 'broo_general_settings', array( &$this, 'sanitize_general_settings' ) );
	
		add_settings_section( 'section_general', __( 'General', 'badgearoo' ), array( &$this, 'section_general_desc' ), 'broo_general_settings' );
		add_settings_section( 'section_moderation', __( 'Moderation', 'badgearoo' ), array( &$this, 'section_moderation_desc' ), 'broo_general_settings' );
		
		$setting_fields = array(
			'broo_show_user_assignment_modal' => array(
					'title' 	=> __( 'Show Assignment Modal', 'badgearoo' ),
					'callback' 	=> 'field_checkbox',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_general',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_show_user_assignment_modal',
							'label' 		=> __( 'Do you want the user to see a popup message on page load upon assignment of badges and points?', 'badgearoo' )
					)
			
			),
			'broo_enable_badge_permalink' => array(
					'title' 	=> __( 'Enable Badge Permalink', 'badgearoo' ),
					'callback' 	=> 'field_checkbox',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_general',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_enable_badge_permalink',
							'label' 		=> __( 'Do you want to enable the badge permalink?', 'badgearoo' )
					)			
			),
			'broo_badge_theme' => array(
					'title' 	=> __( 'Badge Theme', 'badgearoo' ),
					'callback' 	=> 'field_select',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_general',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_badge_theme',
							'label' 		=> __( 'CSS theme to apply to badges.', 'badgearoo' ),
							'select_options' => array(
									'icon'	=> __( 'Icon', 'badgearoo' ),
									'light' 		=> __( 'Light', 'badgearoo' ),
									'dark' 			=> __( 'Dark', 'badgearoo' ),
									'html' 			=> __( 'Custom HTML', 'badgearoo' )
							)
					)
			),
			'broo_assignment_auto_approve' => array( 
					'title' 	=> __( 'Auto Approve Assignments', 'badgearoo' ),
					'callback' 	=> 'field_checkbox',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array( 
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_assignment_auto_approve',
							'label' 		=> __( 'Automatically approve user assignment of new badges and points.', 'badgearoo' )
					)
					 
			),
			'broo_assignment_notify_moderators' => array(
					'title' 	=> __( 'Enable Assignment Moderator Notifications', 'badgearoo' ),
					'callback' 	=> 'field_checkbox',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_assignment_notify_moderators',
							'label' 		=> __( 'Send email notifications for new assignments of badges or points which require moderation.', 'badgearoo' )
					)
			),
			'broo_assignment_moderators' => array(
					'title' 	=> __( 'Moderators', 'badgearoo' ),
					'callback' 	=> 'field_textarea',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_assignment_moderators',
							'label' 		=> __( 'Who should receive the assignment moderation notification?', 'badgearoo' ),
							'footer' 		=> __('Enter the email address(es), one per line.', 'badgearoo' )
					)
			),
			'broo_assignment_moderation_notification_from' => array(
				'title' 	=> __( 'From Name', 'badgearoo' ),
					'callback' 	=> 'field_input',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_assignment_moderation_notification_from',
							'label' 		=> __( 'The name assignment moderation notifications are said to come from.', 'badgearoo' )
				)
			),
			'broo_assignment_moderation_notification_email' => array(
					'title' 	=> __( 'From Email', 'badgearoo' ),
					'callback' 	=> 'field_input',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_assignment_moderation_notification_email',
							'label' 		=> __( 'Email to send assignment moderation notifications from.', 'badgearoo' )
					)
			),
			'broo_assignment_moderation_notification_subject' => array(
					'title' 	=> __( 'Email Subject', 'badgearoo' ),
					'callback' 	=> 'field_input',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_assignment_moderation_notification_subject',
							'label' 		=> __( 'Enter the subject line for the assignment moderation notification email', 'badgearoo' )
					)
			),
			'broo_assignment_moderation_notification_heading' => array(
					'title' 	=> __( 'Email Heading', 'badgearoo' ),
					'callback' 	=> 'field_input',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_assignment_moderation_notification_heading',
							'label' 		=> __( 'Enter the heading for the assignment moderation notification email', 'badgearoo' )
					)
			),
			'broo_assignment_moderation_notification_template' => array(
					'title' 	=> __( 'Email Template', 'badgearoo' ),
					'callback' 	=> 'field_editor',
					'page' 		=> 'broo_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'broo_general_settings',
							'setting_id' 	=> 'broo_assignment_moderation_notification_template',
							'footer' 		=> __( 'Enter the email that is sent to a moderator to notify them new assignment required approval. HTML is accepted. Available template tags:<br />'
									. '{display_name} - The user\'s display name<br />'
									. '{username} - The user\'s username on the site<br />'
									. '{user_email} - The user\'s email address<br />'
									. '{site_name} - Your site name<br />'
									. '{assignment_id} - The unique ID number for this assignment<br />'
									. '{date} - The date of the assignment<br />'
									. '{assignment_details} - A list of badges and points<br />'
									. '{assignment_moderation_link} - Link to assignments page', 'badgearoo' ),
					)
			),
		);

		foreach ( $setting_fields as $setting_id => $setting_data ) {
			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( &$this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}
	
	/**
	 * General section desciption
	 */
	function section_moderation_desc() {
		?>
		<p><?php _e( 'Moderation settings for user assignment of badges and points.', 'badgearoo' ); ?></p>
		<?php
	}
	
	/**
	 * Misc section desciption
	 */
	function section_general_desc() {
		?>
			<p><?php _e( 'Misc settings.', 'badgearoo' ); ?></p>
			<?php
		}
	
	/**
	 * Checkbox setting
	 */
	function field_checkbox( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<input type="checkbox" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="true" <?php checked( true, $settings[$args['setting_id']], true ); ?> />
		<label><?php echo $args['label']; ?></label>
		<?php 
	}
	
	/**
	 * Checkbox setting
	 */
	function field_input( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<input class="regular-text" type="text" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="<?php echo $settings[$args['setting_id']]; ?>" />
		<label><?php echo $args['label']; ?></label>
		<?php 
	}
	
	/**
	 * 
	 */
	function field_textarea( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<p><?php echo $args['label']; ?></p><br />
		<textarea name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" rows="5" cols="75"><?php echo $settings[$args['setting_id']]; ?></textarea>
		<p><?php echo $args['footer']; ?></p><br />
		<?php
	}
	
	/**
	 * Editor field
	 * 
	 * @param unknown $args
	 */
	function field_editor( $args ) {
		
		$settings = (array) get_option( $args['option_name' ] );
		
		if ( ! empty( $args['label' ] ) ) {
			?>
			<p><?php echo $args['label']; ?></p><br />
			<?php
		}
		
		wp_editor( $settings[$args['setting_id']], $args['setting_id'], array(
				'textarea_name' => $args['setting_id'],
				'editor_class' => ''
		) );
		
		echo ( ! empty( $args['footer'] ) ) ? '<br/><p class="description">' . $args['footer'] . '</p>' : '';
	}
	
	/**
	 * Color picker field
	 * 
	 * @param unknown $args
	 */
	function field_color_picker( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<input type="text" class="color-picker" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="<?php echo $settings[$args['setting_id']]; ?>" />
		<?php 
	}
	
	/**
	 * Color picker field
	 *
	 * @param unknown $args
	 */
	function field_select( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		$value = $settings[$args['setting_id']];
		?>
		<select name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]">
			<?php 
			foreach ( $args['select_options'] as $option_value => $option_label ) {
				$selected = '';
				if ( $value == $option_value ) {
					$selected = 'selected="selected"';
				}
				echo '<option value="' . $option_value . '" ' . $selected . '>' . $option_label . '</option>';
			}
			?>
		</select>
		<?php 
		}
	
	/**
	 * Sanitize the general settings
	 *
	 * @param $input
	 * @return boolean
	 */
	function sanitize_general_settings( $input ) {
		
		if ( isset( $input['broo_show_user_assignment_modal'] ) && $input['broo_show_user_assignment_modal'] == 'true' ) {
			$input['broo_show_user_assignment_modal'] = true;
		} else {
			$input['broo_show_user_assignment_modal'] = false;
		}
		
		if ( isset( $input['broo_enable_badge_permalink'] ) && $input['broo_enable_badge_permalink'] == 'true' ) {
			$input['broo_enable_badge_permalink'] = true;
		} else {
			$input['broo_enable_badge_permalink'] = false;
		}
		
		if ( isset( $input['broo_assignment_auto_approve'] ) && $input['broo_assignment_auto_approve'] == 'true' ) {
			$input['broo_assignment_auto_approve'] = true;
		} else {
			$input['broo_assignment_auto_approve'] = false;
		}
		
		if ( isset( $input['broo_assignment_notify_moderators'] ) && $input['broo_assignment_notify_moderators'] == 'true' ) {
			$input['broo_assignment_notify_moderators'] = true;
		} else {
			$input['broo_assignment_notify_moderators'] = false;
		}
		
		$assignment_moderators = preg_split( '/[\r\n,]+/', $input['broo_assignment_moderators'], -1, PREG_SPLIT_NO_EMPTY );
		foreach ( $assignment_moderators as $email ) {
			if (! is_email( $email ) ) {
				add_settings_error( 'broo_general_settings', 'invalid_assignment_moderators', sprintf( __( 'Moderator email  "%s" is invalid.', 'badgearoo' ), $email ) );
				break;
			}
		}
		
		return $input;
	}
}