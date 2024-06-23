$(document).ready(function() {
    $('input').on('input', function() {
        if ($('input[name="user"]').val() != "" &&
            $('input[name="pass"]').val() != "" &&
            $('input[name="pass1"]').val() != "" &&
            $('input[name="pass"]').val() == $('input[name="pass1"]').val()) {
            $('button[type="submit"]').prop('disabled', false);
            $('button[type="submit"]').addClass('ready');
        } else {
            $('button[type="submit"]').prop('disabled', true);
            $('button[type="submit"]').removeClass('ready');
        }

        if ($('input[name="pass"]').val() != "" && $('input[name="pass"]').val() != $('input[name="pass1"]').val()) {
            $('input[name="pass1"]').attr('style', 'border-color: #e44545');
        } else {
            $('input[name="pass1"]').attr('style', 'border-color: #333333');
        }
    });
});

function sanitizeUsername(input) {
    input.value = input.value.replace(/[^a-zA-Z0-9ęóąśłżźćńĘÓĄŚŁŻŹĆŃ]/g, '');
    document.querySelector('button[type="submit"]').disabled = input.value.length < 4;
}