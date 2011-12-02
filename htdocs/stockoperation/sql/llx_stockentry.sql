CREATE TABLE IF NOT EXISTS `llx_stockentry` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_societe` int(11) NOT NULL,
  `date_entree` date NOT NULL,
  `date_validation` date DEFAULT NULL,
  `numerodossier` varchar(32) DEFAULT NULL,
  `transport` varchar(32) DEFAULT NULL,
  `numeroplomb` varchar(32) DEFAULT NULL,
  `numeroconteneur` varchar(32) DEFAULT NULL,
  `ref_ext1` varchar(32) DEFAULT NULL,
  `ref_ext2` varchar(32) DEFAULT NULL,
  `note` text,
  `marchandise_description` text,
  `mode_calcul` enum('valeur','volume','volumeproduct','taxproduct') NOT NULL,
  `coeff_rev_global` float DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `fk_societe` (`fk_societe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


