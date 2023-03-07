<?php

header("Content-type: text/html, charset=utf-8");

require_once '../wp-load.php';
require_once ABSPATH . '/wp-admin/includes/image.php';
require_once ABSPATH . '/wp-admin/includes/file.php';
require_once ABSPATH . '/wp-admin/includes/media.php';

if(!isset($_REQUEST)) {
    return;
}
$data = json_decode(file_get_contents('php://input'));


switch ($data -> type) {

    case 'confirmation':
        echo "b3316b07";
        exit;
        break;

    case 'wall_post_new':
        
        $post_text = $data -> object -> text;

        $post_images = [];
        
        foreach($data->object->attachments as $key => $value){
            
            if ($value -> type == 'photo'){

                if ($value -> photo -> photo_2560) {

                    $post_images[] = $value -> photo -> photo_2560;
                } else if ($value -> photo -> photo_1280) {

                    $post_images[] = $value -> photo -> photo_1280;
                } else if ($value -> photo -> photo_807) {

                    $post_images[] = $value -> photo -> photo_807;
                } else if ($value -> photo -> photo_604) {

                    $post_images[] = $value -> photo -> photo_604;
                } else if ($value -> photo -> photo_130) {

                    $post_images[] = $value -> photo -> photo_130;
                } else if ($value -> photo -> photo_75) {

                    $post_images[] = $value -> photo -> photo_75;
                }
            }
        }
        $first_enter = strpos($post_text, PHP_EOL);
        $post_title = substr($post_text, 0, $first_enter);
        $post_text = substr($post_text, $first_enter);
        $post_text = trim($post_text);
        


        foreach ($post_images as $key => $value) {


                $att_id = media_sideload_image($value, 0, null, 'id');

                $thumbnail_url  = wp_get_attachment_image($att_id, 'medium', $icon = false, $attr = '' );

                $full_url = wp_get_attachment_url($att_id);

                // html code of a page after title

                // show post text and start new line
                $post_text = $post_text . PHP_EOL . 
                    '<div class="vk-images">'.
                    // add link to full_size image
                    '<a href="'.$full_url.'">'.
                    // show medium size image on page
                    $thumbnail_url.
                    '</a>'.
                    '</div>';
                
                

         }

        if ($post_title) {

            $post_data = array(
                'post_title' => $post_title,
                'post_content' => $post_text,
                'post_status' => 'publish',
                'post_category' => array(10)
            );

        $post_id = wp_insert_post($post_data);
 
        set_post_thumbnail($post_id, $thumbnail_url);

    }

    break;
}

echo 'OK';
