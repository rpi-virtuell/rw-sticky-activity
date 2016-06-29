<?php

/**
 * Class RW_Sticky_Activity_Core
 *
 * Contains core functions for sticky activity plugin
 *
 */

class RW_Sticky_Activity_Core {


    function bp_before_activity_post_form() {
        global $wpdb;

        // outputs the content of the widget
        if ( bp_is_group() ) {
            $meta_query_args = array(
                'relation' => 'AND', // Optional, defaults to "AND"
                array(
                    'key'     => 'rw_sticky_activity',
                    'value'   => '1',
                    'compare' => '='
                )
            );
            if ( function_exists( 'bb_bp_activity_url_filter' ) ) {
                // deactivate BuddyBoss Wall activity url preview
                remove_action('bp_get_activity_content_body', 'bb_bp_activity_url_filter');
            }
            add_filter( 'bp_activity_excerpt_length', function() { return 99999; }); ?>
            <aside id="widget_sticky_acivity" class="widget_sticky_acivity">
                <div class="inner">
            <?php
            if ( bp_has_activities( array( 'meta_query' => $meta_query_args) ) ) : ?>
                <?php while ( bp_activities() ) : bp_the_activity(); ?>
                    <div class="buddypress-sa">
                        <div id="factivity-stream">
                            <div class="activity-list">
                                <div class="activity-content" style="margin-left: 0px; margin-top: 10px;">
                                    <?php
                                    $nonce = wp_create_nonce( 'pin-activity-nonce' );
                                    $title = __('Unpin activity', RW_Sticky_Activity::$textdomain);
                                    $class = "sa-button-unpin  pinned";
                                    ?>
                                    <a href="" class="fa fa-map-marker icon-button sa-button <?php echo $class; ?>" title="<?php echo $title; ?>" data-post-nonces="<?php echo $nonce; ?>" data-post-id="<?php echo bp_get_activity_id(); ?>"></a>
                                    <?php if ( bp_activity_has_content() && bp_get_activity_type() != 'bbp_topic_create' && bp_get_activity_type() != 'bbp_reply_create' ) : ?>
                                        <div class="activity-inner">
                                            <?php bp_activity_content_body(); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    if ( bp_get_activity_type() == 'bp_doc_edited' ) {
                                        ?>
                                        <div class="activity-inner"><p>
                                                <?php
                                                $doc = get_post ( url_to_postid( bp_get_activity_feed_item_link() ) );
                                                echo __('Doc: ', RW_Sticky_Activity::$textdomain);
                                                echo "<a href='".get_permalink( $doc->ID ). "'>";
                                                echo $doc->post_title;
                                                echo "</a>";
                                                ?></p>
                                        </div>
                                        <?php
                                    }

                                    // New forum topic created
                                    if ( bp_get_activity_type() == 'bbp_topic_create' ) {
                                        // url_to_postid fails on permalinks like http://gruppen.domain.tld/groups/frank-testgruppe/forum/topic/neues-thema/ !!!
                                        ?>
                                        <div class="activity-inner"><p>
                                                <?php
                                                $link = bp_get_activity_feed_item_link();
                                                $guid =  substr( $link, strpos( $link, "/forum/topic" ) + 6 );
                                                $topicid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid like '%%%s%%'", $guid ) );
                                                $topic = get_post ( $topicid );
                                                echo __('Forum new topic: ', RW_Sticky_Activity::$textdomain);
                                                echo "<a href='".get_permalink( $topic->ID ). "'> ";
                                                echo $topic->post_title;
                                                echo "</a><br>";
                                                ?></p>
                                        </div>
                                        <?php
                                    }


                                    // New forum reply
                                    if ( bp_get_activity_type() == 'bbp_reply_create' ) {
                                        // url_to_postid fails on permalinks like http://gruppen.domain.tld/groups/frank-testgruppe/forum/topic/neues-thema/ !!!
                                        ?>
                                        <div class="activity-inner"><p>
                                                <?php
                                                $link = bp_get_activity_feed_item_link();
                                                $id =  substr( $link, strpos( $link, "/#post-" ) + 7 );
                                                $topic = get_post ( $id );
                                                echo __('Forum reply: ', RW_Sticky_Activity::$textdomain);
                                                echo "<a href='".get_permalink( $topic->ID ). "'> ";
                                                $parent = get_post( $topic->post_parent );
                                                echo $parent->post_title;
                                                echo "</a><br>";
                                                ?></p>
                                        </div>
                                        <?php
                                    }


                                    ?>
                                    <div class="activity-header">
                                        <?php
                                        $userid = bp_get_activity_user_id();
                                        $user = get_user_by( 'id', $userid);
                                        echo "(" . $user->nickname . ")";
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
            </div>
            </aside>
            <?php
            if ( function_exists( 'bb_bp_activity_url_filter' ) ) {
                // activate BuddyBoss Wall activity url preview
                add_action('bp_get_activity_content_body', 'bb_bp_activity_url_filter');
            }
        }
    }
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
     *
     */
    function pin_activity() {
        global $wpdb;
        $nonce = isset( $_REQUEST['nonces'] ) ? sanitize_text_field( $_REQUEST['nonces'] ) : 0;
        if ( !wp_verify_nonce( $nonce, 'pin-activity-nonce' ) ) {
            exit( __( 'Not permitted', RW_Sticky_Activity::$textdomain ) );
        }
        $activityID = ( isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) ? $_REQUEST['id'] : '';
        if ( $activityID != '' ) {
            bp_activity_update_meta( $activityID, 'rw_sticky_activity', 1);

        }

        $meta_query_args = array(
        'relation' => 'AND', // Optional, defaults to "AND"
        array(
        'key'     => 'rw_sticky_activity',
        'value'   => '1',
        'compare' => '='
        )
        );
        if ( function_exists( 'bb_bp_activity_url_filter' ) ) {
        // deactivate BuddyBoss Wall activity url preview
        remove_action('bp_get_activity_content_body', 'bb_bp_activity_url_filter');
        }
        add_filter( 'bp_activity_excerpt_length', function() { return 99999; });
        if ( bp_has_activities( array( 'meta_query' => $meta_query_args) ) ) : ?>
            <?php while ( bp_activities() ) : bp_the_activity(); ?>
                <div class="buddypress-sa">
                    <div id="factivity-stream">
                        <div class="activity-list">
                            <div class="activity-content" style="margin-left: 0px;">
                                <?php
                                $nonce = wp_create_nonce( 'pin-activity-nonce' );
                                $title = __('Unpin activity', RW_Sticky_Activity::$textdomain);
                                $class = "sa-button-unpin  pinned";
                                ?>
                                <a href="" class="fa fa-map-marker icon-button sa-button <?php echo $class; ?>" title="<?php echo $title; ?>" data-post-nonces="<?php echo $nonce; ?>" data-post-id="<?php echo bp_get_activity_id(); ?>"></a>
                                <?php if ( bp_activity_has_content() && bp_get_activity_type() != 'bbp_topic_create' && bp_get_activity_type() != 'bbp_reply_create' ) : ?>
                                    <div class="activity-inner">
                                        <?php bp_activity_content_body(); ?>
                                    </div>
                                <?php endif; ?>
                                <?php
                                if ( bp_get_activity_type() == 'bp_doc_edited' ) {
                                    ?>
                                    <div class="activity-inner"><p>
                                            <?php
                                            $doc = get_post ( url_to_postid( bp_get_activity_feed_item_link() ) );
                                            echo __('Doc: ', RW_Sticky_Activity::$textdomain);
                                            echo "<a href='".get_permalink( $doc->ID ). "'>";
                                            echo $doc->post_title;
                                            echo "</a>";
                                            ?></p>
                                    </div>
                                    <?php
                                }

                                // New forum topic created
                                if ( bp_get_activity_type() == 'bbp_topic_create' ) {
                                    // url_to_postid fails on permalinks like http://gruppen.domain.tld/groups/frank-testgruppe/forum/topic/neues-thema/ !!!
                                    ?>
                                    <div class="activity-inner"><p>
                                            <?php
                                            $link = bp_get_activity_feed_item_link();
                                            $guid =  substr( $link, strpos( $link, "/forum/topic" ) + 6 );
                                            $topicid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid like '%%%s%%'", $guid ) );
                                            $topic = get_post ( $topicid );
                                            echo __('Forum new topic: ', RW_Sticky_Activity::$textdomain);
                                            echo "<a href='".get_permalink( $topic->ID ). "'> ";
                                            echo $topic->post_title;
                                            echo "</a><br>";
                                            ?></p>
                                    </div>
                                    <?php
                                }


                                // New forum reply
                                if ( bp_get_activity_type() == 'bbp_reply_create' ) {
                                    // url_to_postid fails on permalinks like http://gruppen.domain.tld/groups/frank-testgruppe/forum/topic/neues-thema/ !!!
                                    ?>
                                    <div class="activity-inner"><p>
                                            <?php
                                            $link = bp_get_activity_feed_item_link();
                                            $id =  substr( $link, strpos( $link, "/#post-" ) + 7 );
                                            $topic = get_post ( $id );
                                            echo __('Forum reply: ', RW_Sticky_Activity::$textdomain);
                                            echo "<a href='".get_permalink( $topic->ID ). "'> ";
                                            $parent = get_post( $topic->post_parent );
                                            echo $parent->post_title;
                                            echo "</a><br>";
                                            ?></p>
                                    </div>
                                    <?php
                                }


                                ?>
                                <div class="activity-header">
                                    <?php
                                    $userid = bp_get_activity_user_id();
                                    $user = get_user_by( 'id', $userid);
                                    echo "(" . $user->nickname . ")";
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif;
        if ( function_exists( 'bb_bp_activity_url_filter' ) ) {
            // activate BuddyBoss Wall activity url preview
            add_action('bp_get_activity_content_body', 'bb_bp_activity_url_filter');
        }

        wp_die();
    }

    /**
     *
     */
    function unpin_activity() {
        global $wpdb;
        $nonce = isset( $_REQUEST['nonces'] ) ? sanitize_text_field( $_REQUEST['nonces'] ) : 0;
        if ( !wp_verify_nonce( $nonce, 'pin-activity-nonce' ) ) {
            exit( __( 'Not permitted', RW_Sticky_Activity::$textdomain ) );
        }
        $activityID = ( isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) ? $_REQUEST['id'] : '';
        if ( $activityID != '' ) {
            bp_activity_update_meta( $activityID, 'rw_sticky_activity', 0);

        }
        $meta_query_args = array(
            'relation' => 'AND', // Optional, defaults to "AND"
            array(
                'key'     => 'rw_sticky_activity',
                'value'   => '1',
                'compare' => '='
            )
        );
        if ( function_exists( 'bb_bp_activity_url_filter' ) ) {
            // deactivate BuddyBoss Wall activity url preview
            remove_action('bp_get_activity_content_body', 'bb_bp_activity_url_filter');
        }
        add_filter( 'bp_activity_excerpt_length', function() { return 99999; });
        if ( bp_has_activities( array( 'meta_query' => $meta_query_args) ) ) : ?>
            <?php while ( bp_activities() ) : bp_the_activity(); ?>
                <div class="buddypress-sa">
                    <div id="factivity-stream">
                        <div class="activity-list">
                            <div class="activity-content" style="margin-left: 0px;">
                                <?php
                                $nonce = wp_create_nonce( 'pin-activity-nonce' );
                                $title = __('Unpin activity', RW_Sticky_Activity::$textdomain);
                                $class = "sa-button-unpin  pinned";
                                ?>
                                <a href="" class="fa fa-map-marker icon-button sa-button <?php echo $class; ?>" title="<?php echo $title; ?>" data-post-nonces="<?php echo $nonce; ?>" data-post-id="<?php echo bp_get_activity_id(); ?>"></a>
                                <?php if ( bp_activity_has_content() && bp_get_activity_type() != 'bbp_topic_create' && bp_get_activity_type() != 'bbp_reply_create' ) : ?>
                                    <div class="activity-inner">
                                        <?php bp_activity_content_body(); ?>
                                    </div>
                                <?php endif; ?>
                                <?php
                                if ( bp_get_activity_type() == 'bp_doc_edited' ) {
                                    ?>
                                    <div class="activity-inner"><p>
                                            <?php
                                            $doc = get_post ( url_to_postid( bp_get_activity_feed_item_link() ) );
                                            echo __('Doc: ', RW_Sticky_Activity::$textdomain);
                                            echo "<a href='".get_permalink( $doc->ID ). "'>";
                                            echo $doc->post_title;
                                            echo "</a>";
                                            ?></p>
                                    </div>
                                    <?php
                                }

                                // New forum topic created
                                if ( bp_get_activity_type() == 'bbp_topic_create' ) {
                                    // url_to_postid fails on permalinks like http://gruppen.domain.tld/groups/frank-testgruppe/forum/topic/neues-thema/ !!!
                                    ?>
                                    <div class="activity-inner"><p>
                                            <?php
                                            $link = bp_get_activity_feed_item_link();
                                            $guid =  substr( $link, strpos( $link, "/forum/topic" ) + 6 );
                                            $topicid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid like '%%%s%%'", $guid ) );
                                            $topic = get_post ( $topicid );
                                            echo __('Forum new topic: ', RW_Sticky_Activity::$textdomain);
                                            echo "<a href='".get_permalink( $topic->ID ). "'> ";
                                            echo $topic->post_title;
                                            echo "</a><br>";
                                            ?></p>
                                    </div>
                                    <?php
                                }


                                // New forum reply
                                if ( bp_get_activity_type() == 'bbp_reply_create' ) {
                                    // url_to_postid fails on permalinks like http://gruppen.domain.tld/groups/frank-testgruppe/forum/topic/neues-thema/ !!!
                                    ?>
                                    <div class="activity-inner"><p>
                                            <?php
                                            $link = bp_get_activity_feed_item_link();
                                            $id =  substr( $link, strpos( $link, "/#post-" ) + 7 );
                                            $topic = get_post ( $id );
                                            echo __('Forum reply: ', RW_Sticky_Activity::$textdomain);
                                            echo "<a href='".get_permalink( $topic->ID ). "'> ";
                                            $parent = get_post( $topic->post_parent );
                                            echo $parent->post_title;
                                            echo "</a><br>";
                                            ?></p>
                                    </div>
                                    <?php
                                }


                                ?>
                                <div class="activity-header">
                                    <?php
                                    $userid = bp_get_activity_user_id();
                                    $user = get_user_by( 'id', $userid);
                                    echo "(" . $user->nickname . ")";
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif;
        if ( function_exists( 'bb_bp_activity_url_filter' ) ) {
            // activate BuddyBoss Wall activity url preview
            add_action('bp_get_activity_content_body', 'bb_bp_activity_url_filter');
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
