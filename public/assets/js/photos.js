// Delete images with Ajax
window.onload = () => {

    let links = document.querySelectorAll("[data-delete]")

    for (link of links) {

        link.addEventListener("click", function (e) {
            e.preventDefault()
            let img = $(this).parent().parent();

            if (confirm("Voulez-vous supprimer cette image ?")) {
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    dataType: "json",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ "_token": this.dataset.token })
                }).then(
                    img.remove()

                )/* .then(

                        response => { console.log($(this).find('.image')) }
                        //response => response.json()

                    ).then(data => {
                        if (data.success)
                            this.parentElement.remove()
                        else
                            alert(data.error)
                    }).catch(e => alert(e)) */
            }
        })
    }
}

// Zoom in Lightbox
var zoomImg = function () {

    var clone = this.cloneNode();
    clone.classList.remove("zoomD");

    var lb = document.getElementById("lb-img");
    lb.innerHTML = "";
    lb.appendChild(clone);

    lb = document.getElementById("lb-back");
    lb.classList.add("show");
};

window.addEventListener("load", function () {
    // Attach on click events to all .zoomD images
    var images = document.getElementsByClassName("zoomD");
    if (images.length > 0) {
        for (var img of images) {
            img.addEventListener("click", zoomImg);
        }
    }

    // Click event to hide the lightbox
    document.getElementById("lb-back").addEventListener("click", function () {
        this.classList.remove("show");
    })
});

// Upload and resize images
$(document).ready(function () {

    $('#image-to-resize').change(function () {
        let symfoInput = $('.img-to-resize').val();

        if (this.files.length > 0) {

            let url = Routing.generate('gallery_create', true),
                dataImg = [],
                formData = new FormData();

            $.each(this.files, function (i, v) {
                var reader = new FileReader();

                reader.onload = function (event) {
                    var image = new Image();
                    image.src = event.target.result;

                    image.onload = function () {
                        var maxWidth = 1024,
                            maxHeight = 1024,
                            imageWidth = image.width,
                            imageHeight = image.height;


                        if (imageWidth > imageHeight) {
                            if (imageWidth > maxWidth) {
                                imageHeight *= maxWidth / imageWidth;
                                imageWidth = maxWidth;
                            }
                        }
                        else {
                            if (imageHeight > maxHeight) {
                                imageWidth *= maxHeight / imageHeight;
                                imageHeight = maxHeight;
                            }
                        }

                        var canvas = document.createElement('canvas');
                        canvas.width = imageWidth;
                        canvas.height = imageHeight;
                        image.width = imageWidth;
                        image.height = imageHeight;
                        var ctx = canvas.getContext("2d");
                        ctx.drawImage(this, 0, 0, imageWidth, imageHeight);

                        $('#img').append(image);

                        var dataurl = canvas.toDataURL("image/png");
                        formData.append('image', image + i);
/* 
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", url);
                        xhr.send(formData); */

                        console.log(image);

                        /*    symfoInput.prop(image)[i]; */
                    }
                };
                reader.readAsDataURL(this);
            });

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json",
                },
            })
                .done(function (response) {
                    console.log(formData);

                })
                .fail(function (jqXHR, textStatus, error) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(error);
                });
        }
    });
});