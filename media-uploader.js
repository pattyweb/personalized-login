jQuery(document).ready(function ($) {
    var mediaUploader;
    
    $('#upload_logo_button').on('click', function (e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Logo',
            button: {
                text: 'Use this logo'
            },
            multiple: false
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#clb_logo_url').val(attachment.url); // Save the URL to the hidden input
            $('#logo_preview').attr('src', attachment.url); // Show the logo preview
        });

        mediaUploader.open();
    });
});
