-- fecha_nac y ciudad opcionales al dar de alta un candidato
ALTER TABLE candidatos
  MODIFY COLUMN fecha_nac DATETIME NULL,
  MODIFY COLUMN ciudades_id INT NULL;
