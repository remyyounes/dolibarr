ALTER TABLE llx_c_product_alcool ADD CONSTRAINT fk_taxe_ctar1 FOREIGN KEY (fk_ctar1) REFERENCES llx_c_taxe (code);
ALTER TABLE llx_c_product_alcool ADD CONSTRAINT fk_taxe_ctar2 FOREIGN KEY (fk_ctar2) REFERENCES llx_c_taxe (code);
ALTER TABLE llx_c_product_alcool ADD CONSTRAINT fk_taxe_cvig FOREIGN KEY (fk_cvig) REFERENCES llx_c_taxe (code);
ALTER TABLE llx_c_product_alcool ADD CONSTRAINT fk_prod_id FOREIGN KEY (fk_product) REFERENCES llx_product (rowid);
