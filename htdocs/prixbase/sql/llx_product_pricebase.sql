CREATE TABLE  IF NOT EXISTS llx_product_pricebase (
`rowid` INT( 11 ) NOT NULL AUTO_INCREMENT,
`fk_product` INT (11),
`pa` DOUBLE,
`pamp` DOUBLE,
`prht` DOUBLE,
`prmpht` DOUBLE,
`prttc` DOUBLE,
`prmpttc` DOUBLE,
`valorisation` VARCHAR( 1 ),
`peremption` INT( 6 ),
PRIMARY KEY (  `rowid` )
)TYPE=InnoDB AUTO_INCREMENT=1;

