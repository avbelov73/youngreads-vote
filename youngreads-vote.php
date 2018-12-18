<?php
/*
* Plugin Name: [Youngreads] Голосование за регионы
* Description: Управление и вывод результатов голосования за регионы
* Version: 0.1
* Author: A. Belov
*/

if (!defined('WPINC')) die;


add_action('init', function () {
    // создаем тип материала и настраиваем кастомные поля
    include_once('classes/yr-vote-type.php');

    // инициализируем административный раздел плагина
    require_once('classes/yr-vote-admin.php');

    // вывод таблицы с созданными городами
    require_once ('classes/yr-vote-admin-table.php');
});

define('template_path', plugin_dir_path(__FILE__) . 'templates/');
