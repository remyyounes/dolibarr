CREATE TABLE  IF NOT EXISTS llx_product_gammecodebarre (
`rowid` INT( 11 ) NOT NULL AUTO_INCREMENT,
`fk_product` INT (11),
`pgam` VARCHAR (8),
`sgam` VARCHAR (8),
`compopgam` VARCHAR (8),
`composgam` VARCHAR (8),
`codebarre` VARCHAR (255),
`typecode` INT (11),
PRIMARY KEY (  `rowid` )
)TYPE=InnoDB AUTO_INCREMENT=1;

