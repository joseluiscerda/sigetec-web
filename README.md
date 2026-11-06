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

## Qué no se despliega

- `public_html/.htaccess`: el de producción (redirecciones del WordPress, cabeceras solo para sigetec.cl,
  manejador PHP 8.3 de cPanel) se mantiene en el servidor. `defaults/htaccess-referencia.txt` es solo referencia.
- `public_html/api/`: `contacto.php` y su `.htaccess` viven en el servidor; la clave del correo está en
  `/home/sigetec1/sigetec-web.config.php`, fuera de la carpeta pública.
- `config/site-config.js`: se crea la primera vez; después se edita en el servidor y los despliegues no lo pisan.

## Después de publicar

- Escriba el ID de Google Analytics en `public_html/config/site-config.js`.
- Pruebe: portada, `/casos/`, `/integraciones/` (calculadora), `/buscar/` (buscador), `/contacto/` y una URL inexistente (404).
