require(["jquery", "core/str", "core/notification"], function ($, str, notification) {
    $(document).ready(function () {
        $('.view-detail').on('click', function () {
            var objClasses = $(this).find('i').attr("class");
            var classes = (objClasses.indexOf('plus') !== -1 ? objClasses.replace('plus', 'minus') : objClasses.replace('minus', 'plus'));
            $(this).find('i').attr("class", classes);
        });
        
        $('.only-numeric').on('keypress', function (event) {
            return isNumber(event);
        });

        $('.btn-action').on('click', function () {
            var obj = this;
            var msg = 'messageconfirm' + $(this).attr('data-action');

            var strings = [
                {
                    key: 'confirm',
                    component: 'block_goalsahead'
                },
                {
                    key: msg,
                    component: 'block_goalsahead'
                },
                {
                    key: 'continue',
                    component: 'block_goalsahead'
                },
                {
                    key: 'cancel',
                    component: 'block_goalsahead'
                }
            ];

            str.get_strings(strings).then(function (results) {
                notification.confirm(results[0], results[1], results[2], results[3], function () {
                    formSubmit(obj);
                });
            });
        });

        $('.btn-form').on('click', function () {
            formSubmit(this);
        });
    });

    function formSubmit(obj) {
        var inputPage = $('#form_goalsahead').find('input[name="goalsahead_page"]');
        var route = $(obj).attr('route');

        inputPage.attr('name', inputPage.attr('name') + '[' + route + ']');

        $.each(obj.attributes, function () {
            if (this.name.indexOf('data') === 0) {
                if (this.name.indexOf('page') === -1) {
                    inputPage.after('<input type="hidden" name="' + this.name.replace('data-', '') + '" value="' + this.value + '">');
                } else {
                    inputPage.val(this.value);
                }
            }
        });

        $('#form_goalsahead').submit();
    }

    function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode;
        return (iKeyCode > 47 && iKeyCode < 58);
    } 
});