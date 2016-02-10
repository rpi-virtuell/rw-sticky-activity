<?php

/**
 * Class RW_Sticky_Activity_Core
 *
 * Contains core functions for sticky activity plugin
 *
 */

class RW_Sticky_Activity_Core {

    /**
     *
     */
    function add_sticky_icon() {
        if ( bp_is_group() ) {
          if ( bp_group_is_admin() ) {
                $nonce = wp_create_nonce( 'pin-activity-nonce' );
                $sticky =  bp_activity_get_meta( bp_get_activity_id(), 'rw_sticky_activity' ) ;
                $title = __('Pin activity', RW_Sticky_Activity::$textdomain);
                $class = "sa-button-pin notpinned";
                if ( $sticky == 1 ) {
                    $title = __('Unpin activity', RW_Sticky_Activity::$textdomain);
                    $class = "sa-button-unpin  pinned";
                }
                ?>
                <a href="" class="fa fa-map-marker <?php echo $class; ?>" title="<?php echo $title; ?>" data-post-nonces="<?php echo $nonce; ?>" data-post-id="<?php echo bp_get_activity_id(); ?>"></a>
                <?php
          }
        }
    }

    /**
     *
     */
    function pin_activity() {
        $nonce = isset( $_REQUEST['nonces'] ) ? sanitize_text_field( $_REQUEST['nonces'] ) : 0;
        if ( !wp_verify_nonce( $nonce, 'pin-activity-nonce' ) ) {
            exit( __( 'Not permitted', RW_Sticky_Activity::$textdomain ) );
        }
        $activityID = ( isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) ? $_REQUEST['id'] : '';
        if ( $activityID != '' ) {
            bp_activity_update_meta( $activityID, 'rw_sticky_activity', 1);

        }
        wp_die();
    }

    /**
     *
     */
    function unpin_activity() {
        $nonce = isset( $_REQUEST['nonces'] ) ? sanitize_text_field( $_REQUEST['nonces'] ) : 0;
        if ( !wp_verify_nonce( $nonce, 'pin-activity-nonce' ) ) {
            exit( __( 'Not permitted', RW_Sticky_Activity::$textdomain ) );
        }
        $activityID = ( isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) ? $_REQUEST['id'] : '';
        if ( $activityID != '' ) {
            bp_activity_update_meta( $activityID, 'rw_sticky_activity', 0);

        }
        wp_die();
    }


    /**
     *
     */
    function register_script() {
        wp_register_style( 'rw_sticky_activity_css', plugins_url('/css/style.css', RW_Sticky_Activity::$plugin_base_name ), false, RW_Sticky_Activity::$plugin_version, 'all');
        wp_register_script( 'activity-pin', plugins_url('/js/activity-pin.js', RW_Sticky_Activity::$plugin_base_name ) );
    }

    /**
     *
     */
    function enqueue_style() {
        wp_enqueue_style( 'rw_sticky_activity_css' );
        wp_enqueue_script( 'activity-pin');
    }
}
