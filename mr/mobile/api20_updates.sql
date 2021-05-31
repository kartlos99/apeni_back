-- შეიქმნა ახალი ცხრილი გაყიდვებისთვის: sales
-- dzveli gayidvebi -> axal gayidvebshi, aseti stilit gadadis

SELECT null, tarigi, obieqtis_id, distributor_id, ludis_id, chek, ert_fasi, 1, `kasri50`, `comment`, CURRENT_TIMESTAMP, 1 FROM `beerinput`
WHERE id <> 5270 AND `kasri50` > 0

-- realizacia Sekvetis mixedviT
SELECT `orderID`, `beerID`, `chek`,`canTypeID`, sum(`count`) AS `count` FROM `sales`
WHERE `orderID` IN (10677, 10673, 10674)
GROUP BY `orderID`, `beerID`, `canTypeID`

