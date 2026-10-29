-- INNOVA SIRSI V11.2 - Simplificación de registros de equipos
-- Ejecutar una sola vez sobre una base V11/V8 ya existente.
USE innova_sirsi;

ALTER TABLE equipos
    DROP COLUMN nombre,
    DROP COLUMN modelo,
    DROP COLUMN numero_serie,
    DROP COLUMN ip,
    DROP COLUMN mac;

-- La tabla equipos conserva: sala, código de inventario, tipo, marca,
-- sistema operativo, estado, observaciones, QR y campos internos.
