document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("createBrandForm");
    const brandNameInput = document.getElementById("brand_name");
    const errorMessage = document.getElementById("error-message");
    const brandList = document.querySelector(".space-y-2");

    form.addEventListener("submit", async function (event) {
        event.preventDefault();

        const brandName = brandNameInput.value.trim();

        if (brandName === "") {
            errorMessage.textContent = "Назва бренду не може бути порожньою.";
            errorMessage.style.display = "block";
            return;
        }

        try {
            const response = await fetch("/add-brand/store", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `brand_name=${encodeURIComponent(brandName)}`,
            });

            const result = await response.json();

            if (response.ok) {
                const newBrand = document.createElement("li");
                newBrand.classList.add("p-2", "bg-white", "rounded", "shadow");
                newBrand.textContent = brandName;
                brandList.appendChild(newBrand);

                brandNameInput.value = "";
                errorMessage.style.display = "none";
            } else {
                errorMessage.textContent = result.error;
                errorMessage.style.display = "block";
            }
        } catch (error) {
            errorMessage.textContent = "Сталася помилка. Спробуйте ще раз.";
            errorMessage.style.display = "block";
        }
    });
});
