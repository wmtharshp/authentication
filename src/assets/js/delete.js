$(function(){
    $("body").on("click", '.delete', function() {
        var url = $(this).attr('data-href');
        deleteRecord(url);
    });
    function deleteRecord(url) {
        let text = "Are you sure you want to delete record?";
        if (confirm(text) == true) {
            $.ajax({
                type: 'delete', 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function(response) {
                    if (response.status) {
                        location.reload(true);
                    }
                }
            });
        }
    }
});
