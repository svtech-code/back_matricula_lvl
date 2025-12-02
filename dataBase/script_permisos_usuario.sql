-- =====================================================
-- Script para otorgar TODOS los permisos necesarios
-- a un usuario de la base de datos
-- =====================================================
-- INSTRUCCIONES:
-- 1. Reemplaza 'tu_usuario' con el nombre de tu usuario de BD
-- 2. Reemplaza 'tu_base_datos' con el nombre de tu base de datos
-- 3. Ejecuta este script como usuario root/administrador
-- =====================================================

-- Conectarse a la base de datos
\c tu_base_datos;

-- Otorgar permisos sobre la base de datos
GRANT ALL PRIVILEGES ON DATABASE tu_base_datos TO tu_usuario;

-- Otorgar permisos sobre el schema public
GRANT ALL ON SCHEMA public TO tu_usuario;

-- Otorgar permisos sobre todas las tablas existentes
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO tu_usuario;

-- Otorgar permisos sobre todas las secuencias existentes
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO tu_usuario;

-- Otorgar permisos sobre todas las funciones existentes
GRANT ALL PRIVILEGES ON ALL FUNCTIONS IN SCHEMA public TO tu_usuario;

-- Cambiar el owner de todas las tablas
ALTER TABLE periodo_lectivo OWNER TO tu_usuario;
ALTER TABLE antecedentes_junaeb OWNER TO tu_usuario;
ALTER TABLE antecedentes_salud OWNER TO tu_usuario;
ALTER TABLE antecedentes_pie OWNER TO tu_usuario;
ALTER TABLE antecedentes_localidad OWNER TO tu_usuario;
ALTER TABLE antecedentes_sociales OWNER TO tu_usuario;
ALTER TABLE antecedentes_academicos OWNER TO tu_usuario;
ALTER TABLE antecedentes_personales OWNER TO tu_usuario;
ALTER TABLE genero OWNER TO tu_usuario;
ALTER TABLE estudiante OWNER TO tu_usuario;
ALTER TABLE estado_ficha_matricula OWNER TO tu_usuario;
ALTER TABLE ficha_matricula OWNER TO tu_usuario;
ALTER TABLE formacion_general_opciones OWNER TO tu_usuario;
ALTER TABLE ficha_fg_seleccion OWNER TO tu_usuario;
ALTER TABLE escolaridad OWNER TO tu_usuario;
ALTER TABLE familiar OWNER TO tu_usuario;
ALTER TABLE tipo_familiar OWNER TO tu_usuario;
ALTER TABLE familiar_por_ficha_estudiante OWNER TO tu_usuario;

-- Cambiar el owner de todas las secuencias
ALTER SEQUENCE periodo_lectivo_cod_periodo_lectivo_seq OWNER TO tu_usuario;
ALTER SEQUENCE antecedentes_junaeb_cod_antecedentes_junaeb_seq OWNER TO tu_usuario;
ALTER SEQUENCE antecedentes_salud_cod_antecedentes_salud_seq OWNER TO tu_usuario;
ALTER SEQUENCE antecedentes_pie_cod_antecedentes_pie_seq OWNER TO tu_usuario;
ALTER SEQUENCE antecedentes_localidad_cod_antecedentes_localidad_seq OWNER TO tu_usuario;
ALTER SEQUENCE antecedentes_sociales_cod_antecedentes_sociales_seq OWNER TO tu_usuario;
ALTER SEQUENCE antecedentes_academicos_cod_antecedentes_academicos_seq OWNER TO tu_usuario;
ALTER SEQUENCE antecedentes_personales_cod_antecedentes_personales_seq OWNER TO tu_usuario;
ALTER SEQUENCE genero_cod_genero_seq OWNER TO tu_usuario;
ALTER SEQUENCE estudiante_cod_estudiante_seq OWNER TO tu_usuario;
ALTER SEQUENCE estado_ficha_matricula_cod_estado_ficha_matricula_seq OWNER TO tu_usuario;
ALTER SEQUENCE ficha_matricula_cod_ficha_matricula_seq OWNER TO tu_usuario;
ALTER SEQUENCE formacion_general_opciones_cod_fg_opciones_seq OWNER TO tu_usuario;
ALTER SEQUENCE ficha_fg_seleccion_cod_ficha_fg_seleccion_seq OWNER TO tu_usuario;
ALTER SEQUENCE escolaridad_cod_escolaridad_seq OWNER TO tu_usuario;
ALTER SEQUENCE familiar_cod_familiar_seq OWNER TO tu_usuario;
ALTER SEQUENCE tipo_familiar_cod_tipo_familiar_seq OWNER TO tu_usuario;
ALTER SEQUENCE familiar_por_ficha_estudiante_cod_familiar_por_ficha_estudiante_seq OWNER TO tu_usuario;

-- Otorgar permisos para tablas y secuencias futuras
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO tu_usuario;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON SEQUENCES TO tu_usuario;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON FUNCTIONS TO tu_usuario;

-- Verificación final
\echo '========================================='
\echo 'Permisos otorgados exitosamente a tu_usuario'
\echo '========================================='
