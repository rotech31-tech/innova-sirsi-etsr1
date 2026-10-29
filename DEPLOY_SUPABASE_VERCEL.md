# INNOVA SIRSI — Supabase + GitHub + Vercel

Esta rama está adaptada para Vercel y PostgreSQL/Supabase.

## Supabase
Proyecto creado: `innova-sirsi`
Región: São Paulo (`sa-east-1`)
Schema de aplicación: `app`

La estructura ya fue migrada a PostgreSQL. El sistema usa el pooler de transacciones de Supabase en Vercel.

## Variables de Vercel
Ya configuradas en el proyecto:
- DB_HOST
- DB_PORT=6543
- DB_NAME=postgres
- DB_USER=postgres.<project-ref>
- DB_SCHEMA=app
- APP_BASE_URL (vacío)
- SESSION_SECRET (secreto)

Falta configurar solamente `DB_PASS` directamente en Vercel como variable **Sensitive**, usando la contraseña de la base Supabase. No pegar esa contraseña en chats ni commits.

## GitHub
Crear un repositorio vacío llamado `innova-sirsi-etsr` y subir este contenido. Luego vincularlo al proyecto Vercel `innova-sirsi-etsr`.

## PHP en Vercel
Se usa `vercel-php@0.9.0` y `pdo_pgsql`.

## Archivos subidos
Las imágenes ya no se guardan en el filesystem efímero de Vercel. Se almacenan en PostgreSQL (`app.archivos`) y se sirven mediante `archivo.php`.

## Sesiones
Las sesiones PHP se almacenan en cookies cifradas para funcionar correctamente en arquitectura serverless.

## Seguridad
Las credenciales se leen desde variables de entorno; no se incluyen contraseñas en el repositorio.
