// Öppnar skapa kategori popup (modal) när knapp trycks
let createCategoryButton = document.getElementById('createCategoryButton');
let createCategoryModal = document.getElementById('createCategoryModal');
let closeCategoryButtons = document.querySelectorAll('#createCategoryModal button[value="cancel"]');

createCategoryButton?.addEventListener('click', () => {
    createCategoryModal?.showModal();
});

closeCategoryButtons.forEach(button => {
    button.addEventListener('click', () => {
        createCategoryModal.close();
    })
});

// Automatiskt visa modal om showModal är satt
if(createCategoryModal?.hasAttribute("showModal")) {
    createCategoryModal.showModal();
}