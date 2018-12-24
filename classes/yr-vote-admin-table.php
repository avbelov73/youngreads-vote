<?php

    if(!class_exists('WP_List_Table')){
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }

    class TT_Example_List_Table extends WP_List_Table {

        var $data = [];

	    function __construct(){
	        global $status, $page;

	        //Set parent defaults

            parent::__construct( array(


                'singular'  => 'регион',     //singular name of the listed records
                'plural'    => 'региона',    //plural name of the listed records
                'ajax'      => true        //does this table support ajax?

            ) );

            $args = [
                'post_type' => 'city',
                'posts_per_page' => -1,
                'meta_key' => 'vote',
                'orderby' => 'meta_value_num',
                'order' => 'DESC'
            ];

            $query = new WP_Query( $args );

            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $item = [
                        'ID' => get_the_ID(),
                        'title' => get_the_title(),
                        'vote' => get_post_meta(get_the_ID(), 'vote', true)
                    ];
                    array_push($this->data, $item);
                }
            }
	    }

	    function column_default($item, $column_name){
	        switch($column_name){
	            case 'vote':
                    return '<input type="number" name="vote" value="' . $item['vote'] . '"/><button type="button" class="refresh" data-id="' . $item['ID'] . '">Обновить</a>';

                default:
                    return print_r($item,true); //Show the whole array for troubleshooting purposes

	        }

	    }
	    function column_title($item){
	        //Build row actions
	        $actions = [
//	            'edit'      => sprintf('<a href="?page=%s&action=%s&city=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
                'delete'    => '<a href="#" class="remove-city" data-id="' . $item['ID'] . '">Удалить</a>',
            ];
	        //Return the title contents
	        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
                /*$1%s*/ $item['title'],
                /*$2%s*/ $item['ID'],
                /*$3%s*/ $this->row_actions($actions)
            );
	    }

	    function column_cb($item){
	        return sprintf(
	            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
                /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
            );
	    }
	    function get_columns(){
	        $columns = array(
	            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
                'title'     => 'Город',
                'vote'    => 'Количество голосов',
            );
	        return $columns;
	    }

	    function get_sortable_columns() {
            $sortable_columns = [
                'title'     => ['title',false],     //true means it's already sorted
                'vote'    => ['rating',false],
            ];
            return $sortable_columns;
	    }

	    function get_bulk_actions() {
	        $actions = [
	            'delete'    => 'Delete'
            ];
	        return $actions;
	    }

	    function process_bulk_action() {
        //Detect when a bulk action is being triggered...
            if( 'delete'===$this->current_action() ) {
                wp_die('Items deleted (or they would be if we had items to delete)!');
            }
	    }

	    function prepare_items() {
	        global $wpdb; //This is used only if making any database queries

            $per_page = 15;
            $columns = $this->get_columns();
            $hidden = [];
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = [$columns, $hidden, $sortable];
            $this->process_bulk_action();
            $data = $this->data;

            function usort_reorder($a,$b){
                $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
                $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
                return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
            }
            usort($data, 'usort_reorder');

            $current_page = $this->get_pagenum();
	        $total_items = count($data);
	        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
	        $this->items = $data;

	        $this->set_pagination_args( array(
	            'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
            ) );
	    }
    }
