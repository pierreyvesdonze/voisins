
var app = {

    init: function () {

        console.log('app init');

        app.close = $('.close').on('click', app.closeAlertModal);
        app.modal = $('.alert-success');
        setTimeout(function () {
            app.modal.remove();
            app.close.remove();
        }, 3000);

        app.submitButton = $(':submit');
        /*app.submitButton.removeClass('btn');
        app.submitButton.removeClass('btn-primary');*/
        app.submitButton.on('click', app.handleSubmitForm);
    },

    closeAlertModal: function () {
        app.modal.remove();
        app.close.remove();
    },

    handleSubmitForm: function (form) {
        console.log('disable submit button');
        return true;
    },
}

// App Loading
document.addEventListener('DOMContentLoaded', app.init);