<?php

header("Content-type: text/html, charset=utf-8");

require_once '../wp-load.php';
require_once ABSPATH . '/wp-admin/includes/image.php';
require_once ABSPATH . '/wp-admin/includes/file.php';
require_once ABSPATH . '/wp-admin/includes/media.php';

if(!isset($_REQUEST)) {
    return;
}
// decode json post from VK to array
$data = json_decode(file_get_contents('php://input'));


switch ($data -> type) {

    case 'confirmation':
        echo "b3316b07";
        exit;
        break;

    // do when new post    
    case 'wall_post_new':
        
        $post_text = $data -> object -> text;

        $post_images = [];
        $post_videos = [];
        $post_files = [];
        
        foreach($data->object->attachments as $key => $value){
            
            // download max image resolution of all attached images
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

            // set url for all attached videos
            if ($value -> type == 'video'){
                $owner_id = $value -> video -> owner_id;
                $video_id = $value -> video -> id;
                $post_videos[] = 'https://vk.com/video'.$owner_id._.$video_id;
            }
            // set url for all attacched files
            if ($value -> type == 'doc'){
                                //name of file + newline + url
                $post_files [] = $value -> doc -> title . PHP_EOL . $value -> doc -> url;
            }
        }
        $first_enter = strpos($post_text, PHP_EOL);

        // set the title of page
        $post_title = substr($post_text, 0, $first_enter);

        $post_text = substr($post_text, $first_enter);
        //delete spaces
        $post_text = trim($post_text);
        
        $images_html = [];
        $videos_html = [];
        $files_html = [];

        // create html code for all images
        foreach ($post_images as $key => $value) {

            $att_id = media_sideload_image($value, 0, null, 'id');

            $thumbnail_url  = wp_get_attachment_image($att_id, 'medium', $icon = false, $attr = '' );

            $full_url = wp_get_attachment_url($att_id);

            $images_html[] = '<div class="vk-images">'.'<a href="'.$full_url.'">'.$thumbnail_url.'</a>'.'</div>';
        }

        // create html code for all videos
        foreach ($post_videos as $key => $value){

            $videos_html[] = '<div class="vk-videos">'.'<a href="'.$value.'">'.'Видео к записи'.'</a>'.'</div>';
        }

        foreach ($post_files as $key => $value) {

            $first_file_enter = strpos($value, PHP_EOL);
            // from start to first \n - file_name
            $file_name = substr($value, 0, $first_file_enter); 
            // from first \n - file url
            $file_url = substr($value, $first_file_enter);
            //delete spaces
            $file_url = trim($file_url);
            $files_html[] = '<div class="vk-files">'.'<a href="'.$file_url.'">'.$file_name.'</a>'.'</div>';

        }

        // convert array to string
        $images_html = implode($images_html);
        $videos_html = implode($videos_html);
        $files_html = implode($files_html);

        // create html code of all page via concatenating all values
        $post_text = $post_text . PHP_EOL . 
        $images_html .
        '<div>' ."Ознакомтесь с приложенными видео". $videos_html .'</div>'.
        '<div>' ."Ознакомтесь с приложенными файлами". $files_html .'</div>';

        // set data of new post
        if ($post_title) {

            $post_data = array(
                'post_title' => $post_title,
                'post_content' => $post_text,
                'post_status' => 'publish',
                'post_category' => array(10)
            );
        
        // publish new post
        $post_id = wp_insert_post($post_data);
 
        set_post_thumbnail($post_id, $thumbnail_url);

    }
    break;
}

echo 'OK';
