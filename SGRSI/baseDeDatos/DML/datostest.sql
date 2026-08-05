INSERT INTO USUARIO (ci, nombre, correo, clave, activo) VALUES
('11111111', 'Admin Puro', 'admin@sgrsi.edu.uy', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1),
('22222222', 'Docente Puro', 'docente@sgrsi.edu.uy', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1),
('33333333', 'Tecnico Puro', 'tecnico@sgrsi.edu.uy', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1),
('44444444', 'Leandro', 'Leandro@sgrsi.edu.uy', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1),
('55555555', 'Usuario Inactivo', 'inactivo@sgrsi.edu.uy', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 0),
('66666666', 'Usuario Sin Rol', 'sinrol@sgrsi.edu.uy', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1);

INSERT INTO ROL (ci, rol) VALUES
('11111111', 'administrador'),
('22222222', 'docente'),
('33333333', 'tecnico'),
('44444444', 'administrador'),
('44444444', 'tecnico'),
('55555555', 'docente')
;


INSERT INTO ADMINISTRADOR (ci) VALUES ('11111111'), ('44444444');
INSERT INTO DOCENTE (ci) VALUES ('22222222');
INSERT INTO TECNICO (ci) VALUES ('33333333'), ('44444444');