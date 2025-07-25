ALTER TABLE servicio ADD costo INT DEFAULT NULL;
ALTER TABLE pasajero ADD sexo VARCHAR(10) NOT NULL;
ALTER TABLE reserva ADD preference_id VARCHAR(255) DEFAULT NULL;
ALTER TABLE reserva ADD user_id INT DEFAULT NULL;
ALTER TABLE reserva ADD CONSTRAINT FK_188D2E3BA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id);
ALTER TABLE pago CHANGE usuario user_id INT DEFAULT NULL;
ALTER TABLE pago ADD CONSTRAINT FK_F4DF5F3EA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id);

09-07-25
ALTER TABLE servicio ADD origen INT DEFAULT NULL, ADD destino INT DEFAULT NULL;

ALTER TABLE reserva ADD costo INT NOT NULL,

ALTER TABLE reserva ADD fecha_salida DATETIME DEFAULT NULL, ADD fecha_llegada DATETIME DEFAULT NULL;