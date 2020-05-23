window.onload = () => {
    // Gestion des boutons "Supprimer"
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