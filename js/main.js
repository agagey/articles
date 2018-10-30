$(document).ready(function(){

    // Нажали удаление записи
    $('button.uk-icon-remove').click(function(){
        var btn = $(this);
        $.post(
            "/removeArticle",
            {
                id: btn.data("id")
            }
        )
        .done(function () {
            btn.closest('.uk-panel').remove();
        });
    });

    // просмотр записи
    $('button.uk-icon-eye').click(function(){
        btn = $(this);
        window.location.href = '/viewArticle?id=' + btn.data('id');
    });

    // редактирование записи
    $('button.uk-icon-pencil').click(function(){
        btn = $(this);
        window.location.href = '/editArticle?id=' + btn.data('id');
    });
});
