<?php

new YR_Vote_Type();

class YR_Vote_Type
{
    public function __construct()
    {
        // создаем кастомный тип записи, в котором и будет происходить голосование
        $this->add_post_type();
    }

    function add_post_type()
    {
        $labels = array(
            'name' => 'Города',
            'singular_name' => 'Город', // админ панель Добавить->Функцию
            'add_new' => 'Добавить город',
            'add_new_item' => 'Добавить новый город', // заголовок тега <title>
            'edit_item' => 'Редактировать город',
            'new_item' => 'Новый город',
            'all_items' => 'Все города',
            'view_item' => 'Просмотр города на сайте',
            'search_items' => 'Искать город',
            'not_found' =>  'Город не найден.',
            'not_found_in_trash' => 'В корзине нет городов.',
            'menu_name' => 'Рейтинг городов' // ссылка в меню в админке
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'show_ui' => false, // показывать интерфейс в админке
            'supports' => ['title', 'custom-fields']
        );
        register_post_type('city', $args);
    }
}
