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
