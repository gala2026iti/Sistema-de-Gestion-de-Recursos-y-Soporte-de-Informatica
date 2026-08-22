UPDATE PRESTAMO
SET devuelto = :devuelto, entregaAtrasada =
IF(fechaFin < CURRENT_DATE(), TRUE, FALSE)
WHERE id = :idPrestamo;