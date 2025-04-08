function changeImage(imageUrl) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = imageUrl;
}

document.getElementById("commentForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const commentForm = document.getElementById('commentForm');
    const formData = new FormData(commentForm);

    fetch('/add-comment', {
        method: 'POST',
        body: formData,
    })
        .then(response => {
            return response.text();
        })
        .then(responseText => {
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (error) {
                console.error('JSON parsing error:', error);
                alert('Помилка сервера: ' + responseText);
                return;
            }

            if (data.status === 'success') {
                const commentList = document.getElementById('commentList');
                const newComment = document.createElement('li');
                newComment.classList.add('border-b', 'border-gray-200', 'pb-4');
                newComment.innerHTML = `
                <p class="font-semibold">Ви</p>
                <p class="text-yellow-500">Рейтинг: ${data.comment.rating}/5</p>
                <p class="text-gray-700">${data.comment.text}</p>
            `;
                commentList.appendChild(newComment);
                commentForm.reset();
                document.getElementById("noCommentsMessage").style.display = "none";
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });


});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-comment").forEach(button => {
        button.addEventListener("click", function () {
            const commentId = this.dataset.commentId;

            if (!confirm("Ви дійсно хочете видалити цей коментар?")) {
                return;
            }

            fetch("/delete-comment", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `comment_id=${commentId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        this.closest("li").remove();
                        if (document.querySelectorAll("#commentList li").length === 0) {
                            document.getElementById("commentList").style.display = "none";
                        }
                    } else {
                        alert("Помилка: " + data.message);
                    }
                })
                .catch(error => console.error("Помилка:", error));
        });
    });
});
