(function ($) {
    $(document).ready(function () {
        window.name = 'vote';
        var body = $('body');
        getRegions();
        body.on('click', '.vote-item', function () {
            var $this = $(this);
            var id = $this.attr('data-id');
            var title = $this.find('.vote-item-title').html();

            $('.vote-item').removeClass('show');
            $this.addClass('show');

            showLoginPopup(id, title);

        });
        body.on('click', '#vote-login__close', function () {
            body.removeClass('show-login');
            $('#vote-login').remove();
            $('.vote-item').removeClass('show');
        });
    });

    function getRegions() {
        var data = {action: 'get_regions'};
        $.post({
            data: data,
            url: ajaxurl,
            dataType: 'json',
            success:function(response){
                var container = $('#vote');
                container.append('<h2 class="vote-title">Проголосуйте за свой регион!</h2>');
                renderRegions('top', response['max'], response['top'], response['summary']);
                renderRegions('other', response['max'], response['other'], response['summary']);
                container.append('<div class="buttons"><a id="show-vote" data-id="2"><button class="white"><span class="button-icon icon-plus-icon"></span>Показать еще</button></a></div>');
                container.append('<script>$(\'#show-vote\').click(function() {var btn = $(this); var max = $(\'.vote-list\').length - 1; $(\'#vote-other-\' + btn.attr("data-id")).slideDown(); if(btn.attr("data-id") < max) {btn.attr("data-id", (btn.attr("data-id") * 1 + 1))} else {btn.fadeOut()} })</script>');
            },
        });
    }
    function renderRegions(type, max, items, sum) {
        var container = $('#vote');
        switch (type) {
            case 'top':
                container.append('<div id="vote-top" class="vote-content vote-content__top"><h3 class="vote-subtitle">Лидеры голосования</h3></div>');
                $('#vote-top').append('<ul id="vote-top__list" class="vote-list"></ul>');
                var list = $('#vote-top__list');
                items.forEach(function (item) {
                    var percent = ((item.vote / sum) * 100).toFixed(2);
                    var progress = ((item.vote / max) *  100).toFixed(2);
                    list.append('<li class="vote-item vote-item__top" id="vote-item-' + item.id + '" data-id="' + item.id + '"><div class="vote-item-container"><span class="vote-item-title">' + item.title + '</span><span class="vote-item-percent">' + percent + '%</span><div class="vote-item-progress"><div style="width:' + progress + '%"</div></div></li>');
                });
                break;

            case 'other':
                var i = 1;
                container.append('<div id="vote-other" class="vote-content vote-content__other"><h3 class="vote-subtitle">Остальные регионы</h3></div>');
                items.forEach(function (list) {
                    $('#vote-other').append('<ul id="vote-other-' + i + '" class="vote-list"></ul>');
                    list.forEach(function (item) {
                        var percent = ((item.vote / sum) * 100).toFixed(2);
                        var progress = ((item.vote / max) *  100).toFixed(2);
                        $('#vote-other-' + i).append('<li class="vote-item vote-item__other" id="vote-item-' + item.id + '" data-id="' + item.id + '"><div class="vote-item-container"><span class="vote-item-title">' + item.title + '</span><span class="vote-item-percent">' + percent + '%</span><div class="vote-item-progress"><div style="width:' + progress + '%"</div></div></li>');
                    });
                    i++;
                });

                break;
        }
    }

    function showLoginPopup(id, title) {
        $('body').append('<div class="vote-login" id="vote-login"></div>').addClass('show-login');
        var login = $('#vote-login');
        login.append('<span class="vote-login__close" id="vote-login__close"></span>');
        login.append('<div class="vote-login__header"><span class="vote-login__logo"><img src="/wp-content/plugins/youngreads-vote/css/img/life_classic.jpg"></span><span class="vote-login__logo"><img src="/wp-content/plugins/youngreads-vote/css/img/gitis.jpg"></span></div>');
        login.append('<div class="vote-login__content"><h3 class="vote-login__title">Голосование</h3><h4 class="vote-login__subtitle">Вы хотите проголосовать за регион:<span class="vote-login__subtitle-region">' + title + '</h4><div class="vote-login__social"><p>Чтобы проголосовать, войдите через одну из социальных сетей:</p><div class="buttons"><button type="button" class="white" id="vote-auth-vk"><span class="button-icon icon-vk-icon"></span>ВКонтакте</button><button type="button" class="white"><span class="button-icon icon-fb-icon"></span>Facebook</button></div></div></div>');
        login.append('<div class="vote-login__footer"><img src="/wp-content/plugins/youngreads-vote/css/img/y-rman.jpg">Фонд Конкурса юных чтецов<br />«Живая классика» © 2018 год.</div>');

    }

})(jQuery);
