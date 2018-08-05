$(function() {
    $('#add').click(function(event) {

        $('#dark').toggle();
    });
    $(document).click(function(event) {
        if ($(event.target).closest('.spaceWrapper').length == 0 && $(event.target).attr('id') != 'add') {
            $('#dark').hide();
        };
    });
    $('#close').click(function(event) {
        $('#dark').toggle();
    });
	
});

// Сворачиваем-разворачиваем таблицы

$(function() {
    $('#headRu').click(function(e) {
        $('#tableRussia').toggle();
    });

    $('#headCccp').click(function(e) {
        $('#tableCccp').toggle();
    });

    $('#headOlimp').click(function(e) {
        $('#tableOlimpiada').toggle();
    });
});

$(function(){
			$('#add_row').click(function(e){
				$('.hidden_table> .hidden-row:last').after('');
				
				return false;
			   
			});
		});