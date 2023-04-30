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
}