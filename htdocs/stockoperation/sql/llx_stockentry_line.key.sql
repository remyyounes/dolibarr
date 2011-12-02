ALTER TABLE `llx_stockentry_line`
  ADD CONSTRAINT `fk_entrepot` FOREIGN KEY (`lieu_entrepot`) REFERENCES `llx_entrepot` (`rowid`),
  ADD CONSTRAINT `fk_societe` FOREIGN KEY (`nom_fk_societe`) REFERENCES `llx_societe` (`rowid`),
  ADD CONSTRAINT `fk_facture_fourn` FOREIGN KEY (` ref_ext_facture_fourn`) REFERENCES ` llx_facture_fourn` (`rowid`),
  ADD CONSTRAINT `fk_stockentry` FOREIGN KEY (`stockentryref`) REFERENCES `llx_stockentry` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

