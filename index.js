document.addEventListener("DOMContentLoaded", () => {
    let idToRemove;
    const modal = M.Modal.init(document.querySelector("#add_cron"));
    const removeModal = M.Modal.init(document.querySelector("#remove_modal"));
    const webhookModal = M.Modal.init(document.querySelector("#webhook_modal"));
    const form = document.forms.namedItem("addForm");

    const timePickerWrapper = document.querySelector(".timeSelect-wrapper");
    const daySelectWrapper = document.querySelector(".daySelect-wrapper");

    const timePicker = M.Timepicker.init(document.querySelector(".timepicker"), {
        container: "body",
        twelveHour: false,
        onOpenEnd: () => {
            if (form.elements.namedItem("each").value == "heure")
                timePicker.showView("minutes");
        }
    });
    const eachSelect = M.FormSelect.init(form.elements.namedItem("each"));
    const daySelect = M.FormSelect.init(form.elements.namedItem("daySelect"));

    M.FormSelect.init(document.querySelectorAll("select"));
    document.querySelector(".fixed-action-btn").addEventListener("click", () => {
        modal.open();
    });

    form.elements.namedItem("each").addEventListener("change", function() {
        if (this.value == "semaine") {
            timePickerWrapper.style.display = "block";
            daySelectWrapper.style.display = "block";
        } else if (this.value == "jour") {
            timePickerWrapper.style.display = "block";
            daySelectWrapper.style.display = "none";
        } else if (this.value == "heure") {
            timePickerWrapper.style.display = "block";
            daySelectWrapper.style.display = "none";
        } else if (this.value == "minute") {
            timePickerWrapper.style.display = "none";
            daySelectWrapper.style.display = "none";
        }
    });

    document.querySelector("#add_cron .modal-confirm").addEventListener("click", () => {
        const each = form.elements.namedItem("each").value;
        let desc = "Chaque " + each;
        let cron = ["*", "*", "*", "*", "*"];
        if (each == "semaine") {
            const dayValues = daySelectWrapper.querySelectorAll(".selected");
            let selectedDays = {};
            //Pour chaque element selectionné (on a juste le nom)
            daySelectWrapper.querySelectorAll(".selected").forEach((el) => {
                //Pour chaque element proposé (nom + valeur)
                document.querySelectorAll("#daySelect option").forEach((inputEl) => {
                    if (el.textContent == inputEl.textContent)
                        selectedDays[inputEl.value] = inputEl.textContent;
                });
            });
            if (dayValues) {
                desc += " le " + Object.values(selectedDays).join(", ");
                cron[4] = Object.keys(selectedDays).join(",");
            }
            else {
                desc += " le Lundi";
                cron[4] = "1";
            }
            if (timePicker.time) {
                desc += " à " + timePicker.time.replace(":", "h");
                cron[0] = timePicker.time.substring(3,5);
                cron[1] = timePicker.time.substring(0, 2);
            }
            else {
                desc += " à 00h00";
                cron[0] = "00";
                cron[1] = "00"; 
            }
        } else if (each == "jour") {
            if (timePicker.time) {
                desc += " à " + timePicker.time.replace(":", "h");
                cron[0] = timePicker.time.substring(3,5);
                cron[1] = timePicker.time.substring(0, 2);
            }
            else {
                desc += " à " + "00h00";
                cron[0] = "00";
                cron[1] = "00";
            }
        } else if (each == "heure") {
            if (timePicker.time) {
                desc += " à la " + timePicker.time.substring(3,5) + "ème minute";
                cron[0] = timePicker.time.substring(3,5);
            }
            else {
                desc += " à la 1ère minute";
                cron[0] = "00";
            }
        }
        const formData = new FormData();
        formData.append("frequency", desc);
        formData.append("cron", cron.join(" "));
        formData.append("content", form.elements.namedItem("content").value);
        fetch("add_schedule.php", {
            method: "POST",
            body: formData
        }).then((response) => {
            if (response.status != 200) {
                M.toast({html: "Erreur lors de l'ajout de cette horaire"});
                console.log("Error ", response.status, " : ", response.statusText);
            } else response.text().then((responseText) => {
                document.querySelector("tbody").insertAdjacentHTML("afterbegin", responseText);
                document.querySelector("tbody tr").addEventListener("click", function() {
                    idToRemove = this.getAttribute("id");
                    removeModal.open();
                });
                M.toast({html: "Votre message à bien été ajouté"});
            });
            modal.close();
        });
    });  
    document.querySelectorAll("tbody tr").forEach(el => el.addEventListener("click", function() {
        idToRemove = this.getAttribute("id");
        removeModal.open();
    }));
    document.querySelector("#remove_modal .modal-confirm").addEventListener("click", function() {
        fetch("remove_schedule.php?id="+idToRemove).then((response) => {
            if (response.status != 200) {
                console.log("Erreur : ", response.status, " ", responseText);
                M.toast({html: "Erreur lors de la suppression du message"});
            } else response.text().then((responseText) => {
                M.toast({html: responseText});
                document.getElementById(idToRemove).remove();
                removeModal.close();
            });
        });
    });
    document.querySelector(".webhook-status").addEventListener("click", () => {
        webhookModal.open();
    });
    document.querySelector("#webhook_modal .modal-close").addEventListener("click", () => {
        fetch("change_webhook.php?webhook="+document.querySelector("#setWebhook").value).then((response) => {
            if (response.status != 200) {
                M.toast({html: "Ereur lors du changement de webhook"});                
            } else response.text().then((responseText) => {
                M.toast({html: responseText});
                document.querySelector(".webhook-status i").textContent = "done";
            });
        });
    });
});