# Sistema Integral de Relevamiento de Salas de Informática ETSR - UTU

**Empresa desarrolladora:** INNOVA SRL  
**Versión:** V8 Final — Gestión por turnos

Aplicación PHP + MySQL para XAMPP orientada a la gestión de salas de informática de la ETSR - UTU.

## Turnos

El sistema trabaja con tres turnos institucionales:

- Matutino
- Vespertino
- Nocturno

Cada Encargado de Sala tiene un turno asignado y administra únicamente profesores, horarios, anuncios y tickets de ese turno. Un profesor puede estar asociado a más de un turno usando la misma cuenta.

Los registros de Check-In, Check-Out e Historial de Salas son compartidos entre turnos para mantener continuidad sobre el estado físico de las salas.

## Instalación recomendada

Esta versión utiliza una base limpia porque modifica la estructura de turnos y los datos actuales son de prueba.

1. Copiar la carpeta como `C:\xampp\htdocs\innova_sirsi_etsr`.
2. Encender Apache y MySQL.
3. Ejecutar `database/innova_sirsi.sql` en MySQL Workbench.
4. Abrir `http://localhost/innova_sirsi_etsr/setup_admin.php`.
5. Crear el primer Encargado y seleccionar su turno.
6. Iniciar sesión y crear los Encargados de los otros turnos desde **Profesores**.
7. Eliminar `setup_admin.php` después de completar la configuración inicial.

Consultá `GUIA_INSTALACION.txt` y `CAMBIOS_V8.txt` para más detalles.
