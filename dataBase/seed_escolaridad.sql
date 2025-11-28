-- Seed de opciones escolaridad
-- Insertar opciones de escolaridad para familiares

insert into escolaridad (descripcion) values
  ('Sin escolaridad'),
  ('Básica incompleta'),
  ('Básica completa'),
  ('Media incompleta'),
  ('Media completa'),
  ('Técnico nivel superior incompleto'),
  ('Técnico nivel superior completo'),
  ('Universitaria incompleta'),
  ('Universitaria completa'),
  ('Postgrado')
on conflict (descripcion) do nothing
