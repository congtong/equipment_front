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
})(window);



