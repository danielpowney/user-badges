<?php

class UB_Condition {
	
	public $condition_id;
	public $steps;
	public $name;
	public $badges;
	public $points;
	public $created_dt;
	public $enabled;
	public $assignment_expiry;
	
	/**
	 * Constructor
	 */
	function __construct( $condition_id, $name, $badges = array(), $points = 0, $created_dt, $enabled = true, $assignment_expiry = null ) {
		
		$this->condition_id = $condition_id;
		$this->name = $name;
		$this->badges = ( ! is_array( $badges ) ) ? array() : $badges;
		$this->points = intval( $points );
		$this->enabled = ( $enabled == true || $enabled == "true" ) ? true : false;
		$this->steps = array();
		global $wpdb;
		$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME . ' WHERE condition_id = ' . $condition_id );
		foreach ( $results as $row ) {
			array_push( $this->steps, new UB_Step( $row->step_id, $row->condition_id, $row->label, $row->action_name, $row->created_dt ) );
		}
		$this->created_dt = $created_dt;
		$this->assignment_expiry = $assignment_expiry;
	}
	
	/**
	 * Checks if condition is met
	 */
	public function check( $user_id ) {
		
		$condition_result = true;
		
		/*
		// AND
		if ( $steps_and ) {
			$condition_result = apply_filters( 'ub_condition_check_and', $condition_result, $this, $user_id );
		} else { // OR
		}
		*/
		
		if ( $condition_result ) {
			foreach ( $this->steps as $step ) {
					
				if ( $step->action_name == null ) {
					$condition_result = false; // not setup correctly
					break;
				}
			
				$step_result = apply_filters( 'ub_condition_step_check_' . $step->action_name, true, $step, $user_id, $step->action_name );
				
				if ( $step_result == false ) { // if any step is false, condition is not met
					$condition_result = false;
					break;
				}
			}
		}
		
		// if you get this far, condition has been met
		if ( $condition_result == true && count( $this->steps ) > 0 ) {
			
			foreach ( $this->badges as $badge_id ) {
				User_Badges::instance()->api->add_user_assignment( $this->condition_id, $user_id, 'badge', $badge_id, $this->assignment_expiry );
			}
			
			if ( $this->points > 0 ) {
				User_Badges::instance()->api->add_user_assignment( $this->condition_id, $user_id, 'points', $this->points, $this->assignment_expiry );
			}
		}
	}
}