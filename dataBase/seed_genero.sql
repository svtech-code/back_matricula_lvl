-- Seed de opciones de género
-- Insertar opciones de genero para estudiantes

insert into genero (descripcion) values
  ('hombre'),
  ('mujer'),
  ('no especificado')
on conflict do nothing
