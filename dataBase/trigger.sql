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



/** 
* función trigger para eliminar antecedentes al eliminar una ficha
*/
CREATE OR REPLACE FUNCTION fn_eliminar_antecedentes_ficha()
RETURNS TRIGGER AS $$
BEGIN
    -- Esta función se ejecuta justo DESPUÉS de borrar la ficha.
    -- OLD contiene los datos de la ficha que acabas de eliminar.
    
    -- Borramos los antecedentes usando los IDs que tenía la ficha
    DELETE FROM antecedentes_personales WHERE cod_antecedentes_personales = OLD.cod_antecedentes_personales;
    DELETE FROM antecedentes_academicos WHERE cod_antecedentes_academicos = OLD.cod_antecedentes_academicos;
    DELETE FROM antecedentes_localidad  WHERE cod_antecedentes_localidad  = OLD.cod_antecedentes_localidad;
    DELETE FROM antecedentes_pie        WHERE cod_antecedentes_pie        = OLD.cod_antecedentes_pie;
    DELETE FROM antecedentes_salud      WHERE cod_antecedentes_salud      = OLD.cod_antecedentes_salud;
    DELETE FROM antecedentes_sociales   WHERE cod_antecedentes_sociales   = OLD.cod_antecedentes_sociales;
    DELETE FROM antecedentes_junaeb     WHERE cod_antecedentes_junaeb     = OLD.cod_antecedentes_junaeb;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

/**
* función activadora
*/
CREATE TRIGGER trg_limpiar_antecedentes_al_borrar_ficha
AFTER DELETE ON ficha_matricula
FOR EACH ROW
EXECUTE FUNCTION fn_eliminar_antecedentes_ficha();


/** 
* segunda opcion
*/
-- 1. Función que hace la limpieza
CREATE OR REPLACE FUNCTION fn_eliminar_antecedentes_ficha()
RETURNS TRIGGER AS $$
BEGIN
    -- Borra los antecedentes que pertenecían a la ficha eliminada
    DELETE FROM antecedentes_personales WHERE cod_antecedentes_personales = OLD.cod_antecedentes_personales;
    DELETE FROM antecedentes_academicos WHERE cod_antecedentes_academicos = OLD.cod_antecedentes_academicos;
    DELETE FROM antecedentes_localidad  WHERE cod_antecedentes_localidad  = OLD.cod_antecedentes_localidad;
    DELETE FROM antecedentes_pie        WHERE cod_antecedentes_pie        = OLD.cod_antecedentes_pie;
    DELETE FROM antecedentes_salud      WHERE cod_antecedentes_salud      = OLD.cod_antecedentes_salud;
    DELETE FROM antecedentes_sociales   WHERE cod_antecedentes_sociales   = OLD.cod_antecedentes_sociales;
    DELETE FROM antecedentes_junaeb     WHERE cod_antecedentes_junaeb     = OLD.cod_antecedentes_junaeb;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

-- 2. Conectar la función a la tabla ficha_matricula
CREATE TRIGGER trg_limpiar_antecedentes
AFTER DELETE ON ficha_matricula
FOR EACH ROW
EXECUTE FUNCTION fn_eliminar_antecedentes_ficha();
