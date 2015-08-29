<?php
/**
 * Checks whether conditions have been met given a new action has been performed
 *
 * @param unknown $action_name
 * @param unknown $user_id
 */
function broo_check_conditions( $action_name, $user_id ) {

	global $wpdb;

	$query = 'SELECT cs.condition_id as condition_id FROM ' . $wpdb->prefix . BROO_CONDITION_STEP_TABLE_NAME . ' cs, ' 
			. $wpdb->prefix . BROO_CONDITION_TABLE_NAME . ' c WHERE cs.action_name = "' . esc_sql( $action_name ) 
			. '" AND c.condition_id = cs.condition_id AND c.enabled = 1 GROUP BY cs.condition_id';
	
	$conditions = $wpdb->get_col( $query );

	foreach ( $conditions as $condition_id ) {
		$condition = Badgearoo::instance()->api->get_condition( $condition_id, false, true );
		$condition->check( $user_id );
	}

}
add_action( 'broo_check_conditions', 'broo_check_conditions', 10, 2 );

define( 'NEW_USER_ASSIGNMENTS_COOKIE', 'broo_new_assignment' );

/**
 * Sets new user assignments cookie
 * 
 * @param unknown $assignment_id
 * @param unknown $condition_id
 * @param unknown $user_id
 * @param unknown $type
 * @param unknown $value
 * @param unknown $created_dt
 * @param unknown $status
 */
function broo_new_assignment( $assignment_id, $condition_id, $user_id, $type, $value, $created_dt, $status ) {
	
	if ( $status == 'approved' ) {
		
		$assignment = Badgearoo::instance()->api->get_assignment( $assignment_id );
		$user = get_userdata( $user_id );
		
		if ( $assignment && $user ) {
			
			// you can use this filter to only set the cookie for certain assignments
			if ( apply_filters( 'broo_show_user_assignment_modal', true, $assignment ) ) {

				$cookie_data = isset( $_COOKIE[NEW_USER_ASSIGNMENTS_COOKIE] ) ? $_COOKIE[NEW_USER_ASSIGNMENTS_COOKIE] : array();
				
				$message = '';
				if ( $assignment['type'] == 'badge' ) {
					$message = sprintf( __( 'Congratulations %s, you have earned badge %s!' ), $user->display_name, $assignment['badge']->title );
				} else {
					$message = sprintf( __( 'Congratulations %s, you have earned %d points!' ), $user->display_name, $assignment['points'] );
				}
	
				// TODO make sure an assignment is only added once
				array_push( $cookie_data, array(
						'assignment' => $assignment,
						//'user' => $user,
						'message' => $message
				) );
				
				$cookie_data = apply_filters( 'broo_new_user_assignment_cookie_data', $cookie_data, $assignment, $user );
	
				setcookie( NEW_USER_ASSIGNMENTS_COOKIE, json_encode( $cookie_data ), time()+3600, COOKIEPATH, COOKIE_DOMAIN );
			}
		}
	}
}

$general_settings = (array) get_option( 'broo_general_settings' );
if ( $general_settings['broo_show_user_assignment_modal'] ) {
	add_action( 'broo_add_user_assignment', 'broo_new_assignment', 10, 7 );
}