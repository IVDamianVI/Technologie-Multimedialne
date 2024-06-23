//^ Skrypt blokowania przycisku submit, jeśli pola nie spełniają wymagań
$(document).ready(function() {
    $('input').on('input', function() {
        // Blokowanie przycisku submit, jeśli pola nie spełniają wymagań
        if ($('input[name="user"]').val() != "" &&
            $('input[name="pass"]').val() != "" &&
            $('input[name="pass1"]').val() != "" &&
            $('input[name="pass"]').val() == $('input[name="pass1"]').val()) {
            $('button[type="submit"]').prop('disabled', false);
            $('button[type="submit"]').addClass('ready');
        } else { // Odblokowywanie przycisku submit, jeśli pola spełniają wymagania
            $('button[type="submit"]').prop('disabled', true);
            $('button[type="submit"]').removeClass('ready');
        }
        // Kolorowanie inputa, jeśli hasła nie są takie same
        if ($('input[name="pass"]').val() != "" && $('input[name="pass"]').val() != $('input[name="pass1"]').val()) {
            $('input[name="pass1"]').attr('style', 'border-color: #e44545');
        } else {
            $('input[name="pass1"]').attr('style', 'border-color: #333333');
        }
    });
});