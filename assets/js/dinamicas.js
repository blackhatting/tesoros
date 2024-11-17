function mostrarSeccion(seccionId) {
    // Ocultar todas las secciones primero
    const secciones = document.querySelectorAll('.section');
    secciones.forEach(seccion => {
        seccion.classList.remove('active');
    });

    // Mostrar la sección seleccionada
    const seccionSeleccionada = document.getElementById(seccionId);
    if (seccionSeleccionada) {
        seccionSeleccionada.classList.add('active');
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("registroForm");
    
    // Verificar si el formulario de registro existe
    if (form) {
        const passwordInput = document.getElementById("contrasena");
        const confirmPasswordInput = document.getElementById("confirm_password");
        const passwordHelp = document.getElementById("passwordHelp");
        const confirmPasswordHelp = document.getElementById("confirmPasswordHelp");
        const lengthRequirement = document.getElementById("lengthRequirement");
        const uppercaseRequirement = document.getElementById("uppercaseRequirement");
        const lowercaseRequirement = document.getElementById("lowercaseRequirement");
        const numberRequirement = document.getElementById("numberRequirement");
        const specialCharRequirement = document.getElementById("specialCharRequirement");
        const noConsecutiveRequirement = document.getElementById("noConsecutiveRequirement");
        const noSequenceRequirement = document.getElementById("noSequenceRequirement");

        // Inicializar los mensajes de ayuda como vacíos
        passwordHelp.textContent = "";
        confirmPasswordHelp.textContent = "";
        lengthRequirement.style.color = "initial";
        uppercaseRequirement.style.color = "initial";
        lowercaseRequirement.style.color = "initial";
        numberRequirement.style.color = "initial";
        specialCharRequirement.style.color = "initial";
        noConsecutiveRequirement.style.color = "initial";
        noSequenceRequirement.style.color = "initial";

        // Función de detección de secuencia...
        function detectarSecuencia(contrasena) {
            const secuenciaNumerica = /012|123|234|345|456|567|678|789|987|876|765|654|543|432|321|210/;
            const secuenciaAlfabetica = /abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz|zyx|yxw|xwv|wvu|vut|uts|tsr|srq|rqp|qpo|pon|onm|nml|mlk|lkj|kji|jih|ihg|hgf|gfe|fed|edc|dcb|cba/;
            return secuenciaNumerica.test(contrasena) || secuenciaAlfabetica.test(contrasena);
        }

        function validarContrasena(contrasena) {
            const requisitos = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}[\]|:;<>,.?~]).{8,}$/;
            const consecutivos = /(.)\1{2,}/;

            // Actualizar el estado de cada requisito visualmente
            lengthRequirement.style.color = contrasena.length >= 8 ? "green" : "red";
            uppercaseRequirement.style.color = /[A-Z]/.test(contrasena) ? "green" : "red";
            lowercaseRequirement.style.color = /[a-z]/.test(contrasena) ? "green" : "red";
            numberRequirement.style.color = /\d/.test(contrasena) ? "green" : "red";
            specialCharRequirement.style.color = /[!@#$%^&*()_+{}[\]|:;<>,.?~]/.test(contrasena) ? "green" : "red";
            noConsecutiveRequirement.style.color = !consecutivos.test(contrasena) ? "green" : "red";
            noSequenceRequirement.style.color = !detectarSecuencia(contrasena) ? "green" : "red";

            return requisitos.test(contrasena) && !consecutivos.test(contrasena) && !detectarSecuencia(contrasena);
        }

        // Validación de la contraseña principal
        passwordInput.addEventListener("input", function() {
            const contrasena = passwordInput.value;

            if (!validarContrasena(contrasena)) {
                passwordHelp.textContent = "La contraseña no cumple todos los requisitos.";
                passwordHelp.style.color = "red";
            } else {
                passwordHelp.textContent = "La contraseña es válida.";
                passwordHelp.style.color = "green";
            }

            // Validar si las contraseñas coinciden
            if (confirmPasswordInput.value) {
                const confirmContrasena = confirmPasswordInput.value;
                if (confirmContrasena !== contrasena) {
                    confirmPasswordHelp.textContent = "Las contraseñas no coinciden.";
                    confirmPasswordHelp.style.color = "red";
                } else {
                    confirmPasswordHelp.textContent = "Las contraseñas coinciden.";
                    confirmPasswordHelp.style.color = "green";
                }
            } else {
                confirmPasswordHelp.textContent = ""; // Limpiar el mensaje si el campo está vacío
            }
        });

        // Validación de la confirmación de contraseña
        confirmPasswordInput.addEventListener("input", function() {
            const contrasena = passwordInput.value;

            if (confirmPasswordInput.value !== contrasena) {
                confirmPasswordHelp.textContent = "Las contraseñas no coinciden.";
                confirmPasswordHelp.style.color = "red";
            } else {
                confirmPasswordHelp.textContent = "Las contraseñas coinciden.";
                confirmPasswordHelp.style.color = "green";
            }
        });

        // Manejo del evento de envío del formulario
        form.addEventListener("submit", function(event) {
            const contrasena = passwordInput.value;
            const confirmContrasena = confirmPasswordInput.value;

            if (!validarContrasena(contrasena)) {
                event.preventDefault(); // Prevenir el envío del formulario
                passwordHelp.textContent = "Corrige los errores en la contraseña antes de enviar el formulario.";
                passwordHelp.style.color = "red";
            } else if (contrasena !== confirmContrasena) {
                event.preventDefault(); // Prevenir el envío del formulario
                confirmPasswordHelp.textContent = "Las contraseñas no coinciden.";
                confirmPasswordHelp.style.color = "red";
            }
        });

        // Manejo del toggle de la visibilidad de las contraseñas
        const togglePassword = document.getElementById("togglePassword");
        const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

        if (togglePassword) { // Verifica que togglePassword exista
            togglePassword.addEventListener("click", function() {
                const tipo = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", tipo);
                this.textContent = tipo === "password" ? "👁️" : "🙈"; // Cambiar ícono
            });
        }

        if (toggleConfirmPassword) { // Verifica que toggleConfirmPassword exista
            toggleConfirmPassword.addEventListener("click", function() {
                const tipo = confirmPasswordInput.getAttribute("type") === "password" ? "text" : "password";
                confirmPasswordInput.setAttribute("type", tipo);
                this.textContent = tipo === "password" ? "👁️" : "🙈"; // Cambiar ícono
            });
        }

        console.log("Archivo JS cargado correctamente.");
    } else {
        console.log("El formulario de registro no está presente en esta vista.");
    }
});
