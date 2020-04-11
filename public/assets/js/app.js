
var app = {

    init: function () {

        console.log('app init');

        app.close = $('.close').on('click', app.closeAlertModal);
        app.modal = $('.alert-success');
        setTimeout(function () {
            app.modal.remove();
            app.close.remove();
        }, 3000);
    },

    closeAlertModal: function () {
        
        app.modal.remove();
        app.close.remove();
    }
}

// App Loading
document.addEventListener('DOMContentLoaded', app.init);