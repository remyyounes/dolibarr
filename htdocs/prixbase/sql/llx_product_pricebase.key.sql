ALTER TABLE llx_product_pricebase  ADD CONSTRAINT fk_price_base FOREIGN KEY (fk_product) REFERENCES llx_product (rowid);
