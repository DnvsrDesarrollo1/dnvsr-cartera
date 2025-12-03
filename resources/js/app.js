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
                element: "#profile_header",
                popover: {
                    title: "Encabezado",
                    description:
                        "Nombre del beneficiario y acciones disponibles (bloquear, actualizar, liquidar).",
                    position: "bottom",
                },
            },
            {
                element: "#profile_actions",
                popover: {
                    title: "Acciones",
                    description:
                        "Acceso rápido a actualización del beneficiario y módulo de liquidación.",
                    position: "left",
                },
            },
            {
                element: "#profile_identifiers",
                popover: {
                    title: "Identificadores",
                    description:
                        "CI, complemento y código de crédito (IDEPRO) con entidad financiera.",
                    position: "bottom",
                },
            },
            {
                element: "#profile_infocards",
                popover: {
                    title: "Resumen",
                    description:
                        "Estado, proyecto, departamento, activación, montos y capital cancelado.",
                    position: "top",
                },
            },
            {
                element: "#profile_plan",
                popover: {
                    title: "Plan de Pagos",
                    description:
                        "Visualiza y abre el plan de pagos. Muestra días de mora si aplica.",
                    position: "top",
                },
            },
            {
                element: "#profile_payments",
                popover: {
                    title: "Historial de Pagos",
                    description:
                        "Consulta pagos registrados y registra nuevos vouchers cuando sea necesario.",
                    position: "top",
                },
            },
            {
                element: "#timelineChart",
                popover: {
                    title: "Gráfica de Capital",
                    description:
                        "Comparación mensual entre capital planificado y pagado, con % de cumplimiento.",
                    position: "top",
                },
            },
            {
                element: "#profile_mgmt_header",
                popover: {
                    title: "Generador",
                    description:
                        "Configura un nuevo plan de pagos y opciones relacionadas.",
                    position: "bottom",
                },
            },
            {
                element: "#profile_mgmt_toggle",
                popover: {
                    title: "Mostrar/Ocultar",
                    description:
                        "Abre o cierra el formulario para editar parámetros.",
                    position: "top",
                },
            },
            {
                element: "#profile_mgmt_form",
                popover: {
                    title: "Formulario",
                    description:
                        "Parámetros principales del plan y opciones adicionales.",
                    position: "top",
                },
            },
            {
                element: "#profile_mgmt_capital",
                popover: {
                    title: "Capital Inicial",
                    description:
                        "Saldo restante del crédito para iniciar el cálculo.",
                    position: "top",
                },
            },
            {
                element: "#profile_mgmt_months",
                popover: {
                    title: "Meses restantes",
                    description:
                        "Cantidad de meses para distribuir el plan de pagos.",
                    position: "top",
                },
            },
            {
                element: "#taza_interes",
                popover: {
                    title: "Interés",
                    description: "Porcentaje de interés aplicado al crédito.",
                    position: "top",
                },
            },
            {
                element: "#seguro",
                popover: {
                    title: "Seguro",
                    description: "Porcentaje de seguro aplicado al crédito.",
                    position: "top",
                },
            },
            {
                element: "#profile_mgmt_options",
                popover: {
                    title: "Opciones",
                    description:
                        "Ajustes adicionales como tipo de generación y fecha de inicio.",
                    position: "top",
                },
            },
            {
                element: "#profile_mgmt_deferral",
                popover: {
                    title: "Diferimiento",
                    description: "Opcional: define cuotas y montos diferidos.",
                    position: "top",
                },
            },
            {
                element: "#profile_mgmt_submit",
                popover: {
                    title: "Vista Previa",
                    description:
                        "Genera la vista previa del plan antes de confirmar.",
                    position: "left",
                },
            },
            {
                element: "#index_status_grid",
                popover: {
                    title: "Estados de crédito",
                    description:
                        "Distribución y conteo de beneficiarios por estado, con porcentaje y barra.",
                    position: "top",
                },
            },
            {
                element: "#index_seguros_card",
                popover: {
                    title: "Planilla de Seguros",
                    description:
                        "Exporta la planilla de seguros para el periodo seleccionado.",
                    position: "top",
                },
            },
            {
                element: "#periodo",
                popover: {
                    title: "Periodo",
                    description:
                        "Selecciona el mes a exportar en formato YYYY-MM.",
                    position: "bottom",
                },
            },
            {
                element: "#frmXlsxSeguros_submit",
                popover: {
                    title: "Exportar",
                    description: "Genera el archivo de la planilla de seguros.",
                    position: "left",
                },
            },
            {
                element: "#index_create_beneficiary",
                popover: {
                    title: "Alta de Beneficiario",
                    description:
                        "Formulario para crear un nuevo beneficiario en el sistema.",
                    position: "top",
                },
            },
            {
                element: "#index_alerts",
                popover: {
                    title: "Notificaciones",
                    description:
                        "Mensajes de éxito y error de operaciones recientes.",
                    position: "top",
                },
            },
            {
                element: "#index_table",
                popover: {
                    title: "Tabla de Beneficiarios",
                    description:
                        "Listado interactivo para consultar y gestionar beneficiarios.",
                    position: "top",
                },
            },
            {
                element: "#new_beneficiary_button",
                popover: {
                    title: "Nuevo Beneficiario",
                    description:
                        "Abre el formulario para registrar un nuevo beneficiario.",
                    position: "bottom",
                },
            },
            {
                element: "#create_beneficiary_modal",
                popover: {
                    title: "Preferencia General",
                    description:
                        "Mensaje personalizado: ajusta aquí tu instrucción principal del proceso.",
                    position: "top",
                },
            },
            {
                element: "#create_beneficiary_header",
                popover: {
                    title: "Encabezado del Modal",
                    description:
                        "Información del responsable y cierre del diálogo.",
                    position: "bottom",
                },
            },
            {
                element: "#create_beneficiary_form",
                popover: {
                    title: "Formulario",
                    description:
                        "Completa los datos del beneficiario y del crédito.",
                    position: "top",
                },
            },
            {
                element: "#create_beneficiary_personal",
                popover: {
                    title: "Información Personal",
                    description:
                        "Nombre, CI, departamento, género, estado y más.",
                    position: "top",
                },
            },
            {
                element: "#create_beneficiary_credit",
                popover: {
                    title: "Información del Crédito",
                    description:
                        "Montos, fechas, plazo e intereses; responsable y gastos.",
                    position: "top",
                },
            },
            {
                element: "#create_beneficiary_submit",
                popover: {
                    title: "Crear Perfil",
                    description:
                        "Guarda el nuevo beneficiario con confirmación de seguridad.",
                    position: "left",
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
