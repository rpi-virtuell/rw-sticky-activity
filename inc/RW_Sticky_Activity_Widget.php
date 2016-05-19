<?php
/**
 * Class RW_Sticky_Activity_Widget
 *
 * Contains the widget
 *
 * @package   RW Sticky Activity
 * @author    Frank Staude
 * @license   GPL-2.0+
 * @link      https://github.com/rpi-virtuell/rw-sticky-activity
 */
class RW_Sticky_Activity_Widget extends WP_Widget
{

    public function __construct() {
        $widget_ops = array(
            'class_name' => 'sticky_acivity',
            'description' => __('List sticky activity in current buddypress group', '' ),
        );
        parent::__construct( 'sticky_acivity', 'Sticky Acivity', $widget_ops );
    }

    /**
     * Outputs the content of the widget
     *
     * @since   0.0.1
     * @param   array $args
     * @param   array $instance
     */
    public function widget( $args, $instance ) {
        global $wpdb;

        // outputs the content of the widget
        if ( bp_is_group() ) {
            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
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
                                            echo "<a href='".get_permalink( $doc->ID ). "'>'";
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
            echo $args['after_widget'];
        }
    }

    /**
     * Outputs the options form on admin
     *
     * @since   0.0.1
     * @param   array $instance The widget options
     */
    public function form( $instance ) {
        // outputs the options form on admin
        $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
        $title = sanitize_text_field( $instance['title'] );
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @since   0.0.1
     * @param   array $new_instance The new options
     * @param   array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        return $instance;
    }

}

