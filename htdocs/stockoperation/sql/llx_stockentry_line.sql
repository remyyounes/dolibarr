CREATE TABLE IF NOT EXISTS `llx_stockentry_line` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_stockentry` int(11) NOT NULL,
  `fk_entrepot` int(11) DEFAULT NULL,
  `fk_societe` int(11) DEFAULT NULL,
  `fk_facture_fourn` int(11) DEFAULT NULL,
  `numerofacture_externe` varchar(32) DEFAULT NULL,
  `date_facture_fourn` date DEFAULT NULL,
  `date_echeance_facture_fourn` date DEFAULT NULL,
  `total_ht_facture` double NOT NULL DEFAULT '0',
  `total_ttc_facture` double NOT NULL DEFAULT '0',
  `coeff_ht` double NOT NULL DEFAULT '0',
  `coeff_ttc` double NOT NULL DEFAULT '0',
  `daom` double DEFAULT NULL,
  `numeroconteneur` varchar(32) DEFAULT NULL,
  `code_list_control` varchar(1) DEFAULT NULL,
  `code_selection` varchar(1) DEFAULT NULL,
  `repartition` varchar(1) DEFAULT NULL,
  `volume` double NOT NULL DEFAULT '0',
  `weight` double NOT NULL DEFAULT '0',
  `weight_unit` int(4) NOT NULL DEFAULT '-3',
  `volume_unit` int(4) NOT NULL DEFAULT '0',
  `type_facture` enum('Frais','Marchandise','Douane') NOT NULL,
  `mode_calcul` enum('valeur','volume','volumeproduct','taxproduct') NOT NULL,
  `accounting` enum('no','yes') NOT NULL,
  `note` text,
  PRIMARY KEY (`rowid`),
  KEY `fkstockentry` (`fk_stockentry`),
  KEY `fkentrepot` (`fk_entrepot`),
  KEY `fkfacturefourn` (`fk_facture_fourn`),
  KEY `fksociete` (`fk_societe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;