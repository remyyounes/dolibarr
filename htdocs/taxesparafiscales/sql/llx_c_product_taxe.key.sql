ALTER TABLE llx_c_product_taxe ADD CONSTRAINT fk_taxe_ctaom FOREIGN KEY (fk_ctaom) REFERENCES llx_c_taxe (code);
ALTER TABLE llx_c_product_taxe ADD CONSTRAINT fk_taxe_ctax1 FOREIGN KEY (fk_ctax1) REFERENCES llx_c_taxe (code);
ALTER TABLE llx_c_product_taxe ADD CONSTRAINT fk_taxe_ctax2 FOREIGN KEY (fk_ctax2) REFERENCES llx_c_taxe (code);
ALTER TABLE llx_c_product_taxe ADD CONSTRAINT fk_prod_id FOREIGN KEY (fk_product) REFERENCES llx_product (rowid);
