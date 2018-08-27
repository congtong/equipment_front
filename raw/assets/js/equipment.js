(function (window) {
    $(document).ready(function(){
        $('.padding-menu').click(function(){
            var className = $(this).children().children().children()[0].className;
            if(className =='fa fa-angle-down float-right'){
                className = 'fa fa-angle-right float-right';
            }else{
                className = "fa fa-angle-down float-right";
            }
            $(this).children().children().children()[0].className = className;
        })
    });

    $('body').on('submit', 'form[action^="gini-ajax:"]', function (e) {
        if ($(this).data('delegated')) return false;

        e.preventDefault();

        var $form = $(this);

        $form.trigger('ajax-before');

        $.ajax({
            type: $form.attr('method') || "POST",
            url: $form.attr('action').substring(10),
            data: $form.serialize(),
            success: function (html) {
                $form.trigger('ajax-success', html);
                $('body').append(html).find('script[data-ajax]').remove();
            },
            complete: function () {
                $form.trigger('ajax-complete');
            }
        });

        return false;
    });
})(window);
