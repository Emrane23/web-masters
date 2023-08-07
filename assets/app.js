/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import toastr from 'toastr';

$(document).ready(function() {
    $('#myArticles').DataTable();
});

$('#add_comment_form').submit(function (e) {
    e.preventDefault(); // Prevent the default form submission

    var form = $('#add_comment_form');
    var url = form.attr('action');
    var data = form.serialize();
        $('#button-add-comment').css('cursor', 'not-allowed');
        $('#reloadSpinner').show();
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        success: function (response) {
            toastr.success('Comment added successfully!','well done',{timeOut: 5000,closeButton: true});
            $('#button-add-comment').css('cursor', 'auto');
            $('#reloadSpinner').hide();
            $('.new-label').remove();

            var newCommentSection = $(response.html).hide(); // Apply a fade-in animation
            newCommentSection.find('.text-danger').prepend('<span class="new-label">New</span>');
            newCommentSection.fadeIn(1000);

            $('#comments').append(newCommentSection);

            form[0].reset();

            var nmbrComments = $('#nmbr-comments');
            var initialCount = parseInt(nmbrComments.data('initial-count'));
            nmbrComments.text(initialCount + 1 + ' comments');
            nmbrComments.data('initial-count', initialCount + 1);
        },
        error: function (xhr, status, error) {
            toastr.error(error,'Error',{timeOut: 5000,closeButton: true});
            $('#button-add-comment').css('cursor', 'auto');
            $('#reloadSpinner').hide();
            console.error(xhr.responseText);
        }
    });
});

$('#comment_form_comment').on('input', function () {
    checkComment();
});

function checkComment() {
    const textarea = document.getElementById('comment_form_comment'); 
    const addButton = document.getElementById('button-add-comment');

    if (textarea.value.trim() !== '') {
        addButton.removeAttribute('disabled');
        addButton.classList.remove('not-allowed');
    } else {
        addButton.setAttribute('disabled', 'disabled');
        addButton.classList.add('not-allowed');
    }
}