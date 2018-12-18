<div class="wrap">
    <h1><?php echo get_admin_page_title() ?></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <?php
                    $testListTable = new TT_Example_List_Table();
                    //Fetch, prepare, sort, and filter our data...
                    $testListTable->prepare_items();
                ?>
                <?php $testListTable->display() ?>
            </div>
            <div class="postbox-container" id="postbox-container-1">
                <div id="add_city" class="postbox metabox-holder" style="padding: 0">
                    <h2 class="hndle">Добавить город</h2>
                    <div class="inside">
                        <div class="main">
                            <input type="hidden" id="city_nonce" name="nonce" value="<?php echo wp_create_nonce('add_city') ?>">
                            <input type="text" id="city_name" name="post_title" placeholder="Название города" required />
                            <button type="button" id="add_btn" class="button btn-primary">Добавить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function ($) {
        var btn = $('#add_btn');
        btn.click(function (event) {
            event.preventDefault();
            var title = $('#city_name').val();
            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'add_city_ajax',
                    post_title: title,
                    nonce: $('#city_nonce').val()
                },
                success: function (value) {
                    console.log(value);
                    $('#city_name').val('');

                }
            })
        });
        var refresh = $('.refresh');
        refresh.click(function () {
            var vote = $(this).prev().val();
            var id = $(this).attr('data-id');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'update_vote_ajax',
                    post_id: id,
                    vote: vote
                },
                success: function () {
                    console.log('success');
                }
            })
        });
        var remove = $('.remove-city');
        remove.click(function (event) {
            event.preventDefault();

            if(confirm('Вы точно хотите удалить город?')) {
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: {
                        action: 'remove_city_ajax',
                        post_id: $(this).attr('data-id')
                    }
                })
            }
        })
    })(jQuery)
</script>
