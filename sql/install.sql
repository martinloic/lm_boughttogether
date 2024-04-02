CREATE TABLE IF NOT EXISTS `PREFIXlm_product_bought_together` (
    `id_association` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `product_id` INT(10) unsigned  NOT NULL,
    `associated_product_id` INT(10) unsigned NOT NULL,
    `occurrences` INT(10) unsigned NOT NULL,
    PRIMARY KEY (`id_association`)
    -- occurrences INT,
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
