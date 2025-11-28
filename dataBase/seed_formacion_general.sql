-- Seed de opciones de fomración genral
-- Insertar opciones de formación general para selección de estudiantes
insert into formacion_general_opciones (nombre_asignatura, categoria) values
  ('Artes Visuales', 'Artes'),
  ('Artes Musicales', 'Artes'),
  ('Ética', 'Religiones'),
  ('Religión Católica', 'Religiones'),
  ('Religión Evangélica', 'Religiones')
on conflict (nombre_asignatura) do nothing;
