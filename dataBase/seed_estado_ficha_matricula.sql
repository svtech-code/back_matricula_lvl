
-- Seed de estados de una ficha de matricula
-- Insertar opciones para el estado de una ficha de matricula

insert into estado_ficha_matricula(descripcion) values
  ('prematriculado'),
  ('matriculado'),
  ('rechazado'),
  ('anulado')
on conflict do nothing
