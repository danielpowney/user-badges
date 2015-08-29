<?php 
/**
 * BuddyPress actions
 */

define ( 'BP_ACTIVITY_COMMENT_POSTED_ACTION', 'bp_activity_comment_posted' ); // works
define ( 'BP_ACTIVITY_ADD_USER_FAVORITE_ACTION', 'bp_activity_add_user_favorite' ); // works
define ( 'BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION', 'bp_activity_post_type_published' );
define ( 'BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION', 'friends_friendship_accepted' ); // works
define ( 'BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION', 'friends_friendship_requested' ); // works
define ( 'BP_GROUPS_CREATE_GROUP_ACTION', 'groups_create_group' ); // works - may rename
define ( 'BP_GROUPS_JOIN_GROUP_ACTION', 'groups_join_group' ); // workds
// TODO invite members to join a group

// do_action( 'bp_activity_comment_posted', $comment_id, $r, $activity );
// do_action( 'bp_activity_add_user_favorite', $activity_id, $user_id );
// do_action( 'bp_activity_post_type_published', $activity_id, $post, $activity_args );
// do_action( 'friends_friendship_' . $action, $friendship->id, $friendship->initiator_user_id, $friendship->friend_user_id, $friendship );
// do_action( 'groups_create_group', $group->id, $member, $group );
// do_action( 'groups_join_group', $group_id, $user_id ); and groups_accept_invite

function broo_init_bp_actions( $broo_actions ) {
	
	$broo_actions[BP_ACTIVITY_COMMENT_POSTED_ACTION] = array(
			'description' => __( 'Comment on an activity.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION] = array(
			'description' => __( 'Add favorite.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION] = array(
			'description' => __( 'Post activity.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION] = array(
			'description' => __( 'Accept a friend request.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION] = array(
			'description' => __( 'Request a friend.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_GROUPS_CREATE_GROUP_ACTION] = array(
			'description' => __( 'Create Group.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_GROUPS_JOIN_GROUP_ACTION] = array(
			'description' => __( 'Join Group.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	return $broo_actions;
}
add_filter( 'broo_init_actions', 'broo_init_bp_actions', 10, 1 );


/**
 * Adds WP Core actions
 *
 * @param actions
 */
function broo_add_bp_actions( $actions = array() ) {
	
	$actions_enabled = (array) get_option( 'broo_actions_enabled' );

	if ( isset( $actions[BP_ACTIVITY_COMMENT_POSTED_ACTION] ) && $actions[BP_ACTIVITY_COMMENT_POSTED_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_comment_posted',  'broo_bp_activity_comment_posted', 10, 3 );
		add_filter( 'broo_condition_step_check_bp_activity_comment_posted', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION] ) && $actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_add_user_favorite',  'broo_bp_activity_add_user_favorite', 10, 2 );
		add_filter( 'broo_condition_step_check_bp_activity_add_user_favorite', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION] ) && $actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_post_type_published',  'broo_bp_activity_post_type_published', 10, 2 );
		add_filter( 'broo_condition_step_check_bp_activity_post_type_published', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION] ) && $actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION]['enabled'] == true ) {
		add_action( 'friends_friendship_requested',  'broo_friends_friendship_requested', 10, 4 );
		add_filter( 'broo_condition_step_check_friends_friendship_requested', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION] ) && $actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION]['enabled'] == true ) {
		add_action( 'friends_friendship_accepted',  'broo_friends_friendship_accepted', 10, 4 );
		add_filter( 'broo_condition_step_check_friends_friendship_accepted', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_GROUPS_CREATE_GROUP_ACTION] ) && $actions[BP_GROUPS_CREATE_GROUP_ACTION]['enabled'] == true ) {
		add_action( 'groups_create_group',  'broo_groups_create_group', 10, 3 );
		add_filter( 'broo_condition_step_check_groups_create_group', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_GROUPS_JOIN_GROUP_ACTION] ) && $actions[BP_GROUPS_JOIN_GROUP_ACTION]['enabled'] == true ) {
		add_action( 'groups_join_group',  'broo_groups_join_group', 10, 2 );
		add_action( 'groups_accept_invite',  'broo_groups_join_group', 10, 2 );
		add_filter( 'broo_condition_step_check_groups_join_group', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}

	add_filter('broo_step_meta_count_enabled', 'broo_step_meta_count_enabled_bp', 10, 2 );
	//add_filter('broo_step_meta_bp_activity_type_enabled', 'broo_step_meta_bp_activity_type_enabled', 10, 2 );

}
add_action( 'broo_init_actions_complete', 'broo_add_bp_actions' );



/**
 * Checks count for the BuddyPress actions action
 *
 * @param unknown $step_result
 * @param unknown $step
 * @param int $user_id
 * @param string $action_name
 * @return boolean
 */
function broo_condition_step_check_bp_action_count( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}
	
	$meta_count = Badgearoo::instance()->api->get_step_meta_value( $step->step_id, 'count' );	

	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . BROO_USER_ACTION_TABLE_NAME
			. ' ua WHERE ua.action_name = "' . esc_sql( $action_name ) . '"';

	$db_count = $wpdb->get_var( $query );

	if ( intval( $db_count ) < intval( $meta_count ) ) {
		return false;
	}

	return $step_result;
}

/**
 * Activity Comment Posted
 *
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function broo_bp_activity_comment_posted( $comment_id, $r, $activity ) {
	
	$user_id = $activity->user_id;
		
	Badgearoo::instance()->api->add_user_action( BP_ACTIVITY_COMMENT_POSTED_ACTION, $user_id, array(
			'activity_id' => $activity->id,
			'activity_type' => $activity->type,
	) );
}


/**
 * Activity Add User Favorite
 * 
 * @param unknown $activity_id
 * @param unknown $user_id
 */
function broo_bp_activity_add_user_favorite( $activity_id, $user_id ) {
	
	Badgearoo::instance()->api->add_user_action( BP_ACTIVITY_ADD_USER_FAVORITE_ACTION, $user_id, array(
			'activity_id' => $activity_id
	) );
}


/**
 * 
 * @param unknown $activity_id
 * @param unknown $post
 * @param unknown $activity_args
 */
function broo_bp_activity_post_type_published( $activity_id, $post, $activity_args ) {
	
	Badgearoo::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION, $activity_args['user_id'], array() );
	
}


/**
 * 
 * @param unknown $friendship_id
 * @param unknown $initiator_user_id
 * @param unknown $friend_user_id
 * @param unknown $friendship
 */
function broo_friends_friendship_accepted( $friendship_id, $initiator_user_id, $friend_user_id, $friendship ) {
	
	Badgearoo::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION, $initiator_user_id, array() );
	
}


/**
 * 
 * @param unknown $friendship_id
 * @param unknown $initiator_user_id
 * @param unknown $friend_user_id
 * @param unknown $friendship
 */
function broo_friends_friendship_requested( $friendship_id, $initiator_user_id, $friend_user_id, $friendship ) {
	
	Badgearoo::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION, $initiator_user_id, array() );
	
}


/**
 * 
 * @param unknown $group_id
 * @param unknown $member
 * @param unknown $group
 */
function broo_groups_create_group( $group_id, $member, $group ) {
	
	$user_id = $member->user_id;

	Badgearoo::instance()->api->add_user_action( BP_GROUPS_CREATE_GROUP_ACTION, $user_id, array(
			'group_id' => $group_id
	) );
}

/**
 * 
 * @param unknown $group_id
 * @param unknown $user_id
 */
function broo_groups_join_group( $group_id, $user_id ) {
	
	Badgearoo::instance()->api->add_user_action( BP_GROUPS_JOIN_GROUP_ACTION, $user_id, array(
			'group_id' => $group_id
	) );
}


/**
 * Defaults actions enabled
 *
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function broo_default_bp_actions_enabled( $actions_enabled ) {
	
	return array_merge( array(
			BP_ACTIVITY_COMMENT_POSTED_ACTION			=> true,
			BP_ACTIVITY_ADD_USER_FAVORITE_ACTION		=> false,
			BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION		=> true,
			BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION		=> false,
			BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION		=> false,
			BP_GROUPS_CREATE_GROUP_ACTION				=> true,
			BP_GROUPS_JOIN_GROUP_ACTION					=> true
	), $actions_enabled );

}
add_filter( 'broo_default_actions_enabled', 'broo_default_bp_actions_enabled', 10, 1 );


/**
 * Displays badges before BuddPress member header meta
 */
function broo_bp_before_member_header_meta() {
	
	$user_id = bp_displayed_user_id();
	
	$points = Badgearoo::instance()->api->get_user_points( $user_id );
	$badges = Badgearoo::instance()->api->get_user_badges( $user_id );
	
	// count badges by id
	$badge_count_lookup = array();
	foreach ( $badges as $index => $badge ) {
		if ( ! isset( $badge_count_lookup[$badge->id] ) ) {
			$badge_count_lookup[$badge->id] = 1;
		} else {
			$badge_count_lookup[$badge->id]++;
			unset( $badges[$index] );
		}
	}
	
	$general_settings = (array) get_option( 'broo_general_settings' );
	
	broo_get_template_part( 'badgearoo-summary', null, true, array(
			'badge_theme' => $general_settings['broo_badge_theme'],
			'badges' => $badges,
			'points' => $points,
			'badge_count_lookup' => $badge_count_lookup,
			'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
	) );
	
}
add_action( 'bp_before_member_header_meta', 'broo_bp_before_member_header_meta' );


/**
 * Shows a points step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function broo_step_meta_bp_activity_type( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'broo_step_meta_bp_activity_type_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$value = Badgearoo::instance()->api->get_step_meta_value( $step_id, 'activity_type' );
		?>
		<span class="step-meta-value">
			<label for="activity_type"><?php _e( 'Activity Type', 'badgearoo' ); ?></label>
			<select name="activity_type">
				<option value="" <?php selected( ! $value ); ?>><?php _e( 'All activity types', 'badgearoo' ); ?></option>
				<option value="activity_comment"><?php _e( 'Replied to a status update', 'badgearoo' ); ?></option>
				<option value="activity_update"><?php _e( 'Posted a status update', 'badgearoo' ); ?></option>
			</select>
		</span>
		<?php
	}
}
//add_action( 'broo_step_meta', 'broo_step_meta_bp_activity_type', 10, 2 );

/**
 * Sets whether step meta post type is enabled for BuddyPress activity types
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function broo_step_meta_bp_activity_type_enabled( $enabled, $action ) {

	if ( $action == BP_ACTIVITY_COMMENT_POSTED_ACTION ) {
		return true;
	}

	return $enabled;
}

function broo_step_meta_count_enabled_bp( $enabled, $action ) {

	if ( $action == BP_ACTIVITY_COMMENT_POSTED_ACTION || $action == BP_ACTIVITY_ADD_USER_FAVORITE_ACTION
			|| $action == BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION || $action == BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION
			|| $action == BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION || $action == BP_GROUPS_CREATE_GROUP_ACTION
			|| $action == BP_GROUPS_JOIN_GROUP_ACTION ) {
		return true;
	}

	return $enabled;
}

?>