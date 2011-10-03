$(function(){
    $('#search_box').autocomplete({source: "/autocomplete.php", minLength: 2, select:function(event, ui) {
        if(ui.item) {
            if(ui.item.type == 'mob') {
                location.href = ('/mob/' + ui.item.id);
            } else {
                location.href = ('/item/' + ui.item.id);
            }
            return false;
        }
    }}).data("autocomplete")._renderItem = function(ul, item) {
        var icon = '';
        if(item.icon) {
            icon = '<img src=' + item.icon + ' height=16 width=16>';
        }
        return $("<li></li>")
            .data("item.autocomplete", item)
            .append("<a>"+icon+"<span class='pw_color_" + item.colour + "'>" + item.label + "</span><span class='autocomplete-type'>" + item.type + "</span></a>")
            .appendTo(ul);
    }

    $('#addon-tabs').tabs();
    $('#more-tabs').tabs();
    $('#map-tabs').tabs();
    $('#pet-tabs').tabs();

    $('.map-point').hover(function(e) {
        var cls = $(this).attr('class').split(' ');
        var x, y, z;
        $(cls).each(function(index, value) {
            var part = value.substr(0,2);
            var val = value.substr(2);
            switch(part) {
                case 'x-':
                    x = parseInt(val);
                    break;
                case 'y-':
                    y = parseInt(val);
                    break;
                case 'z-':
                    z = parseInt(val)
                    break;
            }
        });
        if(e.type == 'mouseenter') {
            $(this).attr('src', '/images/highlighted_point');
        } else {
            $(this).attr('src', '/images/point');
        }
        $('.map-coord.x-'+x+'.y-'+y+'.z-'+z).toggleClass('highlighted');
    });

    $('.map-coord').hover(function(e) {
        var cls = $(this).attr('class').split(' ');
        var x, y, z;
        $(cls).each(function(index, value) {
            var part = value.substr(0,2);
            var val = value.substr(2);
            switch(part) {
                case 'x-':
                    x = parseInt(val);
                    break;
                case 'y-':
                    y = parseInt(val);
                    break;
                case 'z-':
                    z = parseInt(val)
                    break;
            }
        });
        if(e.type == 'mouseenter') {
            $('.map-point.x-'+x+'.y-'+y+'.z-'+z).attr('src', '/images/highlighted_point').css('z-index', '10');
        } else {
            $('.map-point.x-'+x+'.y-'+y+'.z-'+z).attr('src', '/images/point').css('z-index', '');
        }
        $(this).toggleClass('highlighted');
    });

    // This is ugly. Manually double the size of the map!
    $('.pw-map').each(function() {
        big_map = $(this).clone();
        big_map.css('background-image', big_map.css('background-image').replace('small', 'large')).css('width', '1024px').css('height', '768px');
        $(big_map).find('.map-point').each(function() {
            var left = $(this).css('left');
            left = (parseInt(left.substring(0, left.length - 2)) + 5) * 2 - 5;
            var top = $(this).css('top')
            top = (parseInt(top.substring(0, top.length - 2)) + 5) * 2 - 5;
            $(this).css('left', left + 'px').css('top', top + 'px');
        });

        $(this).fancybox({
            width: 1024,
            height: 768,
            centerOnScroll: true,
            autoDimensions: false,
            autoScale: false,
            titleShow: false,
            type: 'html',
            content: big_map
        });
    });

    $('#level-select').val($.cookie('level-adjust'));
    $('#level-select').change(function() {
        var value = $(this).val();
        $.cookie('level-adjust', value, {path: '/'});
        update_level_values();
    });
    update_level_values(true);
    $('#level-select-display').show();

    $('#map-tabs h3, #map-tabs p').click(function() {
        $(this).parent().toggleClass('squashed');
    });
});

function exp_multiplier(difference) {
    var m = 1.0;
    if(difference >= 40) {
        m = 0.05;
    } else if(difference >= 30) {
        m = 0.10;
    } else if(difference >= 25) {
        m = 0.15;
    } else if(difference >= 20) {
        m = 0.20;
    } else if(difference >= 15) {
        m = 0.30;
    } else if(difference >= 11) {
        m = 0.50;
    } else if(difference >= 8) {
        m = 0.70;
    } else if(difference >= 5) {
        m = 0.80;
    } else if(difference >= 3) {
        m = 0.90;
    } else if(difference <= -5) {
        m = 1.05;
    } else if(difference <= -8) {
        m = 1.10;
    } else if(difference <= -11) {
        m = 1.20;
    }
    return m
}

function drop_multiplier(difference) {
    m = 1.0;
    if(difference >= 40) {
        m = 0.2;
    } else if(difference >= 30) {
        m = 0.25;
    } else if(difference >= 25) {
        m = 0.30;
    } else if(difference >= 20) {
        m = 0.40;
    } else if(difference >= 15) {
        m = 0.50;
    } else if(difference >= 11) {
        m = 0.60;
    } else if(difference >= 8) {
        m = 0.70;
    } else if(difference >= 5) {
        m = 0.80;
    } else if(difference >= 3) {
        m = 0.90;
    }
    return m;
}

function update_with_animation(element, value, no_animation) {
    if(value == $(element).html()) {
        return;
    }
    if(!!no_animation) {
        $(element).html(value);
    } else {
        $(element).fadeOut('fast', function() {
            $(element).html(value).fadeIn('fast');
        });
    }
}

function update_level_values(initial_run) {
    var level = parseInt($.cookie('level-adjust'));
    var mob_level = parseInt($('.mob-level-figure').html());
    var difference = level - mob_level;

    $('.level-adjust-exp').each(function() {
        var o = $(this).attr('original');
        if(o == undefined) {
            o = $(this).html().replace(',','');
            $(this).attr('original', o);
        }
        var base = parseFloat(o);
        var actual = (base * exp_multiplier(difference)).toFixed(0);
        update_with_animation(this, number_format(actual), initial_run);
    });

    $('.level-adjust-drops').each(function() {
        var diff = difference;
        var o = $(this).attr('original');
        if(o == undefined) {
            o = $(this).html().replace(',','');
            $(this).attr('original', o);
        }
        var base = parseFloat(o);
        if(!mob_level) {
            // Try finding it more locally.
            var our_level = $(this).parentsUntil('tbody').find('.row-mob-level').html();
            if(our_level) {
                if(our_level === '?') {
                    our_level = 150;
                }
                diff = level - parseInt(our_level);
            }
        }
        var actual = (base * drop_multiplier(diff)).toFixed(2);
        update_with_animation(this, number_format(actual), initial_run);
    });

    $('.level-requirement').each(function() {
        var required = parseInt($(this).children('.level-requirement-number').html());
        if(level < required && level != 0) {
            $(this).addClass('requirement-unmet').removeClass('requirement-met');
        } else {
            $(this).addClass('requirement-met').removeClass('requirement-unmet');
        }
    });

    $('.recipe-table').tablesorter();
}
