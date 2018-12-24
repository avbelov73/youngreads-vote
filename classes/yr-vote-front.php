<?php

new YR_Vote_Front();

class YR_Vote_Front {

    private $version = '0.0.1';

    public function __construct()
    {
        add_shortcode('vote', [$this, 'handle_shortcode']);
        wp_enqueue_script('vote', script_path . 'vote.js', [], '0.0.2', true);
        wp_enqueue_style('vote_plugin', style_path . 'vote.css', [], '0.0.2', 'all');
        add_action( 'wp_ajax_get_regions', [$this, 'get_regions']);
        add_action( 'wp_ajax_nopriv_get_regions', [$this, 'get_regions']);
//        add_enueue_script('vk_auth', 'https://vk.com/js/api/xd_connection.js', [], '2', true);
    }

    function handle_shortcode() {
        return '<div id="vote" class="vote-container"></div>';
    }

    function get_regions()
    {

        $result = (object) [];
        $summary = 0;

        $args = [
            'post_type' => 'city',
            'posts_per_page' => 20,
            'meta_key' => 'vote',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_query' => [
                [
                    'key' => 'vote',
                    'value' => 0,
                    'compare' => '!='
                ]
            ]
        ];

        $top_regions = new WP_Query($args);
        $exclude_ids = [];
        $i = 0;
        $is_firts = true;

        if($top_regions->have_posts()) {
            $top_result = [];
            while ($top_regions->have_posts()) {
                $top_regions->the_post();
                if($is_firts) {
                    $result->max = (int) get_post_meta(get_the_ID(), 'vote', true);
                    $is_firts = false;
                }
                $item = [
                    'rate' => ++$i,
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'vote' => (int) get_post_meta(get_the_ID(), 'vote', true)
                ];
                array_push($top_result, (object)$item);
                array_push($exclude_ids, get_the_ID());
                $summary = $summary + (int) get_post_meta(get_the_ID(), 'vote', true);
            }
            $result->top = $top_result;
        }

        $second_args = [
            'post_type' => 'city',
            'posts_per_page' => -1,
            'post__not_in' => $exclude_ids,
            'meta_key' => 'vote',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        ];

        $other_regions = new WP_Query($second_args);

        if($other_regions->have_posts()) {
            $other_result = [];
            while ($other_regions->have_posts()) {
                $other_regions->the_post();
                $item = [
                    'rate' => ++$i,
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'vote' => (int) get_post_meta(get_the_ID(), 'vote', true)
                ];
                array_push($other_result, (object)$item);
                $summary = $summary + (int) get_post_meta(get_the_ID(), 'vote', true);
            }

            $result->other = array_chunk($other_result, 10);
        }

        $result->summary = $summary;

        wp_send_json($result);
    }
}
