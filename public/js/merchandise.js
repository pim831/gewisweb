
$(function() {

    // User clicked to add an item to the basket
    $(".add-to-basket").on("click", function () {

        var sibling = $(this).siblings(".card-title");
        var itemId = sibling.attr("id");
        var itemName = sibling.text();

        console.log("Adding " + itemName + " (ID " + itemId + " ) to your basket");

        var newBasketItem = $(" <li class=\"list-group-item basket-item hide " + itemId + " \">\n" +
            "                                <form class=\"form-inline basket-item-form\">\n" +
            "                                    <div class=\"form-group\">\n" +
            "                                        <select class=\"form-control\">\n" +
            "                                            <option value=\"XS\" selected=\"selected\">XS</option>\n" +
            "                                            <option value=\"S\">S</option>\n" +
            "                                            <option value=\"M\">M</option>\n" +
            "                                            <option value=\"L\">L</option>\n" +
            "                                            <option value=\"XL\">XL</option>\n" +
            "                                        </select>\n" +
            "                                        <select class=\"form-control\">\n" +
            "                                            <option value=\"1\" selected=\"selected\">1x</option>\n" +
            "                                            <option value=\"2\">2x</option>\n" +
            "                                            <option value=\"3\">3x</option>\n" +
            "                                            <option value=\"4\">4x</option>\n" +
            "                                            <option value=\"5\">5x</option>\n" +
            "                                        </select>\n" +
            "                                        <label class=\"control-label\"> </label>\n" +
            "                                        <label class=\"control-label\"> </label>\n" +
            "                                        <label class=\"control-label\"><h2 class=\"basket-item-description\">" + itemName + "</h2></label>\n" +
            "                                    </div>\n" +
            "                                </form>\n" +
            "                            </li>");


        if ($(".list-group").children("." + itemId).length !== 0) {
            console.log("Not adding item, because it's already there!");
            return;
        }

        var addedItem = newBasketItem.appendTo(".list-group");

        addedItem.removeClass("hide");

        // Now show remove icon.
        $(this).addClass("hidden");
        $(this).siblings(".remove-from-basket").removeClass("hidden");

    });

    // Remove item from basket is clicked
    $(".remove-from-basket").on("click", function () {

        // Remove the item from the basket and display the add icon again.
        var sibling = $(this).siblings(".card-title");
        var itemId = sibling.attr("id");
        var itemName = sibling.text();

        console.log("Removing " + itemName + " (ID " + itemId + " ) from your basket");

        // Remove the item from the basket.
        $(".list-group").children("." + itemId).remove();

        // Now show add icon.
        $(this).addClass("hidden");
        $(this).siblings(".add-to-basket").removeClass("hidden");
    });

});
