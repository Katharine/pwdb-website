$(function() {
    $('.item-addon-header').click(function() {
        var group = $(this).attr('group');
        $('.item-addon-row.group-' + group).toggle();
    });
    $('.item-addon-row').hide();

    //$('ul.sf-menu').superfish();
});
