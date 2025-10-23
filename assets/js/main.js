// inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    initializeMobileMenu();
    initializeFormValidation();
    initializeFieldEffects();
});

/**
 * inicializar menú móvil
 */
function initializeMobileMenu() {
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // cerrar menú al hacer clic en un enlace
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    }
}

/**
 * Actualizar barra de progreso del formulario
 */
function updateProgress() {
    const fields = ['nombres', 'email', 'celular', 'institucion', 'area'];
    let completedFields = 0;

    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && field.value.trim() !== '') {
            completedFields++;
        }
    });

    const progress = (completedFields / fields.length) * 100;
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');

    if (progressBar && progressText) {
        progressBar.style.width = progress + '%';
        progressText.textContent = Math.round(progress) + '%';

        // Agregar animación cuando se complete
        if (progress === 100) {
            progressBar.classList.add('pulse-animation');
        } else {
            progressBar.classList.remove('pulse-animation');
        }
    }
}

/**
 * Validar email en tiempo real
 */
function validateEmail() {
    const emailField = document.getElementById('email');
    const emailError = document.getElementById('email-error');

    if (!emailField || !emailError) return;

    const email = emailField.value.trim();

    if (email === '') {
        hideError('email-error');
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('email-error', 'Por favor, ingresa un correo electrónico válido');
        emailField.classList.add('border-red-500');
        emailField.classList.remove('border-gray-200', 'border-green-500');
    } else {
        hideError('email-error');
        emailField.classList.remove('border-red-500');
        emailField.classList.add('border-green-500');
    }
}

/**
 * Validar teléfono en tiempo real
 */
function validatePhone() {
    const phoneField = document.getElementById('celular');
    const phoneError = document.getElementById('celular-error');

    if (!phoneField || !phoneError) return;

    const phone = phoneField.value.trim();

    if (phone === '') {
        hideError('celular-error');
        return;
    }

    const phoneRegex = /^[0-9]{9,15}$/;
    if (!phoneRegex.test(phone)) {
        showError('celular-error', 'El número debe tener entre 9 y 15 dígitos');
        phoneField.classList.add('border-red-500');
        phoneField.classList.remove('border-gray-200', 'border-green-500');
    } else {
        hideError('celular-error');
        phoneField.classList.remove('border-red-500');
        phoneField.classList.add('border-green-500');
    }
}

/**
 * Manejar el mensaje de error
 */
function showError(errorId, message) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
        errorElement.classList.add('fade-in-up');
    }
}

/**
 * Ocultar el mensaje de error
 */
function hideError(errorId) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.classList.add('hidden');
        errorElement.classList.remove('fade-in-up');
    }
}

/**
 * inicializar validaciones del formulario
 */
function initializeFormValidation() {
    const form = document.getElementById('preinscripcionForm');
    const submitBtn = document.getElementById('submitBtn');

    if (!form || !submitBtn) return;

    // Agregar event listeners para validación en tiempo real
    const emailField = document.getElementById('email');
    const phoneField = document.getElementById('celular');

    if (emailField) {
        emailField.addEventListener('blur', validateEmail);
        emailField.addEventListener('input', () => {
            if (emailField.classList.contains('border-red-500')) {
                validateEmail();
            }
        });
    }

    if (phoneField) {
        phoneField.addEventListener('blur', validatePhone);
        phoneField.addEventListener('input', () => {
            if (phoneField.classList.contains('border-red-500')) {
                validatePhone();
            }
        });
    }

    // manejar envío del formulario
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Validar todos los campos
        let isValid = true;
        const fields = ['nombres', 'email', 'celular', 'institucion', 'area'];

        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const errorId = fieldId + '-error';

            if (!field || field.value.trim() === '') {
                showError(errorId, 'Este campo es obligatorio');
                field.classList.add('border-red-500');
                field.classList.remove('border-gray-200', 'border-green-500');
                isValid = false;
            } else {
                hideError(errorId);
                field.classList.remove('border-red-500');
                field.classList.add('border-green-500');
            }
        });

        // Validaciones específicas
        validateEmail();
        validatePhone();

        if (isValid) {
            showLoadingState(submitBtn);

            // Enviar formulario después de un breve delay para mostrar el loading
            setTimeout(() => {
                form.submit();
            }, 1000);
        } else {
            // Scroll al primer error
            scrollToFirstError();
        }
    });
}

/**
 * Mostrar estado de carga en el botón
 */
function showLoadingState(button) {
    button.innerHTML = `
    <div class="loading-spinner mr-3"></div>
    Procesando inscripción...
  `;
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
}

/**
 * Scroll al primer campo con error
 */
function scrollToFirstError() {
    const firstError = document.querySelector('.border-red-500');
    if (firstError) {
        firstError.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        firstError.focus();
    }
}

/**
 * inicializar efectos visuales en los campos
 */
function initializeFieldEffects() {
    const inputs = document.querySelectorAll('input, select');

    inputs.forEach(input => {
        // Efecto de focus
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('transform', 'scale-105');
            this.classList.add('form-field');
        });

        input.addEventListener('blur', function () {
            this.parentElement.classList.remove('transform', 'scale-105');
        });

        // Efecto de typing
        input.addEventListener('input', function () {
            if (this.value.trim() !== '') {
                this.classList.add('border-green-500');
                this.classList.remove('border-gray-200');
            } else {
                this.classList.remove('border-green-500');
                this.classList.add('border-gray-200');
            }
        });
    });
}

/**
 * Utilidad para animaciones de scroll para el scroll de la pagina
 */
function initializeScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    });

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
}

// Inicializar animaciones de scroll cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeScrollAnimations);
