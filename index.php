<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>📋 Clipboard Manager</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<style>
  .clipboard-item:hover {
    transform: scale(1.02);
  }
  .dragging {
    opacity: 0.5;
  }
</style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center py-10">

<DIV align="center">
  <img src="https://dihu.fr/appgithub/iconedihu/9.png" width="120" style="border-radius: 20px; margin-bottom: 15px;">
  <H3>📋 Clipboard Manager</H3>
  <h4>Gère plusieurs presse-papiers localement, avec favoris et recherche</h4>
</DIV>

<div class="w-full max-w-xl bg-gray-800 p-6 rounded-xl shadow-xl mt-8">

  <label class="text-lg mb-2 block font-semibold">Ajouter un texte au presse-papier :</label>
  <textarea id="inputText"
            class="w-full h-20 p-3 rounded bg-gray-700 text-white outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Collez ou écrivez ici">
  </textarea>

  <div class="flex mt-3 gap-2">
    <button onclick="addClipboard()"
            class="flex-1 bg-blue-600 hover:bg-blue-700 transition p-3 rounded-lg text-lg font-semibold">
      Ajouter <i class="fa-solid fa-plus"></i>
    </button>
    <button onclick="clearAll()"
            class="flex-1 bg-red-600 hover:bg-red-700 transition p-3 rounded-lg font-semibold">
      Tout supprimer <i class="fa-solid fa-trash"></i>
    </button>
  </div>

  <label class="text-lg mt-5 block font-semibold">Rechercher :</label>
  <input type="text" id="searchText" placeholder="Rechercher dans les clips"
         class="w-full p-3 rounded bg-gray-700 outline-none focus:ring-2 focus:ring-blue-500" 
         oninput="filterList()">

  <h3 class="text-xl font-bold mt-6 mb-3">Mes Presse-Papiers :</h3>
  <div id="clipboardList" class="flex flex-col gap-3 max-h-96 overflow-y-auto">
    <!-- Items générés ici -->
  </div>
</div>

<script>
const STORAGE_KEY = "clipboardManagerData";

function loadClipboard() {
    const data = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    const list = document.getElementById("clipboardList");
    list.innerHTML = "";

    data.forEach((item, index) => {
        const div = document.createElement("div");
        div.className = "clipboard-item bg-gray-700 p-3 rounded flex justify-between items-center cursor-move transition hover:bg-gray-600";
        div.setAttribute("draggable", "true");
        div.dataset.index = index;

        div.innerHTML = `
            <div class="flex-1 break-words">${item.text}</div>
            <div class="flex gap-2">
                <button onclick="copyItem(${index})" class="bg-green-600 hover:bg-green-700 p-2 rounded text-sm"><i class="fa-solid fa-copy"></i></button>
                <button onclick="toggleFavorite(${index})" class="p-2 rounded text-sm ${item.favorite ? 'bg-yellow-500' : 'bg-gray-500'}"><i class="fa-solid fa-star"></i></button>
                <button onclick="deleteItem(${index})" class="bg-red-600 hover:bg-red-700 p-2 rounded text-sm"><i class="fa-solid fa-trash"></i></button>
            </div>
        `;
        list.appendChild(div);

        div.addEventListener('dragstart', dragStart);
        div.addEventListener('dragover', dragOver);
        div.addEventListener('drop', dropItem);
        div.addEventListener('dragend', dragEnd);
    });
}

function addClipboard() {
    const input = document.getElementById("inputText");
    const text = input.value.trim();
    if (!text) return;

    const data = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    data.unshift({text, favorite: false});
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    input.value = "";
    loadClipboard();
}

function copyItem(index) {
    const data = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    navigator.clipboard.writeText(data[index].text);
    alert("Texte copié !");
}

function deleteItem(index) {
    const data = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    data.splice(index, 1);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    loadClipboard();
}

function clearAll() {
    if(confirm("Voulez-vous vraiment tout supprimer ?")) {
        localStorage.removeItem(STORAGE_KEY);
        loadClipboard();
    }
}

function toggleFavorite(index) {
    const data = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    data[index].favorite = !data[index].favorite;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    loadClipboard();
}

function filterList() {
    const filter = document.getElementById("searchText").value.toLowerCase();
    const items = document.querySelectorAll("#clipboardList .clipboard-item");
    items.forEach(item => {
        const text = item.querySelector("div.flex-1").innerText.toLowerCase();
        item.style.display = text.includes(filter) ? "flex" : "none";
    });
}

// Drag & drop
let dragSrcEl = null;

function dragStart(e) {
    dragSrcEl = this;
    this.classList.add('dragging');
}

function dragOver(e) {
    e.preventDefault();
}

function dropItem(e) {
    e.preventDefault();
    if (dragSrcEl === this) return;

    const data = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    const srcIndex = parseInt(dragSrcEl.dataset.index);
    const tgtIndex = parseInt(this.dataset.index);

    // swap
    const temp = data[srcIndex];
    data.splice(srcIndex, 1);
    data.splice(tgtIndex, 0, temp);

    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    loadClipboard();
}

function dragEnd(e) {
    this.classList.remove('dragging');
}

window.onload = loadClipboard;
</script>
</body>
</html>
