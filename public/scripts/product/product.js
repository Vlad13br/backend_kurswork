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
        .then(response => response.json())
        .then(data => {
            if (data.status == 'success') {
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
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Сталася помилка при додаванні коментаря.');
        });
});
