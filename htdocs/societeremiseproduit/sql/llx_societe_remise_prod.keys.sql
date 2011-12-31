ALTER TABLE llx_societe_remise_prod ADD CONSTRAINT fk_soc_id FOREIGN KEY (fk_soc) REFERENCES llx_societe (rowid);
ALTER TABLE llx_societe_remise_prod ADD CONSTRAINT fk_cat_soc_id FOREIGN KEY (fk_categorie_soc) REFERENCES llx_categorie (rowid);
ALTER TABLE llx_societe_remise_prod ADD CONSTRAINT fk_user_author_id FOREIGN KEY (fk_user_author) REFERENCES llx_user (rowid);
ALTER TABLE llx_societe_remise_prod ADD CONSTRAINT fk_prod_id FOREIGN KEY (fk_product) REFERENCES llx_product (rowid);
ALTER TABLE llx_societe_remise_prod ADD CONSTRAINT fk_cat_id FOREIGN KEY (fk_categorie) REFERENCES llx_categorie (rowid);