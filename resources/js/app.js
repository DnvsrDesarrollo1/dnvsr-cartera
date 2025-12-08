import "./bootstrap";
import { driver } from "driver.js";
import "driver.js/dist/driver.css";

window.startBeneficiaryTour = () => {
    const driverObj = driver({
        allowClose: true,
        animate: true,
        overlayOpacity: 0.35,
        stagePadding: 6,
        showProgress: true,
        steps: [
            {
                element: "#beneficiary_header",
                popover: {
                    title: "Perfil del Beneficiario",
                    description:
                        "Vista general del beneficiario con información clave y acciones rápidas.",
                    position: "bottom",
                },
            },
            {
                element: "#identity_info",
                popover: {
                    title: "Identificación",
                    description:
                        "Nombre, CI, y código de crédito (IDEPRO) para identificación rápida.",
                    position: "bottom",
                },
            },
            {
                element: "#action_buttons",
                popover: {
                    title: "Barra de Acciones",
                    description:
                        "Herramientas para editar datos del beneficiario, realizar liquidaciones o consultar ayuda.",
                    position: "left",
                },
            },
            {
                element: "#status_indicators",
                popover: {
                    title: "Estado y Mora",
                    description:
                        "Indicadores visuales del estado actual del crédito y días de atraso en pagos.",
                    position: "right",
                },
            },
            {
                element: "#financial_summary",
                popover: {
                    title: "Resumen Financiero",
                    description:
                        "Balance general: saldo de crédito, capital cancelado y barra de progreso de pagos.",
                    position: "right",
                },
            },
            {
                element: "#details_list",
                popover: {
                    title: "Detalles Adicionales",
                    description:
                        "Información complementaria como proyecto, ubicación y gastos adicionales.",
                    position: "right",
                },
            },
            {
                element: "#chart_card",
                popover: {
                    title: "Evolución de Pagos",
                    description:
                        "Gráfico interactivo que compara el plan de pagos vs. los pagos realizados en el tiempo.",
                    position: "left",
                },
            },
            {
                element: "#plan_management",
                popover: {
                    title: "Gestión de Planes",
                    description:
                        "Accede a modificaciones del plan (mutaciones) o descarga el PDF oficial.",
                    position: "left",
                },
            },
            {
                element: "#payments_management",
                popover: {
                    title: "Control de Pagos",
                    description:
                        "Registra nuevos pagos o visualiza el historial detallado de transacciones.",
                    position: "left",
                },
            },
            {
                element: "#plan_generator",
                popover: {
                    title: "Generador de Planes",
                    description:
                        "Herramienta avanzada para reestructurar la deuda o generar nuevos planes de pago. Haz clic para expandir.",
                    position: "top",
                },
            },
        ].filter((s) => document.querySelector(s.element)),
    });

    driverObj.drive();
};

window.startCreateBeneficiaryTour = async () => {
    const ensureOpen = () =>
        new Promise((resolve) => {
            if (document.querySelector("#create_beneficiary_modal"))
                return resolve();
            const btn = document.querySelector("#new_beneficiary_button");
            if (btn) btn.click();
            const start = Date.now();
            const timer = setInterval(() => {
                if (document.querySelector("#create_beneficiary_modal")) {
                    clearInterval(timer);
                    resolve();
                } else if (Date.now() - start > 2000) {
                    clearInterval(timer);
                    resolve();
                }
            }, 100);
        });

    await ensureOpen();

    const driverObj = driver({
        allowClose: true,
        animate: true,
        overlayOpacity: 0.4,
        stagePadding: 8,
        showProgress: true,
        steps: [
            {
                element: "#create_beneficiary_modal",
                popover: {
                    title: "Preferencia General",
                    description:
                        "Mensaje personalizado: instrucciones generales del proceso de registro.",
                    position: "top",
                },
            },
            {
                element: "#create_beneficiary_header",
                popover: {
                    title: "Encabezado",
                    description: "Responsable y cierre del diálogo.",
                    position: "bottom",
                },
            },
            {
                element: "#create_beneficiary_form",
                popover: {
                    title: "Formulario",
                    description: "Completa datos personales y del crédito.",
                    position: "top",
                },
            },
            {
                element: "#create_beneficiary_personal",
                popover: {
                    title: "Información Personal",
                    description:
                        "Nombre, CI, IDEPRO, lugar de expedición y más.",
                    position: "top",
                },
            },
            {
                element: "#nombre",
                popover: {
                    title: "Nombre",
                    description: "Nombre completo en mayúsculas.",
                    position: "bottom",
                },
            },
            {
                element: "#ci",
                popover: {
                    title: "C.I.",
                    description: "Documento de identidad del beneficiario.",
                    position: "bottom",
                },
            },
            {
                element: "#idepro",
                popover: {
                    title: "IDEPRO",
                    description: "Código autogenerado del crédito.",
                    position: "bottom",
                },
            },
            {
                element: "#expedido",
                popover: {
                    title: "Expedido",
                    description: "Departamento de emisión del CI.",
                    position: "bottom",
                },
            },
            {
                element: "#departamento",
                popover: {
                    title: "Departamento",
                    description: "Lugar de residencia.",
                    position: "bottom",
                },
            },
            {
                element: "#genero",
                popover: {
                    title: "Género",
                    description: "Selecciona el género del beneficiario.",
                    position: "bottom",
                },
            },
            {
                element: "#estado",
                popover: {
                    title: "Estado",
                    description: "Estado inicial del crédito.",
                    position: "bottom",
                },
            },
            {
                element: "#fecha_nacimiento",
                popover: {
                    title: "Nacimiento",
                    description: "Fecha de nacimiento.",
                    position: "bottom",
                },
            },
            {
                element: "#create_beneficiary_credit",
                popover: {
                    title: "Información del Crédito",
                    description: "Montos, fechas, plazo e intereses.",
                    position: "top",
                },
            },
            {
                element: "#monto_credito",
                popover: {
                    title: "Monto Crédito",
                    description: "Valor total del crédito.",
                    position: "bottom",
                },
            },
            {
                element: "#monto_activado",
                popover: {
                    title: "Monto Activado",
                    description: "Base para generar el plan.",
                    position: "bottom",
                },
            },
            {
                element: "#total_activado",
                popover: {
                    title: "Total Activado",
                    description: "Relacionado a migración IEF.",
                    position: "bottom",
                },
            },
            {
                element: "#saldo_credito",
                popover: {
                    title: "Saldo Crédito",
                    description: "Monto restante por pagar.",
                    position: "bottom",
                },
            },
            {
                element: "#monto_recuperado",
                popover: {
                    title: "Monto Recuperado",
                    description: "Recuperado por la entidad.",
                    position: "bottom",
                },
            },
            {
                element: "#fecha_activacion",
                popover: {
                    title: "Fecha de Activación",
                    description: "Inicio del crédito.",
                    position: "bottom",
                },
            },
            {
                element: "#plazo_credito",
                popover: {
                    title: "Plazo",
                    description: "Meses para cancelar el crédito.",
                    position: "bottom",
                },
            },
            {
                element: "#tasa_interes",
                popover: {
                    title: "Interés",
                    description: "Porcentaje de interés.",
                    position: "bottom",
                },
            },
            {
                element: "#user_id",
                popover: {
                    title: "Responsable",
                    description: "Cuenta que registra el beneficiario.",
                    position: "bottom",
                },
            },
            {
                element: "#gastos_administrativos",
                popover: {
                    title: "Gastos Administrativos",
                    description: "Costos administrativos.",
                    position: "bottom",
                },
            },
            {
                element: "#gastos_judiciales",
                popover: {
                    title: "Gastos Judiciales",
                    description: "Costos judiciales.",
                    position: "bottom",
                },
            },
            {
                element: "#gastos_notariales",
                popover: {
                    title: "Gastos Notariales",
                    description: "Costos notariales.",
                    position: "bottom",
                },
            },
            {
                element: "#create_beneficiary_submit",
                popover: {
                    title: "Crear Perfil",
                    description: "Guarda el nuevo beneficiario.",
                    position: "left",
                },
            },
            {
                element: "#create_beneficiary_modal",
                popover: {
                    title: "Que procede?",
                    description: "Una vez completada la generacion del nuevo perfil de beneficiario, la pagina se recargará para mostrarte el nuevo perfil en la tabla de beneficiarios. Ahi se puede proceder a VER PERFIL y realizar los ejercicios de edicion o cancelacion de la cuenta.",
                    position: "top",
                },
            },
        ].filter((s) => document.querySelector(s.element)),
    });

    driverObj.drive();
};
