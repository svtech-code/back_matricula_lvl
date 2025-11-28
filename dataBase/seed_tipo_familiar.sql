-- Seed de tipos de familiares
-- Insertar tipos de familiares que puede seleccionar un apoderado

insert into tipo_familiar (descripcion_familiar) values
  ('Padre'),
  ('Madre'),
  ('Tutor legal'),
  ('Abuelo'),
  ('Abuela'),
  ('Tío'),
  ('Tía'),
  ('Hermano'),
  ('Hermana'),
  ('Otro')
on conflict do nothing;
