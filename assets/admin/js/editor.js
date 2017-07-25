jQuery(document).ready(function ($) {

    $('.gf-select').change(function () {

        $('form.cio-edit-map').append('<input type="hidden" name="load_fields" />');

        $('#submit').attr('formnovalidate', true)
            .trigger('click');

    });


    $('.email-field').change(function () {

        // Reset all
        $('.field-name').filter(function (index, el) {

            return !$(el).parents('tr')
                .find('.id-field')
                .attr('checked');

        }).prop('disabled', false);

        // Set only the corresponding field to disabled
        $(this).parents('tr')
            .find('.field-name')
            .val('')
            .prop('disabled', true);

    });

    $('.email-field').filter(function (index, el) {

        return $(el).prop('checked')

    }).parents('tr')
        .find('.field-name')
        .prop('disabled', true);


    $('.id-field').change(function () {

        // Reset all
        $('.field-name').filter(function (index, el) {

            return !$(el).parents('tr')
                .find('.email-field')
                .attr('checked');

        }).prop('disabled', false);

        // Set only the corresponding field to disabled
        $(this).parents('tr')
            .find('.field-name')
            .val('')
            .prop('disabled', true);

    });

    $('.id-field').filter(function (index, el) {

        return $(el).prop('checked')

    }).parents('tr')
        .find('.field-name')
        .prop('disabled', true);

});