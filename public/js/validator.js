/**
 * FLOCK — Agenda Cultural Municipal
 * validator.js: Validación dinámica de formularios en cliente (Issue #13 / DWC RA2 & RA4)
 *
 * Soporta formularios con atributo data-validate="inscripcion" o data-validate="evento".
 * Utiliza clases nativas de Bootstrap 5 (is-invalid / is-valid / .invalid-feedback)
 * sin anular la validación en servidor.
 */

const FlockValidator = (() => {
    const REGEX = {
        nombre: /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s'.-]{2,100}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        telefono: /^[0-9+\s()-]{6,20}$/,
        soloDigitos: /\d/g,
        hora: /^([01]\d|2[0-3]):[0-5]\d$/
    };

    /**
     * Obtiene o crea el contenedor .invalid-feedback asociado al campo.
     */
    function obtenerFeedbackElement(campo) {
        const contenedor = campo.closest('.input-group')
            ? campo.closest('.input-group').parentElement
            : campo.parentElement;

        let feedback = contenedor.querySelector(`.invalid-feedback[data-for="${campo.id}"]`);
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.dataset.for = campo.id || campo.name;
            if (campo.closest('.input-group')) {
                campo.closest('.input-group').insertAdjacentElement('afterend', feedback);
            } else {
                campo.insertAdjacentElement('afterend', feedback);
            }
        }
        return feedback;
    }

    /**
     * Aplica o limpia el estado visual de validación sobre un campo.
     */
    function aplicarEstado(campo, mensajeError) {
        const feedback = obtenerFeedbackElement(campo);

        if (mensajeError) {
            campo.classList.remove('is-valid');
            campo.classList.add('is-invalid');
            campo.setAttribute('aria-invalid', 'true');
            feedback.textContent = mensajeError;
            feedback.style.display = 'block';
            return false;
        }

        campo.classList.remove('is-invalid');
        campo.classList.add('is-valid');
        campo.setAttribute('aria-invalid', 'false');
        feedback.textContent = '';
        feedback.style.display = '';
        return true;
    }

    /**
     * Limpia el estado de error mientras el usuario corrige el valor.
     */
    function limpiarError(campo) {
        if (!campo.classList.contains('is-invalid')) {
            return;
        }
        campo.classList.remove('is-invalid');
        campo.removeAttribute('aria-invalid');
        const contenedor = campo.closest('.input-group')
            ? campo.closest('.input-group').parentElement
            : campo.parentElement;
        const feedback = contenedor.querySelector(`.invalid-feedback[data-for="${campo.id || campo.name}"]`);
        if (feedback) {
            feedback.textContent = '';
            feedback.style.display = '';
        }
    }

    /**
     * Reglas de validación para el formulario de inscripción (#11).
     */
    function validarCampoInscripcion(campo) {
        const valor = campo.value.trim();

        switch (campo.name) {
            case 'nombre':
                if (valor === '') {
                    return 'El nombre y apellidos son obligatorios.';
                }
                if (valor.length < 2 || valor.length > 100) {
                    return 'El nombre debe tener entre 2 y 100 caracteres.';
                }
                if (!REGEX.nombre.test(valor)) {
                    return 'El nombre contiene caracteres no permitidos.';
                }
                return '';

            case 'email':
                if (valor === '') {
                    return 'El correo electrónico es obligatorio.';
                }
                if (valor.length > 120 || !REGEX.email.test(valor)) {
                    return 'Introduce un correo electrónico válido (ejemplo@dominio.com).';
                }
                return '';

            case 'telefono': {
                if (valor === '') {
                    return 'El teléfono de contacto es obligatorio.';
                }
                const digitos = (valor.match(REGEX.soloDigitos) || []).length;
                if (!REGEX.telefono.test(valor) || digitos < 6) {
                    return 'Introduce un teléfono válido con al menos 6 dígitos.';
                }
                return '';
            }

            case 'plazas': {
                const plazas = Number.parseInt(valor, 10);
                const maxPlazas = Number.parseInt(campo.dataset.maxPlazas || '5', 10);
                if (Number.isNaN(plazas) || plazas < 1 || plazas > maxPlazas) {
                    return `Selecciona un número de plazas válido (entre 1 y ${maxPlazas}).`;
                }
                return '';
            }

            case 'observaciones':
                if (valor.length > 500) {
                    return 'Las observaciones no pueden superar los 500 caracteres.';
                }
                return '';

            default:
                return '';
        }
    }

    /**
     * Reglas preparadas para formularios de creación/edición de eventos (#9).
     */
    function validarCampoEvento(campo) {
        const valor = campo.value.trim();

        switch (campo.name) {
            case 'titulo':
                if (valor.length < 3 || valor.length > 150) {
                    return 'El título debe tener entre 3 y 150 caracteres.';
                }
                return '';

            case 'descripcion':
                if (valor.length < 10) {
                    return 'La descripción debe tener al menos 10 caracteres.';
                }
                return '';

            case 'fecha': {
                if (valor === '') {
                    return 'La fecha del evento es obligatoria.';
                }
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                const fechaEvento = new Date(`${valor}T00:00:00`);
                if (Number.isNaN(fechaEvento.getTime()) || fechaEvento < hoy) {
                    return 'La fecha del evento debe ser igual o posterior a hoy.';
                }
                return '';
            }

            case 'hora':
                if (!REGEX.hora.test(valor)) {
                    return 'Introduce una hora válida (HH:MM).';
                }
                return '';

            case 'aforo_total': {
                const aforo = Number.parseInt(valor, 10);
                if (Number.isNaN(aforo) || aforo < 1) {
                    return 'El aforo debe ser un número entero mayor que 0.';
                }
                return '';
            }

            case 'precio': {
                if (valor === '') {
                    return '';
                }
                const precio = Number.parseFloat(valor);
                if (Number.isNaN(precio) || precio < 0) {
                    return 'El precio no puede ser negativo.';
                }
                return '';
            }

            default:
                return '';
        }
    }

    /**
     * Vincula eventos de validación en tiempo real y al enviar sobre un formulario.
     */
    function inicializarFormulario(form, validadorCampo) {
        const campos = Array.from(form.querySelectorAll('input:not([type="hidden"]), select, textarea'));

        campos.forEach((campo) => {
            campo.addEventListener('blur', () => {
                campo.dataset.touched = 'true';
                aplicarEstado(campo, validadorCampo(campo));
            });

            const eventoCorreccion = campo.tagName === 'SELECT' ? 'change' : 'input';
            campo.addEventListener(eventoCorreccion, () => {
                limpiarError(campo);
                if (campo.dataset.touched === 'true') {
                    aplicarEstado(campo, validadorCampo(campo));
                }
            });
        });

        form.addEventListener('submit', (event) => {
            let primerCampoInvalido = null;

            campos.forEach((campo) => {
                campo.dataset.touched = 'true';
                const esValido = aplicarEstado(campo, validadorCampo(campo));
                if (!esValido && !primerCampoInvalido) {
                    primerCampoInvalido = campo;
                }
            });

            if (primerCampoInvalido) {
                event.preventDefault();
                event.stopPropagation();
                primerCampoInvalido.focus();
            }
        });
    }

    function init() {
        document.querySelectorAll('form[data-validate="inscripcion"]').forEach((form) => {
            inicializarFormulario(form, validarCampoInscripcion);
        });

        document.querySelectorAll('form[data-validate="evento"]').forEach((form) => {
            inicializarFormulario(form, validarCampoEvento);
        });
    }

    return { init, validarCampoInscripcion, validarCampoEvento };
})();

document.addEventListener('DOMContentLoaded', () => {
    FlockValidator.init();
});
