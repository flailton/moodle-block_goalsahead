require(["jquery"], function($) {
	$(document).ready(function() {
		$('.view-detail').on('click',function(){
            var objClasses = $(this).find('i').attr("class");
            var classes = (objClasses.indexOf('plus') !== -1? objClasses.replace('plus','minus') : objClasses.replace('minus','plus'));
            $(this).find('i').attr("class", classes);
        });

        $('.btn-create').on('click', function(){
            var action = $(this).attr('data-page');
            $('input[name="goalsahead_page[form]"]').val(action);
            $('#form_create').submit();
        });
    });
});