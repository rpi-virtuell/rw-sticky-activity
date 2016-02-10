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
        // outputs the content of the widget
        if ( bp_is_group() ) {
            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
            //echo __( 'Hello, World!', 'text_domain' );
            $meta_query_args = array(
                'relation' => 'AND', // Optional, defaults to "AND"
                array(
                    'key'     => 'rw_sticky_activity',
                    'value'   => '1',
                    'compare' => '='
                )
            );

            if ( bp_has_activities( array( 'meta_query' => $meta_query_args) ) ) : ?>
                <?php while ( bp_activities() ) : bp_the_activity(); ?>
                    <div id="buddypress">
                        <div id="factivity-stream">
                            <div class="activity-list">
                                <div class="activity-content" style="margin-left: 0px;">
                                    <div class="activity-header">
                                        <?php bp_activity_action(); ?>
                                    </div>
                                    <?php if ( bp_activity_has_content() ) : ?>
                                        <div class="activity-inner">
                                            <?php bp_activity_content_body(); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    $nonce = wp_create_nonce( 'pin-activity-nonce' );
                                    $title = __('Unpin activity', RW_Sticky_Activity::$textdomain);
                                    $class = "sa-button-unpin  pinned";
                                    ?>
                                    <a href="" class="fa fa-map-marker button icon-button bp-secondary-action sa-button <?php echo $class; ?>" title="<?php echo $title; ?>" data-post-nonces="<?php echo $nonce; ?>" data-post-id="<?php echo bp_get_activity_id(); ?>"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif;

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

