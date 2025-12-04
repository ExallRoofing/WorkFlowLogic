import './bootstrap';
document.getElementById("contactForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = this;
    const formMessage = document.getElementById("formMessage");
    const formErrors = document.getElementById("formErrors");
    const submitBtn = document.getElementById("submitBtn");
    const btnText = submitBtn.querySelector(".btn-text");
    const loader = submitBtn.querySelector(".loader");

    // Reset UI errors
    formMessage.textContent = "";
    formErrors.innerHTML = "";
    form.querySelectorAll(".error").forEach(el => el.classList.remove("border-red-500"));
    form.querySelectorAll(".error-text").forEach(el => el.remove());

    // 🔵 Show loader
    submitBtn.disabled = true;
    loader.classList.remove("hidden");
    btnText.classList.add("invisible");

    const response = await fetch(form.action, {
        method: "POST",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        },
        body: new FormData(form)
    });

    const data = await response.json();

    // 🟢 Success - Show Modal
    if (data.success) {
        form.reset();

        // Reset button
        submitBtn.disabled = false;
        loader.classList.add("hidden");
        btnText.classList.remove("invisible");

        // Show success modal with animation
        showSuccessModal();

        return;
    }

    // 🔴 Field errors
    if (data.error && typeof data.error === "object") {
        Object.entries(data.error).forEach(([field, message]) => {
            const input = document.getElementById(field);
            if (!input) return;

            input.classList.add("border-red-500", "error");

            const errorText = document.createElement("div");
            errorText.className = "text-red-600 text-sm mt-1 error-text";
            errorText.textContent = message;
            input.insertAdjacentElement("afterend", errorText);
        });
    }

    // Reset button on errors
    submitBtn.disabled = false;
    loader.classList.add("hidden");
    btnText.classList.remove("invisible");
});

// Modal Functions
function showSuccessModal() {
    const modal = document.getElementById("successModal");
    const backdrop = document.getElementById("modalBackdrop");
    const content = document.getElementById("modalContent");

    // Show modal
    modal.classList.remove("hidden");
    document.body.classList.add("modal-open");

    // Trigger animations
    requestAnimationFrame(() => {
        backdrop.classList.remove("opacity-0");
        content.classList.remove("scale-95", "opacity-0");
        content.classList.add("scale-100", "opacity-100");
    });
}

function hideSuccessModal() {
    const modal = document.getElementById("successModal");
    const backdrop = document.getElementById("modalBackdrop");
    const content = document.getElementById("modalContent");

    // Reverse animations
    backdrop.classList.add("opacity-0");
    content.classList.add("scale-95", "opacity-0");
    content.classList.remove("scale-100", "opacity-100");

    // Hide after animation
    setTimeout(() => {
        modal.classList.add("hidden");
        document.body.classList.remove("modal-open");
    }, 300);
}

// Close modal event listeners
document.getElementById("closeModal").addEventListener("click", hideSuccessModal);
document.getElementById("closeModalX").addEventListener("click", hideSuccessModal);
document.getElementById("modalBackdrop").addEventListener("click", hideSuccessModal);

// Close on Escape key
document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
        hideSuccessModal();
    }
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function (e) {
        const href = this.getAttribute("href");

        // 1. Skip empty "#" (modal triggers)
        if (href === "#") return;

        // 2. Skip links handled by Alpine (they use @click)
        if (this.hasAttribute('@click') || this.hasAttribute('x-on:click')) return;

        // 3. Get target section
        const target = document.querySelector(href);
        if (!target) return;

        e.preventDefault();

        // 4. Smooth scroll with offset for sticky header
        window.scrollTo({
            top: target.offsetTop - 80,
            behavior: "smooth"
        });
    });
});

