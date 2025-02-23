<?php

const AllBeerSqlQuery = "SELECT * FROM `beer` where `beer`.`status` > 0 order by `sortValue`";
const AllBottleSqlQuery = "SELECT * FROM `bottles` WHERE `bottles`.`status` > 0 ORDER BY `sortValue`";