<?php

class UserDataManager extends BaseDataManager
{

    public function getUsers(): array
    {
        $sql = "SELECT `ID`, `userName`, `name`, `type`, `maker`, `phone`, `address`, `comment`, `status` FROM `users`
                WHERE
                    `status` > 0";
        return $this->getDataAsArray($sql);
    }

    public function identifyUser($userName, $password): array
    {
        $sql = "SELECT
                    id, username, name, type
                FROM
                    `users`
                WHERE
                    `status` = 1 AND username = '$userName' AND pass = '$password'";
        return $this->getDataAsArray($sql);
    }

    function findUser($userID): array
    {
        $sql = "SELECT
                    `id`,
                    `username`,
                    `pass`,
                    `name`,
                    `type`,
                    `status`
                FROM
                    `users`
                WHERE
                    id = $userID";
        return $this->getDataAsArray($sql);
    }

    function updatePassword($userID, $password): array
    {
        $sql = "UPDATE `users`
                SET `pass` = '$password'
                WHERE id = $userID";
        return $this->baseInsert($sql);
    }
}