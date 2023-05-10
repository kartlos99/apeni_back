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
}