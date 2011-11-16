CREATE TABLE  IF NOT EXISTS llx_c_product_alcool (
`rowid` INT( 11 ) NOT NULL AUTO_INCREMENT,
`fk_product` INT( 11 ) NOT NULL,
`fk_ctar1` VARCHAR( 2 ),
`fk_ctar2` VARCHAR( 2 ),
`fk_cvig` VARCHAR( 2 ),
`cont` FLOAT,
`alcp` FLOAT,
PRIMARY KEY (  `rowid` )
)TYPE=InnoDB AUTO_INCREMENT=1;
