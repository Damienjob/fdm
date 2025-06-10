// Modal functionality
const activityModal = document.getElementById("activityModal");
const viewModal = document.getElementById("viewModal");
const deleteModal = document.getElementById("deleteModal");
const closeModal = document.getElementById("closeModal");
const closeModal2 = document.getElementById("closeModal2");
const cancelBtn = document.getElementById("cancelBtn");
const cancelBtn2 = document.getElementById("cancelBtn2");
const closeViewModal = document.getElementById("closeViewModal");
const closeViewBtn = document.getElementById("closeViewBtn");
const editFromViewBtn = document.getElementById("editFromViewBtn");
const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
const modalTitle = document.getElementById("modalTitle");
const modifbtn = document.getElementById("modifbtn");
// View buttons
const viewBtns = document.querySelectorAll(".view-btn");
const editBtns = document.querySelectorAll(".edit-btn");
const deleteBtns = document.querySelectorAll(".delete-btn");
// Open add activity modal
addActivityBtn.addEventListener("click", () => {
  modalTitle.textContent = "Ajouter une activité";
  document.getElementById("mainImagePreview").classList.add("hidden");
  document.getElementById("mainImagePlaceholder").classList.remove("hidden");
  document.getElementById("additionalImagesPreview").innerHTML = "";
  document.getElementById("editor-content").classList.add("active-editor");
  // Clear WYSIWYG editor
  document.getElementById("editor-content").innerHTML = "";
  document.getElementById("description").value = "";

  activityModal.classList.remove("hidden");
});

// Close modals
closeModal.addEventListener("click", () => {
  activityModal.classList.add("hidden");
  document.getElementById("modifModal").classList.add("hidden");
});
closeModal2.addEventListener("click", () => {
  document.getElementById("modifModal").classList.add("hidden");
});
cancelBtn.addEventListener("click", () => {
  activityModal.classList.add("hidden");
});
cancelBtn2.addEventListener("click", () => {
  document.getElementById("modifModal").classList.add("hidden");
});
closeViewModal.addEventListener("click", () => {
  viewModal.classList.add("hidden");
});
closeViewBtn.addEventListener("click", () => {
  viewModal.classList.add("hidden");
});

// Open view modal
// Ajouter un écouteur d'événements pour chaque bouton
viewBtns.forEach((btn) => {
  btn.addEventListener("click", function () {
    const activityId = this.getAttribute("data-id"); // Récupérer l'ID de l'activité
    // Avant fetch
    Swal.fire({
      title: "Chargement...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Utiliser AJAX pour récupérer les détails de l'activité
    fetch(`activite/activity-details.php?id=${activityId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Mettre à jour le modal avec les informations de l'activité
          document.getElementById("viewMainImage").src =
            "http://localhost/fdm-main/uploads/" + data.activity.image;
          document.getElementById("viewTitle").innerText = data.activity.titre;

          document.getElementById("viewDescription").innerHTML =
            data.activity.description;

          // Mettre à jour la galerie d'images
          const imageGallery = document.getElementById("gallery");
          imageGallery.innerHTML = ""; // Vider la galerie actuelle
          data.activity.images.forEach((image) => {
            const img = document.createElement("img");
            img.src = "http://localhost/fdm-main/uploads/" + image.chemin;
            img.alt = "Image supplémentaire";
            img.classList.add(
              "w-full", // Largeur 100%
              "h-40", // Hauteur fixe (ex: h-40 = 10rem)
              "object-cover",
              "aspect-video",
              "bg-gray-100",
              "rounded",
              "overflow-hidden"
            );
            imageGallery.appendChild(img);
          });

          // Mettre l'ID dans le bouton "Modifier"
          editFromViewBtn.setAttribute("data-id", activityId);

          // Afficher le modal
          viewModal.classList.remove("hidden");
          Swal.close();
        }
      })
      .catch((error) => {
        console.error("Erreur:", error);
      });
  });
});
// Lorsque le DOM est chargé
document.addEventListener("DOMContentLoaded", function () {
  const mainImageInput = document.getElementById("main_image2");
  const mainImagePreview = document.getElementById("mainImagePreview2");
  const mainImagePlaceholder = document.getElementById("mainImagePlaceholder2");

  // Lors du changement de l'image principale
  mainImageInput.addEventListener("change", function (event) {
    const file = event.target.files[0];

    if (file) {
      const objectURL = URL.createObjectURL(file);
      mainImagePreview.src = objectURL;
      mainImagePreview.classList.remove("hidden");
      mainImagePlaceholder.classList.add("hidden");
    } else {
      mainImagePreview.classList.add("hidden");
      mainImagePlaceholder.classList.remove("hidden");
    }
  });

  // Bouton pour ouvrir le modal de modification depuis la vue
  const editFromViewBtn = document.getElementById("editFromViewBtn");
  editFromViewBtn.addEventListener("click", () => {
    document.getElementById("editor-content2").classList.add("active-editor");
    document.getElementById("editor-content").classList.remove("active-editor");

    viewModal.classList.add("hidden");
    const activityId = editFromViewBtn.getAttribute("data-id");

    fetch(`activite/activity-details.php?id=${activityId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Remplissage des champs
          document.getElementById("title2").value = data.activity.titre;
          const descriptionContent = data.activity.description;
          document.getElementById("editor-content2").innerHTML =
            descriptionContent;
          document.getElementById("description2").value = descriptionContent;
          document
            .getElementById("modification")
            .setAttribute("data-id", activityId);

          // 🔹 Image principale depuis la base
          if (data.activity.image) {
            mainImagePreview.src =
              "http://localhost/fdm-main/uploads/" + data.activity.image;
            mainImagePreview.classList.remove("hidden");
            mainImagePlaceholder.classList.add("hidden");
          } else {
            mainImagePreview.classList.add("hidden");
            mainImagePlaceholder.classList.remove("hidden");
          }

          // 🔹 Images supplémentaires
          const additionalImagesPreview = document.getElementById(
            "additionalImagesPreview2"
          );
          additionalImagesPreview.innerHTML = "";
          data.activity.images.forEach((img) => {
            const imgContainer = document.createElement("div");
            imgContainer.className =
              "image-preview aspect-video bg-gray-100 rounded overflow-hidden relative";

            const imgElement = document.createElement("img");
            imgElement.src = "http://localhost/fdm-main/uploads/" + img.chemin;
            imgElement.className = "w-full h-full object-cover";

            const removeBtn = document.createElement("div");
            removeBtn.className = "remove-image";
            removeBtn.innerHTML = '<i class="ri-close-line"></i>';
            removeBtn.style.cursor = "pointer";

            // Ajout de l'écouteur de suppression
            removeBtn.addEventListener("click", function () {
              Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Cette image sera définitivement supprimée.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer",
                cancelButtonText: "Annuler",
              }).then((result) => {
                if (result.isConfirmed) {
                  fetch("activite/delete-image.php", {
                    method: "POST",
                    headers: {
                      "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                      imageId: img.id, // L’ID de l’image dans la base
                    }),
                  })
                    .then((res) => res.json())
                    .then((data) => {
                      if (data.success) {
                        imgContainer.remove();
                        Swal.fire(
                          "Supprimée!",
                          "L'image a été supprimée.",
                          "success"
                        );
                      } else {
                        Swal.fire(
                          "Erreur",
                          "Impossible de supprimer l’image.",
                          "error"
                        );
                      }
                    })
                    .catch((err) => {
                      console.error(err);
                      Swal.fire(
                        "Erreur",
                        "Erreur lors de la suppression.",
                        "error"
                      );
                    });
                }
              });
            });

            imgContainer.appendChild(imgElement);
            imgContainer.appendChild(removeBtn);
            additionalImagesPreview.appendChild(imgContainer);
          });

          document.getElementById("modifModal").classList.remove("hidden");
        } else {
          console.error("Erreur lors de la récupération des données.");
        }
      })
      .catch((error) => {
        console.error("Erreur:", error);
      });
  });

  // Formulaire de modification
  document.getElementById("editForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const activity = document
      .getElementById("modification")
      .getAttribute("data-id");

    const formData = new FormData(this);
    formData.append("id", activity);
    // Ajoutez la description via l'éditeur si vous utilisez un éditeur riche
    const descriptionContent =
      document.getElementById("editor-content2").innerHTML;
    formData.append("description", descriptionContent);

    Swal.fire({
      title: "Mise à jour...",
      text: "Veuillez patienter pendant la mise à jour de l'activité",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    fetch("activite/update-activity.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        Swal.close();
        if (data.success) {
          // 🔹 Si l’image principale a été modifiée
          if (data.image_principale_modifiee && data.nouvelle_image) {
            mainImagePreview.src =
              "http://localhost/fdm-main/uploads/" + data.nouvelle_image;
            mainImagePreview.classList.remove("hidden");
            mainImagePlaceholder.classList.add("hidden");
          }

          // 🔹 Si des images supplémentaires ont été ajoutées
          if (
            data.images_supplementaires &&
            Array.isArray(data.images_supplementaires)
          ) {
            const additionalImagesPreview = document.getElementById(
              "additionalImagesPreview2"
            );
            data.images_supplementaires.forEach((imgFileName) => {
              const imgContainer = document.createElement("div");
              imgContainer.className =
                "image-preview aspect-video bg-gray-100 rounded overflow-hidden relative";
              const imgElement = document.createElement("img");
              imgElement.src =
                "http://localhost/fdm-main/uploads/" + imgFileName;
              imgElement.className = "w-full h-full object-cover";
              const removeBtn = document.createElement("div");
              removeBtn.className = "remove-image";
              removeBtn.innerHTML = '<i class="ri-close-line"></i>';
              removeBtn.addEventListener("click", function () {
                imgContainer.remove();
              });
              imgContainer.appendChild(imgElement);
              imgContainer.appendChild(removeBtn);
              additionalImagesPreview.appendChild(imgContainer);
            });
          }

          Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Activité mise à jour avec succès 🎉",
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
          }).then(() => {
            document.getElementById("modifModal")?.classList.add("hidden");
            location.reload();
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: "Une erreur s'est produite.",
          });
        }
      })
      .catch((err) => {
        console.error("Erreur fetch:", err);
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Erreur réseau",
          text:
            "Impossible de contacter le serveur. Veuillez réessayer plus tard.\n" +
            err,
        });
      });
  });
});

// Open edit modal

// WYSIWYG Editor functionality
const editorButtons = document.querySelectorAll("#toolbar button");
const editorContent = document.getElementById("editor-content");
const descriptionTextarea = document.getElementById("description");
// Initialize editor with textarea content if exists
editorContent.addEventListener("input", function () {
  descriptionTextarea.value = editorContent.innerHTML;
});
// Set up editor commands
editorButtons.forEach((button) => {
  button.addEventListener("click", function () {
    const command = this.dataset.command;
    // 🔄 Récupère dynamiquement l'éditeur actif
    const editorContent = document.querySelector(
      "[contenteditable].active-editor"
    );
    const descriptionTextarea =
      editorContent.id === "editor-content2"
        ? document.getElementById("description2")
        : document.getElementById("description");

    if (command === "createLink") {
      const url = prompt("Entrez l'URL du lien:", "https://");
      if (url) {
        document.execCommand(command, false, url);
      }
    } else if (button.id === "imageInsertBtn") {
      const imageModal = document.createElement("div");
      imageModal.className =
        "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50";
      imageModal.innerHTML = `
<div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
<div class="p-4 border-b border-gray-200 flex justify-between items-center">
<h3 class="text-lg font-medium">Insérer une image</h3>
<button class="text-gray-400 hover:text-gray-500" id="closeImageModal">
<i class="ri-close-line text-2xl"></i>
</button>
</div>
<div class="p-4">
<div class="mb-4">
<label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner une image</label>
<div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-primary" id="imageDropzone">
<i class="ri-upload-cloud-2-line text-4xl text-gray-400 mb-2"></i>
<p class="text-gray-500">Glissez et déposez une image ou <span class="text-primary">parcourir</span></p>
<input type="file" class="hidden" id="imageInput" accept="image/*">
</div>
</div>
<div class="mb-4 hidden" id="imagePreviewContainer">
<img src="" alt="Aperçu" id="imagePreview" class="max-h-32 mx-auto mb-4">
<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-2">Largeur (px)</label>
<input type="number" id="imageWidth" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-2">Hauteur (px)</label>
<input type="number" id="imageHeight" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
</div>
</div>
<div class="mt-4">
<label class="block text-sm font-medium text-gray-700 mb-2">Alignement</label>
<div class="flex space-x-4">
<button class="p-2 rounded hover:bg-gray-100" data-align="left" id="alignLeft">
<i class="ri-align-left"></i>
</button>
<button class="p-2 rounded hover:bg-gray-100" data-align="center" id="alignCenter">
<i class="ri-align-center"></i>
</button>
<button class="p-2 rounded hover:bg-gray-100" data-align="right" id="alignRight">
<i class="ri-align-right"></i>
</button>
</div>
</div>
<div class="mt-4">
<label class="block text-sm font-medium text-gray-700 mb-2">Légende</label>
<input type="text" id="imageCaption" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm" placeholder="Ajouter une légende (optionnel)">
</div>
</div>
<div class="flex justify-end space-x-4 mt-4">
<button class="px-4 py-2 border border-gray-300 rounded-button text-gray-700 hover:bg-gray-50" id="cancelImageInsert">Annuler</button>
<button class="px-4 py-2 bg-primary text-white rounded-button hover:bg-opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" id="insertImageBtn" disabled>Insérer</button>
</div>
</div>
</div>
`;
      document.body.appendChild(imageModal);
      const imageDropzone = document.getElementById("imageDropzone");
      const imageInput = document.getElementById("imageInput");
      const imagePreview = document.getElementById("imagePreview");
      const imagePreviewContainer = document.getElementById(
        "imagePreviewContainer"
      );
      const insertImageBtn = document.getElementById("insertImageBtn");
      const closeImageModal = document.getElementById("closeImageModal");
      const cancelImageInsert = document.getElementById("cancelImageInsert");
      const imageWidth = document.getElementById("imageWidth");
      const imageHeight = document.getElementById("imageHeight");
      const imageCaption = document.getElementById("imageCaption");
      imageDropzone.addEventListener("click", () => imageInput.click());
      imageInput.addEventListener("change", handleImageSelect);
      imageDropzone.addEventListener("dragover", (e) => {
        e.preventDefault();
        imageDropzone.classList.add("border-primary");
      });
      imageDropzone.addEventListener("dragleave", () => {
        imageDropzone.classList.remove("border-primary");
      });
      imageDropzone.addEventListener("drop", (e) => {
        e.preventDefault();
        imageDropzone.classList.remove("border-primary");
        if (e.dataTransfer.files.length) {
          handleImageFile(e.dataTransfer.files[0]);
        }
      });
      function handleImageSelect(e) {
        if (e.target.files.length) {
          handleImageFile(e.target.files[0]);
        }
      }
      function handleImageFile(file) {
        if (!file.type.match("image.*")) {
          alert("Veuillez sélectionner une image.");
          return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = new Image();
          img.onload = function () {
            const maxWidth = 400;
            const maxHeight = 300;
            let newWidth = this.width;
            let newHeight = this.height;
            if (this.width > maxWidth || this.height > maxHeight) {
              const ratio = Math.min(
                maxWidth / this.width,
                maxHeight / this.height
              );
              newWidth = Math.floor(this.width * ratio);
              newHeight = Math.floor(this.height * ratio);
            }
            imageWidth.value = newWidth;
            imageHeight.value = newHeight;
            imagePreview.src = e.target.result;
            imagePreviewContainer.classList.remove("hidden");
            insertImageBtn.disabled = false;
          };
          img.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
      document.querySelectorAll("[data-align]").forEach((btn) => {
        btn.addEventListener("click", function () {
          document
            .querySelectorAll("[data-align]")
            .forEach((b) => b.classList.remove("bg-gray-100"));
          this.classList.add("bg-gray-100");
        });
      });
      insertImageBtn.addEventListener("click", () => {
        const img = document.createElement("img");
        img.src = imagePreview.src;
        img.width = Math.min(imageWidth.value, 400);
        img.height = Math.min(imageHeight.value, 300);
        img.style.maxWidth = "400px";
        img.style.maxHeight = "300px";
        const alignBtn = document.querySelector("[data-align].bg-gray-100");
        if (alignBtn) {
          const align = alignBtn.getAttribute("data-align");
          img.style.display = "block";
          img.style.margin =
            align === "center"
              ? "0 auto"
              : align === "right"
              ? "0 0 0 auto"
              : "0";
        }
        const figure = document.createElement("figure");
        figure.appendChild(img);
        if (imageCaption.value.trim()) {
          const figcaption = document.createElement("figcaption");
          figcaption.textContent = imageCaption.value;
          figcaption.className = "text-center text-sm text-gray-500 mt-2";
          figure.appendChild(figcaption);
        }
        editorContent.focus();
        const selection = window.getSelection();
        const range = selection.getRangeAt(0);
        range.deleteContents();
        range.insertNode(figure);
        imageModal.remove();
      });
      [closeImageModal, cancelImageInsert].forEach((btn) => {
        btn.addEventListener("click", () => imageModal.remove());
      });
    } else {
      document.execCommand(command, false, null);
    }

    editorContent.focus();
    descriptionTextarea.value = editorContent.innerHTML;
  });
});
// Form submission

// Image preview functionality
document.getElementById("main_image").addEventListener("change", function (e) {
  if (e.target.files.length > 0) {
    const file = e.target.files[0];
    const reader = new FileReader();
    reader.onload = function (e) {
      const preview = document.getElementById("mainImagePreview");
      preview.src = e.target.result;
      preview.classList.remove("hidden");
      document.getElementById("mainImagePlaceholder").classList.add("hidden");
    };
    reader.readAsDataURL(file);
  }
});
document
  .getElementById("additional_images")
  .addEventListener("change", function (e) {
    if (e.target.files.length > 0) {
      const container = document.getElementById("additionalImagesPreview");
      for (let i = 0; i < e.target.files.length; i++) {
        const file = e.target.files[i];
        const reader = new FileReader();
        reader.onload = function (e) {
          const imgContainer = document.createElement("div");
          imgContainer.className =
            "image-preview aspect-video bg-gray-100 rounded overflow-hidden relative";
          const img = document.createElement("img");
          img.src = e.target.result;
          img.className = "w-full h-full object-cover";
          const removeBtn = document.createElement("div");
          removeBtn.className = "remove-image";
          removeBtn.innerHTML = '<i class="ri-close-line"></i>';
          removeBtn.addEventListener("click", function () {
            imgContainer.remove();
          });
          imgContainer.appendChild(img);
          imgContainer.appendChild(removeBtn);
          container.appendChild(imgContainer);
        };
        reader.readAsDataURL(file);
      }
    }
  });
// Confirm delete

document.querySelectorAll(".delete-btn").forEach((button) => {
  button.addEventListener("click", () => {
    const activityId = button.dataset.id;

    Swal.fire({
      title: "Êtes-vous sûr ?",
      text: "Voulez-vous vraiment supprimer cette activité ?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Oui, supprimer",
      cancelButtonText: "Annuler",
    }).then((result) => {
      if (result.isConfirmed) {
        fetch("delete_activity.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: "id=" + encodeURIComponent(activityId),
        })
          .then((response) => response.text())
          .then((data) => {
            if (data.trim() === "success") {
              // Supprimer la ligne du tableau
              const row = button.closest("tr");
              row.remove();

              // ✅ Afficher un toast de succès avec SweetAlert2
              Swal.fire({
                toast: true,
                position: "bottom-end",
                icon: "success",
                title:
                  '<i class="ri-delete-bin-line mr-2"></i>Activité supprimée avec succès !',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: "#f87171", // optionnel pour une couleur rouge
                color: "#fff",
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Erreur",
                text: "Une erreur est survenue lors de la suppression.",
              });
            }
          });
      }
    });
  });
});

// Gestion de l'export d'emails
document.addEventListener("DOMContentLoaded", function () {
  // Export d'emails
  const exportEmailsBtn = document.getElementById("exportEmailsBtn");
  if (exportEmailsBtn) {
    exportEmailsBtn.addEventListener("click", function () {
      window.location.href = "export_emails.php";
    });
  }

  // Sélection d'emails
  const selectAllEmails = document.getElementById("selectAllEmails");
  const emailCheckboxes = document.querySelectorAll(".email-checkbox");
  const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");

  if (selectAllEmails) {
    selectAllEmails.addEventListener("change", function () {
      const isChecked = this.checked;
      emailCheckboxes.forEach((checkbox) => {
        checkbox.checked = isChecked;
      });
      updateBulkDeleteButton();
    });
  }

  emailCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", updateBulkDeleteButton);
  });

  // Mise à jour du bouton de suppression en masse
  function updateBulkDeleteButton() {
    const checkedCount = document.querySelectorAll(
      ".email-checkbox:checked"
    ).length;
    if (bulkDeleteBtn) {
      bulkDeleteBtn.disabled = checkedCount === 0;
      bulkDeleteBtn.innerHTML = `
                <div class="w-4 h-4 flex items-center justify-center mr-1">
                    <i class="ri-delete-bin-line"></i>
                </div>
                Supprimer (${checkedCount})
            `;
    }
  }

  // Suppression en masse
  if (bulkDeleteBtn) {
    bulkDeleteBtn.addEventListener("click", function () {
      const selectedIds = [];
      document
        .querySelectorAll(".email-checkbox:checked")
        .forEach((checkbox) => {
          selectedIds.push(parseInt(checkbox.value));
        });

      if (selectedIds.length === 0) return;

      // Afficher la confirmation
      const deleteEmailModal = document.getElementById("deleteEmailModal");
      const confirmDeleteEmailBtn = document.getElementById(
        "confirmDeleteEmailBtn"
      );
      const cancelDeleteEmailBtn = document.getElementById(
        "cancelDeleteEmailBtn"
      );

      if (deleteEmailModal) {
        deleteEmailModal.querySelector(
          "p"
        ).textContent = `Êtes-vous sûr de vouloir supprimer ${selectedIds.length} email(s) de la liste des abonnés ? Cette action est irréversible.`;
        deleteEmailModal.classList.remove("hidden");

        // Gérer la confirmation
        confirmDeleteEmailBtn.onclick = function () {
          // Envoyer la requête AJAX
          fetch("bulk_delete_emails.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "ids=" + JSON.stringify(selectedIds),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                // Recharger la page après la suppression
                Swal.fire({
                  title: "Suppression réussie !",
                  text: `${data.count} email(s) ont été supprimés.`,
                  icon: "success",
                  confirmButtonColor: "#19CDFE",
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire({
                  title: "Erreur",
                  text:
                    data.message ||
                    "Une erreur est survenue lors de la suppression.",
                  icon: "error",
                  confirmButtonColor: "#19CDFE",
                });
              }
              deleteEmailModal.classList.add("hidden");
            })
            .catch((error) => {
              console.error("Erreur:", error);
              Swal.fire({
                title: "Erreur",
                text: "Une erreur technique est survenue.",
                icon: "error",
                confirmButtonColor: "#19CDFE",
              });
              deleteEmailModal.classList.add("hidden");
            });
        };

        // Gérer l'annulation
        cancelDeleteEmailBtn.onclick = function () {
          deleteEmailModal.classList.add("hidden");
        };
      }
    });
  }

  // Suppression individuelle d'email
  const emailDeleteBtns = document.querySelectorAll(".email-delete-btn");
  emailDeleteBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      const emailId = this.getAttribute("data-id");
      const deleteEmailModal = document.getElementById("deleteEmailModal");
      const confirmDeleteEmailBtn = document.getElementById(
        "confirmDeleteEmailBtn"
      );
      const cancelDeleteEmailBtn = document.getElementById(
        "cancelDeleteEmailBtn"
      );

      if (deleteEmailModal) {
        deleteEmailModal.querySelector("p").textContent =
          "Êtes-vous sûr de vouloir supprimer cet email de la liste des abonnés ? Cette action est irréversible.";
        deleteEmailModal.classList.remove("hidden");

        // Gérer la confirmation
        confirmDeleteEmailBtn.onclick = function () {
          // Envoyer la requête AJAX
          fetch("delete_email.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "id=" + emailId,
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                Swal.fire({
                  title: "Suppression réussie !",
                  text: "L'email a été supprimé avec succès.",
                  icon: "success",
                  confirmButtonColor: "#19CDFE",
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire({
                  title: "Erreur",
                  text:
                    data.message ||
                    "Une erreur est survenue lors de la suppression.",
                  icon: "error",
                  confirmButtonColor: "#19CDFE",
                });
              }
              deleteEmailModal.classList.add("hidden");
            })
            .catch((error) => {
              console.error("Erreur:", error);
              Swal.fire({
                title: "Erreur",
                text: "Une erreur technique est survenue.",
                icon: "error",
                confirmButtonColor: "#19CDFE",
              });
              deleteEmailModal.classList.add("hidden");
            });
        };

        // Gérer l'annulation
        cancelDeleteEmailBtn.onclick = function () {
          deleteEmailModal.classList.add("hidden");
        };
      }
    });
  });
});
