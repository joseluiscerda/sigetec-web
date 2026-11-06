# sigetec.cl — sitio compilado para despliegue

Este repositorio contiene solo el sitio **ya compilado** (carpeta `site/`), listo para copiarse a `public_html`.
El código fuente (Astro) se mantiene aparte (sigetec-web-v2-fuente.zip).

## Publicar en cPanel con Git Version Control (recomendado)

1. cPanel → **Git™ Version Control** → *Create* → Clone a Repository: URL de este repositorio, ruta
   `repositories/sigetec-web`, nombre `sigetec-web`.
2. *Manage* → pestaña **Pull or Deploy** → **Deploy HEAD Commit**. El archivo `.cpanel.yml` copia `site/` a
   `public_html`.
3. Cada actualización futura: *Update from Remote* y luego *Deploy HEAD Commit*.

## Alternativa: Administrador de archivos

Suba `sigetec-site.zip` a `public_html` y extráigalo ahí (reemplazando). Compruebe permisos 755/644.

## Después de publicar

- Cree `public_html/api/config.php` a partir de `api/config.example.php` (correo del formulario).
- Escriba el ID de Google Analytics en `public_html/config/site-config.js`.
- Pruebe: portada, `/casos/`, `/integraciones/` (calculadora), `/buscar/` (buscador), `/contacto/` y una URL inexistente (404).
