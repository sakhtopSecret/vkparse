<?php
// IMPORTANT - For VK callback API 5.50
header("Content-type: text/html, charset=utf-8");


// required files for wp fuctions
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
        echo ""; //confirmation value from api
        exit;
        break;

    // check for new post
    case 'wall_post_new':
        
        $post_text = $data -> object -> text;

        $post_images = [];

        // get image with best quelity
        foreach ($data->object->attachments as $key => $value) {
            
            
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
        // define points for title and text of site post 
        $first_enter = strpos($post_text, PHP_EOL);
        // post title will be first wtring of iriginal post
        $post_title = substr($post_text, 0, $first_enter);
        $post_text = substr($post_text, $first_enter);
        $post_text = trim($post_text);
        

        // download images and generate post text with images below
        foreach ($post_images as $key => $value) {


                $att_id = media_sideload_image($value, 0);
                
                $post_text = $post_text . PHP_EOL . $att_id;

        }

        if ($post_title) {

            $post_data = array(
                'post_title' => $post_title,
                'post_content' => $post_text,
                'post_status' => 'publish'
            );
        
        // place post to site
        $post_id = wp_insert_post($post_data);
 
        set_post_thumbnail($post_id, $att_id);

    }

    break;
}

echo 'OK';
