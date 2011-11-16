CREATE TABLE  IF NOT EXISTS llx_c_gamme_ligne (
`rowid` INT( 11 ) NOT NULL AUTO_INCREMENT,
`fk_cgam` INT (11),
`composante` VARCHAR (8),
`libelle` VARCHAR( 50 ),
`active` INT( 4 ),
PRIMARY KEY (  `rowid` )
)TYPE=InnoDB AUTO_INCREMENT=1;

