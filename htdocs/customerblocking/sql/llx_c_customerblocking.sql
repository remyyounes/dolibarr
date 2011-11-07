CREATE TABLE  IF NOT EXISTS llx_c_customerblocking (
`rowid` INT( 11 ) NOT NULL AUTO_INCREMENT,
`code` VARCHAR( 1 ),
`libelle` VARCHAR( 50 ),
`fk_pays` INT( 11 ),
`active` INT( 4 ),
UNIQUE KEY `code` (`code`),
PRIMARY KEY (  `rowid` )
)TYPE=InnoDB AUTO_INCREMENT=1;

