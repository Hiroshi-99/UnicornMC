// Replace jQuery AJAX setup with vanilla JS
const token = document.querySelector('meta[name="csrf-token"]').content;

// Add this to any fetch/XHR requests
const headers = {
    "X-CSRF-TOKEN": token,
    // other headers as needed
};

// Global function for form submission
window.showLoading = async function () {
    try {
        // Basic client-side validation
        const username = document.getElementById("username").value.trim();
        const platform = document.querySelector(
            'input[name="platform"]:checked'
        );
        const proof = document.getElementById("proof").files[0];
        const recaptcha = grecaptcha.getResponse();

        let errors = [];

        if (!username) {
            errors.push("Please enter your Minecraft username");
        } else if (username.length > 16) {
            errors.push("Username cannot exceed 16 characters");
        } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            errors.push(
                "Username can only contain letters, numbers, and underscores"
            );
        }

        if (!platform) {
            errors.push("Please select your Minecraft platform");
        }

        if (!proof) {
            errors.push("Please upload your payment proof");
        } else {
            // Check file size
            if (proof.size > 5 * 1024 * 1024) {
                errors.push("Image size cannot exceed 5MB");
            }
        }

        if (!recaptcha) {
            errors.push(
                "Please verify that you're not a robot by completing the reCAPTCHA"
            );
        }

        if (errors.length > 0) {
            showErrors(errors);
            return false;
        }

        // Show loading state
        document.getElementById("loadingOverlay").style.display = "flex";
        document.getElementById("orderForm").classList.add("form-submitting");

        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.classList.add("processing");
        submitBtn.disabled = true;
        submitBtn.setAttribute("data-original-text", submitBtn.textContent);
        submitBtn.textContent = "Processing...";

        return true;
    } catch (error) {
        console.error("Form submission error:", error);
        return false;
    }
};

function showErrors(errors) {
    const errorDiv = document.createElement("div");
    errorDiv.className = "alert alert-error";
    errorDiv.innerHTML = `<ul>${errors
        .map((e) => `<li>${e}</li>`)
        .join("")}</ul>`;

    const existingAlert = document.querySelector(".alert");
    if (existingAlert) {
        existingAlert.remove();
    }

    document
        .querySelector(".order-form-container")
        .insertBefore(errorDiv, document.getElementById("orderForm"));
}

function startLoading() {
    // Show loading overlay with animation
    document.getElementById("loadingOverlay").style.display = "flex";
    document.getElementById("orderForm").classList.add("form-submitting");

    // Animate submit button
    const submitBtn = document.querySelector('button[type="submit"]');
    submitBtn.classList.add("processing");
    submitBtn.disabled = true;

    // Store original button text
    submitBtn.setAttribute("data-original-text", submitBtn.textContent);
    submitBtn.textContent = "";

    return true;
}

document.addEventListener("DOMContentLoaded", () => {
    const uploadArea = document.getElementById("uploadArea");
    const fileInput = document.getElementById("proof");
    const previewArea = document.querySelector(".preview-area");
    const imagePreview = document.getElementById("imagePreview");
    const placeholder = document.querySelector(".upload-placeholder");
    const removeButton = document.querySelector(".remove-image");
    const orderForm = document.getElementById("orderForm");

    // Copy phone number functionality
    document.querySelector(".copy-btn").addEventListener("click", function () {
        const phoneNumber = document.getElementById("phoneNumber").textContent;
        navigator.clipboard.writeText(phoneNumber).then(() => {
            const copyBtn = this;
            const copyText = copyBtn.querySelector(".copy-text");

            // Show copied state
            copyBtn.classList.add("copied");
            copyText.textContent = "Copied!";

            // Reset after 2 seconds
            setTimeout(() => {
                copyBtn.classList.remove("copied");
                copyText.textContent = "Copy";
            }, 2000);
        });
    });

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
    if (orderForm) {
        orderForm.addEventListener("submit", function (e) {
            const rankInput = this.querySelector('input[name="rank"]');
            const priceInput = this.querySelector('input[name="price"]');

            // Make inputs readonly
            if (rankInput) rankInput.readOnly = true;
            if (priceInput) priceInput.readOnly = true;

            // Prevent double submission
            if (this.classList.contains("submitted")) {
                e.preventDefault();
                return;
            }
            this.classList.add("submitted");
        });
    }

    // Initialize page state
    document.getElementById("loadingOverlay").style.display = "none";
    orderForm.classList.remove("form-submitting");
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn.hasAttribute("data-original-text")) {
        submitBtn.textContent = submitBtn.getAttribute("data-original-text");
        submitBtn.classList.remove("processing");
        submitBtn.disabled = false;
    }
});

// QR Modal functionality
document
    .querySelector(".payment-qr-container")
    .addEventListener("click", function () {
        const modal = document.getElementById("qrModal");
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
    });

document.querySelector(".close-modal").addEventListener("click", function () {
    const modal = document.getElementById("qrModal");
    modal.style.display = "none";
    document.body.style.overflow = "";
});

document.getElementById("qrModal").addEventListener("click", function (e) {
    if (e.target === this) {
        this.style.display = "none";
        document.body.style.overflow = "";
    }
});

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        const modal = document.getElementById("qrModal");
        modal.style.display = "none";
        document.body.style.overflow = "";
    }
});

// CSRF handling
document.addEventListener("DOMContentLoaded", function () {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    if (token) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": token,
            },
        });
    }
});

// Remove the automatic token refresh to avoid message channel issues
// Instead, handle token refresh only when needed (on 419 errors)
$(document).ajaxError(function (event, jqXHR) {
    if (jqXHR.status === 419) {
        window.location.reload();
    }
});
