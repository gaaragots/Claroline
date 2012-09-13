//window.onload(function(){alert("loading...")});
(function () {
    $('html, body').animate({scrollTop: 0}, 0);
    $('#loading').hide();

    var route = Routing.generate('claro_admin_paginated_user_list', {
        'page' : 1,
        'format': 'html'
    });

    ClaroUtils.sendRequest(route, function(users){
        $('#user-table-body').append($(users));
    })

    var page = 2;
    var loading = false;

    $(window).scroll(function(){
        if  (($(window).scrollTop()+100 >= $(document).height() - $(window).height()) && loading === false){
            loading = true;
            $('#loading').show();
            var route = Routing.generate('claro_admin_paginated_user_list', {
                'page' : page,
                'format': 'html'
            });
            page++;
            ClaroUtils.sendRequest(route, function(users){
                $('#user-table-body').append($(users));
                loading = false;
                $('#loading').hide();
            })
        }
    });

    $('.link-delete-user').live('click', function(e){
        e.preventDefault();
        var route = $(this).attr('href');
        var element = $(this).parent().parent();
        ClaroUtils.sendRequest(
            route,
            function(){
                element.remove();
            },
            undefined,
            'DELETE'
        )
    });
})();