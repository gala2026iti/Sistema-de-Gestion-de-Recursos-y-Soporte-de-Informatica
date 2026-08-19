INSERT INTO USUARIO (ci, nombre, clave, activo) VALUES
('11111111', 'Admin Puro', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1),
('22222222', 'Docente Puro', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1),
('33333333', 'Tecnico Puro', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1),
('44444444', 'Leandro', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', 1);

INSERT INTO ROL (ci, rol) VALUES
('11111111', 'administrador'),
('22222222', 'docente'),
('33333333', 'tecnico'),
('44444444', 'administrador'),
('44444444', 'tecnico')
;

INSERT INTO CORREO (ci, correo) VALUES
('11111111', 'admin@sgrsi.edu.uy'),
('22222222', 'docente@sgrsi.edu.uy'),
('33333333', 'tecnico@sgrsi.edu.uy'),
('44444444', 'Leandro@sgrsi.edu.uy');



INSERT INTO ADMINISTRADOR (ci) VALUES ('11111111'), ('44444444');
INSERT INTO DOCENTE (ci) VALUES ('22222222');
INSERT INTO TECNICO (ci) VALUES ('33333333'), ('44444444');