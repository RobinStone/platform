setTimeout(function() {
    let url = new URL(location.href);
    show_next_pagination_cards(8, {owner_login: url.searchParams.get('o')});
}, 1000);