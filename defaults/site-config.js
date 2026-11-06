/* Parámetros del sitio sigetec.cl que se pueden cambiar en el servidor sin recompilar.
   Edite este archivo en cPanel (public_html/config/site-config.js) y guarde. Los despliegues no lo sobrescriben.
   - googleTagManagerId: contenedor de Google Tag Manager, formato "GTM-XXXXXXX". Vacío = sin GTM.
   - googleAnalyticsId:  ID de medición de GA4, formato "G-XXXXXXXXXX". Úselo solo si GA4 NO está ya configurado
                         dentro del contenedor de GTM (si no, las visitas se contarían dos veces).
*/
window.SIGETEC_CONFIG = {
  googleTagManagerId: "GTM-T2DL422P",
  googleAnalyticsId: "",
};
