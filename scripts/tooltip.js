$(function() {
    $(".item-link").each(function() {
        var item_id = $(this).attr('href').split('/')[2];
        $(this).qtip({
            content: {
                url: '/tooltip.php',
                data: {id: item_id, kind: 'item'},
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
    $(".mob-link").each(function() {
        var item_id = $(this).attr('href').split('/')[2];
        $(this).qtip({
            content: {
                url: '/tooltip.php',
                data: {id: item_id, kind: 'mob'},
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
    $(".resource-link").each(function() {
        var item_id = $(this).attr('href').split('/')[2];
        $(this).qtip({
            content: {
                url: '/tooltip.php',
                data: {id: item_id, kind: 'resource'},
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
});
