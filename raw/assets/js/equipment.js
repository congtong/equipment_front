(function (window) {
    $(document).ready(function(){
        $(".panel-title a").click(function(){
            var className = $(this).children()[0].className;
            if(className =='fa fa-angle-down float-right'){
                className = 'fa fa-angle-right float-right';
            }else{
                className = "fa fa-angle-down float-right";
            }
            $(this).children()[0].className = className;
        });
    });
})(window);
