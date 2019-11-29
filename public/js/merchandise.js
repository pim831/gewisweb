
$(function() {

    // Click on add
    $(".add-to-basket").on("click", function () {

        var sibling = $(this).siblings(".card-title");
        var itemName = sibling.attr("id");

        console.log($(".basket-item:first"));

        var newBasketItem = $(" <li class=\"list-group-item basket-item hide\">\n" +
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
            "                                        <label class=\"control-label\"><h2 class=\"basket-item-description\">Zwart sportshirt</h2></label>\n" +
            "                                    </div>\n" +
            "                                </form>\n" +
            "                            </li>");

        var test = newBasketItem.appendTo(".list-group");

        test.removeClass("hide");
        //console.log(newBasketItem);

    });

});
