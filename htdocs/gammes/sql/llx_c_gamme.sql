CREATE TABLE  IF NOT EXISTS llx_c_gamme (
`rowid` INT( 11 ) NOT NULL AUTO_INCREMENT,
`cgam` VARCHAR (8),
`fk_pays` INT (11),
`libelle` VARCHAR( 50 ),
`active` INT( 4 ),
PRIMARY KEY (  `rowid` )
)TYPE=InnoDB AUTO_INCREMENT=1;

