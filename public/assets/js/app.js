/*let app = {

    init: function () {
        console.log("init");



        app.max_fields = 10;
        app.wrapper = $(".container-groceries-fields");
        app.add_button = $(".add_form_field");

        $(app.add_button).on("click", app.addFields);
        $(app.wrapper).on("click", ".delete", app.addFields);
        $(".groceries-submit").on("click", app.handleAddListFormSubmit)

    },

    addFields: function (e) {
        let x = 1;

        e.preventDefault();
        if (x < app.max_fields) {
            x++;
            $(app.wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="delete">Delete</a></div>'); //add input box
        } else {
            alert("J'ai fixé la limite à 10 articles... si c'est trop peu, fais-le moi savoir ! ")
        }
    },

    wrapper: function (e) {
        e.preventDefault();
        $(this).parent('div').remove();
        x--;
    },

    handleAddListFormSubmit: function (e) {

        e.preventDefault();

        // Get Current Id
        let url = location.href;
        let urlSplit = url.split('/');
        let groceriesId = urlSplit[urlSplit.length - 1];

        let shoppingListTitle = $("input[name='list-title']");
        let shoppingListingredients = $("input[name='mytext[]']").map(function () {
            return $(this).val();
        }).get();

        console.log(shoppingListTitle);

        $.ajax({
            url: "../request/" + groceriesId,
            method: "POST",
            dataType: "json",
            data: {
                title: shoppingListTitle,
                ingredients: shoppingListingredients
            }
        }).done(

            function (data) {
                console.log(data);
            }
        )
            .fail(function () {
                alert("Ajax Fail.");
            });
    },
}
document.addEventListener('DOMContentLoaded', app.init);
*/