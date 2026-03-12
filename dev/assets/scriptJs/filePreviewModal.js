$(document).ready(function () {
    const $modal = $('#filePreviewModal');
    const $closeBtn = $('#file-preview-close');
    const $overlay = $('.custom-modal-overlay');
    const $title = $('#file-preview-title');
    const $body = $('#file-preview-body');

    $(document).on('click', '.open-file-modal', function (e) {
        e.preventDefault();

        const strainId = $(this).data('strain-id');
        const type = $(this).data('type');

        if (!strainId || !type) {
            return;
        }

        $title.text(`Preview - ${type} - strain ${strainId}`);
        $body.html('<p>Loading...</p>');
        $modal.css('display', 'block');

        $.ajax({
            url: `/strain/${strainId}/file-preview/${type}`,
            method: 'GET',
            success: function (html) {
                $body.html(html);
            },
            error: function () {
                $body.html('<p>Erreur de chargement.</p>');
            }
        });
    });

    $closeBtn.on('click', function () {
        $modal.hide();
    });

    $overlay.on('click', function () {
        $modal.hide();
    });
});