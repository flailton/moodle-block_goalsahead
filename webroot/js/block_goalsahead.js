require(["jquery"], function($) {
	$(document).ready(function() {
		$('.view-detail').on('click',function(){
            var objClasses = $(this).find('i').attr("class");
            var classes = (objClasses.indexOf('plus') !== -1? objClasses.replace('plus','minus') : objClasses.replace('minus','plus'));
            $(this).find('i').attr("class", classes);
        });

        if($('#block_goalsahead_editor').length > 0){
            ClassicEditor
                .create( document.querySelector( '#block_goalsahead_editor' ) )
                .catch( error => {
                    console.error( error );
            } );
        }
        
    });
});