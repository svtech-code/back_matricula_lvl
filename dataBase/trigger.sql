/* * PASO 2: Crear la FUNCIÓN (Hacer esto UNA SOLA VEZ en tu BD)
 * Esta función actualiza la columna 'updated_at' a la hora actual.
 */
CREATE OR REPLACE FUNCTION trigger_set_timestamp()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

/* * PASO 3: Crear el TRIGGER (Hacer esto para CADA tabla)
 * Conecta la tabla 'periodo_lectivo' con la función, antes de cada UPDATE.
 */
CREATE TRIGGER set_timestamp
BEFORE UPDATE ON periodo_lectivo
FOR EACH ROW
EXECUTE FUNCTION trigger_set_timestamp();