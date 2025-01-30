document.addEventListener("DOMContentLoaded", () => {
    const uploadArea = document.getElementById("uploadArea");
    const fileInput = document.getElementById("proof");
    const previewArea = document.querySelector(".preview-area");
    const imagePreview = document.getElementById("imagePreview");
    const placeholder = document.querySelector(".upload-placeholder");
    const removeButton = document.querySelector(".remove-image");

    // Maximum file size in bytes (5MB)
    const MAX_FILE_SIZE = 5 * 1024 * 1024;
    const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/gif"];

    // Handle drag and drop
    ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ["dragenter", "dragover"].forEach((eventName) => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.classList.add("dragover");
        });
    });

    ["dragleave", "drop"].forEach((eventName) => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.classList.remove("dragover");
        });
    });

    uploadArea.addEventListener("drop", handleDrop);
    fileInput.addEventListener("change", handleFiles);
    removeButton.addEventListener("click", removeImage);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files } });
    }

    function handleFiles(e) {
        const file = e.target.files[0];

        if (!file) return;

        // Validate file type
        if (!ALLOWED_TYPES.includes(file.type)) {
            alert("Please upload an image file (JPEG, PNG, or GIF)");
            fileInput.value = "";
            return;
        }

        // Validate file size
        if (file.size > MAX_FILE_SIZE) {
            alert("File size must be less than 5MB");
            fileInput.value = "";
            return;
        }

        const reader = new FileReader();

        // Add loading state
        placeholder.innerHTML = '<div class="spinner">Loading...</div>';

        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            placeholder.style.display = "none";
            previewArea.style.display = "block";
        };

        reader.onerror = function () {
            alert("Error reading file");
            removeImage();
        };

        reader.readAsDataURL(file);
    }

    function removeImage() {
        fileInput.value = "";
        imagePreview.src = "#";
        placeholder.innerHTML = `
            <div class="upload-icon">📁</div>
            <div class="upload-text">Drag and drop or click to upload payment proof</div>
        `;
        placeholder.style.display = "flex";
        previewArea.style.display = "none";
    }

    // Add form submit handler
    const orderForm = document.getElementById("orderForm");
    if (orderForm) {
        orderForm.addEventListener("submit", function (e) {
            const rankInput = this.querySelector('input[name="rank"]');
            const priceInput = this.querySelector('input[name="price"]');

            // Make inputs readonly
            if (rankInput) rankInput.readOnly = true;
            if (priceInput) priceInput.readOnly = true;
        });
    }
});
