$(function() {
    function cls_to_coords(element) {
        element = $(element).find('.map-point');
        if(element.length) {
            var cls = element.attr('class').split(' ');
            var x, y, map;
            $(cls).each(function(index, value) {
                var parts = value.split('-');
                if(parts.length != 2) {
                    return true;
                }
                var part = parts[0];
                var val = parts[1];
                switch(part) {
                    case 'x':
                        x = parseInt(val);
                        break;
                    case 'y':
                        y = parseInt(val);
                        break;
                    case 'map':
                        map = val;
                        break;
                }
            });
            if(map) {
                return {x: x, y: y, map: map};
            }
        }
        return null;
    }

    function add_tips(kind) {
        $("." + kind + "-link").each(function() {
            var item_id = $(this).attr('href').split('/')[2];
            var coords = cls_to_coords(this);
            var data = {id: item_id, kind: kind};
            if(coords) {
                data.x = coords.x;
                data.y = coords.y;
                data.map = coords.map;
            }
            $(this).qtip({
                content: {
                    url: '/tooltip.php',
                    data: data,
                    method: 'get'}, 
                position: {
                    target: 'mouse',
                    adjust: {
                        screen: true,
                        mouse: false,
                        x: 5,
                        y: 5}}, 
                style: {
                    width: 260,
                    padding: 0,
                    background: 'rgba(0,0,0,0)',
                    border: {color: 'rgba(0,0,0,0)'}}});
        });
    }

    add_tips('item');
    add_tips('mob');
    add_tips('resource');
    add_tips('pet');
    add_tips('npc');
    add_tips('quest');
});
