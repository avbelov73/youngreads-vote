<?php

new YR_Vote_Admin();

class YR_Vote_Admin {

    public function __construct()
    {
        add_menu_page( 'Рейтинг регионов', 'Рейтинг регионов', 'edit_others_posts', 'vote-rate', [$this, 'render_page']);
        add_action('wp_ajax_add_city_ajax', [$this, 'add_city']);
        add_action('wp_ajax_update_vote_ajax', [$this, 'update_vote']);
        add_action('wp_ajax_remove_city_ajax', [$this, 'remove_city']);
    }

    function render_page()
    {
        include template_path . 'admin.php';
    }

    function add_city()
    {
        $errors = '';

        $nonce = $_POST['nonce'];
        $user_id = get_current_user_id();
        if (!wp_verify_nonce($nonce, 'add_city')) {
            $errors .= 'Данные отправлены с левой страницы ';
        }

        $title = strip_tags($_POST['post_title']);

        if (!$title) $errors .= 'Не заполнено поле "Название региона"';

        if (!$errors) {
            $fields = [
                'post_type' => 'city',
                'post_title' => $title,
                'post_status' => 'publish',
                'post_author' => $user_id
            ];

            $post_id = wp_insert_post($fields);
        }

        add_post_meta($post_id, 'vote', 0);

        if ($errors)
            wp_send_json_error($errors);
        else
            wp_send_json_success('Все прошло отлично! Добавлено ID:'.$post_id);

        die();
    }

    function update_vote()
    {
        $post_id = (int) $_POST['post_id'];
        $vote = (int) $_POST['vote'];

        update_post_meta($post_id, 'vote', $vote);
    }

    function remove_city()
    {
        $post_id = (int) $_POST['post_id'];
        $deleted = wp_delete_post($post_id);
    }


}
