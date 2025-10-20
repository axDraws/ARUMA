const modal = document.getElementById("imgModal");
const modalImg = document.getElementById("imgModalContent");
const closeBtn = document.querySelector(".close");

document.querySelectorAll(".masonry-gallery img").forEach(img => {
  img.addEventListener("click", () => {
    modal.style.display = "block";
    modalImg.src = img.src;
  });
});

closeBtn.addEventListener("click", () => {
  modal.style.display = "none";
});

window.addEventListener("click", e => {
  if (e.target === modal) {
    modal.style.display = "none";
  }
});

