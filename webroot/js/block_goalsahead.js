require(["jquery"], function($) {
	$(document).ready(function() {
		$('.view-detail').on('click',function(){
            var objClasses = $(this).find('i').attr("class");
            var classes = (objClasses.indexOf('plus') !== -1? objClasses.replace('plus','minus') : objClasses.replace('minus','plus'));
            $(this).find('i').attr("class", classes);
        });

        $('.btn-form').on('click', function(){
            var page = $(this).attr('data-page');
            var id = $(this).attr('data-id');

            $('#form_goalsahead').find('input[name="goalsahead_page[form]"]').val(page);
            $('#form_goalsahead').find('input[name="id"]').val(id);

            $('#form_goalsahead').submit();
        });
    });
});