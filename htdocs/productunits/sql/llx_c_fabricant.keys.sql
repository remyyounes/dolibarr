ALTER TABLE llx_c_fabricant ADD CONSTRAINT fk_pays_id FOREIGN KEY (fk_pays) REFERENCES llx_c_pays (rowid);
