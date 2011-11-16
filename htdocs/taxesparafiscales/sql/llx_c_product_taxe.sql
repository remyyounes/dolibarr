CREATE TABLE  IF NOT EXISTS llx_c_product_taxe (
`rowid` INT( 11 ) NOT NULL AUTO_INCREMENT,
`fk_product` INT( 11 ) NOT NULL,
`fk_ctaom` VARCHAR( 2 ),
`fk_ctax1` VARCHAR( 2 ),
`fk_ctax2` VARCHAR ( 2 ),
PRIMARY KEY (  `rowid` )
)TYPE=InnoDB AUTO_INCREMENT=1;

