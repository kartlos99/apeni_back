<?php

class MessageDataManager extends BaseDataManager
{

    function addMessage(
        $messageType,
        $message,
        $modifyUserID
    ): array
    {
        $sql = "INSERT INTO `messages`(
                    `messageType`,
                    `message`,
                    `modifyUserID`
                )
                VALUES(
                       $messageType,
                       '$message',
                       $modifyUserID
                )";
        return $this->baseInsert($sql);
    }

    function getMessages($limit): array {
        $sql = "SELECT `ID`, `messageType`, `message`, `modifyDate`, `modifyUserID` as `userID` FROM `messages`
                WHERE 1
                ORDER BY `modifyDate` desc 
                LIMIT $limit";
        return $this->getDataAsArray($sql);
    }
}