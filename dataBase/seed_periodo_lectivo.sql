-- Seed de opciones escolaridad
-- Insertar opciones de escolaridad para familiares

insert into periodo_lectivo (
  periodo_lectivo,
  esta_activo, 
  fecha_inicio_clases,
  fecha_termino_clases,
  permite_modificar_fecha,
  autocorrelativo_lista
) values
 ( 2025, true, '2025-03-05', '2025-12-31', false, false ),
 ( 2026, true, '2026-03-05', '2026-12-31', false, false )
on conflict do nothing
