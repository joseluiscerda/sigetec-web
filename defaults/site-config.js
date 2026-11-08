/* Parámetros del sitio sigetec.cl que se pueden cambiar en el servidor sin recompilar.
   Edite este archivo en cPanel (public_html/config/site-config.js) y guarde. Los despliegues no lo sobrescriben.
   - googleTagManagerId: contenedor de Google Tag Manager, formato "GTM-XXXXXXX". Vacío = sin GTM.
   - googleAnalyticsId:  ID de medición de GA4, formato "G-XXXXXXXXXX". Úselo solo si GA4 NO está ya configurado
                         dentro del contenedor de GTM (si no, las visitas se contarían dos veces).
   Consentimiento de cookies (v2.4). Google (GTM o GA4) se carga solo después de que la persona acepta.
   - consentVersion: fecha (AAAA-MM-DD) de la versión de la política de cookies. Si la cambia, el banner se vuelve a
                     mostrar a todos los visitantes. Hágalo cuando agregue un proveedor o una finalidad nueva.
                     Si se omite, vale "2026-11-08".
   - consentLogUrl:  receptor que guarda la prueba de cada consentimiento (sin IP). Si se omite, vale
                     "/api/consentimiento.php". Escriba "" para no registrar.
*/
window.SIGETEC_CONFIG = {
  googleTagManagerId: "GTM-T2DL422P",
  googleAnalyticsId: "",
  consentVersion: "2026-11-08",
  consentLogUrl: "/api/consentimiento.php",
};
