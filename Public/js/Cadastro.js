document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".tab");
    const empresaFields = document.querySelector(".empresa-fields");
    const produtorFields = document.querySelector(".produtor-fields");
    const adminFields = document.querySelector(".admin-fields");
    const formCadastro = document.getElementById("registerForm");
    let tipoSelecionado = "empresa"; // padrão inicial

    // Alterna o tipo de usuário ao clicar nas abas
    tabs.forEach((tab) => {
        tab.addEventListener("click", function () {
            tabs.forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            tipoSelecionado = this.getAttribute("data-type");

            empresaFields.style.display =
                tipoSelecionado === "empresa" ? "block" : "none";
            produtorFields.style.display =
                tipoSelecionado === "produtor" ? "block" : "none";
            adminFields.style.display =
                tipoSelecionado === "admin" ? "block" : "none";
        });
    });

    // Envia o formulário via AJAX
    formCadastro.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(formCadastro);
        formData.append("tipo", tipoSelecionado); // adiciona o tipo escolhido

        // Ação do formulário é 'CadUsua.php'
        fetch("CadUsua.php", { 
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Usuário cadastrado com sucesso!");
                    formCadastro.reset();
                    // Redireciona para o login após o sucesso
                    window.location.href = "Login.php"; 
                } else {
                    alert("Erro ao cadastrar: " + data.message);
                }
            })
            .catch((error) => console.error("Erro:", error));
    });
});