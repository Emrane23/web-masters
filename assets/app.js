/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import toastr from 'toastr';

$(document).ready(function () {
    $('#myArticles').DataTable({
        "lengthMenu": [5, 10, 15, 20]
    });

    var rating_data = $('#rating_data').data('rate');
    $(document).on('mouseenter', '.submit_star', function () {
        var rating = $(this).data('rating');
        reset_background();

        for (var count = 1; count <= rating; count++) {
            $('#submit_star_' + count).addClass('text-warning');
        }

    });

    function reset_background() {
        for (var count = 1; count <= 5; count++) {
            $('#submit_star_' + count).addClass('star-light');
            $('#submit_star_' + count).removeClass('text-warning');
        }
    }

    $(document).on('mouseleave', '.submit_star', function () {
        reset_background();
        for (var count = 1; count <= rating_data; count++) {
            $('#submit_star_' + count).removeClass('star-light');
            $('#submit_star_' + count).addClass('text-warning');
        }

    });
    $(document).on('click', '.submit_star', function () {
        let token = document.getElementById("_token").getAttribute("data-token");
        let article = document.getElementById("article_id").getAttribute("data-article");
        rating_data = $(this).data('rating');
        $.ajax({

            url: `/index.php/feedback/${article}`,
            method: "POST",
            data: {
                '_token': token,
                vote: rating_data,
                article: article,
            },
            success: function (data) {
                toastr.success(`${data.message}! ${rating_data}`, 'well done', { timeOut: 5000, closeButton: true });
                $("#average-vote").text(data.averageVotes);
                $("#total-vote").text(`${data.review}`);
                var newAverageRating = data.averageVotes;
                $("#stars .star-rating").each(function (index) {
                    if ((index - newAverageRating) < -0.5) {
                        $(this).addClass("text-warning");
                    } else {
                        $(this).removeClass("text-warning");
                    }
                });
            },
            error: function (data) {
                toastr.error(`Something worong! `, 'Error', { timeOut: 5000, closeButton: true });
            },

        })
    });
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
            toastr.success('Comment added successfully!', 'well done', { timeOut: 5000, closeButton: true });
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
            toastr.error(error, 'Error', { timeOut: 5000, closeButton: true });
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