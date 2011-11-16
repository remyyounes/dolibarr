ALTER TABLE llx_product_gammecodebarre ADD CONSTRAINT fk1_gamme_product FOREIGN KEY (fk_product) REFERENCES llx_product (rowid);
ALTER TABLE llx_product_gammecodebarre ADD CONSTRAINT fk1_gamme_pgam FOREIGN KEY (pgam) REFERENCES llx_c_gamme (cgam);
ALTER TABLE llx_product_gammecodebarre ADD CONSTRAINT fk1_gamme_sgam FOREIGN KEY (sgam) REFERENCES llx_c_gamme (cgam);
ALTER TABLE llx_product_gammecodebarre ADD CONSTRAINT fk1_gamme_compopgam FOREIGN KEY (compopgam) REFERENCES llx_c_gamme_ligne (composante);
ALTER TABLE llx_product_gammecodebarre ADD CONSTRAINT fk1_gamme_composgam FOREIGN KEY (composgam) REFERENCES llx_c_gamme_ligne (composante);
ALTER TABLE llx_product_gammecodebarre ADD CONSTRAINT fk1_gamme_barcodetype FOREIGN KEY (typecode) REFERENCES llx_c_barcode_type (rowid);

