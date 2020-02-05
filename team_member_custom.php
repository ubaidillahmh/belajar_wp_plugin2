<?php

    add_action( 'admin_init', 'team_member_admin' );

    function team_member_admin() {
        add_meta_box( 'team_member_meta_box',
            'member Details',
            'display_team_member_meta_box',
            'team_members', 'normal', 'high'
        );
    }

    function display_team_member_meta_box( $team_member ) {
        
        $member_position = esc_html( get_post_meta( $team_member->ID, 'member_position', true ) );
        $member_email = esc_html( get_post_meta( $team_member->ID, 'member_email', true ) );
        $member_phone = esc_html( get_post_meta( $team_member->ID, 'member_phone', true ) );
        $member_website = esc_html( get_post_meta( $team_member->ID, 'member_website', true ) );
        $member_image = esc_html( get_post_meta( $team_member->ID, 'member_image', true ) );

        // get_template_part('content', 'single');
        $image[] = get_post_meta($team_member->ID, 'member_image', true);

        ?>
        <table>
            <tr>
                <td style="width: 100%">Position</td>
                <td><input type="text" size="40" name="team_member_position" value="<?php echo $member_position; ?>" /></td>
            </tr>
            <tr>
                <td style="width: 100%">Email</td>
                <td><input type="email" size="40" name="team_member_email" value="<?php echo $member_email; ?>" /></td>
            </tr>
            <tr>
                <td style="width: 100%">Phone</td>
                <td><input type="text" size="40" name="team_member_phone" value="<?php echo $member_phone; ?>" /></td>
            </tr>
            <tr>
                <td style="width: 100%">Website</td>
                <td><input type="text" size="40" name="team_member_website" value="<?php echo $member_website; ?>" /></td>
            </tr>
            <tr>
                <?php
                    wp_nonce_field(plugin_basename(__FILE__), 'wp_custom_attachment_nonce');
                    // echo $image['url'];
                ?>
                <td style="width: 100%">Image</td>
                <td><input type="file" size="40" id="wp_custom_attachment" name="team_member_image" /></td>
            </tr>
        </table>
        <?php
    }

    function update_edit_form() {
        echo ' enctype="multipart/form-data"';
    } 

    add_action('post_edit_form_tag', 'update_edit_form');

    add_action( 'save_post', 'add_team_member_fields', 10, 2 );

    function add_team_member_fields( $team_member_id, $team_member ) {
        // Check post type for member team
        if ( $team_member->post_type == 'team_members' ) {
            // Store data in post meta table if present in post data
            if ( isset( $_POST['team_member_position'] ) && $_POST['team_member_position'] != '' ) {
                update_post_meta( $team_member_id, 'member_position', $_POST['team_member_position'] );
            }
            if ( isset( $_POST['team_member_email'] ) && $_POST['team_member_email'] != '' ) {
                update_post_meta( $team_member_id, 'member_email', $_POST['team_member_email'] );
            }
            if ( isset( $_POST['team_member_phone'] ) && $_POST['team_member_phone'] != '' ) {
                update_post_meta( $team_member_id, 'member_phone', $_POST['team_member_phone'] );
            }
            if ( isset( $_POST['team_member_website'] ) && $_POST['team_member_website'] != '' ) {
                update_post_meta( $team_member_id, 'member_website', $_POST['team_member_website'] );
            }
            if (! empty($_FILES['team_member_image']['name'])) {

                // $upload = wp_upload_bits($_FILES['team_member_image']['name'],null, file_get_contents($_FILES['team_member_image']['tmp_name']));
                if ( ! function_exists( 'wp_handle_upload' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }

                $file   = $_FILES['team_member_image']; 
                $upload = wp_handle_upload($file, array('test_form' => false));
                // var_dump($upload);die;
                // $upload = wp_upload_bits($_FILES['team_member_image']['name'], null, $_FILES['team_member_image']['tmp_name']);
                if($upload['url'] != null)
                {
                    update_post_meta( $team_member_id, 'member_image', $upload['url'] );
                }
            }
        }
    }

    function team_member_list($pos, $ema, $web)
    {
        get_header();
        $args = array('post_type' => 'team_members');
        $query = new WP_Query($args);
        // var_dump(json_encode($query));
        $i = 1;
        while ( $query->have_posts() ) : $query->the_post();
        $id         = $query->post->ID;
        $position   = get_post_meta($id, 'member_position', true);
        $email      = get_post_meta($id, 'member_email', true);
        $website    = get_post_meta($id, 'member_website', true); 
        $image      = get_post_meta($id, 'member_image', true);

        echo '<div class="grid-item">';

        if($image != null)
        {
            echo    '<p><img href="'.$image.'" alt="Photo Profile"/></p>';
        }

        echo    '<p>'.the_title().'</p>';

        if ($pos != '0') {
            echo    '<p>'.$position.'</p>';
        }

        if ($ema != '0') {
            echo    '<p>'.$email.'</p>';
        }
        if ($web != '0') {
            echo    '<p>'.$website.'</p>';
        }
        echo '</div>';
        endwhile;
    }

    function team_member_shortcode($atts, $content = null)
    {
        extract( shortcode_atts(
            array(
              'posi' => '',
              'emai' => '',
              'webs' => '',
            ), $atts )
          );
        // die('x');
        ob_start();
        // echo 'bb';
        team_member_list($posi, $emai, $webs);

        return ob_get_clean();
    }
    
    // contoh shortcode [team_member_short posi="0"] -> apabila posisi kosong
    add_shortcode('team_member_short', 'team_member_shortcode');

?>