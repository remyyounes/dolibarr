ALTER TABLE `llx_stockentry`
  ADD CONSTRAINT `fk_societe_stockentry` FOREIGN KEY (`fk_societe`) REFERENCES `llx_societe` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;
