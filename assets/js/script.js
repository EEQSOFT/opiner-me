jQuery(document).ready(function($) {
    $('#opiner-me-load-more').on('click', function(e) {
        e.preventDefault();

        let button = $(this);
        let postId = button.data('postId');
        let limit  = button.data('limit');
        let offset = button.data('offset');
        let total  = button.data('total');

        $.post(opiner_me_ajax.ajaxurl, {
            action: 'opiner_me_load_more',
            post_id: postId,
            offset: offset
        }, function(response) {
            if (response.trim() !== '') {
                $('#opiner-me-list').append(response);

                let newOffset = offset + limit;

                button.data('offset', newOffset);
                button.attr('href', '?show=' + newOffset);

                if (newOffset >= total) {
                    button.remove();
                }
            } else {
                button.remove();
            }
        });
    });
});
