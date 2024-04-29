$('.fetch-data-btn').click(function() {
    var key = $(this).data('key');
    var app = $(this).data('app');
	var textArea = $(this).next();
    $.ajax({
        url: '/' + app + '-fhir/fetch/'+key,
        method: 'GET',
        success: function(response) {
            textArea.val(response);
			console.log(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});
