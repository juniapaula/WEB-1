document.querySelectorAll('#bottomContainer img').forEach(img => {
    img.addEventListener('click', function() {
        let primeiroVazio = document.querySelector('#topContainer .espacoVazio:not(.filled)');
        if (primeiroVazio) {
            let newImg = this.cloneNode();
            newImg.addEventListener('click', function() {
                this.remove();
                primeiroVazio.classList.remove('filled');
            });
            primeiroVazio.appendChild(newImg);
            primeiroVazio.classList.add('filled');
        }
    });
});
