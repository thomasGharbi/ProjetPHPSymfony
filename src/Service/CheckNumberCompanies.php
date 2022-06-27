<?php

namespace App\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;


class CheckNumberCompanies
{
    private EntityManagerInterface $manager;

    public function __construct(
        EntityManagerInterface $manager,
    )
    {
        $this->manager = $manager;
    }

    /**
     * @param int $userId
     * @return false|mixed
     * @throws Exception
     */
    public function CheckNumberCompanies(int $userId)
    {

        $sqlQuery = "SELECT COUNT(*) FROM Company WHERE user_id = \"" . $userId . "\"";
        $result = $this->manager->getConnection()->fetchOne($sqlQuery);
        return $result;
    }

}