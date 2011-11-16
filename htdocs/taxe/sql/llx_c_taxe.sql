CREATE TABLE  IF NOT EXISTS llx_c_taxe (
`rowid` INT( 11 ) NOT NULL AUTO_INCREMENT,
`code` VARCHAR( 2 ),
`libelle` VARCHAR( 50 ),
`fk_pays` INT( 11 ),
`taxetype` VARCHAR( 1 ),
`mont` FLOAT,
`cdouane` VARCHAR( 30 ),
`active` INT( 4 ),
UNIQUE KEY `code` (`code`),
PRIMARY KEY (  `rowid` )
)TYPE=InnoDB AUTO_INCREMENT=1;

