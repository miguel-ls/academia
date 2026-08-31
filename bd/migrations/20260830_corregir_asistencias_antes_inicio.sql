DELETE ac
FROM asistencia_cliente ac
  JOIN matriculas_detalle md
    ON md.id_matricula_detalle = ac.id_matricula_detalle
WHERE ac.fecha_clase < md.fecha_inicio_clases;