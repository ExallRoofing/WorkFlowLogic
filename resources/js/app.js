import './bootstrap';

document.getElementById("contactForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = this;
    const formMessage = document.getElementById("formMessage");
    const formErrors = document.getElementById("formErrors");

    // Reset UI
    formMessage.textContent = "";
    formErrors.innerHTML = "";
    form.querySelectorAll(".error").forEach(el => el.classList.remove("border-red-500"));
    form.querySelectorAll(".error-text").forEach(el => el.remove());

    const response = await fetch(form.action, {
        method: "POST",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        },
        body: new FormData(form)
    });

    const data = await response.json();

    if (data.success) {
        formMessage.textContent = "Message sent successfully!";
        form.reset();
        return;
    }

    // ----- NEW: handle Statamic's error format -----
    // Field-specific errors
    if (data.error && typeof data.error === "object") {
        Object.entries(data.error).forEach(([field, message]) => {
            const input = document.getElementById(field);
            if (!input) return;

            // Highlight field
            input.classList.add("border-red-500", "error");

            // Inline error message
            const errorText = document.createElement("div");
            errorText.className = "text-red-600 text-sm mt-1 error-text";
            errorText.textContent = message;
            input.insertAdjacentElement("afterend", errorText);
        });
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

